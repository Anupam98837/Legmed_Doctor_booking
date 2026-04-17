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

class ReferenceMasterController extends Controller
{
    use PersistsAdminNotifications;

    private const MASTERS = [
        'specializations' => [
            'table'        => 'specializations',
            'module'       => 'specializations',
            'page_href'    => '/specializations/manage',
            'menu_name'    => 'Specializations',
            'menu_icon'    => 'fa-solid fa-stethoscope',
            'menu_desc'    => 'Manage doctor specializations',
            'singular'     => 'Specialization',
            'plural'       => 'Specializations',
            'search'       => ['name', 'short_form', 'slug'],
            'text'         => ['name', 'short_form', 'description'],
            'file'         => ['icon', 'image'],
            'int'          => ['sort_order'],
            'json_object'  => ['metadata'],
            'unique'       => ['name'],
        ],
        'designations' => [
            'table'        => 'designations',
            'module'       => 'designations',
            'page_href'    => '/designations/manage',
            'menu_name'    => 'Designations',
            'menu_icon'    => 'fa-solid fa-user-tie',
            'menu_desc'    => 'Manage doctor designations',
            'singular'     => 'Designation',
            'plural'       => 'Designations',
            'search'       => ['name', 'short_form', 'slug'],
            'text'         => ['name', 'short_form', 'description'],
            'file'         => [],
            'int'          => ['sort_order'],
            'json_object'  => ['metadata'],
            'unique'       => ['name'],
        ],
        'registration_councils' => [
            'table'        => 'registration_councils',
            'module'       => 'registration_councils',
            'page_href'    => '/registration-councils/manage',
            'menu_name'    => 'Registration Councils',
            'menu_icon'    => 'fa-solid fa-id-card',
            'menu_desc'    => 'Manage registration councils',
            'singular'     => 'Registration Council',
            'plural'       => 'Registration Councils',
            'search'       => ['name', 'short_form', 'slug', 'country', 'state'],
            'text'         => ['name', 'short_form', 'country', 'state', 'description', 'website'],
            'file'         => [],
            'int'          => ['sort_order'],
            'json_object'  => ['metadata'],
            'unique'       => ['name'],
        ],
        'languages' => [
            'table'        => 'languages',
            'module'       => 'languages',
            'page_href'    => '/languages/manage',
            'menu_name'    => 'Languages',
            'menu_icon'    => 'fa-solid fa-language',
            'menu_desc'    => 'Manage spoken languages',
            'singular'     => 'Language',
            'plural'       => 'Languages',
            'search'       => ['name', 'code', 'slug'],
            'text'         => ['name', 'code', 'description'],
            'file'         => [],
            'int'          => ['sort_order'],
            'json_object'  => ['metadata'],
            'unique'       => ['name', 'code'],
        ],
        'services' => [
            'table'        => 'services',
            'module'       => 'services',
            'page_href'    => '/services/manage',
            'menu_name'    => 'Services',
            'menu_icon'    => 'fa-solid fa-briefcase-medical',
            'menu_desc'    => 'Manage medical services',
            'singular'     => 'Service',
            'plural'       => 'Services',
            'search'       => ['name', 'short_form', 'slug'],
            'text'         => ['name', 'short_form', 'description'],
            'file'         => ['icon', 'image'],
            'int'          => ['sort_order', 'default_duration_minutes'],
            'decimal'      => ['default_price'],
            'json_object'  => ['metadata'],
            'unique'       => ['name'],
        ],
        'qualifications' => [
            'table'        => 'qualifications',
            'module'       => 'qualifications',
            'page_href'    => '/qualifications/manage',
            'menu_name'    => 'Qualifications',
            'menu_icon'    => 'fa-solid fa-graduation-cap',
            'menu_desc'    => 'Manage doctor qualifications',
            'singular'     => 'Qualification',
            'plural'       => 'Qualifications',
            'search'       => ['name', 'short_form', 'slug', 'qualification_type'],
            'text'         => ['name', 'short_form', 'qualification_type', 'description'],
            'file'         => [],
            'int'          => ['sort_order'],
            'json_object'  => ['metadata'],
            'unique'       => ['name'],
        ],
    ];

    private function masterKey(Request $request): string
    {
        return (string) $request->route('master');
    }

    private function config(string $master): array
    {
        if (!isset(self::MASTERS[$master])) {
            abort(404, 'Invalid master type');
        }

        return self::MASTERS[$master];
    }

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

