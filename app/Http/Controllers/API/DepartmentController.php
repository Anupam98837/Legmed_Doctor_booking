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

class DepartmentController extends Controller
{
    use PersistsAdminNotifications;

    private const MODULE_NAME = 'departments';
    private const TABLE_NAME = 'departments';
    private const PAGE_HREF = '/departments';

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
            // Never break the main flow for activity logging.
        }
    }

    private function makeSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name, '-');
        $slug = $base !== '' ? $base : 'department';

        $i = 1;
        while (true) {
            $query = DB::table('departments')->where('slug', $slug);

            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $i++;
            $slug = ($base !== '' ? $base : 'department') . '-' . $i;
        }
    }

    private function normalizeStatus(?string $status): string
    {
        $value = strtolower(trim((string) $status));

        if (in_array($value, ['active', 'inactive', 'archived'], true)) {
            return $value;
        }

        return 'active';
    }

    private function resolveDepartment($identifier, bool $withDeleted = false): ?object
    {
        $q = DB::table('departments');

        if (!$withDeleted) {
            $q->whereNull('deleted_at');
        }

        if (is_numeric($identifier)) {
            return $q->where('id', (int) $identifier)->first();
        }

        $identifier = (string) $identifier;

        return $q->where(function ($sub) use ($identifier) {
            $sub->where('uuid', $identifier)
                ->orWhere('slug', $identifier);
        })->first();
    }

    protected function saveDepartmentImage($uploadedFile)
    {
        if (!$uploadedFile || !$uploadedFile->isValid()) {
            return false;
        }

        $destDir = public_path('assets/media/images/deptImage');
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $ext      = strtolower($uploadedFile->getClientOriginalExtension() ?: 'bin');
        $filename = 'dept_' . date('Ymd_His') . '_' . Str::lower(Str::random(16)) . '.' . $ext;

        $uploadedFile->move($destDir, $filename);

        return '/assets/media/images/deptImage/' . $filename;
    }

    protected function deleteManagedDepartmentImage(?string $url): void
    {
        if (empty($url)) {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH);
        $path = $path ?: $url;
        $path = '/' . ltrim($path, '/');

        if (Str::startsWith($path, '/assets/media/images/deptImage/')) {
            $abs = public_path(ltrim($path, '/'));
            if (File::exists($abs)) {
                @File::delete($abs);
            }
        }
    }

    protected function publicImageUrl(?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        $path = parse_url($value, PHP_URL_PATH);
        $path = $path ?: $value;
        $path = '/' . ltrim($path, '/');

        return url($path);
    }

    private function departmentPayload(object $row): array
    {
        return [
            'id'            => (int) $row->id,
            'uuid'          => (string) ($row->uuid ?? ''),
            'name'          => (string) ($row->name ?? ''),
            'short_form'    => $row->short_form,
            'slug'          => (string) ($row->slug ?? ''),
            'image'         => $this->publicImageUrl($row->image ?? null),
            'image_path'    => (string) ($row->image ?? ''),
            'description'   => $row->description,
            'status'        => (string) ($row->status ?? 'active'),
            'sort_order'    => (int) ($row->sort_order ?? 0),
            'metadata'      => $row->metadata,
            'created_by'    => $row->created_by ? (int) $row->created_by : null,
            'created_at_ip' => $row->created_at_ip,
            'created_at'    => $row->created_at,
            'updated_at'    => $row->updated_at,
            'deleted_at'    => $row->deleted_at,
        ];
    }

    private function academicsHeaderMenuId(Request $request): ?int
    {
        $existing = DB::table('dashboard_menu')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereRaw('LOWER(name) = ?', ['academics'])
                  ->orWhereRaw('LOWER(name) = ?', ['academic']);
            })
            ->orderBy('id')
            ->first();

        if ($existing) {
            return (int) $existing->id;
        }

        $actor = $this->actor($request);

        return (int) DB::table('dashboard_menu')->insertGetId([
            'parent_id'     => null,
            'uuid'          => (string) Str::uuid(),
            'position'      => 0,
            'name'          => 'Academics',
            'icon_class'    => 'fa-solid fa-building-columns',
            'href'          => null,
            'description'   => 'Academic management menu',
            'is_dropdown_head' => 1,
            'status'        => 'Active',
            'created_at'    => now(),
            'updated_at'    => now(),
            'created_by'    => $actor['id'] ?: null,
            'updated_by'    => $actor['id'] ?: null,
            'created_at_ip' => $request->ip(),
            'updated_at_ip' => $request->ip(),
            'deleted_at'    => null,
        ]);
    }

    private function ensureDepartmentPrivileges(Request $request): void
    {
        if (!Schema::hasTable('dashboard_menu') || !Schema::hasTable('page_privilege')) {
            return;
        }

        $menu = DB::table('dashboard_menu')
            ->whereNull('deleted_at')
            ->where('href', self::PAGE_HREF)
            ->first();

        if (!$menu) {
            $parentId = $this->academicsHeaderMenuId($request);
            $actor = $this->actor($request);

            $menuId = DB::table('dashboard_menu')->insertGetId([
                'parent_id'        => $parentId,
                'uuid'             => (string) Str::uuid(),
                'position'         => 0,
                'name'             => 'Departments',
                'icon_class'       => 'fa-solid fa-university',
                'href'             => self::PAGE_HREF,
                'description'      => 'Manage department records',
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

            $menu = DB::table('dashboard_menu')->where('id', $menuId)->first();
        }

        $menuId = (int) $menu->id;
        $defaults = [
            'view'         => ['/departments', '/departments/all', '/departments/archived', '/departments/bin', '/departments/{identifier}'],
            'create'       => ['/departments'],
            'update'       => ['/departments/{identifier}'],
            'delete'       => ['/departments/{identifier}'],
            'restore'      => ['/departments/{identifier}/restore'],
            'force_delete' => ['/departments/{identifier}/force'],
        ];

        foreach ($defaults as $action => $apis) {
            $exists = DB::table('page_privilege')
                ->where('dashboard_menu_id', $menuId)
                ->where('action', $action)
                ->first();

            if ($exists) {
                continue;
            }

            DB::table('page_privilege')->insert([
                'uuid'             => (string) Str::uuid(),
                'key'              => 'departments.' . $action,
                'dashboard_menu_id'=> $menuId,
                'action'           => $action,
                'description'      => 'Department ' . str_replace('_', ' ', $action),
                'order_no'         => null,
                'status'           => 'Active',
                'assigned_apis'    => json_encode($apis, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'meta'             => null,
                'created_at'       => now(),
                'updated_at'       => now(),
                'created_by'       => $this->actor($request)['id'] ?: null,
                'created_at_ip'    => $request->ip(),
                'deleted_at'       => null,
            ]);
        }
    }

    public function all(Request $request)
    {
        $this->ensureDepartmentPrivileges($request);

        $q      = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'active'));
        $limit  = min(1000, max(1, (int) $request->query('limit', 1000)));

        $query = DB::table('departments')->whereNull('deleted_at');

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('short_form', 'like', $like)
                    ->orWhere('slug', 'like', $like);
            });
        }

        $rows = $query->orderBy('sort_order')->orderBy('name')->limit($limit)->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rows->map(fn ($row) => $this->departmentPayload($row))->values(),
            'meta'   => ['count' => $rows->count()],
        ]);
    }

    public function index(Request $request)
    {
        $this->ensureDepartmentPrivileges($request);

        $page    = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('per_page', 10)));
        $q       = trim((string) $request->query('q', ''));
        $status  = trim((string) $request->query('status', 'active'));

        $query = DB::table('departments')->whereNull('deleted_at');

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('short_form', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        $total = (clone $query)->count();
        $rows = $query->orderBy('sort_order')->orderBy('name')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rows->map(fn ($row) => $this->departmentPayload($row))->values(),
            'meta'   => [
                'page'        => $page,
                'per_page'    => $perPage,
                'total'       => $total,
                'total_pages' => (int) ceil($total / max(1, $perPage)),
            ],
        ]);
    }

    public function archived(Request $request)
    {
        $request->merge(['status' => 'archived']);
        return $this->index($request);
    }

    public function bin(Request $request)
    {
        $page    = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('per_page', 10)));
        $q       = trim((string) $request->query('q', ''));

        $query = DB::table('departments')->whereNotNull('deleted_at');

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('short_form', 'like', $like)
                    ->orWhere('slug', 'like', $like);
            });
        }

        $total = (clone $query)->count();
        $rows = $query->orderByDesc('deleted_at')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rows->map(fn ($row) => $this->departmentPayload($row))->values(),
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
        $this->ensureDepartmentPrivileges($request);

        $row = $this->resolveDepartment($identifier, false);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Department not found'], 404);
        }

        return response()->json([
            'status'     => 'success',
            'department' => $this->departmentPayload($row),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureDepartmentPrivileges($request);

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:150',
            'short_form'  => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'image'       => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'status'      => 'nullable|string|max:20',
            'sort_order'  => 'nullable|integer|min:0',
            'metadata'    => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $actor = $this->actor($request);

        $metadata = null;
        if ($request->has('metadata')) {
            $raw = $request->input('metadata');
            if (is_array($raw)) {
                $metadata = json_encode($raw, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } elseif (is_string($raw) && trim($raw) !== '') {
                $decoded = json_decode($raw, true);
                $metadata = json_last_error() === JSON_ERROR_NONE
                    ? json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                    : null;
            }
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $this->saveDepartmentImage($request->file('image'));
            if ($imageUrl === false) {
                return response()->json(['status' => 'error', 'message' => 'Invalid image upload'], 422);
            }
        }

        $payload = [
            'uuid'          => (string) Str::uuid(),
            'name'          => trim((string) $data['name']),
            'short_form'    => !empty($data['short_form']) ? strtoupper(trim((string) $data['short_form'])) : null,
            'slug'          => $this->makeSlug((string) $data['name']),
            'image'         => $imageUrl,
            'description'   => $data['description'] ?? null,
            'status'        => $this->normalizeStatus($data['status'] ?? 'active'),
            'sort_order'    => (int) ($data['sort_order'] ?? 0),
            'metadata'      => $metadata,
            'created_at'    => now(),
            'updated_at'    => now(),
            'created_by'    => $actor['id'] ?: null,
            'created_at_ip' => $request->ip(),
            'deleted_at'    => null,
        ];

        try {
            $id = DB::table('departments')->insertGetId($payload);
            $fresh = DB::table('departments')->where('id', $id)->first();

            $actorName = $this->actorName($actor['id']);
            $this->logActivity(
                $request,
                'store',
                (int) $fresh->id,
                ['name', 'short_form', 'slug', 'image', 'status', 'sort_order'],
                null,
                $this->departmentPayload($fresh),
                'Department created' . ($actorName ? ' by ' . $actorName : '')
            );

            $this->notifyAdmins(
                'Department created',
                ($fresh->name ?? 'A department') . ' was created successfully.',
                [
                    'action'     => 'created',
                    'module'     => self::MODULE_NAME,
                    'actor_id'   => $actor['id'],
                    'actor_role' => $actor['role'],
                    'department' => $this->departmentPayload($fresh),
                ],
                self::PAGE_HREF,
                'department'
            );

            return response()->json([
                'status'     => 'success',
                'message'    => 'Department created',
                'department' => $this->departmentPayload($fresh),
            ], 201);
        } catch (\Throwable $e) {
            if ($imageUrl) {
                $this->deleteManagedDepartmentImage($imageUrl);
            }

            Log::error('department.store.failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Could not create department',
            ], 500);
        }
    }

    public function update(Request $request, $identifier)
    {
        $this->ensureDepartmentPrivileges($request);

        $row = $this->resolveDepartment($identifier, false);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Department not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:150',
            'short_form'  => 'sometimes|nullable|string|max:20',
            'description' => 'sometimes|nullable|string',
            'image'       => 'sometimes|nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'status'      => 'sometimes|nullable|string|max:20',
            'sort_order'  => 'sometimes|nullable|integer|min:0',
            'metadata'    => 'sometimes|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $update = [];
        $changed = [];
        $newImageUrl = null;
        $oldImageUrl = $row->image ?? null;

        if (array_key_exists('name', $data)) {
            $update['name'] = trim((string) $data['name']);
            $update['slug'] = $this->makeSlug($update['name'], (int) $row->id);
            $changed[] = 'name';
            $changed[] = 'slug';
        }

        if (array_key_exists('short_form', $data)) {
            $update['short_form'] = !empty($data['short_form']) ? strtoupper(trim((string) $data['short_form'])) : null;
            $changed[] = 'short_form';
        }

        if (array_key_exists('description', $data)) {
            $update['description'] = $data['description'];
            $changed[] = 'description';
        }

        if (array_key_exists('status', $data)) {
            $update['status'] = $this->normalizeStatus($data['status']);
            $changed[] = 'status';
        }

        if (array_key_exists('sort_order', $data)) {
            $update['sort_order'] = (int) ($data['sort_order'] ?? 0);
            $changed[] = 'sort_order';
        }

        if ($request->has('metadata')) {
            $raw = $request->input('metadata');

            if (is_array($raw)) {
                $update['metadata'] = json_encode($raw, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } elseif (is_string($raw) && trim($raw) !== '') {
                $decoded = json_decode($raw, true);
                $update['metadata'] = json_last_error() === JSON_ERROR_NONE
                    ? json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                    : null;
            } else {
                $update['metadata'] = null;
            }

            $changed[] = 'metadata';
        }

        if ($request->hasFile('image')) {
            $newImageUrl = $this->saveDepartmentImage($request->file('image'));
            if ($newImageUrl === false) {
                return response()->json(['status' => 'error', 'message' => 'Invalid image upload'], 422);
            }

            $update['image'] = $newImageUrl;
            $changed[] = 'image';
        }

        if (empty($update)) {
            return response()->json(['status' => 'error', 'message' => 'No changes supplied'], 422);
        }

        $update['updated_at'] = now();

        try {
            DB::table('departments')->where('id', $row->id)->update($update);
            $fresh = DB::table('departments')->where('id', $row->id)->first();

            if ($newImageUrl && !empty($oldImageUrl) && $oldImageUrl !== $newImageUrl) {
                $this->deleteManagedDepartmentImage($oldImageUrl);
            }

            $actor = $this->actor($request);
            $actorName = $this->actorName($actor['id']);

            $this->logActivity(
                $request,
                'update',
                (int) $fresh->id,
                array_values(array_unique($changed)),
                $this->departmentPayload($row),
                $this->departmentPayload($fresh),
                'Department updated' . ($actorName ? ' by ' . $actorName : '')
            );

            $this->notifyAdmins(
                'Department updated',
                ($fresh->name ?? 'A department') . ' was updated.',
                [
                    'action'         => 'updated',
                    'module'         => self::MODULE_NAME,
                    'actor_id'       => $actor['id'],
                    'actor_role'     => $actor['role'],
                    'changed_fields' => array_values(array_unique($changed)),
                    'department'     => $this->departmentPayload($fresh),
                ],
                self::PAGE_HREF,
                'department'
            );

            return response()->json([
                'status'     => 'success',
                'message'    => 'Department updated',
                'department' => $this->departmentPayload($fresh),
            ]);
        } catch (\Throwable $e) {
            if ($newImageUrl) {
                $this->deleteManagedDepartmentImage($newImageUrl);
            }

            Log::error('department.update.failed', [
                'identifier' => $identifier,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Could not update department',
            ], 500);
        }
    }

    public function destroy(Request $request, $identifier)
    {
        $row = $this->resolveDepartment($identifier, false);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Department not found'], 404);
        }

        DB::table('departments')->where('id', $row->id)->update([
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
            $this->departmentPayload($row),
            ['deleted_at' => now()->toDateTimeString(), 'status' => 'inactive'],
            'Department soft deleted' . ($actorName ? ' by ' . $actorName : '')
        );

        $this->notifyAdmins(
            'Department deleted',
            ($row->name ?? 'A department') . ' was moved to deleted state.',
            [
                'action'     => 'soft_deleted',
                'module'     => self::MODULE_NAME,
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'department' => $this->departmentPayload($row),
            ],
            self::PAGE_HREF,
            'department'
        );

        return response()->json(['status' => 'success', 'message' => 'Department deleted']);
    }

    public function restore(Request $request, $identifier)
    {
        $row = $this->resolveDepartment($identifier, true);
        if (!$row || !$row->deleted_at) {
            return response()->json(['status' => 'error', 'message' => 'Department not found or not deleted'], 404);
        }

        DB::table('departments')->where('id', $row->id)->update([
            'deleted_at' => null,
            'status'     => 'active',
            'updated_at' => now(),
        ]);

        $fresh = DB::table('departments')->where('id', $row->id)->first();
        $actor = $this->actor($request);
        $actorName = $this->actorName($actor['id']);

        $this->logActivity(
            $request,
            'restore',
            (int) $fresh->id,
            ['deleted_at', 'status'],
            $this->departmentPayload($row),
            $this->departmentPayload($fresh),
            'Department restored' . ($actorName ? ' by ' . $actorName : '')
        );

        $this->notifyAdmins(
            'Department restored',
            ($fresh->name ?? 'A department') . ' was restored.',
            [
                'action'     => 'restored',
                'module'     => self::MODULE_NAME,
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'department' => $this->departmentPayload($fresh),
            ],
            self::PAGE_HREF,
            'department'
        );

        return response()->json(['status' => 'success', 'message' => 'Department restored']);
    }

    public function forceDelete(Request $request, $identifier)
    {
        $row = $this->resolveDepartment($identifier, true);
        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Department not found'], 404);
        }

        DB::table('departments')->where('id', $row->id)->delete();

        if (!empty($row->image)) {
            $this->deleteManagedDepartmentImage($row->image);
        }

        $actor = $this->actor($request);
        $actorName = $this->actorName($actor['id']);

        $this->logActivity(
            $request,
            'force_delete',
            (int) $row->id,
            ['department'],
            $this->departmentPayload($row),
            null,
            'Department permanently deleted' . ($actorName ? ' by ' . $actorName : '')
        );

        $this->notifyAdmins(
            'Department permanently deleted',
            ($row->name ?? 'A department') . ' was permanently deleted.',
            [
                'action'     => 'force_deleted',
                'module'     => self::MODULE_NAME,
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'department' => $this->departmentPayload($row),
            ],
            self::PAGE_HREF,
            'department',
            'high'
        );

        return response()->json(['status' => 'success', 'message' => 'Department permanently deleted']);
    }
}