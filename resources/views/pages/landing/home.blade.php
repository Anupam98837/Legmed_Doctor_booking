@extends('pages.landing.layout')

@section('title', 'LegMed Directory | Find Doctors by Department')
@section('meta_description', 'Browse departments, featured doctors, and hospitals on LegMed.')
@section('body_class', 'landing-home')

@section('content')
@php
  $defaultHeroImage = 'https://images.pexels.com/photos/5452242/pexels-photo-5452242.jpeg?cs=srgb&dl=pexels-tima-miroshnichenko-5452242.jpg&fm=jpg';
  $heroHospital = $hospitals->first();
  $heroDepartment = $departments->first();
  $heroDoctor = $featuredDoctors->first();
  $heroImage = $defaultHeroImage;
  $aboutImage = $heroDepartment['image'] ?? $heroHospital['image'] ?? asset('/assets/media/images/web/logo.png');
@endphp

<section class="landing-home-hero-bleed" style="--hero-image:url('{{ $heroImage }}')">
  <div class="landing-shell">
    <div class="landing-home-hero">
      <div class="landing-home-hero-grid">
        <div>
          <span class="landing-kicker"><i class="fa-solid fa-heart-pulse"></i> Trusted care discovery</span>
          <h1 class="landing-display">Choose the right department, compare doctors, and move into care with more confidence.</h1>
          <p class="landing-lead">LegMed turns your live departments, hospitals, and doctor profiles into a modern public browsing experience that feels clear, credible, and easy to navigate for patients.</p>

          <div class="landing-hero-actions">
            <a href="{{ route('directory.departments.index') }}" class="landing-btn landing-btn-primary">
              <i class="fa-solid fa-user-doctor"></i>
              <span>Find A Doctor</span>
            </a>
            <a href="{{ route('directory.departments.index') }}" class="landing-btn landing-btn-light">
              <i class="fa-solid fa-stethoscope"></i>
              <span>Explore Departments</span>
            </a>
          </div>

          <div class="landing-stat-row mt-4">
            <div class="landing-home-stat">
              <strong>{{ number_format($stats['doctor_count']) }}</strong>
              <span>doctor profiles ready for public browse</span>
            </div>
            <div class="landing-home-stat">
              <strong>{{ number_format($stats['department_count']) }}</strong>
              <span>departments to begin the search journey</span>
            </div>
            <div class="landing-home-stat">
              <strong>{{ number_format($stats['hospital_count']) }}</strong>
              <span>hospital networks behind the care flow</span>
            </div>
          </div>
        </div>

        <div class="landing-hero-panel">
          <div class="landing-hero-float">
            <span class="eyebrow"><i class="fa-solid fa-star-of-life"></i> Better care journey</span>
            <h3>Start with the department, then trust the doctor details.</h3>
            <p>Use the browse flow to move from broad care needs into specific doctor profiles with stronger medical context.</p>
          </div>

          <div class="landing-mini-card">
            <h3>Start the patient journey faster</h3>
            <p>Browse by department first, then open doctors with clear context around expertise, hospital, consultation fee, and profile detail.</p>

            <div class="landing-inline-list">
              <div class="landing-inline-item"><i class="fa-solid fa-circle-check text-success"></i><span>Department-led navigation</span></div>
              <div class="landing-inline-item"><i class="fa-solid fa-circle-check text-success"></i><span>Doctor cards powered by live data</span></div>
              <div class="landing-inline-item"><i class="fa-solid fa-circle-check text-success"></i><span>Detailed public doctor pages</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="landing-shell">
  <section class="landing-feature-strip">
    <article class="landing-feature-card">
      <div class="landing-feature-icon"><i class="fa-solid fa-user-doctor"></i></div>
      <h3 class="h5 mb-2">Verified Doctor Discovery</h3>
      <p class="landing-copy mb-0">Browse clean doctor cards with real department, hospital, experience, and rating context.</p>
    </article>

    <article class="landing-feature-card">
      <div class="landing-feature-icon"><i class="fa-solid fa-building-shield"></i></div>
      <h3 class="h5 mb-2">Hospital-backed Profiles</h3>
      <p class="landing-copy mb-0">Every care journey is grounded in the institutions and departments that support the doctor.</p>
    </article>

    <article class="landing-feature-card">
      <div class="landing-feature-icon"><i class="fa-solid fa-notes-medical"></i></div>
      <h3 class="h5 mb-2">Rich Public Details</h3>
      <p class="landing-copy mb-0">Open deeper public pages for qualifications, services, clinics, languages, and more.</p>
    </article>

    <article class="landing-feature-card">
      <div class="landing-feature-icon"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
      <h3 class="h5 mb-2">Simple Browse Flow</h3>
      <p class="landing-copy mb-0">Patients can start broad with specialties, then narrow naturally into doctor-level decisions.</p>
    </article>
  </section>

  <section class="landing-section">
    <div class="landing-about-grid">
      <div>
        <img src="{{ $aboutImage }}" alt="LegMed department preview" class="landing-about-photo">
      </div>

      <div class="landing-slab">
        <span class="landing-badge"><i class="fa-solid fa-shield-heart"></i> Why LegMed public browse works</span>
        <h2 class="mt-3 mb-2">Designed like a real medical destination page, backed by your actual app records.</h2>
        <p class="landing-copy mb-0">Instead of a plain data listing, this public home now behaves more like a healthcare marketing front door. It introduces the care network, gives patients clear ways to start, and turns your data into something more trustworthy and readable.</p>

        <div class="landing-check-list">
          <div class="landing-check-item">
            <i class="fa-solid fa-check-circle"></i>
            <div>Departments act as the main entry point for discovery, so patients do not need to know a doctor name to begin.</div>
          </div>
          <div class="landing-check-item">
            <i class="fa-solid fa-check-circle"></i>
            <div>Doctor cards showcase the most decision-making information first, including expertise, experience, and consultation signals.</div>
          </div>
          <div class="landing-check-item">
            <i class="fa-solid fa-check-circle"></i>
            <div>Hospital and profile detail sections add the trust layer that makes the browse flow feel like a complete care ecosystem.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="landing-section">
    <div class="landing-section-head">
      <div>
        <span class="landing-badge">Departments</span>
        <h2 class="mt-3 mb-0">Browse care by medical department</h2>
        <p>Use departments as the starting point, then open the doctors connected to each specialty and move deeper into their public profiles.</p>
      </div>
      <a href="{{ route('directory.departments.index') }}" class="landing-btn landing-btn-light">See All Departments</a>
    </div>

    <div class="landing-department-showcase">
      @foreach($departments->take(6) as $department)
        @php($softClass = 'landing-soft-' . (($loop->index % 5) + 1))
        <a href="{{ $department['href'] }}" class="landing-department-link">
          <article class="landing-department-tile {{ $softClass }}">
            <div class="landing-department-visual {{ $softClass }}">
            @if($department['image'])
              <img src="{{ $department['image'] }}" alt="{{ $department['name'] }}">
            @else
              <div class="landing-department-icon {{ $softClass }}">
                <i class="fa-solid fa-stethoscope"></i>
              </div>
            @endif
            </div>
            <h3 class="landing-department-title">{{ $department['name'] }}</h3>
            <div class="landing-department-meta">{{ $department['doctor_count_label'] }}</div>
            <div class="landing-department-cta">Consult Now</div>
          </article>
        </a>
      @endforeach
    </div>
  </section>

  <section class="landing-section">
    <div class="landing-section-head">
      <div>
        <span class="landing-badge">Doctors</span>
        <h2 class="mt-3 mb-0">Featured doctors from your live directory</h2>
        <p>These cards preview the stronger doctor browsing layer and link straight into the dedicated public doctor detail page.</p>
      </div>
    </div>

    <div class="landing-grid doctors">
      @foreach($featuredDoctors as $doctor)
        @php($softClass = 'landing-soft-' . (($loop->index % 5) + 1))
        <a href="{{ $doctor['href'] }}" class="landing-card-link">
          <article class="landing-card">
            <div class="d-flex gap-3 align-items-start">
              @if($doctor['image'])
                <img src="{{ $doctor['image'] }}" alt="{{ $doctor['name'] }}" class="landing-avatar">
              @else
                <div class="landing-avatar-fallback {{ $softClass }}"><i class="fa-solid fa-user-doctor"></i></div>
              @endif

              <div class="flex-grow-1">
                @if($doctor['featured'])
                  <div class="landing-badge mb-2"><i class="fa-solid fa-award"></i> Featured Doctor</div>
                @endif
                <h3 class="mb-1">{{ $doctor['name'] }}</h3>
                <div class="landing-muted">{{ $doctor['designation'] ?: 'Doctor' }}</div>
                @if($doctor['specialization'])
                  <div class="landing-copy mt-1">{{ $doctor['specialization'] }}</div>
                @endif
              </div>
            </div>

            <div class="landing-metric-row">
              @if($doctor['years_of_experience'])
                <span class="landing-metric"><i class="fa-solid fa-briefcase-medical"></i>{{ $doctor['years_of_experience'] }} yrs exp</span>
              @endif
              <span class="landing-metric"><i class="fa-solid fa-star"></i>{{ $doctor['rating'] }} ({{ $doctor['review_count'] }})</span>
              @if($doctor['consultation_fee'])
                <span class="landing-metric"><i class="fa-solid fa-indian-rupee-sign"></i>{{ $doctor['consultation_fee'] }}</span>
              @endif
            </div>

            <p class="landing-copy mt-3 mb-0">{{ $doctor['short_bio'] ?: ($doctor['department'] ?: 'Public profile ready to explore.') }}</p>
          </article>
        </a>
      @endforeach
    </div>
  </section>

  <section class="landing-section">
    <div class="landing-section-head">
      <div>
        <span class="landing-badge">Hospitals</span>
        <h2 class="mt-3 mb-0">Hospitals that anchor the care network</h2>
        <p>Hospital context helps patients understand where doctors practice and gives the directory a fuller institutional story.</p>
      </div>
    </div>

    <div class="landing-grid hospitals">
      @foreach($hospitals as $hospital)
        <article class="landing-card">
          @if($hospital['image'])
            <img src="{{ $hospital['image'] }}" alt="{{ $hospital['name'] }}" class="landing-media mb-3">
          @else
            <div class="landing-media mb-3 d-flex align-items-center justify-content-center">
              <div class="landing-avatar-fallback mx-auto">
                <i class="fa-solid fa-hospital"></i>
              </div>
            </div>
          @endif

          <div class="landing-badge mb-3"><i class="fa-solid fa-hospital"></i>{{ $hospital['doctor_count'] }} doctors</div>
          <h3 class="mb-1">{{ $hospital['name'] }}</h3>
          <div class="landing-muted mb-2">{{ $hospital['hospital_type'] ?: 'Hospital' }}{{ $hospital['location'] ? ' • ' . $hospital['location'] : '' }}</div>
          <p class="landing-copy mb-0">{{ $hospital['description'] }}</p>
        </article>
      @endforeach
    </div>
  </section>

  <section class="landing-section">
    <div class="landing-cta-band">
      <div>
        <span class="landing-badge" style="background:rgba(255,255,255,.14);color:#fff"><i class="fa-solid fa-calendar-check"></i> Discover Better Care</span>
        <h2 class="mt-3 mb-0 text-white">Start with a department and open the doctors who match the care need.</h2>
        <p>That gives patients a simpler first click, and it gives your public doctor profiles a stronger path to be discovered.</p>
      </div>

      <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('directory.departments.index') }}" class="landing-btn landing-btn-light">
          <i class="fa-solid fa-arrow-right"></i>
          <span>Browse Now</span>
        </a>
      </div>
    </div>
  </section>
</div>
@endsection
