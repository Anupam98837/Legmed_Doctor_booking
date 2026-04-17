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

class ClinicController extends Controller
{
    use PersistsAdminNotifications;

    private const MODULE_NAME = 'clinics';
    private const TABLE_NAME = 'clinics';
    private const PAGE_HREF = '/clinics/manage';

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

        $value = $request->input($key);
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return ((int) $value) === 1;
        }

        $value = strtolower(trim((string) $value));
        return in_array($value, ['1', 'true', 'yes', 'on'], true);
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
                'changed_fields'    => $changedFields ? $this->toJsonOrNull(array_values(array_unique($changedFields))) : null,
                'old_values'        => $this->toJsonOrNull($oldValues),
                'new_values'        => $this->toJsonOrNull($newValues),
                'log_note'          => $note,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        } catch (\Throwable $e) {
            // Never break main flow.
        }
    }

    private function pageHeaderMenuId(Request $request): ?int
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

    private function ensureMenuItem(Request $request, int $parentId): int
    {
        $menu = DB::table('dashboard_menu')
            ->whereNull('deleted_at')
            ->where('href', self::PAGE_HREF)
            ->first();

        if ($menu) {
            return (int) $menu->id;
        }

        $actor = $this->actor($request);

        return (int) DB::table('dashboard_menu')->insertGetId([
            'parent_id'        => $parentId,
            'uuid'             => (string) Str::uuid(),
            'position'         => 0,
            'name'             => 'Clinics',
            'icon_class'       => 'fa-solid fa-clinic-medical',
            'href'             => self::PAGE_HREF,
            'description'      => 'Manage clinic records',
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

    private function ensureClinicPrivileges(Request $request): void
    {
        if (!Schema::hasTable('dashboard_menu') || !Schema::hasTable('page_privilege')) {
            return;
        }

        $parentId = $this->pageHeaderMenuId($request);
        if (!$parentId) {
            return;
        }

        $menuId = $this->ensureMenuItem($request, $parentId);
        $defaults = [
            'view'         => [self::PAGE_HREF, '/api/clinics', '/api/clinics/all', '/api/clinics/bin', '/api/clinics/{identifier}'],
            'create'       => ['/api/clinics'],
            'update'       => ['/api/clinics/{identifier}'],
            'delete'       => ['/api/clinics/{identifier}'],
            'restore'      => ['/api/clinics/{identifier}/restore'],
            'force_delete' => ['/api/clinics/{identifier}/force'],
        ];

        foreach ($defaults as $action => $apis) {
            $this->ensurePrivilege($request, $menuId, 'clinics.' . $action, $action, $apis, 'Clinic ' . str_replace('_', ' ', $action));
        }
    }

    private function makeSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name, '-');
        $slug = $base !== '' ? $base : 'clinic';

        $i = 1;
        while (true) {
            $query = DB::table('clinics')->where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $i++;
            $slug = ($base !== '' ? $base : 'clinic') . '-' . $i;
        }
    }

    private function saveAsset($uploadedFile, string $prefix)
    {
        if (!$uploadedFile || !$uploadedFile->isValid()) {
            return false;
        }

        $destDir = public_path('assets/media/images/clinics');
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $ext = strtolower($uploadedFile->getClientOriginalExtension() ?: 'bin');
        $filename = $prefix . '_' . date('Ymd_His') . '_' . Str::lower(Str::random(16)) . '.' . $ext;
        $uploadedFile->move($destDir, $filename);

        return '/assets/media/images/clinics/' . $filename;
    }

    private function deleteManagedAsset(?string $url): void
    {
        if (empty($url)) {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH);
        $path = $path ?: $url;
        $path = '/' . ltrim($path, '/');

        if (!Str::startsWith($path, '/assets/media/images/clinics/')) {
            return;
        }

        $abs = public_path(ltrim($path, '/'));
        if (File::exists($abs)) {
            @File::delete($abs);
        }
    }

    private function publicAssetUrl(?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        $path = parse_url($value, PHP_URL_PATH);
        $path = $path ?: $value;
        $path = '/' . ltrim($path, '/');
        return url($path);
    }

    private function resolveClinic($identifier, bool $withDeleted = false): ?object
    {
        $query = DB::table('clinics');
        if (!$withDeleted) {
            $query->whereNull('deleted_at');
        }

        if (is_numeric($identifier)) {
            return $query->where('id', (int) $identifier)->first();
        }

        $identifier = (string) $identifier;
        return $query->where(function ($sub) use ($identifier) {
            $sub->where('uuid', $identifier)->orWhere('slug', $identifier);
        })->first();
    }

    private function clinicPayload(object $row): array
    {
        return [
            'id'                              => (int) $row->id,
            'uuid'                            => (string) ($row->uuid ?? ''),
            'name'                            => (string) ($row->name ?? ''),
            'short_name'                      => $row->short_name,
            'slug'                            => (string) ($row->slug ?? ''),
            'clinic_code'                     => $row->clinic_code,
            'clinic_type'                     => $row->clinic_type,
            'email'                           => $row->email,
            'phone_number'                    => $row->phone_number,
            'alternative_phone_number'        => $row->alternative_phone_number,
            'whatsapp_number'                 => $row->whatsapp_number,
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
            'timings'                         => $this->decodeJsonValue($row->timings ?? null),
            'facilities'                      => $this->decodeJsonValue($row->facilities ?? null),
            'social_links'                    => $this->decodeJsonValue($row->social_links ?? null),
            'online_consultation_available'   => (bool) ($row->online_consultation_available ?? false),
            'appointment_booking_available'   => (bool) ($row->appointment_booking_available ?? true),
            'status'                          => (string) ($row->status ?? 'active'),
            'sort_order'                      => (int) ($row->sort_order ?? 0),
            'metadata'                        => $this->decodeJsonValue($row->metadata ?? null),
            'created_by'                      => $row->created_by ? (int) $row->created_by : null,
            'created_at_ip'                   => $row->created_at_ip,
            'created_at'                      => $row->created_at,
            'updated_at'                      => $row->updated_at,
            'deleted_at'                      => $row->deleted_at,
        ];
    }

    private function validateUnique(array $values, ?int $ignoreId = null): ?string
    {
        foreach ($values as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $query = DB::table('clinics')->where($field, $value);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if ($query->exists()) {
                return Str::headline($field) . ' already exists';
            }
        }

        return null;
    }

    private function validator(Request $request, bool $isUpdate = false)
    {
        $required = $isUpdate ? 'sometimes|required' : 'required';

        return Validator::make($request->all(), [
            'name'                          => $required . '|string|max:255',
            'short_name'                    => 'sometimes|nullable|string|max:120',
            'clinic_code'                   => 'sometimes|nullable|string|max:80',
            'clinic_type'                   => 'sometimes|nullable|string|max:100',
            'email'                         => 'sometimes|nullable|email|max:255',
            'phone_number'                  => 'sometimes|nullable|string|max:32',
            'alternative_phone_number'      => 'sometimes|nullable|string|max:32',
            'whatsapp_number'               => 'sometimes|nullable|string|max:32',
            'website'                       => 'sometimes|nullable|url|max:255',
            'logo'                          => 'sometimes|nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'image'                         => 'sometimes|nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'gallery'                       => 'sometimes|nullable',
            'description'                   => 'sometimes|nullable|string',
            'short_description'             => 'sometimes|nullable|string|max:500',
            'address_line_1'                => 'sometimes|nullable|string|max:255',
            'address_line_2'                => 'sometimes|nullable|string|max:255',
            'landmark'                      => 'sometimes|nullable|string|max:255',
            'area'                          => 'sometimes|nullable|string|max:150',
            'city'                          => 'sometimes|nullable|string|max:120',
            'state'                         => 'sometimes|nullable|string|max:120',
            'country'                       => 'sometimes|nullable|string|max:120',
            'pincode'                       => 'sometimes|nullable|string|max:20',
            'latitude'                      => 'sometimes|nullable|numeric|between:-90,90',
            'longitude'                     => 'sometimes|nullable|numeric|between:-180,180',
            'map_url'                       => 'sometimes|nullable|url|max:500',
            'timings'                       => 'sometimes|nullable',
            'facilities'                    => 'sometimes|nullable',
            'social_links'                  => 'sometimes|nullable',
            'online_consultation_available' => 'sometimes|nullable',
            'appointment_booking_available' => 'sometimes|nullable',
            'status'                        => 'sometimes|nullable|in:active,inactive',
            'sort_order'                    => 'sometimes|nullable|integer|min:0',
            'metadata'                      => 'sometimes|nullable',
        ]);
    }

    private function parseArrayInput($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            return array_values(array_filter(array_map(fn ($item) => trim((string) $item), $value), fn ($item) => $item !== ''));
        }

        $decoded = json_decode((string) $value, true);
        if (is_array($decoded)) {
            return array_values(array_filter(array_map(fn ($item) => trim((string) $item), $decoded), fn ($item) => $item !== ''));
        }

        $items = preg_split('/[\n,]+/', (string) $value) ?: [];
        return array_values(array_filter(array_map(fn ($item) => trim((string) $item), $items), fn ($item) => $item !== ''));
    }

    private function parseObjectInput($value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : null;
    }

    private function buildPayload(Request $request, array $data, ?object $existing = null): array
    {
        $payload = [];

        foreach ([
            'name', 'short_name', 'clinic_code', 'clinic_type', 'email', 'phone_number',
            'alternative_phone_number', 'whatsapp_number', 'website', 'description',
            'short_description', 'address_line_1', 'address_line_2', 'landmark', 'area',
            'city', 'state', 'country', 'pincode', 'map_url',
        ] as $field) {
            if (array_key_exists($field, $data)) {
                $value = is_string($data[$field]) ? trim($data[$field]) : $data[$field];
                $payload[$field] = $value === '' ? null : $value;
            }
        }

        foreach (['sort_order'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field] === null || $data[$field] === '' ? null : (int) $data[$field];
            }
        }

        foreach (['latitude', 'longitude'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field] === null || $data[$field] === '' ? null : $data[$field];
            }
        }

        if (array_key_exists('name', $data)) {
            $payload['name'] = trim((string) $data['name']);
            $payload['slug'] = $this->makeSlug($payload['name'], $existing?->id ? (int) $existing->id : null);
        }

        if (array_key_exists('status', $data)) {
            $payload['status'] = $this->normalizeStatus($data['status']);
        }

        if ($request->exists('gallery')) {
            $payload['gallery'] = $this->toJsonOrNull($this->parseArrayInput($request->input('gallery')));
        }

        if ($request->exists('facilities')) {
            $payload['facilities'] = $this->toJsonOrNull($this->parseArrayInput($request->input('facilities')));
        }

        foreach (['timings', 'social_links', 'metadata'] as $field) {
            if ($request->exists($field)) {
                $payload[$field] = $this->toJsonOrNull($this->parseObjectInput($request->input($field)));
            }
        }

        foreach ([
            'online_consultation_available' => false,
            'appointment_booking_available' => true,
        ] as $field => $default) {
            $value = $this->normalizeBool($request, $field, $existing ? (bool) ($existing->{$field} ?? $default) : $default);
            if ($value !== null) {
                $payload[$field] = $value;
            }
        }

        return $payload;
    }

    public function all(Request $request)
    {
        $this->ensureClinicPrivileges($request);

        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'active'));
        $limit = min(1000, max(1, (int) $request->query('limit', 1000)));

        $query = DB::table('clinics')->whereNull('deleted_at');
        if ($status !== '' && $status !== 'all') {
            $query->where('status', $this->normalizeStatus($status));
        }

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('short_name', 'like', $like)
                    ->orWhere('clinic_code', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhere('city', 'like', $like)
                    ->orWhere('state', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone_number', 'like', $like)
                    ->orWhere('clinic_type', 'like', $like);
            });
        }

        $rows = $query->orderBy('sort_order')->orderBy('name')->limit($limit)->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rows->map(fn ($row) => $this->clinicPayload($row))->values(),
            'meta'   => ['count' => $rows->count()],
        ]);
    }

    public function index(Request $request)
    {
        $this->ensureClinicPrivileges($request);

        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'active'));

        $query = DB::table('clinics')->whereNull('deleted_at');
        if ($status !== '' && $status !== 'all') {
            $query->where('status', $this->normalizeStatus($status));
        }

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('short_name', 'like', $like)
                    ->orWhere('clinic_code', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhere('city', 'like', $like)
                    ->orWhere('state', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone_number', 'like', $like)
                    ->orWhere('clinic_type', 'like', $like);
            });
        }

        $total = (clone $query)->count();
        $rows = $query->orderBy('sort_order')->orderBy('name')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rows->map(fn ($row) => $this->clinicPayload($row))->values(),
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
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('clinics')->whereNotNull('deleted_at');
        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('short_name', 'like', $like)
                    ->orWhere('clinic_code', 'like', $like)
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
            'data'   => $rows->map(fn ($row) => $this->clinicPayload($row))->values(),
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
        $this->ensureClinicPrivileges($request);

        $row = $this->resolveClinic($identifier, false);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Clinic not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $this->clinicPayload($row),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureClinicPrivileges($request);

        $validator = $this->validator($request, false);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $payload = $this->buildPayload($request, $data, null);
        $payload['uuid'] = (string) Str::uuid();
        $payload['slug'] = $this->makeSlug((string) $data['name']);
        $payload['status'] = $payload['status'] ?? 'active';
        $payload['country'] = $payload['country'] ?? 'India';
        $payload['sort_order'] = $payload['sort_order'] ?? 0;
        $payload['created_at'] = now();
        $payload['updated_at'] = now();
        $payload['created_by'] = $this->actor($request)['id'] ?: null;
        $payload['created_at_ip'] = $request->ip();

        $dupe = $this->validateUnique([
            'clinic_code' => $payload['clinic_code'] ?? null,
            'slug'        => $payload['slug'] ?? null,
        ]);
        if ($dupe) {
            return response()->json(['status' => 'error', 'message' => $dupe], 422);
        }

        $newLogo = null;
        $newImage = null;
        if ($request->hasFile('logo')) {
            $newLogo = $this->saveAsset($request->file('logo'), 'clinic_logo');
            if ($newLogo === false) {
                return response()->json(['status' => 'error', 'message' => 'Invalid logo upload'], 422);
            }
            $payload['logo'] = $newLogo;
        }

        if ($request->hasFile('image')) {
            $newImage = $this->saveAsset($request->file('image'), 'clinic_image');
            if ($newImage === false) {
                if ($newLogo) {
                    $this->deleteManagedAsset($newLogo);
                }
                return response()->json(['status' => 'error', 'message' => 'Invalid image upload'], 422);
            }
            $payload['image'] = $newImage;
        }

        try {
            $id = DB::table('clinics')->insertGetId($payload);
            $fresh = DB::table('clinics')->where('id', $id)->first();
            $actor = $this->actor($request);

            $this->logActivity(
                $request,
                'store',
                (int) $fresh->id,
                array_keys($payload),
                null,
                $this->clinicPayload($fresh),
                'Clinic created' . (($name = $this->actorName($actor['id'])) ? ' by ' . $name : '')
            );

            $this->notifyAdmins(
                'Clinic created',
                ($fresh->name ?? 'Clinic') . ' was created successfully.',
                [
                    'action'     => 'created',
                    'module'     => self::MODULE_NAME,
                    'actor_id'   => $actor['id'],
                    'actor_role' => $actor['role'],
                    'clinic'     => $this->clinicPayload($fresh),
                ],
                self::PAGE_HREF,
                self::MODULE_NAME
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Clinic created',
                'data'    => $this->clinicPayload($fresh),
            ], 201);
        } catch (\Throwable $e) {
            if ($newLogo) {
                $this->deleteManagedAsset($newLogo);
            }
            if ($newImage) {
                $this->deleteManagedAsset($newImage);
            }

            Log::error('clinic.store.failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Could not create clinic'], 500);
        }
    }

    public function update(Request $request, $identifier)
    {
        $this->ensureClinicPrivileges($request);

        $row = $this->resolveClinic($identifier, false);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Clinic not found'], 404);
        }

        $validator = $this->validator($request, true);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $update = $this->buildPayload($request, $data, $row);
        $changed = array_keys($update);

        $dupe = $this->validateUnique([
            'clinic_code' => $update['clinic_code'] ?? null,
            'slug'        => $update['slug'] ?? null,
        ], (int) $row->id);
        if ($dupe) {
            return response()->json(['status' => 'error', 'message' => $dupe], 422);
        }

        $oldLogo = $row->logo ?? null;
        $oldImage = $row->image ?? null;
        $newLogo = null;
        $newImage = null;

        if ($request->hasFile('logo')) {
            $newLogo = $this->saveAsset($request->file('logo'), 'clinic_logo');
            if ($newLogo === false) {
                return response()->json(['status' => 'error', 'message' => 'Invalid logo upload'], 422);
            }
            $update['logo'] = $newLogo;
            $changed[] = 'logo';
        }

        if ($request->hasFile('image')) {
            $newImage = $this->saveAsset($request->file('image'), 'clinic_image');
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
            DB::table('clinics')->where('id', $row->id)->update($update);
            $fresh = DB::table('clinics')->where('id', $row->id)->first();

            if ($newLogo && $oldLogo && $oldLogo !== $newLogo) {
                $this->deleteManagedAsset($oldLogo);
            }
            if ($newImage && $oldImage && $oldImage !== $newImage) {
                $this->deleteManagedAsset($oldImage);
            }

            $actor = $this->actor($request);
            $this->logActivity(
                $request,
                'update',
                (int) $fresh->id,
                array_values(array_unique($changed)),
                $this->clinicPayload($row),
                $this->clinicPayload($fresh),
                'Clinic updated' . (($name = $this->actorName($actor['id'])) ? ' by ' . $name : '')
            );

            $this->notifyAdmins(
                'Clinic updated',
                ($fresh->name ?? 'Clinic') . ' was updated.',
                [
                    'action'         => 'updated',
                    'module'         => self::MODULE_NAME,
                    'actor_id'       => $actor['id'],
                    'actor_role'     => $actor['role'],
                    'changed_fields' => array_values(array_unique($changed)),
                    'clinic'         => $this->clinicPayload($fresh),
                ],
                self::PAGE_HREF,
                self::MODULE_NAME
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Clinic updated',
                'data'    => $this->clinicPayload($fresh),
            ]);
        } catch (\Throwable $e) {
            if ($newLogo) {
                $this->deleteManagedAsset($newLogo);
            }
            if ($newImage) {
                $this->deleteManagedAsset($newImage);
            }

            Log::error('clinic.update.failed', ['identifier' => $identifier, 'error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Could not update clinic'], 500);
        }
    }

    public function destroy(Request $request, $identifier)
    {
        $row = $this->resolveClinic($identifier, false);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Clinic not found'], 404);
        }

        DB::table('clinics')->where('id', $row->id)->update([
            'deleted_at' => now(),
            'status'     => 'inactive',
            'updated_at' => now(),
        ]);

        $actor = $this->actor($request);
        $this->logActivity(
            $request,
            'destroy',
            (int) $row->id,
            ['deleted_at', 'status'],
            $this->clinicPayload($row),
            ['deleted_at' => now()->toDateTimeString(), 'status' => 'inactive'],
            'Clinic soft deleted' . (($name = $this->actorName($actor['id'])) ? ' by ' . $name : '')
        );

        $this->notifyAdmins(
            'Clinic deleted',
            ($row->name ?? 'Clinic') . ' was moved to Bin.',
            [
                'action'     => 'soft_deleted',
                'module'     => self::MODULE_NAME,
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'clinic'     => $this->clinicPayload($row),
            ],
            self::PAGE_HREF,
            self::MODULE_NAME
        );

        return response()->json(['status' => 'success', 'message' => 'Clinic deleted']);
    }

    public function restore(Request $request, $identifier)
    {
        $row = $this->resolveClinic($identifier, true);
        if (!$row || !$row->deleted_at) {
            return response()->json(['status' => 'error', 'message' => 'Clinic not found or not deleted'], 404);
        }

        DB::table('clinics')->where('id', $row->id)->update([
            'deleted_at' => null,
            'status'     => 'active',
            'updated_at' => now(),
        ]);

        $fresh = DB::table('clinics')->where('id', $row->id)->first();
        $actor = $this->actor($request);
        $this->logActivity(
            $request,
            'restore',
            (int) $fresh->id,
            ['deleted_at', 'status'],
            $this->clinicPayload($row),
            $this->clinicPayload($fresh),
            'Clinic restored' . (($name = $this->actorName($actor['id'])) ? ' by ' . $name : '')
        );

        $this->notifyAdmins(
            'Clinic restored',
            ($fresh->name ?? 'Clinic') . ' was restored.',
            [
                'action'     => 'restored',
                'module'     => self::MODULE_NAME,
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'clinic'     => $this->clinicPayload($fresh),
            ],
            self::PAGE_HREF,
            self::MODULE_NAME
        );

        return response()->json(['status' => 'success', 'message' => 'Clinic restored']);
    }

    public function forceDelete(Request $request, $identifier)
    {
        $row = $this->resolveClinic($identifier, true);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Clinic not found'], 404);
        }

        DB::table('clinics')->where('id', $row->id)->delete();
        if (!empty($row->logo)) {
            $this->deleteManagedAsset($row->logo);
        }
        if (!empty($row->image)) {
            $this->deleteManagedAsset($row->image);
        }

        $actor = $this->actor($request);
        $this->logActivity(
            $request,
            'force_delete',
            (int) $row->id,
            ['clinic'],
            $this->clinicPayload($row),
            null,
            'Clinic permanently deleted' . (($name = $this->actorName($actor['id'])) ? ' by ' . $name : '')
        );

        $this->notifyAdmins(
            'Clinic permanently deleted',
            ($row->name ?? 'Clinic') . ' was permanently deleted.',
            [
                'action'     => 'force_deleted',
                'module'     => self::MODULE_NAME,
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'clinic'     => $this->clinicPayload($row),
            ],
            self::PAGE_HREF,
            self::MODULE_NAME,
            'high'
        );

        return response()->json(['status' => 'success', 'message' => 'Clinic permanently deleted']);
    }
}
