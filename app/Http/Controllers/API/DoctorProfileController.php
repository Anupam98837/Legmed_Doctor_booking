<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\PersistsAdminNotifications;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DoctorProfileController extends Controller
{
    use PersistsAdminNotifications;

    private const MODULE_NAME = 'doctors';
    private const TABLE_NAME = 'doctors';
    private const PAGE_HREF = '/doctor/profile';

    private function actor(Request $request): array
    {
        return [
            'id'   => (int) ($request->attributes->get('auth_tokenable_id') ?? 0),
            'role' => (string) ($request->attributes->get('auth_role') ?? ''),
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

    private function decodeJsonInput(Request $request, string $key, $default = [])
    {
        $value = $request->input($key);
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
    }

    private function normalizeBool($value, bool $default = false): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return ((int) $value) === 1;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on'], true);
    }

    private function normalizeStatus(?string $value, string $default = 'active'): string
    {
        $value = strtolower(trim((string) $value));
        return in_array($value, ['active', 'inactive'], true) ? $value : $default;
    }

    private function normalizeProfileVisibility(?string $value): string
    {
        $value = strtolower(trim((string) $value));
        return in_array($value, ['public', 'private'], true) ? $value : 'public';
    }

    private function normalizeVerificationStatus(?string $value): string
    {
        $value = strtolower(trim((string) $value));
        return in_array($value, ['pending', 'verified', 'rejected'], true) ? $value : 'pending';
    }

    private function publicAssetUrl(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = (string) $value;
        if (Str::startsWith($value, ['http://', 'https://', '//'])) {
            return $value;
        }

        return '/' . ltrim($value, '/');
    }

    private function saveAsset($uploadedFile, string $prefix)
    {
        if (!$uploadedFile || !$uploadedFile->isValid()) {
            return false;
        }

        $destDir = public_path('assets/media/images/doctors');
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $ext = strtolower($uploadedFile->getClientOriginalExtension() ?: 'bin');
        $filename = $prefix . '_' . date('Ymd_His') . '_' . Str::lower(Str::random(16)) . '.' . $ext;
        $uploadedFile->move($destDir, $filename);

        return '/assets/media/images/doctors/' . $filename;
    }

    private function deleteManagedAsset(?string $url): void
    {
        if (empty($url)) {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        $path = '/' . ltrim($path, '/');
        if (!Str::startsWith($path, '/assets/media/images/doctors/')) {
            return;
        }

        $absolute = public_path(ltrim($path, '/'));
        if (File::exists($absolute)) {
            @File::delete($absolute);
        }
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
            // Logging must never break flow.
        }
    }

    private function makeSlug(string $name, int $userId, ?int $ignoreId = null): string
    {
        $base = Str::slug($name, '-');
        $slug = ($base !== '' ? $base : 'doctor') . '-' . $userId;
        $i = 1;

        while (true) {
            $query = DB::table('doctors')->where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $i++;
            $slug = (($base !== '' ? $base : 'doctor') . '-' . $userId . '-' . $i);
        }
    }

    private function pagePayload(object $user, ?object $doctor): array
    {
        return [
            'user' => [
                'id'           => (int) $user->id,
                'uuid'         => (string) $user->uuid,
                'name'         => (string) ($user->name ?? ''),
                'email'        => (string) ($user->email ?? ''),
                'phone_number' => (string) ($user->phone_number ?? ''),
                'alternative_email' => (string) ($user->alternative_email ?? ''),
                'alternative_phone_number' => (string) ($user->alternative_phone_number ?? ''),
                'whatsapp_number' => (string) ($user->whatsapp_number ?? ''),
                'address' => (string) ($user->address ?? ''),
                'image'        => $this->publicAssetUrl($user->image ?? null),
                'image_path'   => (string) ($user->image ?? ''),
                'role'         => (string) ($user->role ?? ''),
                'status'       => (string) ($user->status ?? ''),
            ],
            'doctor' => $doctor ? [
                'id'                               => (int) $doctor->id,
                'uuid'                             => (string) $doctor->uuid,
                'doctor_code'                      => (string) ($doctor->doctor_code ?? ''),
                'slug'                             => (string) ($doctor->slug ?? ''),
                'user_id'                          => (int) $doctor->user_id,
                'designation_id'                   => $doctor->designation_id ? (int) $doctor->designation_id : null,
                'primary_hospital_id'              => $doctor->primary_hospital_id ? (int) $doctor->primary_hospital_id : null,
                'primary_department_id'            => $doctor->primary_department_id ? (int) $doctor->primary_department_id : null,
                'primary_specialization_id'        => $doctor->primary_specialization_id ? (int) $doctor->primary_specialization_id : null,
                'registration_council_id'          => $doctor->registration_council_id ? (int) $doctor->registration_council_id : null,
                'qualification_summary'            => (string) ($doctor->qualification_summary ?? ''),
                'years_of_experience'              => (int) ($doctor->years_of_experience ?? 0),
                'medical_registration_number'      => (string) ($doctor->medical_registration_number ?? ''),
                'registration_year'                => $doctor->registration_year ? (int) $doctor->registration_year : null,
                'short_bio'                        => (string) ($doctor->short_bio ?? ''),
                'about_doctor'                     => (string) ($doctor->about_doctor ?? ''),
                'cover_photo'                      => $this->publicAssetUrl($doctor->cover_photo ?? null),
                'cover_photo_path'                 => (string) ($doctor->cover_photo ?? ''),
                'gallery'                          => is_array($gallery = json_decode((string) ($doctor->gallery ?? '[]'), true)) ? $gallery : [],
                'consultation_fee'                 => $doctor->consultation_fee,
                'followup_fee'                     => $doctor->followup_fee,
                'video_consultation_fee'           => $doctor->video_consultation_fee,
                'home_visit_fee'                   => $doctor->home_visit_fee,
                'online_consultation_available'    => (bool) $doctor->online_consultation_available,
                'in_person_consultation_available' => (bool) $doctor->in_person_consultation_available,
                'home_visit_available'             => (bool) $doctor->home_visit_available,
                'appointment_booking_available'    => (bool) $doctor->appointment_booking_available,
                'total_patients_treated'           => (int) ($doctor->total_patients_treated ?? 0),
                'total_surgeries'                  => (int) ($doctor->total_surgeries ?? 0),
                'total_consultations'              => (int) ($doctor->total_consultations ?? 0),
                'average_rating'                   => $doctor->average_rating,
                'review_count'                     => (int) ($doctor->review_count ?? 0),
                'featured_status'                  => (bool) $doctor->featured_status,
                'verification_status'              => (string) ($doctor->verification_status ?? 'pending'),
                'profile_visibility'               => (string) ($doctor->profile_visibility ?? 'public'),
                'status'                           => (string) ($doctor->status ?? 'active'),
                'sort_order'                       => (int) ($doctor->sort_order ?? 0),
                'profile_completion_percentage'    => (int) ($doctor->profile_completion_percentage ?? 0),
                'seo_title'                        => (string) ($doctor->seo_title ?? ''),
                'seo_description'                  => (string) ($doctor->seo_description ?? ''),
                'metadata'                         => is_array($meta = json_decode((string) ($doctor->metadata ?? '[]'), true)) ? $meta : null,
            ] : null,
        ];
    }

    private function optionList(string $table, array $columns = ['id', 'name']): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        $selectColumns = array_values(array_filter($columns, fn ($column) => Schema::hasColumn($table, $column)));
        if (Schema::hasColumn($table, 'id') && !in_array('id', $selectColumns, true)) {
            array_unshift($selectColumns, 'id');
        }
        if (empty($selectColumns)) {
            return [];
        }

        $query = DB::table($table);
        if (Schema::hasColumn($table, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }
        if (Schema::hasColumn($table, 'status')) {
            $query->where(function ($q) {
                $q->where('status', 'active')->orWhere('status', 'Active');
            });
        }
        if (Schema::hasColumn($table, 'sort_order')) {
            $query->orderBy('sort_order');
        }
        if (Schema::hasColumn($table, 'name')) {
            $query->orderBy('name');
        }

        return $query->get($selectColumns)->map(function ($row) use ($table) {
            $payload = (array) $row;
            if ($table === 'hospitals') {
                $payload['label'] = trim((string) (($row->name ?? '') . (!empty($row->hospital_code) ? ' (' . $row->hospital_code . ')' : '')));
            } elseif ($table === 'clinics') {
                $payload['label'] = trim((string) (($row->name ?? '') . (!empty($row->clinic_code) ? ' (' . $row->clinic_code . ')' : '')));
            } elseif ($table === 'departments') {
                $payload['label'] = (string) ($row->title ?? $row->name ?? '');
            } elseif ($table === 'languages') {
                $payload['label'] = trim((string) (($row->name ?? '') . (!empty($row->code) ? ' (' . $row->code . ')' : '')));
            } else {
                $payload['label'] = (string) ($row->name ?? '');
            }

            return $payload;
        })->values()->all();
    }

    private function optionsPayload(): array
    {
        return [
            'designations'          => $this->optionList('designations', ['id', 'name', 'short_form']),
            'hospitals'             => $this->optionList('hospitals', ['id', 'name', 'hospital_code']),
            'departments'           => $this->optionList('departments', ['id', 'name', 'short_form']),
            'specializations'       => $this->optionList('specializations', ['id', 'name', 'short_form']),
            'registration_councils' => $this->optionList('registration_councils', ['id', 'name', 'short_form']),
            'languages'             => $this->optionList('languages', ['id', 'name', 'code']),
            'services'              => $this->optionList('services', ['id', 'name', 'short_form', 'default_price', 'default_duration_minutes']),
            'qualifications'        => $this->optionList('qualifications', ['id', 'name', 'short_form', 'qualification_type']),
            'clinics'               => $this->optionList('clinics', ['id', 'name', 'clinic_code']),
        ];
    }

    private function doctorRelations(int $doctorId): array
    {
        return [
            'specializations' => DB::table('doctor_specializations')
                ->where('doctor_id', $doctorId)
                ->orderBy('sort_order')
                ->orderByDesc('is_primary')
                ->get(['specialization_id', 'is_primary', 'sort_order'])
                ->map(fn ($row) => [
                    'specialization_id' => (int) $row->specialization_id,
                    'is_primary'        => (bool) $row->is_primary,
                    'sort_order'        => (int) ($row->sort_order ?? 0),
                ])->values()->all(),
            'languages' => DB::table('doctor_languages')
                ->where('doctor_id', $doctorId)
                ->orderBy('sort_order')
                ->get(['language_id', 'proficiency_level', 'sort_order'])
                ->map(fn ($row) => [
                    'language_id'        => (int) $row->language_id,
                    'proficiency_level'  => (string) ($row->proficiency_level ?? ''),
                    'sort_order'         => (int) ($row->sort_order ?? 0),
                ])->values()->all(),
            'services' => DB::table('doctor_services')
                ->where('doctor_id', $doctorId)
                ->orderBy('sort_order')
                ->get(['service_id', 'custom_fee', 'custom_duration_minutes', 'notes', 'sort_order'])
                ->map(fn ($row) => [
                    'service_id'               => (int) $row->service_id,
                    'custom_fee'               => $row->custom_fee,
                    'custom_duration_minutes'  => $row->custom_duration_minutes ? (int) $row->custom_duration_minutes : null,
                    'notes'                    => (string) ($row->notes ?? ''),
                    'sort_order'               => (int) ($row->sort_order ?? 0),
                ])->values()->all(),
            'qualifications' => DB::table('doctor_qualifications')
                ->where('doctor_id', $doctorId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['id', 'qualification_id', 'institute_name', 'university_name', 'country', 'start_year', 'end_year', 'description', 'sort_order'])
                ->map(fn ($row) => [
                    'id'               => (int) $row->id,
                    'qualification_id' => (int) $row->qualification_id,
                    'institute_name'   => (string) ($row->institute_name ?? ''),
                    'university_name'  => (string) ($row->university_name ?? ''),
                    'country'          => (string) ($row->country ?? ''),
                    'start_year'       => $row->start_year ? (int) $row->start_year : null,
                    'end_year'         => $row->end_year ? (int) $row->end_year : null,
                    'description'      => (string) ($row->description ?? ''),
                    'sort_order'       => (int) ($row->sort_order ?? 0),
                ])->values()->all(),
            'clinics' => DB::table('doctor_clinics')
                ->where('doctor_id', $doctorId)
                ->orderBy('sort_order')
                ->orderByDesc('is_primary')
                ->get([
                    'clinic_id',
                    'is_primary',
                    'consultation_fee',
                    'followup_fee',
                    'video_consultation_fee',
                    'online_consultation_available',
                    'in_person_consultation_available',
                    'appointment_booking_available',
                    'room_no',
                    'visit_note',
                    'sort_order',
                ])
                ->map(fn ($row) => [
                    'clinic_id'                        => (int) $row->clinic_id,
                    'is_primary'                       => (bool) $row->is_primary,
                    'consultation_fee'                 => $row->consultation_fee,
                    'followup_fee'                     => $row->followup_fee,
                    'video_consultation_fee'           => $row->video_consultation_fee,
                    'online_consultation_available'    => (bool) $row->online_consultation_available,
                    'in_person_consultation_available' => (bool) $row->in_person_consultation_available,
                    'appointment_booking_available'    => (bool) $row->appointment_booking_available,
                    'room_no'                          => (string) ($row->room_no ?? ''),
                    'visit_note'                       => (string) ($row->visit_note ?? ''),
                    'sort_order'                       => (int) ($row->sort_order ?? 0),
                ])->values()->all(),
        ];
    }

    private function resolveUser(string $userUuid): ?object
    {
        return DB::table('users')
            ->where('uuid', $userUuid)
            ->whereNull('deleted_at')
            ->first([
                'id',
                'uuid',
                'name',
                'email',
                'phone_number',
                'alternative_email',
                'alternative_phone_number',
                'whatsapp_number',
                'address',
                'image',
                'role',
                'status',
            ]);
    }

    private function doctorForUser(int $userId): ?object
    {
        return DB::table('doctors')->where('user_id', $userId)->whereNull('deleted_at')->first();
    }

    private function validateUniqueFields(array $payload, ?int $ignoreId = null): ?string
    {
        foreach (['doctor_code', 'slug', 'medical_registration_number'] as $field) {
            if (empty($payload[$field])) {
                continue;
            }

            $query = DB::table('doctors')->where($field, $payload[$field]);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if ($query->exists()) {
                return ucwords(str_replace('_', ' ', $field)) . ' already exists';
            }
        }

        return null;
    }

    private function calculateProfileCompletion(array $doctor, array $relations): int
    {
        $checks = [
            !empty($doctor['designation_id']),
            !empty($doctor['primary_hospital_id']),
            !empty($doctor['primary_department_id']),
            !empty($doctor['primary_specialization_id']),
            !empty($doctor['registration_council_id']),
            !empty($doctor['qualification_summary']),
            !empty($doctor['medical_registration_number']),
            !empty($doctor['short_bio']),
            !empty($doctor['about_doctor']),
            !empty($doctor['cover_photo']),
            !empty($relations['specializations']),
            !empty($relations['languages']),
            !empty($relations['services']),
            !empty($relations['qualifications']),
            !empty($relations['clinics']),
        ];

        $done = count(array_filter($checks));
        return (int) round(($done / max(1, count($checks))) * 100);
    }

    private function normalizedSpecializations(array $rows): array
    {
        $out = [];
        foreach ($rows as $index => $row) {
            $id = (int) ($row['specialization_id'] ?? $row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $out[] = [
                'specialization_id' => $id,
                'is_primary'        => $this->normalizeBool($row['is_primary'] ?? false),
                'sort_order'        => (int) ($row['sort_order'] ?? $index),
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        return collect($out)->unique('specialization_id')->values()->all();
    }

    private function normalizedLanguages(array $rows): array
    {
        $out = [];
        foreach ($rows as $index => $row) {
            $id = (int) ($row['language_id'] ?? $row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $out[] = [
                'language_id'        => $id,
                'proficiency_level'  => trim((string) ($row['proficiency_level'] ?? '')),
                'sort_order'         => (int) ($row['sort_order'] ?? $index),
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        }

        return collect($out)->unique('language_id')->values()->all();
    }

    private function normalizedServices(array $rows): array
    {
        $out = [];
        foreach ($rows as $index => $row) {
            $id = (int) ($row['service_id'] ?? $row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $out[] = [
                'service_id'               => $id,
                'custom_fee'               => ($row['custom_fee'] ?? '') !== '' ? (float) $row['custom_fee'] : null,
                'custom_duration_minutes'  => ($row['custom_duration_minutes'] ?? '') !== '' ? (int) $row['custom_duration_minutes'] : null,
                'notes'                    => trim((string) ($row['notes'] ?? '')),
                'sort_order'               => (int) ($row['sort_order'] ?? $index),
                'created_at'               => now(),
                'updated_at'               => now(),
            ];
        }

        return collect($out)->unique('service_id')->values()->all();
    }

    private function normalizedQualifications(array $rows): array
    {
        $out = [];
        foreach ($rows as $index => $row) {
            $id = (int) ($row['qualification_id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $out[] = [
                'qualification_id' => $id,
                'institute_name'   => trim((string) ($row['institute_name'] ?? '')),
                'university_name'  => trim((string) ($row['university_name'] ?? '')),
                'country'          => trim((string) ($row['country'] ?? '')),
                'start_year'       => ($row['start_year'] ?? '') !== '' ? (int) $row['start_year'] : null,
                'end_year'         => ($row['end_year'] ?? '') !== '' ? (int) $row['end_year'] : null,
                'description'      => trim((string) ($row['description'] ?? '')),
                'sort_order'       => (int) ($row['sort_order'] ?? $index),
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        return array_values($out);
    }

    private function normalizedClinics(array $rows): array
    {
        $out = [];
        foreach ($rows as $index => $row) {
            $id = (int) ($row['clinic_id'] ?? $row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $out[] = [
                'clinic_id'                        => $id,
                'is_primary'                       => $this->normalizeBool($row['is_primary'] ?? false),
                'consultation_fee'                 => ($row['consultation_fee'] ?? '') !== '' ? (float) $row['consultation_fee'] : null,
                'followup_fee'                     => ($row['followup_fee'] ?? '') !== '' ? (float) $row['followup_fee'] : null,
                'video_consultation_fee'           => ($row['video_consultation_fee'] ?? '') !== '' ? (float) $row['video_consultation_fee'] : null,
                'online_consultation_available'    => $this->normalizeBool($row['online_consultation_available'] ?? false),
                'in_person_consultation_available' => $this->normalizeBool($row['in_person_consultation_available'] ?? true, true),
                'appointment_booking_available'    => $this->normalizeBool($row['appointment_booking_available'] ?? true, true),
                'room_no'                          => trim((string) ($row['room_no'] ?? '')),
                'visit_note'                       => trim((string) ($row['visit_note'] ?? '')),
                'sort_order'                       => (int) ($row['sort_order'] ?? $index),
                'created_at'                       => now(),
                'updated_at'                       => now(),
            ];
        }

        return collect($out)->unique('clinic_id')->values()->all();
    }

    private function syncRelations(int $doctorId, array $relations): void
    {
        DB::table('doctor_specializations')->where('doctor_id', $doctorId)->delete();
        if (!empty($relations['specializations'])) {
            DB::table('doctor_specializations')->insert(array_map(function ($row) use ($doctorId) {
                $row['doctor_id'] = $doctorId;
                return $row;
            }, $relations['specializations']));
        }

        DB::table('doctor_languages')->where('doctor_id', $doctorId)->delete();
        if (!empty($relations['languages'])) {
            DB::table('doctor_languages')->insert(array_map(function ($row) use ($doctorId) {
                $row['doctor_id'] = $doctorId;
                return $row;
            }, $relations['languages']));
        }

        DB::table('doctor_services')->where('doctor_id', $doctorId)->delete();
        if (!empty($relations['services'])) {
            DB::table('doctor_services')->insert(array_map(function ($row) use ($doctorId) {
                $row['doctor_id'] = $doctorId;
                return $row;
            }, $relations['services']));
        }

        DB::table('doctor_qualifications')->where('doctor_id', $doctorId)->delete();
        if (!empty($relations['qualifications'])) {
            DB::table('doctor_qualifications')->insert(array_map(function ($row) use ($doctorId) {
                $row['doctor_id'] = $doctorId;
                return $row;
            }, $relations['qualifications']));
        }

        DB::table('doctor_clinics')->where('doctor_id', $doctorId)->delete();
        if (!empty($relations['clinics'])) {
            DB::table('doctor_clinics')->insert(array_map(function ($row) use ($doctorId) {
                $row['doctor_id'] = $doctorId;
                return $row;
            }, $relations['clinics']));
        }
    }

    public function show(Request $request, string $userUuid)
    {
        $user = $this->resolveUser($userUuid);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $doctor = $this->doctorForUser((int) $user->id);
        $data = $this->pagePayload($user, $doctor);
        $data['relations'] = $doctor ? $this->doctorRelations((int) $doctor->id) : [
            'specializations' => [],
            'languages' => [],
            'services' => [],
            'qualifications' => [],
            'clinics' => [],
        ];
        $data['options'] = $this->optionsPayload();

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    public function save(Request $request, string $userUuid)
    {
        $user = $this->resolveUser($userUuid);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $doctor = $this->doctorForUser((int) $user->id);
        $validator = Validator::make($request->all(), [
            'user_name'                        => 'sometimes|nullable|string|max:255',
            'user_phone_number'                => 'sometimes|nullable|string|max:32',
            'user_alternative_email'           => 'sometimes|nullable|email|max:255',
            'user_alternative_phone_number'    => 'sometimes|nullable|string|max:32',
            'user_whatsapp_number'             => 'sometimes|nullable|string|max:32',
            'user_address'                     => 'sometimes|nullable|string',
            'user_image'                       => 'sometimes|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'doctor_code'                      => 'sometimes|nullable|string|max:80',
            'designation_id'                   => 'sometimes|nullable|integer|exists:designations,id',
            'primary_hospital_id'              => 'sometimes|nullable|integer|exists:hospitals,id',
            'primary_department_id'            => 'sometimes|nullable|integer|exists:departments,id',
            'primary_specialization_id'        => 'sometimes|nullable|integer|exists:specializations,id',
            'registration_council_id'          => 'sometimes|nullable|integer|exists:registration_councils,id',
            'qualification_summary'            => 'sometimes|nullable|string|max:255',
            'years_of_experience'              => 'sometimes|nullable|integer|min:0|max:80',
            'medical_registration_number'      => 'sometimes|nullable|string|max:120',
            'registration_year'                => 'sometimes|nullable|integer|min:1900|max:2100',
            'short_bio'                        => 'sometimes|nullable|string|max:500',
            'about_doctor'                     => 'sometimes|nullable|string',
            'consultation_fee'                 => 'sometimes|nullable|numeric|min:0',
            'followup_fee'                     => 'sometimes|nullable|numeric|min:0',
            'video_consultation_fee'           => 'sometimes|nullable|numeric|min:0',
            'home_visit_fee'                   => 'sometimes|nullable|numeric|min:0',
            'total_patients_treated'           => 'sometimes|nullable|integer|min:0',
            'total_surgeries'                  => 'sometimes|nullable|integer|min:0',
            'total_consultations'              => 'sometimes|nullable|integer|min:0',
            'average_rating'                   => 'sometimes|nullable|numeric|min:0|max:5',
            'review_count'                     => 'sometimes|nullable|integer|min:0',
            'verification_status'              => 'sometimes|nullable|string|max:20',
            'profile_visibility'               => 'sometimes|nullable|string|max:20',
            'status'                           => 'sometimes|nullable|string|max:20',
            'sort_order'                       => 'sometimes|nullable|integer|min:0',
            'seo_title'                        => 'sometimes|nullable|string|max:255',
            'seo_description'                  => 'sometimes|nullable|string|max:500',
            'cover_photo'                      => 'sometimes|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'gallery'                          => 'sometimes|nullable',
            'metadata'                         => 'sometimes|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $resolvedUserName = trim((string) ($validated['user_name'] ?? $user->name ?? ''));
        $relations = [
            'specializations' => $this->normalizedSpecializations((array) $this->decodeJsonInput($request, 'specializations', [])),
            'languages'       => $this->normalizedLanguages((array) $this->decodeJsonInput($request, 'languages', [])),
            'services'        => $this->normalizedServices((array) $this->decodeJsonInput($request, 'services', [])),
            'qualifications'  => $this->normalizedQualifications((array) $this->decodeJsonInput($request, 'qualifications', [])),
            'clinics'         => $this->normalizedClinics((array) $this->decodeJsonInput($request, 'clinics', [])),
        ];

        $userPayload = [
            'name' => $resolvedUserName !== '' ? $resolvedUserName : (string) ($user->name ?? ''),
            'phone_number' => trim((string) ($validated['user_phone_number'] ?? '')) ?: null,
            'alternative_email' => trim((string) ($validated['user_alternative_email'] ?? '')) ?: null,
            'alternative_phone_number' => trim((string) ($validated['user_alternative_phone_number'] ?? '')) ?: null,
            'whatsapp_number' => trim((string) ($validated['user_whatsapp_number'] ?? '')) ?: null,
            'address' => trim((string) ($validated['user_address'] ?? '')) ?: null,
        ];

        $payload = [
            'doctor_code'                      => trim((string) ($validated['doctor_code'] ?? '')) ?: null,
            'designation_id'                   => $validated['designation_id'] ?? null,
            'primary_hospital_id'              => $validated['primary_hospital_id'] ?? null,
            'primary_department_id'            => $validated['primary_department_id'] ?? null,
            'primary_specialization_id'        => $validated['primary_specialization_id'] ?? null,
            'registration_council_id'          => $validated['registration_council_id'] ?? null,
            'qualification_summary'            => trim((string) ($validated['qualification_summary'] ?? '')) ?: null,
            'years_of_experience'              => (int) ($validated['years_of_experience'] ?? 0),
            'medical_registration_number'      => trim((string) ($validated['medical_registration_number'] ?? '')) ?: null,
            'registration_year'                => ($validated['registration_year'] ?? '') !== '' ? (int) $validated['registration_year'] : null,
            'short_bio'                        => trim((string) ($validated['short_bio'] ?? '')) ?: null,
            'about_doctor'                     => trim((string) ($validated['about_doctor'] ?? '')) ?: null,
            'gallery'                          => $this->toJsonOrNull($this->decodeJsonInput($request, 'gallery', [])),
            'consultation_fee'                 => ($validated['consultation_fee'] ?? '') !== '' ? (float) $validated['consultation_fee'] : null,
            'followup_fee'                     => ($validated['followup_fee'] ?? '') !== '' ? (float) $validated['followup_fee'] : null,
            'video_consultation_fee'           => ($validated['video_consultation_fee'] ?? '') !== '' ? (float) $validated['video_consultation_fee'] : null,
            'home_visit_fee'                   => ($validated['home_visit_fee'] ?? '') !== '' ? (float) $validated['home_visit_fee'] : null,
            'online_consultation_available'    => $this->normalizeBool($request->input('online_consultation_available')),
            'in_person_consultation_available' => $this->normalizeBool($request->input('in_person_consultation_available'), true),
            'home_visit_available'             => $this->normalizeBool($request->input('home_visit_available')),
            'appointment_booking_available'    => $this->normalizeBool($request->input('appointment_booking_available'), true),
            'total_patients_treated'           => (int) ($validated['total_patients_treated'] ?? 0),
            'total_surgeries'                  => (int) ($validated['total_surgeries'] ?? 0),
            'total_consultations'              => (int) ($validated['total_consultations'] ?? 0),
            'average_rating'                   => ($validated['average_rating'] ?? '') !== '' ? (float) $validated['average_rating'] : 0,
            'review_count'                     => (int) ($validated['review_count'] ?? 0),
            'featured_status'                  => $this->normalizeBool($request->input('featured_status')),
            'verification_status'              => $this->normalizeVerificationStatus($validated['verification_status'] ?? null),
            'profile_visibility'               => $this->normalizeProfileVisibility($validated['profile_visibility'] ?? null),
            'status'                           => $this->normalizeStatus($validated['status'] ?? null),
            'sort_order'                       => (int) ($validated['sort_order'] ?? 0),
            'seo_title'                        => trim((string) ($validated['seo_title'] ?? '')) ?: null,
            'seo_description'                  => trim((string) ($validated['seo_description'] ?? '')) ?: null,
            'metadata'                         => $this->toJsonOrNull($this->decodeJsonInput($request, 'metadata', null)),
        ];

        if (!empty($userPayload['phone_number'])) {
            $phoneInUse = DB::table('users')
                ->where('phone_number', $userPayload['phone_number'])
                ->where('id', '!=', $user->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($phoneInUse) {
                return response()->json(['status' => 'error', 'message' => 'Phone number already exists'], 422);
            }
        }

        $payload['profile_completion_percentage'] = $this->calculateProfileCompletion($payload, $relations);
        $payload['slug'] = $this->makeSlug((string) ($userPayload['name'] ?: 'doctor'), (int) $user->id, $doctor ? (int) $doctor->id : null);

        $duplicate = $this->validateUniqueFields($payload, $doctor ? (int) $doctor->id : null);
        if ($duplicate) {
            return response()->json(['status' => 'error', 'message' => $duplicate], 422);
        }

        $newCover = null;
        if ($request->hasFile('cover_photo')) {
            $newCover = $this->saveAsset($request->file('cover_photo'), 'doctor_cover');
            if ($newCover === false) {
                return response()->json(['status' => 'error', 'message' => 'Invalid cover photo upload'], 422);
            }
            $payload['cover_photo'] = $newCover;
        }

        $newUserImage = null;
        if ($request->hasFile('user_image')) {
            $newUserImage = $this->saveAsset($request->file('user_image'), 'doctor_user');
            if ($newUserImage === false) {
                return response()->json(['status' => 'error', 'message' => 'Invalid user image upload'], 422);
            }
            $userPayload['image'] = $newUserImage;
        }

        $actor = $this->actor($request);
        $oldDoctorSnapshot = $doctor ? array_merge($this->pagePayload($user, $doctor)['doctor'] ?? [], $this->doctorRelations((int) $doctor->id)) : null;
        $oldUserImage = (string) ($user->image ?? '');

        try {
            DB::transaction(function () use (&$doctor, $user, $userPayload, $payload, $relations, $request, $actor) {
                DB::table('users')->where('id', $user->id)->update(array_merge($userPayload, [
                    'updated_at' => now(),
                ]));

                if ($doctor) {
                    $payload['updated_by'] = $actor['id'] ?: null;
                    $payload['updated_at_ip'] = $request->ip();
                    $payload['updated_at'] = now();
                    DB::table('doctors')->where('id', $doctor->id)->update($payload);
                } else {
                    $payload['uuid'] = (string) Str::uuid();
                    $payload['user_id'] = (int) $user->id;
                    $payload['created_by'] = $actor['id'] ?: null;
                    $payload['updated_by'] = $actor['id'] ?: null;
                    $payload['created_at_ip'] = $request->ip();
                    $payload['updated_at_ip'] = $request->ip();
                    $payload['created_at'] = now();
                    $payload['updated_at'] = now();
                    $doctorId = DB::table('doctors')->insertGetId($payload);
                    $doctor = DB::table('doctors')->where('id', $doctorId)->first();
                }

                $freshDoctorId = (int) $doctor->id;
                $this->syncRelations($freshDoctorId, $relations);
                $doctor = DB::table('doctors')->where('id', $freshDoctorId)->first();
            });

            if ($newCover && $oldDoctorSnapshot && !empty($oldDoctorSnapshot['cover_photo_path'])) {
                $this->deleteManagedAsset($oldDoctorSnapshot['cover_photo_path']);
            }

            if ($newUserImage && $oldUserImage && $oldUserImage !== $newUserImage) {
                $this->deleteManagedAsset($oldUserImage);
            }

            $freshUser = $this->resolveUser((string) $user->uuid);
            $fresh = DB::table('doctors')->where('id', $doctor->id)->first();
            $newSnapshot = array_merge($this->pagePayload($freshUser ?? $user, $fresh)['doctor'] ?? [], $this->doctorRelations((int) $fresh->id));

            $this->logActivity(
                $request,
                $oldDoctorSnapshot ? 'update' : 'store',
                (int) $fresh->id,
                array_keys($payload),
                $oldDoctorSnapshot,
                $newSnapshot,
                'Doctor profile ' . ($oldDoctorSnapshot ? 'updated' : 'created') . (($name = $this->actorName($actor['id'])) ? ' by ' . $name : '')
            );

            $this->notifyAdmins(
                $oldDoctorSnapshot ? 'Doctor profile updated' : 'Doctor profile created',
                ($user->name ?? 'Doctor') . ' profile was ' . ($oldDoctorSnapshot ? 'updated' : 'created') . '.',
                [
                    'action'     => $oldDoctorSnapshot ? 'updated' : 'created',
                    'module'     => self::MODULE_NAME,
                    'actor_id'   => $actor['id'],
                    'actor_role' => $actor['role'],
                    'user_uuid'  => (string) $user->uuid,
                    'doctor'     => $newSnapshot,
                ],
                self::PAGE_HREF . '/' . $user->uuid,
                self::MODULE_NAME
            );

            return response()->json([
                'status'  => 'success',
                'message' => $oldDoctorSnapshot ? 'Doctor profile updated' : 'Doctor profile created',
                'data'    => [
                    'profile'   => $this->pagePayload($freshUser ?? $user, $fresh),
                    'relations' => $this->doctorRelations((int) $fresh->id),
                    'options'   => $this->optionsPayload(),
                ],
            ]);
        } catch (\Throwable $e) {
            if ($newCover) {
                $this->deleteManagedAsset($newCover);
            }
            if ($newUserImage) {
                $this->deleteManagedAsset($newUserImage);
            }

            return response()->json([
                'status'  => 'error',
                'message' => 'Could not save doctor profile',
                'error'   => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