    private function logActivity(
        Request $request,
        array $cfg,
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
                'module'            => $cfg['module'],
                'table_name'        => $cfg['table'],
                'record_id'         => $recordId,
                'changed_fields'    => $changedFields ? $this->toJsonOrNull(array_values(array_unique($changedFields))) : null,
                'old_values'        => $this->toJsonOrNull($oldValues),
                'new_values'        => $this->toJsonOrNull($newValues),
                'log_note'          => $note,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        } catch (\Throwable $e) {
            // Never break main flow for logging.
        }
    }

    private function masterHeaderMenuId(Request $request): ?int
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

    private function ensureMasterPrivileges(Request $request, array $cfg): void
    {
        if (!Schema::hasTable('dashboard_menu') || !Schema::hasTable('page_privilege')) {
            return;
        }

        $parentId = $this->masterHeaderMenuId($request);
        if (!$parentId) {
            return;
        }

        $menuId = $this->ensureMenuItem(
            $request,
            $parentId,
            $cfg['menu_name'],
            $cfg['page_href'],
            $cfg['menu_icon'],
            $cfg['menu_desc']
        );

        $resource = '/api/' . str_replace('_', '-', $cfg['table']);
        $defaults = [
            'view'         => [$cfg['page_href'], $resource, $resource . '/all', $resource . '/bin', $resource . '/{identifier}'],
            'create'       => [$resource],
            'update'       => [$resource . '/{identifier}'],
            'delete'       => [$resource . '/{identifier}'],
            'restore'      => [$resource . '/{identifier}/restore'],
            'force_delete' => [$resource . '/{identifier}/force'],
        ];

        foreach ($defaults as $action => $apis) {
            $this->ensurePrivilege(
                $request,
                $menuId,
                $cfg['module'] . '.' . $action,
                $action,
                $apis,
                $cfg['singular'] . ' ' . str_replace('_', ' ', $action)
            );
        }
    }

    private function makeSlug(string $table, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name, '-');
        $slug = $base !== '' ? $base : 'record';

        $i = 1;
        while (true) {
            $query = DB::table($table)->where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $i++;
            $slug = ($base !== '' ? $base : 'record') . '-' . $i;
        }
    }

    private function saveAsset($uploadedFile, string $master, string $field)
    {
        if (!$uploadedFile || !$uploadedFile->isValid()) {
            return false;
        }

        $destDir = public_path('assets/media/images/masters/' . $master);
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $ext = strtolower($uploadedFile->getClientOriginalExtension() ?: 'bin');
        $filename = $master . '_' . $field . '_' . date('Ymd_His') . '_' . Str::lower(Str::random(16)) . '.' . $ext;
        $uploadedFile->move($destDir, $filename);

        return '/assets/media/images/masters/' . $master . '/' . $filename;
    }

    private function deleteManagedAsset(?string $url): void
    {
        if (empty($url)) {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH);
        $path = $path ?: $url;
        $path = '/' . ltrim($path, '/');

        if (!Str::startsWith($path, '/assets/media/images/masters/')) {
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

    private function baseRules(string $master, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes|required' : 'required';

        return match ($master) {
            'specializations' => [
                'name'        => $required . '|string|max:150',
                'short_form'  => 'sometimes|nullable|string|max:30',
                'description' => 'sometimes|nullable|string',
                'icon'        => 'sometimes|nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
                'image'       => 'sometimes|nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
                'status'      => 'sometimes|nullable|in:active,inactive',
                'sort_order'  => 'sometimes|nullable|integer|min:0',
                'metadata'    => 'sometimes|nullable',
            ],
            'designations' => [
                'name'        => $required . '|string|max:150',
                'short_form'  => 'sometimes|nullable|string|max:30',
                'description' => 'sometimes|nullable|string',
                'status'      => 'sometimes|nullable|in:active,inactive',
                'sort_order'  => 'sometimes|nullable|integer|min:0',
                'metadata'    => 'sometimes|nullable',
            ],
            'registration_councils' => [
                'name'        => $required . '|string|max:180',
                'short_form'  => 'sometimes|nullable|string|max:40',
                'country'     => 'sometimes|nullable|string|max:120',
                'state'       => 'sometimes|nullable|string|max:120',
                'description' => 'sometimes|nullable|string',
                'website'     => 'sometimes|nullable|url|max:255',
                'status'      => 'sometimes|nullable|in:active,inactive',
                'sort_order'  => 'sometimes|nullable|integer|min:0',
                'metadata'    => 'sometimes|nullable',
            ],
            'languages' => [
                'name'        => $required . '|string|max:120',
                'code'        => 'sometimes|nullable|string|max:20',
                'description' => 'sometimes|nullable|string',
                'status'      => 'sometimes|nullable|in:active,inactive',
                'sort_order'  => 'sometimes|nullable|integer|min:0',
                'metadata'    => 'sometimes|nullable',
            ],
            'services' => [
                'name'                     => $required . '|string|max:180',
                'short_form'               => 'sometimes|nullable|string|max:40',
                'description'              => 'sometimes|nullable|string',
                'icon'                     => 'sometimes|nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
                'image'                    => 'sometimes|nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
                'default_price'            => 'sometimes|nullable|numeric|min:0',
                'default_duration_minutes' => 'sometimes|nullable|integer|min:0',
                'status'                   => 'sometimes|nullable|in:active,inactive',
                'sort_order'               => 'sometimes|nullable|integer|min:0',
                'metadata'                 => 'sometimes|nullable',
            ],
            'qualifications' => [
                'name'               => $required . '|string|max:180',
                'short_form'         => 'sometimes|nullable|string|max:40',
                'qualification_type' => 'sometimes|nullable|string|max:100',
                'description'        => 'sometimes|nullable|string',
                'status'             => 'sometimes|nullable|in:active,inactive',
                'sort_order'         => 'sometimes|nullable|integer|min:0',
                'metadata'           => 'sometimes|nullable',
            ],
            default => [],
        };
    }

    private function validateUniqueFields(array $cfg, array $data, ?int $ignoreId = null): ?string
    {
        foreach ($cfg['unique'] as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                continue;
            }

            $query = DB::table($cfg['table'])->where($field, $data[$field]);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if ($query->exists()) {
                return Str::headline($field) . ' already exists';
            }
        }

        if (array_key_exists('slug', $data) && !empty($data['slug'])) {
            $query = DB::table($cfg['table'])->where('slug', $data['slug']);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if ($query->exists()) {
                return 'Slug already exists';
            }
        }

        return null;
    }

    private function buildPayload(Request $request, array $cfg, array $data, ?object $existing = null): array
    {
        $payload = [];

        foreach ($cfg['text'] as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $value = is_string($data[$field]) ? trim($data[$field]) : $data[$field];
            $payload[$field] = $value === '' ? null : $value;
        }

        foreach ($cfg['int'] ?? [] as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $payload[$field] = $data[$field] === null || $data[$field] === '' ? null : (int) $data[$field];
        }

        foreach ($cfg['decimal'] ?? [] as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $payload[$field] = $data[$field] === null || $data[$field] === '' ? null : $data[$field];
        }

        foreach ($cfg['json_object'] ?? [] as $field) {
            if (!$request->exists($field)) {
                continue;
            }

            $raw = $request->input($field);
            if ($raw === null || $raw === '') {
                $payload[$field] = null;
                continue;
            }

            if (is_array($raw)) {
                $payload[$field] = $this->toJsonOrNull($raw);
                continue;
            }

            $decoded = json_decode((string) $raw, true);
            $payload[$field] = json_last_error() === JSON_ERROR_NONE ? $this->toJsonOrNull($decoded) : $this->toJsonOrNull(['value' => $raw]);
        }

        if (array_key_exists('name', $payload) && $payload['name']) {
            $payload['slug'] = $this->makeSlug($cfg['table'], (string) $payload['name'], $existing?->id ? (int) $existing->id : null);
        }

        if (array_key_exists('status', $data)) {
            $payload['status'] = $this->normalizeStatus($data['status']);
        }

        if ($cfg['table'] === 'registration_councils' && (!isset($payload['country']) || $payload['country'] === null)) {
            $payload['country'] = $existing->country ?? 'India';
        }

        return $payload;
    }

    private function resolveRecord(array $cfg, $identifier, bool $withDeleted = false): ?object
    {
        $query = DB::table($cfg['table']);
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

    private function payload(array $cfg, object $row): array
    {
        $out = [
            'id'            => (int) $row->id,
            'uuid'          => (string) ($row->uuid ?? ''),
            'name'          => (string) ($row->name ?? ''),
            'slug'          => (string) ($row->slug ?? ''),
            'description'   => $row->description ?? null,
            'status'        => (string) ($row->status ?? 'active'),
            'sort_order'    => (int) ($row->sort_order ?? 0),
            'metadata'      => $this->decodeJsonValue($row->metadata ?? null),
            'created_by'    => $row->created_by ? (int) $row->created_by : null,
            'created_at_ip' => $row->created_at_ip ?? null,
            'created_at'    => $row->created_at ?? null,
            'updated_at'    => $row->updated_at ?? null,
            'deleted_at'    => $row->deleted_at ?? null,
        ];

        foreach (['short_form', 'country', 'state', 'website', 'code', 'qualification_type', 'default_price', 'default_duration_minutes'] as $field) {
            if (property_exists($row, $field)) {
                $out[$field] = $row->{$field};
            }
        }

        foreach ($cfg['file'] as $field) {
            $out[$field] = $this->publicAssetUrl($row->{$field} ?? null);
            $out[$field . '_path'] = (string) ($row->{$field} ?? '');
        }

        return $out;
    }

    public function all(Request $request)
    {
        $cfg = $this->config($this->masterKey($request));
        $this->ensureMasterPrivileges($request, $cfg);

        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'active'));
        $limit = min(1000, max(1, (int) $request->query('limit', 1000)));

        $query = DB::table($cfg['table'])->whereNull('deleted_at');
        if ($status !== '' && $status !== 'all') {
            $query->where('status', $this->normalizeStatus($status));
        }

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($cfg, $like) {
                foreach ($cfg['search'] as $field) {
                    $sub->orWhere($field, 'like', $like);
                }
            });
        }

        $rows = $query->orderBy('sort_order')->orderBy('name')->limit($limit)->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rows->map(fn ($row) => $this->payload($cfg, $row))->values(),
            'meta'   => ['count' => $rows->count()],
        ]);
    }

    public function index(Request $request)
    {
        $cfg = $this->config($this->masterKey($request));
        $this->ensureMasterPrivileges($request, $cfg);

        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'active'));

        $query = DB::table($cfg['table'])->whereNull('deleted_at');
        if ($status !== '' && $status !== 'all') {
            $query->where('status', $this->normalizeStatus($status));
        }

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($cfg, $like) {
                foreach ($cfg['search'] as $field) {
                    $sub->orWhere($field, 'like', $like);
                }
            });
        }

        $total = (clone $query)->count();
        $rows = $query->orderBy('sort_order')->orderBy('name')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rows->map(fn ($row) => $this->payload($cfg, $row))->values(),
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
        $cfg = $this->config($this->masterKey($request));

        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $q = trim((string) $request->query('q', ''));

        $query = DB::table($cfg['table'])->whereNotNull('deleted_at');
        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($cfg, $like) {
                foreach ($cfg['search'] as $field) {
                    $sub->orWhere($field, 'like', $like);
                }
            });
        }

        $total = (clone $query)->count();
        $rows = $query->orderByDesc('deleted_at')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rows->map(fn ($row) => $this->payload($cfg, $row))->values(),
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
        $cfg = $this->config($this->masterKey($request));
        $this->ensureMasterPrivileges($request, $cfg);

        $row = $this->resolveRecord($cfg, $identifier, false);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => $cfg['singular'] . ' not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $this->payload($cfg, $row),
        ]);
    }

    public function store(Request $request)
    {
        $master = $this->masterKey($request);
        $cfg = $this->config($master);
        $this->ensureMasterPrivileges($request, $cfg);

        $validator = Validator::make($request->all(), $this->baseRules($master, false));
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $payload = $this->buildPayload($request, $cfg, $data, null);
        $payload['uuid'] = (string) Str::uuid();
        $payload['status'] = $payload['status'] ?? 'active';
        $payload['sort_order'] = $payload['sort_order'] ?? 0;
        $payload['created_at'] = now();
        $payload['updated_at'] = now();
        $payload['created_at_ip'] = $request->ip();
        $payload['created_by'] = $this->actor($request)['id'] ?: null;

        $dupe = $this->validateUniqueFields($cfg, $payload);
        if ($dupe) {
            return response()->json(['status' => 'error', 'message' => $dupe], 422);
        }

        $newFiles = [];
        foreach ($cfg['file'] as $field) {
            if ($request->hasFile($field)) {
                $stored = $this->saveAsset($request->file($field), $master, $field);
                if ($stored === false) {
                    return response()->json(['status' => 'error', 'message' => 'Invalid ' . $field . ' upload'], 422);
                }
                $payload[$field] = $stored;
                $newFiles[] = $stored;
            }
        }

        try {
            $id = DB::table($cfg['table'])->insertGetId($payload);
            $fresh = DB::table($cfg['table'])->where('id', $id)->first();

            $actor = $this->actor($request);
            $this->logActivity(
                $request,
                $cfg,
                'store',
                (int) $fresh->id,
                array_keys($payload),
                null,
                $this->payload($cfg, $fresh),
                $cfg['singular'] . ' created' . (($name = $this->actorName($actor['id'])) ? ' by ' . $name : '')
            );

            $this->notifyAdmins(
                $cfg['singular'] . ' created',
                ($fresh->name ?? $cfg['singular']) . ' was created successfully.',
                [
                    'action'     => 'created',
                    'module'     => $cfg['module'],
                    'actor_id'   => $actor['id'],
                    'actor_role' => $actor['role'],
                    'record'     => $this->payload($cfg, $fresh),
                ],
                $cfg['page_href'],
                $cfg['module']
            );

            return response()->json([
                'status'  => 'success',
                'message' => $cfg['singular'] . ' created',
                'data'    => $this->payload($cfg, $fresh),
            ], 201);
        } catch (\Throwable $e) {
            foreach ($newFiles as $file) {
                $this->deleteManagedAsset($file);
            }

            Log::error('reference_master.store.failed', ['master' => $master, 'error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Could not create ' . strtolower($cfg['singular'])], 500);
        }
    }

    public function update(Request $request, $identifier)
    {
        $master = $this->masterKey($request);
        $cfg = $this->config($master);
        $this->ensureMasterPrivileges($request, $cfg);

        $row = $this->resolveRecord($cfg, $identifier, false);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => $cfg['singular'] . ' not found'], 404);
        }

        $validator = Validator::make($request->all(), $this->baseRules($master, true));
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $update = $this->buildPayload($request, $cfg, $data, $row);
        $changed = array_keys($update);

        $dupe = $this->validateUniqueFields($cfg, $update, (int) $row->id);
        if ($dupe) {
            return response()->json(['status' => 'error', 'message' => $dupe], 422);
        }

        $oldFiles = [];
        $newFiles = [];
        foreach ($cfg['file'] as $field) {
            if ($request->hasFile($field)) {
                $stored = $this->saveAsset($request->file($field), $master, $field);
                if ($stored === false) {
                    return response()->json(['status' => 'error', 'message' => 'Invalid ' . $field . ' upload'], 422);
                }
                $update[$field] = $stored;
                $changed[] = $field;
                $newFiles[] = $stored;
                if (!empty($row->{$field})) {
                    $oldFiles[] = $row->{$field};
                }
            }
        }

        if (empty($update)) {
            return response()->json(['status' => 'error', 'message' => 'No changes supplied'], 422);
        }

        $update['updated_at'] = now();

        try {
            DB::table($cfg['table'])->where('id', $row->id)->update($update);
            $fresh = DB::table($cfg['table'])->where('id', $row->id)->first();

            foreach ($oldFiles as $oldFile) {
                $this->deleteManagedAsset($oldFile);
            }

            $actor = $this->actor($request);
            $this->logActivity(
                $request,
                $cfg,
                'update',
                (int) $fresh->id,
                array_values(array_unique($changed)),
                $this->payload($cfg, $row),
                $this->payload($cfg, $fresh),
                $cfg['singular'] . ' updated' . (($name = $this->actorName($actor['id'])) ? ' by ' . $name : '')
            );

            $this->notifyAdmins(
                $cfg['singular'] . ' updated',
                ($fresh->name ?? $cfg['singular']) . ' was updated.',
                [
                    'action'         => 'updated',
                    'module'         => $cfg['module'],
                    'actor_id'       => $actor['id'],
                    'actor_role'     => $actor['role'],
                    'changed_fields' => array_values(array_unique($changed)),
                    'record'         => $this->payload($cfg, $fresh),
                ],
                $cfg['page_href'],
                $cfg['module']
            );

            return response()->json([
                'status'  => 'success',
                'message' => $cfg['singular'] . ' updated',
                'data'    => $this->payload($cfg, $fresh),
            ]);
        } catch (\Throwable $e) {
            foreach ($newFiles as $newFile) {
                $this->deleteManagedAsset($newFile);
            }

            Log::error('reference_master.update.failed', ['master' => $master, 'identifier' => $identifier, 'error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Could not update ' . strtolower($cfg['singular'])], 500);
        }
    }

    public function destroy(Request $request, $identifier)
    {
        $cfg = $this->config($this->masterKey($request));
        $row = $this->resolveRecord($cfg, $identifier, false);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => $cfg['singular'] . ' not found'], 404);
        }

        DB::table($cfg['table'])->where('id', $row->id)->update([
            'deleted_at' => now(),
            'status'     => 'inactive',
            'updated_at' => now(),
        ]);

        $actor = $this->actor($request);
        $this->logActivity(
            $request,
            $cfg,
            'destroy',
            (int) $row->id,
            ['deleted_at', 'status'],
            $this->payload($cfg, $row),
            ['deleted_at' => now()->toDateTimeString(), 'status' => 'inactive'],
            $cfg['singular'] . ' soft deleted' . (($name = $this->actorName($actor['id'])) ? ' by ' . $name : '')
        );

        $this->notifyAdmins(
            $cfg['singular'] . ' deleted',
            ($row->name ?? $cfg['singular']) . ' was moved to Bin.',
            [
                'action'     => 'soft_deleted',
                'module'     => $cfg['module'],
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'record'     => $this->payload($cfg, $row),
            ],
            $cfg['page_href'],
            $cfg['module']
        );

        return response()->json(['status' => 'success', 'message' => $cfg['singular'] . ' deleted']);
    }

    public function restore(Request $request, $identifier)
    {
        $cfg = $this->config($this->masterKey($request));
        $row = $this->resolveRecord($cfg, $identifier, true);
        if (!$row || !$row->deleted_at) {
            return response()->json(['status' => 'error', 'message' => $cfg['singular'] . ' not found or not deleted'], 404);
        }

        DB::table($cfg['table'])->where('id', $row->id)->update([
            'deleted_at' => null,
            'status'     => 'active',
            'updated_at' => now(),
        ]);

        $fresh = DB::table($cfg['table'])->where('id', $row->id)->first();
        $actor = $this->actor($request);

        $this->logActivity(
            $request,
            $cfg,
            'restore',
            (int) $fresh->id,
            ['deleted_at', 'status'],
            $this->payload($cfg, $row),
            $this->payload($cfg, $fresh),
            $cfg['singular'] . ' restored' . (($name = $this->actorName($actor['id'])) ? ' by ' . $name : '')
        );

        $this->notifyAdmins(
            $cfg['singular'] . ' restored',
            ($fresh->name ?? $cfg['singular']) . ' was restored.',
            [
                'action'     => 'restored',
                'module'     => $cfg['module'],
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'record'     => $this->payload($cfg, $fresh),
            ],
            $cfg['page_href'],
            $cfg['module']
        );

        return response()->json(['status' => 'success', 'message' => $cfg['singular'] . ' restored']);
    }

    public function forceDelete(Request $request, $identifier)
    {
        $cfg = $this->config($this->masterKey($request));
        $row = $this->resolveRecord($cfg, $identifier, true);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => $cfg['singular'] . ' not found'], 404);
        }

        DB::table($cfg['table'])->where('id', $row->id)->delete();
        foreach ($cfg['file'] as $field) {
            if (!empty($row->{$field})) {
                $this->deleteManagedAsset($row->{$field});
            }
        }

        $actor = $this->actor($request);
        $this->logActivity(
            $request,
            $cfg,
            'force_delete',
            (int) $row->id,
            [$cfg['module']],
            $this->payload($cfg, $row),
            null,
            $cfg['singular'] . ' permanently deleted' . (($name = $this->actorName($actor['id'])) ? ' by ' . $name : '')
        );

        $this->notifyAdmins(
            $cfg['singular'] . ' permanently deleted',
            ($row->name ?? $cfg['singular']) . ' was permanently deleted.',
            [
                'action'     => 'force_deleted',
                'module'     => $cfg['module'],
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'record'     => $this->payload($cfg, $row),
            ],
            $cfg['page_href'],
            $cfg['module'],
            'high'
        );

        return response()->json(['status' => 'success', 'message' => $cfg['singular'] . ' permanently deleted']);
    }
}
