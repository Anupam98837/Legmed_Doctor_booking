<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\PersistsAdminNotifications;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HospitalController extends Controller
{
    use PersistsAdminNotifications;

    private const MODULE_NAME = 'hospitals';
    private const TABLE_NAME = 'hospitals';
    private const PAGE_HREF_CREATE = '/hospital/create';
    private const PAGE_HREF_MANAGE = '/hospital/manage';

    private function actor(Request $request): array
    {
        return [
            'id'   => (int) ($request->attributes->get('auth_tokenable_id') ?? 0),
            'role' => (string) ($request->attributes->get('auth_role') ?? ''),
            'type' => (string) ($request->attributes->get('auth_tokenable_type') ?? ''),
        ];
    }

    private function actorName(int $id): ?string
    {
        if ($id <= 0 || !Schema::hasTable('users')) {
            return null;
        }

        return DB::table('users')->where('id', $id)->value('name');
    }

    private function toJsonOrNull($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    private function logActivity(
        Request $request,
        string $activity,
        ?int $recordId = null,
        ?array $changedFields = null,
        $oldValues = null,
        $newValues = null,
        ?string $note = null
    ): void {
        try {
            if (!Schema::hasTable('user_data_activity_log')) {
                return;
            }

            $actor = $this->actor($request);

            DB::table('user_data_activity_log')->insert([
                'performed_by'      => (int) ($actor['id'] ?? 0),
                'performed_by_role' => $actor['role'] ?: null,
                'ip'                => $request->ip(),
                'user_agent'        => substr((string) $request->userAgent(), 0, 512),
                'activity'          => $activity,
                'module'            => self::MODULE_NAME,
                'table_name'        => self::TABLE_NAME,
                'record_id'         => $recordId,
                'changed_fields'    => $changedFields ? $this->toJsonOrNull(array_values($changedFields)) : null,
                'old_values'        => $this->toJsonOrNull($oldValues),
                'new_values'        => $this->toJsonOrNull($newValues),
                'log_note'          => $note,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        } catch (\Throwable $e) {
            // Never block the main flow for logging.
        }
    }

    private function makeSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name, '-');
        $slug = $base !== '' ? $base : 'hospital';

        $i = 1;
        while (true) {
            $query = DB::table('hospitals')->where('slug', $slug);

            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $i++;
            $slug = ($base !== '' ? $base : 'hospital') . '-' . $i;
        }
    }

    private function normalizeStatus(?string $status): string
    {
        $value = strtolower(trim((string) $status));
        return in_array($value, ['active', 'inactive'], true) ? $value : 'active';
    }

    private function normalizeBool(Request $request, string $key, ?bool $default = null): ?bool
    {
        if (!$request->exists($key)) {
            return $default;
        }

        $value = filter_var($request->input($key), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        return $value === null ? $default : $value;
    }

    private function parseArrayInput($value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            $items = array_values(array_filter(array_map(function ($item) {
                if (is_scalar($item)) {
                    return trim((string) $item);
                }
                return null;
            }, $value), fn ($item) => $item !== null && $item !== ''));

            return empty($items) ? null : $items;
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $this->parseArrayInput($decoded);
        }

        $items = preg_split('/[\r\n,]+/', $text) ?: [];
        $items = array_values(array_filter(array_map(fn ($item) => trim((string) $item), $items), fn ($item) => $item !== ''));

        return empty($items) ? null : $items;
    }

    private function parseObjectInput($value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return empty($value) ? null : $value;
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        $decoded = json_decode($text, true);
        return (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : null;
    }

    private function decodeJsonValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    private function publicAssetUrl(?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        if (preg_match('#^https?://#i', (string) $value)) {
            return (string) $value;
        }

        $path = parse_url($value, PHP_URL_PATH);
        $path = $path ?: $value;
        $path = '/' . ltrim($path, '/');

        return url($path);
    }

    private function saveHospitalAsset($uploadedFile, string $prefix)
    {
        if (!$uploadedFile || !$uploadedFile->isValid()) {
            return false;
        }

        $destDir = public_path('assets/media/images/hospitals');
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $ext = strtolower($uploadedFile->getClientOriginalExtension() ?: 'bin');
        $filename = $prefix . '_' . date('Ymd_His') . '_' . Str::lower(Str::random(16)) . '.' . $ext;

        $uploadedFile->move($destDir, $filename);

        return '/assets/media/images/hospitals/' . $filename;
    }

    private function deleteManagedAsset(?string $url): void
    {
        if (empty($url)) {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH);
        $path = $path ?: $url;
        $path = '/' . ltrim($path, '/');

        if (!Str::startsWith($path, '/assets/media/images/hospitals/')) {
            return;
        }

        $abs = public_path(ltrim($path, '/'));
        if (File::exists($abs)) {
            @File::delete($abs);
        }
    }

    private function resolveHospital($identifier, bool $withDeleted = false): ?object
    {
        $query = DB::table('hospitals');

        if (!$withDeleted) {
            $query->whereNull('deleted_at');
        }

        if (is_numeric($identifier)) {
            return $query->where('id', (int) $identifier)->first();
        }

        $identifier = (string) $identifier;

        return $query->where(function ($sub) use ($identifier) {
            $sub->where('uuid', $identifier)
                ->orWhere('slug', $identifier);
        })->first();
    }

    private function hospitalEditLink(?string $uuid): string
    {
        return self::PAGE_HREF_CREATE . ($uuid ? ('?uuid=' . urlencode($uuid)) : '');
    }

    private function hospitalPayload(object $row): array
    {
        return [
            'id'                              => (int) $row->id,
            'uuid'                            => (string) ($row->uuid ?? ''),
            'name'                            => (string) ($row->name ?? ''),
            'short_name'                      => $row->short_name,
            'slug'                            => (string) ($row->slug ?? ''),
            'hospital_code'                   => $row->hospital_code,
            'registration_number'             => $row->registration_number,
            'license_number'                  => $row->license_number,
            'established_year'                => $row->established_year ? (int) $row->established_year : null,
            'hospital_type'                   => $row->hospital_type,
            'ownership_type'                  => $row->ownership_type,
            'email'                           => $row->email,
            'phone_number'                    => $row->phone_number,
            'alternative_phone_number'        => $row->alternative_phone_number,
            'whatsapp_number'                 => $row->whatsapp_number,
            'emergency_contact_number'        => $row->emergency_contact_number,
            'website'                         => $row->website,
            'logo'                            => $this->publicAssetUrl($row->logo ?? null),
            'logo_path'                       => (string) ($row->logo ?? ''),
            'image'                           => $this->publicAssetUrl($row->image ?? null),
            'image_path'                      => (string) ($row->image ?? ''),
            'gallery'                         => $this->decodeJsonValue($row->gallery ?? null),
            'description'                     => $row->description,
            'short_description'               => $row->short_description,
            'address_line_1'                  => $row->address_line_1,
            'address_line_2'                  => $row->address_line_2,
            'landmark'                        => $row->landmark,
            'area'                            => $row->area,
            'city'                            => $row->city,
            'state'                           => $row->state,
            'country'                         => $row->country,
            'pincode'                         => $row->pincode,
            'latitude'                        => $row->latitude !== null ? (float) $row->latitude : null,
            'longitude'                       => $row->longitude !== null ? (float) $row->longitude : null,
            'map_url'                         => $row->map_url,
            'departments'                     => $this->decodeJsonValue($row->departments ?? null),
            'services'                        => $this->decodeJsonValue($row->services ?? null),
            'facilities'                      => $this->decodeJsonValue($row->facilities ?? null),
            'specialities'                    => $this->decodeJsonValue($row->specialities ?? null),
            'timings'                         => $this->decodeJsonValue($row->timings ?? null),
            'social_links'                    => $this->decodeJsonValue($row->social_links ?? null),
            'accreditations'                  => $this->decodeJsonValue($row->accreditations ?? null),
            'insurances_accepted'             => $this->decodeJsonValue($row->insurances_accepted ?? null),
            'bed_count'                       => (int) ($row->bed_count ?? 0),
            'ambulance_available'             => (bool) ($row->ambulance_available ?? false),
            'emergency_available'             => (bool) ($row->emergency_available ?? false),
            'pharmacy_available'              => (bool) ($row->pharmacy_available ?? false),
            'lab_available'                   => (bool) ($row->lab_available ?? false),
            'icu_available'                   => (bool) ($row->icu_available ?? false),
            'cashless_available'              => (bool) ($row->cashless_available ?? false),
            'online_consultation_available'   => (bool) ($row->online_consultation_available ?? false),
            'appointment_booking_available'   => (bool) ($row->appointment_booking_available ?? true),
            'average_rating'                  => (float) ($row->average_rating ?? 0),
            'review_count'                    => (int) ($row->review_count ?? 0),
            'seo_title'                       => $row->seo_title,
            'seo_description'                 => $row->seo_description,
            'status'                          => (string) ($row->status ?? 'active'),
            'sort_order'                      => (int) ($row->sort_order ?? 0),
            'created_by'                      => $row->created_by ? (int) $row->created_by : null,
            'created_at_ip'                   => $row->created_at_ip,
            'created_at'                      => $row->created_at,
            'updated_at'                      => $row->updated_at,
            'deleted_at'                      => $row->deleted_at,
            'metadata'                        => $this->decodeJsonValue($row->metadata ?? null),
        ];
    }

    private function hospitalHeaderMenuId(Request $request): ?int
    {
        $existing = DB::table('dashboard_menu')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereRaw('LOWER(name) = ?', ['hospital'])
                    ->orWhereRaw('LOWER(name) = ?', ['hospitals']);
            })
            ->where(function ($q) {
                $q->whereNull('href')->orWhere('href', '');
            })
            ->orderBy('id')
            ->first();

        if ($existing) {
            return (int) $existing->id;
        }

        $actor = $this->actor($request);

        return (int) DB::table('dashboard_menu')->insertGetId([
            'parent_id'        => null,
            'uuid'             => (string) Str::uuid(),
            'position'         => 0,
            'name'             => 'Hospital',
            'icon_class'       => 'fa-solid fa-hospital',
            'href'             => null,
            'description'      => 'Hospital management menu',
            'is_dropdown_head' => 1,
            'status'           => 'Active',
            'created_at'       => now(),
            'updated_at'       => now(),
            'created_by'       => $actor['id'] ?: null,
            'updated_by'       => $actor['id'] ?: null,
            'created_at_ip'    => $request->ip(),
            'updated_at_ip'    => $request->ip(),
            'deleted_at'       => null,
        ]);
    }

    private function ensureMenuItem(
        Request $request,
        int $parentId,
        string $name,
        string $href,
        string $icon,
        string $description
    ): int {
        $menu = DB::table('dashboard_menu')
            ->whereNull('deleted_at')
            ->where('href', $href)
            ->first();

        if ($menu) {
            return (int) $menu->id;
        }

        $actor = $this->actor($request);

        return (int) DB::table('dashboard_menu')->insertGetId([
            'parent_id'        => $parentId,
            'uuid'             => (string) Str::uuid(),
            'position'         => 0,
            'name'             => $name,
            'icon_class'       => $icon,
            'href'             => $href,
            'description'      => $description,
            'is_dropdown_head' => 0,
            'status'           => 'Active',
            'created_at'       => now(),
            'updated_at'       => now(),
            'created_by'       => $actor['id'] ?: null,
            'updated_by'       => $actor['id'] ?: null,
            'created_at_ip'    => $request->ip(),
            'updated_at_ip'    => $request->ip(),
            'deleted_at'       => null,
        ]);
    }

    private function ensurePrivilege(Request $request, int $menuId, string $key, string $action, array $apis, string $description): void
    {
        $exists = DB::table('page_privilege')
            ->where('dashboard_menu_id', $menuId)
            ->where('action', $action)
            ->whereNull('deleted_at')
            ->first();

        if ($exists) {
            return;
        }

        DB::table('page_privilege')->insert([
            'uuid'              => (string) Str::uuid(),
            'key'               => $key,
            'dashboard_menu_id' => $menuId,
            'action'            => $action,
            'description'       => $description,
            'order_no'          => null,
            'status'            => 'Active',
            'assigned_apis'     => json_encode($apis, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'meta'              => null,
            'created_at'        => now(),
            'updated_at'        => now(),
            'created_by'        => $this->actor($request)['id'] ?: null,
            'created_at_ip'     => $request->ip(),
            'deleted_at'        => null,
        ]);
    }

    private function ensureHospitalPrivileges(Request $request): void
    {
        if (!Schema::hasTable('dashboard_menu') || !Schema::hasTable('page_privilege')) {
            return;
        }

        $parentId = $this->hospitalHeaderMenuId($request);
        if (!$parentId) {
            return;
        }

        $createMenuId = $this->ensureMenuItem(
            $request,
            $parentId,
            'Create Hospital',
            self::PAGE_HREF_CREATE,
            'fa-solid fa-plus',
            'Create and edit hospital records'
        );

        $manageMenuId = $this->ensureMenuItem(
            $request,
            $parentId,
            'Manage Hospital',
            self::PAGE_HREF_MANAGE,
            'fa-solid fa-hospital',
            'Manage hospital records'
        );

        $this->ensurePrivilege($request, $createMenuId, 'hospitals.create.view', 'view', [
            self::PAGE_HREF_CREATE,
            '/api/hospitals/{identifier}',
        ], 'View hospital create/edit page');
        $this->ensurePrivilege($request, $createMenuId, 'hospitals.create.create', 'create', [
            '/api/hospitals',
        ], 'Create hospitals');
        $this->ensurePrivilege($request, $createMenuId, 'hospitals.create.update', 'update', [
            '/api/hospitals/{identifier}',
        ], 'Update hospitals from create page');

        $this->ensurePrivilege($request, $manageMenuId, 'hospitals.manage.view', 'view', [
            self::PAGE_HREF_MANAGE,
            '/api/hospitals',
            '/api/hospitals/all',
            '/api/hospitals/bin',
            '/api/hospitals/{identifier}',
        ], 'View hospitals list');
        $this->ensurePrivilege($request, $manageMenuId, 'hospitals.manage.update', 'update', [
            '/api/hospitals/{identifier}',
        ], 'Open hospitals for edit from manage page');
        $this->ensurePrivilege($request, $manageMenuId, 'hospitals.manage.delete', 'delete', [
            '/api/hospitals/{identifier}',
        ], 'Delete hospitals');
        $this->ensurePrivilege($request, $manageMenuId, 'hospitals.manage.restore', 'restore', [
            '/api/hospitals/{identifier}/restore',
        ], 'Restore hospitals');
        $this->ensurePrivilege($request, $manageMenuId, 'hospitals.manage.force_delete', 'force_delete', [
            '/api/hospitals/{identifier}/force',
        ], 'Permanently delete hospitals');
    }

    private function validateUniqueColumns(array $values, ?int $ignoreId = null): ?string
    {
        foreach ($values as $column => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $query = DB::table('hospitals')->where($column, $value);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if ($query->exists()) {
                return ucfirst(str_replace('_', ' ', $column)) . ' already exists';
            }
        }

        return null;
    }

    private function baseValidator(Request $request, bool $isUpdate = false)
    {
        $required = $isUpdate ? 'sometimes|required' : 'required';

        return Validator::make($request->all(), [
            'name'                            => $required . '|string|max:255',
            'short_name'                      => 'sometimes|nullable|string|max:120',
            'hospital_code'                   => 'sometimes|nullable|string|max:80',
            'registration_number'             => 'sometimes|nullable|string|max:120',
            'license_number'                  => 'sometimes|nullable|string|max:120',
            'established_year'                => 'sometimes|nullable|integer|min:1800|max:2100',
            'hospital_type'                   => 'sometimes|nullable|string|max:100',
            'ownership_type'                  => 'sometimes|nullable|string|max:100',
            'email'                           => 'sometimes|nullable|email|max:255',
            'phone_number'                    => 'sometimes|nullable|string|max:32',
            'alternative_phone_number'        => 'sometimes|nullable|string|max:32',
            'whatsapp_number'                 => 'sometimes|nullable|string|max:32',
            'emergency_contact_number'        => 'sometimes|nullable|string|max:32',
            'website'                         => 'sometimes|nullable|url|max:255',
            'description'                     => 'sometimes|nullable|string',
            'short_description'               => 'sometimes|nullable|string|max:500',
            'address_line_1'                  => 'sometimes|nullable|string|max:255',
            'address_line_2'                  => 'sometimes|nullable|string|max:255',
            'landmark'                        => 'sometimes|nullable|string|max:255',
            'area'                            => 'sometimes|nullable|string|max:150',
            'city'                            => 'sometimes|nullable|string|max:120',
            'state'                           => 'sometimes|nullable|string|max:120',
            'country'                         => 'sometimes|nullable|string|max:120',
            'pincode'                         => 'sometimes|nullable|string|max:20',
            'latitude'                        => 'sometimes|nullable|numeric|between:-90,90',
            'longitude'                       => 'sometimes|nullable|numeric|between:-180,180',
            'map_url'                         => 'sometimes|nullable|url|max:500',
            'bed_count'                       => 'sometimes|nullable|integer|min:0',
            'average_rating'                  => 'sometimes|nullable|numeric|min:0|max:5',
            'review_count'                    => 'sometimes|nullable|integer|min:0',
            'seo_title'                       => 'sometimes|nullable|string|max:255',
            'seo_description'                 => 'sometimes|nullable|string|max:500',
            'status'                          => 'sometimes|nullable|in:active,inactive',
            'sort_order'                      => 'sometimes|nullable|integer|min:0',
            'logo'                            => 'sometimes|nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'image'                           => 'sometimes|nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'departments'                     => 'sometimes|nullable',
            'services'                        => 'sometimes|nullable',
            'facilities'                      => 'sometimes|nullable',
            'specialities'                    => 'sometimes|nullable',
            'gallery'                         => 'sometimes|nullable',
            'timings'                         => 'sometimes|nullable',
            'social_links'                    => 'sometimes|nullable',
            'accreditations'                  => 'sometimes|nullable',
            'insurances_accepted'             => 'sometimes|nullable',
            'metadata'                        => 'sometimes|nullable',
            'ambulance_available'             => 'sometimes|nullable',
            'emergency_available'             => 'sometimes|nullable',
            'pharmacy_available'              => 'sometimes|nullable',
            'lab_available'                   => 'sometimes|nullable',
            'icu_available'                   => 'sometimes|nullable',
            'cashless_available'              => 'sometimes|nullable',
            'online_consultation_available'   => 'sometimes|nullable',
            'appointment_booking_available'   => 'sometimes|nullable',
        ]);
    }

    private function buildPayload(Request $request, array $data, ?object $existing = null): array
    {
        $payload = [];

        $textFields = [
            'name', 'short_name', 'hospital_code', 'registration_number', 'license_number',
            'hospital_type', 'ownership_type', 'email', 'phone_number', 'alternative_phone_number',
            'whatsapp_number', 'emergency_contact_number', 'website', 'description',
            'short_description', 'address_line_1', 'address_line_2', 'landmark', 'area',
            'city', 'state', 'country', 'pincode', 'map_url', 'seo_title', 'seo_description',
        ];

        foreach ($textFields as $field) {
            if (array_key_exists($field, $data)) {
                $value = $data[$field];
                $payload[$field] = is_string($value) ? trim($value) : $value;
                if ($payload[$field] === '') {
                    $payload[$field] = null;
                }
            }
        }

        if (array_key_exists('name', $data)) {
            $payload['name'] = trim((string) $data['name']);
            $payload['slug'] = $this->makeSlug($payload['name'], $existing?->id ? (int) $existing->id : null);
        }

        foreach (['established_year', 'bed_count', 'review_count', 'sort_order'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field] === null || $data[$field] === '' ? null : (int) $data[$field];
            }
        }

        foreach (['latitude', 'longitude', 'average_rating'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field] === null || $data[$field] === '' ? null : $data[$field];
            }
        }

        if (array_key_exists('status', $data)) {
            $payload['status'] = $this->normalizeStatus($data['status']);
        }

        $jsonArrayFields = [
            'gallery',
            'departments',
            'services',
            'facilities',
            'specialities',
            'accreditations',
            'insurances_accepted',
        ];

        foreach ($jsonArrayFields as $field) {
            if ($request->exists($field)) {
                $payload[$field] = $this->toJsonOrNull($this->parseArrayInput($request->input($field)));
            }
        }

        $jsonObjectFields = ['timings', 'social_links', 'metadata'];
        foreach ($jsonObjectFields as $field) {
            if ($request->exists($field)) {
                $payload[$field] = $this->toJsonOrNull($this->parseObjectInput($request->input($field)));
            }
        }

        $booleanFields = [
            'ambulance_available'           => false,
            'emergency_available'           => false,
            'pharmacy_available'            => false,
            'lab_available'                 => false,
            'icu_available'                 => false,
            'cashless_available'            => false,
            'online_consultation_available' => false,
            'appointment_booking_available' => true,
        ];

        foreach ($booleanFields as $field => $default) {
            $value = $this->normalizeBool($request, $field, $existing ? (bool) ($existing->{$field} ?? $default) : $default);
            if ($value !== null) {
                $payload[$field] = $value;
            }
        }

        return $payload;
    }

    public function all(Request $request)
    {
        $this->ensureHospitalPrivileges($request);

        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'active'));
        $limit = min(1000, max(1, (int) $request->query('limit', 1000)));

        $query = DB::table('hospitals')->whereNull('deleted_at');

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $this->normalizeStatus($status));
        }

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('short_name', 'like', $like)
                    ->orWhere('hospital_code', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhere('city', 'like', $like)
                    ->orWhere('state', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone_number', 'like', $like);
            });
        }

        $rows = $query->orderBy('sort_order')->orderBy('name')->limit($limit)->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rows->map(fn ($row) => $this->hospitalPayload($row))->values(),
            'meta'   => ['count' => $rows->count()],
        ]);
    }

    public function index(Request $request)
    {
        $this->ensureHospitalPrivileges($request);

        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('per_page', 10)));
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'active'));

        $query = DB::table('hospitals')->whereNull('deleted_at');

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $this->normalizeStatus($status));
        }

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('short_name', 'like', $like)
                    ->orWhere('hospital_code', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhere('city', 'like', $like)
                    ->orWhere('state', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone_number', 'like', $like)
                    ->orWhere('hospital_type', 'like', $like)
                    ->orWhere('ownership_type', 'like', $like);
            });
        }

        $total = (clone $query)->count();
        $rows = $query->orderBy('sort_order')->orderBy('name')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rows->map(fn ($row) => $this->hospitalPayload($row))->values(),
            'meta'   => [
                'page'        => $page,
                'per_page'    => $perPage,
                'total'       => $total,
                'total_pages' => (int) ceil($total / max(1, $perPage)),
            ],
        ]);
    }

    public function bin(Request $request)
    {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('per_page', 10)));
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('hospitals')->whereNotNull('deleted_at');

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('short_name', 'like', $like)
                    ->orWhere('hospital_code', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhere('city', 'like', $like)
                    ->orWhere('state', 'like', $like);
            });
        }

        $total = (clone $query)->count();
        $rows = $query->orderByDesc('deleted_at')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rows->map(fn ($row) => $this->hospitalPayload($row))->values(),
            'meta'   => [
                'page'        => $page,
                'per_page'    => $perPage,
                'total'       => $total,
                'total_pages' => (int) ceil($total / max(1, $perPage)),
            ],
        ]);
    }

    public function show(Request $request, $identifier)
    {
        $this->ensureHospitalPrivileges($request);

        $row = $this->resolveHospital($identifier, false);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Hospital not found'], 404);
        }

        return response()->json([
            'status'   => 'success',
            'hospital' => $this->hospitalPayload($row),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureHospitalPrivileges($request);

        $validator = $this->baseValidator($request, false);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $actor = $this->actor($request);

        $dupe = $this->validateUniqueColumns([
            'email'         => $data['email'] ?? null,
            'hospital_code' => $data['hospital_code'] ?? null,
        ]);
        if ($dupe) {
            return response()->json(['status' => 'error', 'message' => $dupe], 422);
        }

        $payload = $this->buildPayload($request, $data, null);
        $payload['uuid'] = (string) Str::uuid();
        $payload['slug'] = $this->makeSlug((string) $data['name']);
        $payload['country'] = $payload['country'] ?? 'India';
        $payload['status'] = $payload['status'] ?? 'active';
        $payload['bed_count'] = $payload['bed_count'] ?? 0;
        $payload['average_rating'] = $payload['average_rating'] ?? 0.00;
        $payload['review_count'] = $payload['review_count'] ?? 0;
        $payload['sort_order'] = $payload['sort_order'] ?? 0;
        $payload['created_at'] = now();
        $payload['updated_at'] = now();
        $payload['created_by'] = $actor['id'] ?: null;
        $payload['created_at_ip'] = $request->ip();
        $payload['deleted_at'] = null;

        $newLogo = null;
        $newImage = null;

        if ($request->hasFile('logo')) {
            $newLogo = $this->saveHospitalAsset($request->file('logo'), 'hospital_logo');
            if ($newLogo === false) {
                return response()->json(['status' => 'error', 'message' => 'Invalid logo upload'], 422);
            }
            $payload['logo'] = $newLogo;
        }

        if ($request->hasFile('image')) {
            $newImage = $this->saveHospitalAsset($request->file('image'), 'hospital_image');
            if ($newImage === false) {
                if ($newLogo) {
                    $this->deleteManagedAsset($newLogo);
                }
                return response()->json(['status' => 'error', 'message' => 'Invalid image upload'], 422);
            }
            $payload['image'] = $newImage;
        }

        try {
            $id = DB::table('hospitals')->insertGetId($payload);
            $fresh = DB::table('hospitals')->where('id', $id)->first();

            $actorName = $this->actorName($actor['id']);
            $this->logActivity(
                $request,
                'store',
                (int) $fresh->id,
                array_keys($payload),
                null,
                $this->hospitalPayload($fresh),
                'Hospital created' . ($actorName ? ' by ' . $actorName : '')
            );

            $this->notifyAdmins(
                'Hospital created',
                ($fresh->name ?? 'A hospital') . ' was created successfully.',
                [
                    'action'     => 'created',
                    'module'     => self::MODULE_NAME,
                    'actor_id'   => $actor['id'],
                    'actor_role' => $actor['role'],
                    'hospital'   => $this->hospitalPayload($fresh),
                ],
                $this->hospitalEditLink($fresh->uuid ?? null),
                'hospital'
            );

            return response()->json([
                'status'   => 'success',
                'message'  => 'Hospital created',
                'hospital' => $this->hospitalPayload($fresh),
            ], 201);
        } catch (\Throwable $e) {
            if ($newLogo) {
                $this->deleteManagedAsset($newLogo);
            }
            if ($newImage) {
                $this->deleteManagedAsset($newImage);
            }

            Log::error('hospital.store.failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Could not create hospital',
            ], 500);
        }
    }

    public function update(Request $request, $identifier)
    {
        $this->ensureHospitalPrivileges($request);

        $row = $this->resolveHospital($identifier, false);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Hospital not found'], 404);
        }

        $validator = $this->baseValidator($request, true);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $update = $this->buildPayload($request, $data, $row);
        $changed = array_keys($update);

        if (array_key_exists('name', $update) && empty($update['slug'])) {
            $update['slug'] = $this->makeSlug((string) $update['name'], (int) $row->id);
        }

        $dupe = $this->validateUniqueColumns([
            'email'         => $update['email'] ?? null,
            'hospital_code' => $update['hospital_code'] ?? null,
            'slug'          => $update['slug'] ?? null,
        ], (int) $row->id);
        if ($dupe) {
            return response()->json(['status' => 'error', 'message' => $dupe], 422);
        }

        $oldLogo = $row->logo ?? null;
        $oldImage = $row->image ?? null;
        $newLogo = null;
        $newImage = null;

        if ($request->hasFile('logo')) {
            $newLogo = $this->saveHospitalAsset($request->file('logo'), 'hospital_logo');
            if ($newLogo === false) {
                return response()->json(['status' => 'error', 'message' => 'Invalid logo upload'], 422);
            }
            $update['logo'] = $newLogo;
            $changed[] = 'logo';
        }

        if ($request->hasFile('image')) {
            $newImage = $this->saveHospitalAsset($request->file('image'), 'hospital_image');
            if ($newImage === false) {
                if ($newLogo) {
                    $this->deleteManagedAsset($newLogo);
                }
                return response()->json(['status' => 'error', 'message' => 'Invalid image upload'], 422);
            }
            $update['image'] = $newImage;
            $changed[] = 'image';
        }

        if (empty($update)) {
            return response()->json(['status' => 'error', 'message' => 'No changes supplied'], 422);
        }

        $update['updated_at'] = now();

        try {
            DB::table('hospitals')->where('id', $row->id)->update($update);
            $fresh = DB::table('hospitals')->where('id', $row->id)->first();

            if ($newLogo && $oldLogo && $oldLogo !== $newLogo) {
                $this->deleteManagedAsset($oldLogo);
            }
            if ($newImage && $oldImage && $oldImage !== $newImage) {
                $this->deleteManagedAsset($oldImage);
            }

            $actor = $this->actor($request);
            $actorName = $this->actorName($actor['id']);

            $this->logActivity(
                $request,
                'update',
                (int) $fresh->id,
                array_values(array_unique($changed)),
                $this->hospitalPayload($row),
                $this->hospitalPayload($fresh),
                'Hospital updated' . ($actorName ? ' by ' . $actorName : '')
            );

            $this->notifyAdmins(
                'Hospital updated',
                ($fresh->name ?? 'A hospital') . ' was updated.',
                [
                    'action'         => 'updated',
                    'module'         => self::MODULE_NAME,
                    'actor_id'       => $actor['id'],
                    'actor_role'     => $actor['role'],
                    'changed_fields' => array_values(array_unique($changed)),
                    'hospital'       => $this->hospitalPayload($fresh),
                ],
                $this->hospitalEditLink($fresh->uuid ?? null),
                'hospital'
            );

            return response()->json([
                'status'   => 'success',
                'message'  => 'Hospital updated',
                'hospital' => $this->hospitalPayload($fresh),
            ]);
        } catch (\Throwable $e) {
            if ($newLogo) {
                $this->deleteManagedAsset($newLogo);
            }
            if ($newImage) {
                $this->deleteManagedAsset($newImage);
            }

            Log::error('hospital.update.failed', [
                'identifier' => $identifier,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Could not update hospital',
            ], 500);
        }
    }

    public function destroy(Request $request, $identifier)
    {
        $row = $this->resolveHospital($identifier, false);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Hospital not found'], 404);
        }

        DB::table('hospitals')->where('id', $row->id)->update([
            'deleted_at' => now(),
            'status'     => 'inactive',
            'updated_at' => now(),
        ]);

        $actor = $this->actor($request);
        $actorName = $this->actorName($actor['id']);

        $this->logActivity(
            $request,
            'destroy',
            (int) $row->id,
            ['deleted_at', 'status'],
            $this->hospitalPayload($row),
            ['deleted_at' => now()->toDateTimeString(), 'status' => 'inactive'],
            'Hospital soft deleted' . ($actorName ? ' by ' . $actorName : '')
        );

        $this->notifyAdmins(
            'Hospital deleted',
            ($row->name ?? 'A hospital') . ' was moved to deleted state.',
            [
                'action'     => 'soft_deleted',
                'module'     => self::MODULE_NAME,
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'hospital'   => $this->hospitalPayload($row),
            ],
            self::PAGE_HREF_MANAGE,
            'hospital'
        );

        return response()->json(['status' => 'success', 'message' => 'Hospital deleted']);
    }

    public function restore(Request $request, $identifier)
    {
        $row = $this->resolveHospital($identifier, true);
        if (!$row || !$row->deleted_at) {
            return response()->json(['status' => 'error', 'message' => 'Hospital not found or not deleted'], 404);
        }

        DB::table('hospitals')->where('id', $row->id)->update([
            'deleted_at' => null,
            'status'     => 'active',
            'updated_at' => now(),
        ]);

        $fresh = DB::table('hospitals')->where('id', $row->id)->first();
        $actor = $this->actor($request);
        $actorName = $this->actorName($actor['id']);

        $this->logActivity(
            $request,
            'restore',
            (int) $fresh->id,
            ['deleted_at', 'status'],
            $this->hospitalPayload($row),
            $this->hospitalPayload($fresh),
            'Hospital restored' . ($actorName ? ' by ' . $actorName : '')
        );

        $this->notifyAdmins(
            'Hospital restored',
            ($fresh->name ?? 'A hospital') . ' was restored.',
            [
                'action'     => 'restored',
                'module'     => self::MODULE_NAME,
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'hospital'   => $this->hospitalPayload($fresh),
            ],
            self::PAGE_HREF_MANAGE,
            'hospital'
        );

        return response()->json(['status' => 'success', 'message' => 'Hospital restored']);
    }

    public function forceDelete(Request $request, $identifier)
    {
        $row = $this->resolveHospital($identifier, true);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Hospital not found'], 404);
        }

        DB::table('hospitals')->where('id', $row->id)->delete();

        if (!empty($row->logo)) {
            $this->deleteManagedAsset($row->logo);
        }
        if (!empty($row->image)) {
            $this->deleteManagedAsset($row->image);
        }

        $actor = $this->actor($request);
        $actorName = $this->actorName($actor['id']);

        $this->logActivity(
            $request,
            'force_delete',
            (int) $row->id,
            ['hospital'],
            $this->hospitalPayload($row),
            null,
            'Hospital permanently deleted' . ($actorName ? ' by ' . $actorName : '')
        );

        $this->notifyAdmins(
            'Hospital permanently deleted',
            ($row->name ?? 'A hospital') . ' was permanently deleted.',
            [
                'action'     => 'force_deleted',
                'module'     => self::MODULE_NAME,
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'hospital'   => $this->hospitalPayload($row),
            ],
            self::PAGE_HREF_MANAGE,
            'hospital',
            'high'
        );

        return response()->json(['status' => 'success', 'message' => 'Hospital permanently deleted']);
    }
}
