<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DoctorKeyDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $specializations = [
            ['name' => 'Cardiology', 'short_form' => 'CARD', 'description' => 'Heart and cardiovascular care.'],
            ['name' => 'Neurology', 'short_form' => 'NEURO', 'description' => 'Brain, spine, and nervous system care.'],
            ['name' => 'Orthopedics', 'short_form' => 'ORTHO', 'description' => 'Bones, joints, and musculoskeletal care.'],
            ['name' => 'Dermatology', 'short_form' => 'DERM', 'description' => 'Skin, nail, and hair treatment.'],
            ['name' => 'Pediatrics', 'short_form' => 'PEDS', 'description' => 'Child and adolescent healthcare.'],
            ['name' => 'General Surgery', 'short_form' => 'GS', 'description' => 'Surgical evaluation and procedures.'],
        ];

        $designations = [
            ['name' => 'Consultant', 'short_form' => 'CONS'],
            ['name' => 'Senior Consultant', 'short_form' => 'SR CONS'],
            ['name' => 'Junior Consultant', 'short_form' => 'JR CONS'],
            ['name' => 'Head of Department', 'short_form' => 'HOD'],
            ['name' => 'Visiting Consultant', 'short_form' => 'VISIT'],
        ];

        $registrationCouncils = [
            ['name' => 'National Medical Commission', 'short_form' => 'NMC', 'country' => 'India', 'state' => null, 'website' => 'https://www.nmc.org.in'],
            ['name' => 'West Bengal Medical Council', 'short_form' => 'WBMC', 'country' => 'India', 'state' => 'West Bengal', 'website' => 'https://wbmc.in'],
            ['name' => 'Delhi Medical Council', 'short_form' => 'DMC', 'country' => 'India', 'state' => 'Delhi', 'website' => 'https://delhimedicalcouncil.org'],
            ['name' => 'Maharashtra Medical Council', 'short_form' => 'MMC', 'country' => 'India', 'state' => 'Maharashtra', 'website' => 'https://www.maharashtramedicalcouncil.in'],
        ];

        $languages = [
            ['name' => 'English', 'code' => 'en', 'description' => 'English language support.'],
            ['name' => 'Hindi', 'code' => 'hi', 'description' => 'Hindi language support.'],
            ['name' => 'Bengali', 'code' => 'bn', 'description' => 'Bengali language support.'],
            ['name' => 'Tamil', 'code' => 'ta', 'description' => 'Tamil language support.'],
            ['name' => 'Telugu', 'code' => 'te', 'description' => 'Telugu language support.'],
        ];

        $services = [
            ['name' => 'OPD Consultation', 'short_form' => 'OPD', 'default_price' => 800, 'default_duration_minutes' => 20, 'description' => 'Standard outpatient consultation.'],
            ['name' => 'Video Consultation', 'short_form' => 'VIDEO', 'default_price' => 700, 'default_duration_minutes' => 20, 'description' => 'Remote video consultation.'],
            ['name' => 'ECG Reporting', 'short_form' => 'ECG', 'default_price' => 500, 'default_duration_minutes' => 15, 'description' => 'ECG review and reporting.'],
            ['name' => 'Follow-up Consultation', 'short_form' => 'FOLLOW', 'default_price' => 500, 'default_duration_minutes' => 15, 'description' => 'Review of previous treatment plan.'],
            ['name' => 'Procedure Assessment', 'short_form' => 'PROC', 'default_price' => 1200, 'default_duration_minutes' => 30, 'description' => 'Pre-procedure specialist assessment.'],
        ];

        $qualifications = [
            ['name' => 'MBBS', 'short_form' => 'MBBS', 'qualification_type' => 'degree'],
            ['name' => 'MD General Medicine', 'short_form' => 'MD', 'qualification_type' => 'degree'],
            ['name' => 'MS Orthopedics', 'short_form' => 'MS', 'qualification_type' => 'degree'],
            ['name' => 'DNB Cardiology', 'short_form' => 'DNB', 'qualification_type' => 'degree'],
            ['name' => 'DM Neurology', 'short_form' => 'DM', 'qualification_type' => 'degree'],
            ['name' => 'Fellowship in Critical Care', 'short_form' => 'FICU', 'qualification_type' => 'fellowship'],
        ];

        $departments = [
            ['name' => 'Cardiology', 'short_form' => 'CARD', 'description' => 'Heart and vascular department.'],
            ['name' => 'Neurology', 'short_form' => 'NEURO', 'description' => 'Brain and nervous system department.'],
            ['name' => 'Orthopedics', 'short_form' => 'ORTHO', 'description' => 'Bone and joint care department.'],
            ['name' => 'Dermatology', 'short_form' => 'DERM', 'description' => 'Skin and cosmetic care department.'],
            ['name' => 'Pediatrics', 'short_form' => 'PEDS', 'description' => 'Child health department.'],
        ];

        $hospitals = [
            [
                'name' => 'City Heart & Multispeciality Hospital',
                'short_name' => 'City Heart',
                'hospital_code' => 'HSP-CITY-001',
                'registration_number' => 'REG-CH-2024-01',
                'license_number' => 'LIC-CH-4582',
                'established_year' => 2008,
                'hospital_type' => 'Multi-speciality Hospital',
                'ownership_type' => 'Private',
                'email' => 'info@cityheart.example',
                'phone_number' => '+91 9000000001',
                'alternative_phone_number' => '+91 9000000002',
                'whatsapp_number' => '+91 9000000003',
                'emergency_contact_number' => '+91 9000000099',
                'website' => 'https://cityheart.example',
                'description' => 'A full-scale urban hospital focused on cardiac, neuro, and emergency care.',
                'short_description' => 'Cardiac and multi-speciality tertiary care hospital.',
                'address_line_1' => '12 Park Street',
                'address_line_2' => 'Central Business District',
                'landmark' => 'Near Metro Gate 3',
                'area' => 'Park Street',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'country' => 'India',
                'pincode' => '700016',
                'map_url' => 'https://maps.example/city-heart',
                'departments' => ['Cardiology', 'Neurology', 'Orthopedics'],
                'services' => ['OPD Consultation', 'ECG Reporting', 'Procedure Assessment'],
                'facilities' => ['ICU', 'Pharmacy', 'Laboratory', 'Ambulance'],
                'specialities' => ['Cardiology', 'Neurology'],
                'timings' => ['Mon-Sat 08:00-20:00', 'Emergency 24x7'],
                'social_links' => ['facebook' => 'https://facebook.example/cityheart'],
                'accreditations' => ['NABH', 'ISO 9001'],
                'insurances_accepted' => ['Star Health', 'MediAssist', 'ICICI Lombard'],
                'bed_count' => 180,
                'ambulance_available' => 1,
                'emergency_available' => 1,
                'pharmacy_available' => 1,
                'lab_available' => 1,
                'icu_available' => 1,
                'cashless_available' => 1,
                'online_consultation_available' => 1,
                'appointment_booking_available' => 1,
                'seo_title' => 'City Heart Hospital',
                'seo_description' => 'Multi-speciality hospital for cardiology and neurology.',
            ],
            [
                'name' => 'Sunrise Care Institute',
                'short_name' => 'Sunrise Care',
                'hospital_code' => 'HSP-SUN-002',
                'registration_number' => 'REG-SC-2024-08',
                'license_number' => 'LIC-SC-9901',
                'established_year' => 2014,
                'hospital_type' => 'Speciality Hospital',
                'ownership_type' => 'Trust',
                'email' => 'hello@sunrisecare.example',
                'phone_number' => '+91 9100000001',
                'alternative_phone_number' => '+91 9100000002',
                'whatsapp_number' => '+91 9100000003',
                'emergency_contact_number' => '+91 9100000099',
                'website' => 'https://sunrisecare.example',
                'description' => 'A modern speciality institute offering planned care, daycare procedures, and digital consultations.',
                'short_description' => 'Speciality care institute with strong outpatient operations.',
                'address_line_1' => '88 EM Bypass',
                'address_line_2' => 'Block B',
                'landmark' => 'Opposite City Mall',
                'area' => 'Kasba',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'country' => 'India',
                'pincode' => '700107',
                'map_url' => 'https://maps.example/sunrise-care',
                'departments' => ['Dermatology', 'Pediatrics', 'Orthopedics'],
                'services' => ['OPD Consultation', 'Video Consultation', 'Follow-up Consultation'],
                'facilities' => ['Pharmacy', 'Laboratory'],
                'specialities' => ['Dermatology', 'Pediatrics'],
                'timings' => ['Mon-Sun 09:00-21:00'],
                'social_links' => ['instagram' => 'https://instagram.example/sunrisecare'],
                'accreditations' => ['NABH'],
                'insurances_accepted' => ['HDFC Ergo', 'Care Health'],
                'bed_count' => 90,
                'ambulance_available' => 0,
                'emergency_available' => 0,
                'pharmacy_available' => 1,
                'lab_available' => 1,
                'icu_available' => 0,
                'cashless_available' => 1,
                'online_consultation_available' => 1,
                'appointment_booking_available' => 1,
                'seo_title' => 'Sunrise Care Institute',
                'seo_description' => 'Speciality outpatient institute for dermatology, pediatrics, and ortho.',
            ],
        ];

        $clinics = [
            [
                'name' => 'Heart First Clinic',
                'short_name' => 'Heart First',
                'clinic_code' => 'CLN-HRT-01',
                'clinic_type' => 'chamber',
                'email' => 'contact@heartfirst.example',
                'phone_number' => '+91 9200000001',
                'alternative_phone_number' => '+91 9200000002',
                'whatsapp_number' => '+91 9200000003',
                'website' => 'https://heartfirst.example',
                'description' => 'Focused chamber for heart consultation and follow-up care.',
                'short_description' => 'Dedicated cardiology chamber.',
                'address_line_1' => '22 Lake Road',
                'address_line_2' => '2nd Floor',
                'landmark' => 'Near Lake Market',
                'area' => 'Lake Road',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'country' => 'India',
                'pincode' => '700029',
                'map_url' => 'https://maps.example/heart-first',
                'timings' => ['Mon-Fri 17:00-21:00', 'Sat 10:00-14:00'],
                'facilities' => ['ECG', 'Waiting Lounge'],
                'social_links' => ['facebook' => 'https://facebook.example/heartfirst'],
                'online_consultation_available' => 1,
                'appointment_booking_available' => 1,
            ],
            [
                'name' => 'Neuro Plus Clinic',
                'short_name' => 'Neuro Plus',
                'clinic_code' => 'CLN-NEU-02',
                'clinic_type' => 'opd',
                'email' => 'desk@neuroplus.example',
                'phone_number' => '+91 9300000001',
                'alternative_phone_number' => '+91 9300000002',
                'whatsapp_number' => '+91 9300000003',
                'website' => 'https://neuroplus.example',
                'description' => 'Specialist neuro OPD with in-person and digital follow-up support.',
                'short_description' => 'Neurology OPD and consultation clinic.',
                'address_line_1' => '5 Southern Avenue',
                'address_line_2' => 'Ground Floor',
                'landmark' => 'Near Rabindra Sarobar',
                'area' => 'Southern Avenue',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'country' => 'India',
                'pincode' => '700026',
                'map_url' => 'https://maps.example/neuro-plus',
                'timings' => ['Tue-Sun 11:00-19:00'],
                'facilities' => ['EEG', 'Online Consultation'],
                'social_links' => ['linkedin' => 'https://linkedin.example/neuroplus'],
                'online_consultation_available' => 1,
                'appointment_booking_available' => 1,
            ],
            [
                'name' => 'Kids & Skin Polyclinic',
                'short_name' => 'Kids & Skin',
                'clinic_code' => 'CLN-KSP-03',
                'clinic_type' => 'branch',
                'email' => 'info@kidsskin.example',
                'phone_number' => '+91 9400000001',
                'alternative_phone_number' => '+91 9400000002',
                'whatsapp_number' => '+91 9400000003',
                'website' => 'https://kidsskin.example',
                'description' => 'Friendly family clinic supporting pediatrics and dermatology.',
                'short_description' => 'Family-focused polyclinic.',
                'address_line_1' => '14 Salt Lake Sector 1',
                'address_line_2' => 'Unit 3',
                'landmark' => 'Near Tank 4',
                'area' => 'Salt Lake',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'country' => 'India',
                'pincode' => '700064',
                'map_url' => 'https://maps.example/kids-skin',
                'timings' => ['Mon-Sun 09:00-20:00'],
                'facilities' => ['Vaccination', 'Minor Procedures'],
                'social_links' => ['instagram' => 'https://instagram.example/kidsskin'],
                'online_consultation_available' => 0,
                'appointment_booking_available' => 1,
            ],
        ];

        $this->seedSpecializations($specializations, $now);
        $this->seedDesignations($designations, $now);
        $this->seedRegistrationCouncils($registrationCouncils, $now);
        $this->seedLanguages($languages, $now);
        $this->seedServices($services, $now);
        $this->seedQualifications($qualifications, $now);
        $this->seedDepartments($departments, $now);
        $this->seedHospitals($hospitals, $now);
        $this->seedClinics($clinics, $now);
    }

    private function seedSpecializations(array $rows, $now): void
    {
        if (!Schema::hasTable('specializations')) {
            return;
        }

        foreach ($rows as $index => $row) {
            DB::table('specializations')->updateOrInsert(
                ['name' => $row['name']],
                [
                    'uuid' => DB::table('specializations')->where('name', $row['name'])->value('uuid') ?: (string) Str::uuid(),
                    'short_form' => $row['short_form'],
                    'slug' => Str::slug($row['name']),
                    'icon' => null,
                    'image' => null,
                    'description' => $row['description'] ?? null,
                    'status' => 'active',
                    'sort_order' => $index,
                    'created_at' => DB::table('specializations')->where('name', $row['name'])->value('created_at') ?: $now,
                    'updated_at' => $now,
                    'metadata' => json_encode(['seed' => 'DoctorKeyDataSeeder']),
                ]
            );
        }
    }

    private function seedDesignations(array $rows, $now): void
    {
        if (!Schema::hasTable('designations')) {
            return;
        }

        foreach ($rows as $index => $row) {
            DB::table('designations')->updateOrInsert(
                ['name' => $row['name']],
                [
                    'uuid' => DB::table('designations')->where('name', $row['name'])->value('uuid') ?: (string) Str::uuid(),
                    'short_form' => $row['short_form'] ?? null,
                    'slug' => Str::slug($row['name']),
                    'description' => $row['description'] ?? null,
                    'status' => 'active',
                    'sort_order' => $index,
                    'created_at' => DB::table('designations')->where('name', $row['name'])->value('created_at') ?: $now,
                    'updated_at' => $now,
                    'metadata' => json_encode(['seed' => 'DoctorKeyDataSeeder']),
                ]
            );
        }
    }

    private function seedRegistrationCouncils(array $rows, $now): void
    {
        if (!Schema::hasTable('registration_councils')) {
            return;
        }

        foreach ($rows as $index => $row) {
            DB::table('registration_councils')->updateOrInsert(
                ['name' => $row['name']],
                [
                    'uuid' => DB::table('registration_councils')->where('name', $row['name'])->value('uuid') ?: (string) Str::uuid(),
                    'short_form' => $row['short_form'] ?? null,
                    'slug' => Str::slug($row['name']),
                    'country' => $row['country'] ?? 'India',
                    'state' => $row['state'] ?? null,
                    'description' => $row['description'] ?? null,
                    'website' => $row['website'] ?? null,
                    'status' => 'active',
                    'sort_order' => $index,
                    'created_at' => DB::table('registration_councils')->where('name', $row['name'])->value('created_at') ?: $now,
                    'updated_at' => $now,
                    'metadata' => json_encode(['seed' => 'DoctorKeyDataSeeder']),
                ]
            );
        }
    }

    private function seedLanguages(array $rows, $now): void
    {
        if (!Schema::hasTable('languages')) {
            return;
        }

        foreach ($rows as $index => $row) {
            $lookup = !empty($row['code']) ? ['code' => $row['code']] : ['name' => $row['name']];

            DB::table('languages')->updateOrInsert(
                $lookup,
                [
                    'uuid' => DB::table('languages')->where($lookup)->value('uuid') ?: (string) Str::uuid(),
                    'name' => $row['name'],
                    'code' => $row['code'] ?? null,
                    'slug' => Str::slug($row['name']),
                    'description' => $row['description'] ?? null,
                    'status' => 'active',
                    'sort_order' => $index,
                    'created_at' => DB::table('languages')->where($lookup)->value('created_at') ?: $now,
                    'updated_at' => $now,
                    'metadata' => json_encode(['seed' => 'DoctorKeyDataSeeder']),
                ]
            );
        }
    }

    private function seedServices(array $rows, $now): void
    {
        if (!Schema::hasTable('services')) {
            return;
        }

        foreach ($rows as $index => $row) {
            DB::table('services')->updateOrInsert(
                ['name' => $row['name']],
                [
                    'uuid' => DB::table('services')->where('name', $row['name'])->value('uuid') ?: (string) Str::uuid(),
                    'short_form' => $row['short_form'] ?? null,
                    'slug' => Str::slug($row['name']),
                    'icon' => null,
                    'image' => null,
                    'description' => $row['description'] ?? null,
                    'default_price' => $row['default_price'] ?? null,
                    'default_duration_minutes' => $row['default_duration_minutes'] ?? null,
                    'status' => 'active',
                    'sort_order' => $index,
                    'created_at' => DB::table('services')->where('name', $row['name'])->value('created_at') ?: $now,
                    'updated_at' => $now,
                    'metadata' => json_encode(['seed' => 'DoctorKeyDataSeeder']),
                ]
            );
        }
    }

    private function seedQualifications(array $rows, $now): void
    {
        if (!Schema::hasTable('qualifications')) {
            return;
        }

        foreach ($rows as $index => $row) {
            DB::table('qualifications')->updateOrInsert(
                ['name' => $row['name']],
                [
                    'uuid' => DB::table('qualifications')->where('name', $row['name'])->value('uuid') ?: (string) Str::uuid(),
                    'short_form' => $row['short_form'] ?? null,
                    'slug' => Str::slug($row['name']),
                    'qualification_type' => $row['qualification_type'] ?? null,
                    'description' => $row['description'] ?? null,
                    'status' => 'active',
                    'sort_order' => $index,
                    'created_at' => DB::table('qualifications')->where('name', $row['name'])->value('created_at') ?: $now,
                    'updated_at' => $now,
                    'metadata' => json_encode(['seed' => 'DoctorKeyDataSeeder']),
                ]
            );
        }
    }

    private function seedDepartments(array $rows, $now): void
    {
        if (!Schema::hasTable('departments')) {
            return;
        }

        foreach ($rows as $index => $row) {
            DB::table('departments')->updateOrInsert(
                ['slug' => Str::slug($row['name'])],
                [
                    'uuid' => DB::table('departments')->where('slug', Str::slug($row['name']))->value('uuid') ?: (string) Str::uuid(),
                    'name' => $row['name'],
                    'short_form' => $row['short_form'] ?? null,
                    'image' => null,
                    'description' => $row['description'] ?? null,
                    'status' => 'active',
                    'sort_order' => $index,
                    'created_at' => DB::table('departments')->where('slug', Str::slug($row['name']))->value('created_at') ?: $now,
                    'updated_at' => $now,
                    'metadata' => json_encode(['seed' => 'DoctorKeyDataSeeder']),
                ]
            );
        }
    }

    private function seedHospitals(array $rows, $now): void
    {
        if (!Schema::hasTable('hospitals')) {
            return;
        }

        foreach ($rows as $index => $row) {
            $lookup = ['hospital_code' => $row['hospital_code']];

            DB::table('hospitals')->updateOrInsert(
                $lookup,
                [
                    'uuid' => DB::table('hospitals')->where($lookup)->value('uuid') ?: (string) Str::uuid(),
                    'name' => $row['name'],
                    'short_name' => $row['short_name'] ?? null,
                    'slug' => Str::slug($row['name']),
                    'registration_number' => $row['registration_number'] ?? null,
                    'license_number' => $row['license_number'] ?? null,
                    'established_year' => $row['established_year'] ?? null,
                    'hospital_type' => $row['hospital_type'] ?? null,
                    'ownership_type' => $row['ownership_type'] ?? null,
                    'email' => $row['email'] ?? null,
                    'phone_number' => $row['phone_number'] ?? null,
                    'alternative_phone_number' => $row['alternative_phone_number'] ?? null,
                    'whatsapp_number' => $row['whatsapp_number'] ?? null,
                    'emergency_contact_number' => $row['emergency_contact_number'] ?? null,
                    'website' => $row['website'] ?? null,
                    'logo' => null,
                    'image' => null,
                    'gallery' => json_encode([]),
                    'description' => $row['description'] ?? null,
                    'short_description' => $row['short_description'] ?? null,
                    'address_line_1' => $row['address_line_1'] ?? null,
                    'address_line_2' => $row['address_line_2'] ?? null,
                    'landmark' => $row['landmark'] ?? null,
                    'area' => $row['area'] ?? null,
                    'city' => $row['city'] ?? null,
                    'state' => $row['state'] ?? null,
                    'country' => $row['country'] ?? 'India',
                    'pincode' => $row['pincode'] ?? null,
                    'latitude' => null,
                    'longitude' => null,
                    'map_url' => $row['map_url'] ?? null,
                    'departments' => json_encode($row['departments'] ?? []),
                    'services' => json_encode($row['services'] ?? []),
                    'facilities' => json_encode($row['facilities'] ?? []),
                    'specialities' => json_encode($row['specialities'] ?? []),
                    'timings' => json_encode($row['timings'] ?? []),
                    'social_links' => json_encode($row['social_links'] ?? []),
                    'accreditations' => json_encode($row['accreditations'] ?? []),
                    'insurances_accepted' => json_encode($row['insurances_accepted'] ?? []),
                    'bed_count' => $row['bed_count'] ?? 0,
                    'ambulance_available' => $row['ambulance_available'] ?? 0,
                    'emergency_available' => $row['emergency_available'] ?? 0,
                    'pharmacy_available' => $row['pharmacy_available'] ?? 0,
                    'lab_available' => $row['lab_available'] ?? 0,
                    'icu_available' => $row['icu_available'] ?? 0,
                    'cashless_available' => $row['cashless_available'] ?? 0,
                    'online_consultation_available' => $row['online_consultation_available'] ?? 0,
                    'appointment_booking_available' => $row['appointment_booking_available'] ?? 1,
                    'average_rating' => 0,
                    'review_count' => 0,
                    'seo_title' => $row['seo_title'] ?? null,
                    'seo_description' => $row['seo_description'] ?? null,
                    'status' => 'active',
                    'sort_order' => $index,
                    'created_at' => DB::table('hospitals')->where($lookup)->value('created_at') ?: $now,
                    'updated_at' => $now,
                    'metadata' => json_encode(['seed' => 'DoctorKeyDataSeeder']),
                ]
            );
        }
    }

    private function seedClinics(array $rows, $now): void
    {
        if (!Schema::hasTable('clinics')) {
            return;
        }

        foreach ($rows as $index => $row) {
            $lookup = ['clinic_code' => $row['clinic_code']];

            DB::table('clinics')->updateOrInsert(
                $lookup,
                [
                    'uuid' => DB::table('clinics')->where($lookup)->value('uuid') ?: (string) Str::uuid(),
                    'name' => $row['name'],
                    'short_name' => $row['short_name'] ?? null,
                    'slug' => Str::slug($row['name']),
                    'clinic_type' => $row['clinic_type'] ?? null,
                    'email' => $row['email'] ?? null,
                    'phone_number' => $row['phone_number'] ?? null,
                    'alternative_phone_number' => $row['alternative_phone_number'] ?? null,
                    'whatsapp_number' => $row['whatsapp_number'] ?? null,
                    'website' => $row['website'] ?? null,
                    'logo' => null,
                    'image' => null,
                    'gallery' => json_encode([]),
                    'description' => $row['description'] ?? null,
                    'short_description' => $row['short_description'] ?? null,
                    'address_line_1' => $row['address_line_1'] ?? null,
                    'address_line_2' => $row['address_line_2'] ?? null,
                    'landmark' => $row['landmark'] ?? null,
                    'area' => $row['area'] ?? null,
                    'city' => $row['city'] ?? null,
                    'state' => $row['state'] ?? null,
                    'country' => $row['country'] ?? 'India',
                    'pincode' => $row['pincode'] ?? null,
                    'latitude' => null,
                    'longitude' => null,
                    'map_url' => $row['map_url'] ?? null,
                    'timings' => json_encode($row['timings'] ?? []),
                    'facilities' => json_encode($row['facilities'] ?? []),
                    'social_links' => json_encode($row['social_links'] ?? []),
                    'online_consultation_available' => $row['online_consultation_available'] ?? 0,
                    'appointment_booking_available' => $row['appointment_booking_available'] ?? 1,
                    'status' => 'active',
                    'sort_order' => $index,
                    'created_at' => DB::table('clinics')->where($lookup)->value('created_at') ?: $now,
                    'updated_at' => $now,
                    'metadata' => json_encode(['seed' => 'DoctorKeyDataSeeder']),
                ]
            );
        }
    }
}
