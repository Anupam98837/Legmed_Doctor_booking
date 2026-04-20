@extends('pages.landing.layout')

@section('title', $department['name'] . ' Doctors | LegMed Directory')
@section('meta_description', 'Browse doctors in the ' . $department['name'] . ' department.')

@section('content')
<div class="landing-shell">
  <div class="landing-page-bar">
    <div class="landing-breadcrumbs">
      <a href="{{ route('directory.home') }}">Home</a>
      <span>&gt;</span>
      <a href="{{ route('directory.departments.index') }}">Departments</a>
      <span>&gt;</span>
      <span>{{ $department['name'] }}</span>
    </div>
    <div class="landing-page-meta-minimal">
      {{ $doctors->count() }} doctors
    </div>
  </div>
  <section class="landing-hero">
    <div class="landing-hero-grid">
      <div>
        <span class="landing-kicker"><i class="fa-solid fa-stethoscope"></i>{{ $department['name'] }}</span>
        <h1 class="landing-display">Doctors in {{ $department['name'] }} ready to be explored in detail.</h1>
        <p class="landing-lead">{{ $department['description'] ?: 'Compare profiles, locations, fees, experience, and move into a richer public doctor detail page.' }}</p>
      </div>
      <div class="landing-search-card">
        <form method="GET" action="{{ route('directory.departments.show', ['slug' => $department['slug']]) }}" class="landing-search-form">
          <input type="search" class="landing-search-input" name="q" value="{{ $search }}" placeholder="Search doctor, specialization, hospital">
          <button type="submit" class="landing-btn landing-btn-primary landing-search-submit">Find Doctors</button>
        </form>
        <div class="landing-copy mt-3">{{ $doctors->count() }} doctors found{{ $search ? ' for your search' : '' }}.</div>
      </div>
    </div>
  </section>

  <section class="landing-section">
    @if($doctors->isEmpty())
      <div class="landing-empty">
        <h3 class="mb-2">No doctors matched this department search.</h3>
        <p class="mb-0">Try another keyword or browse a different department.</p>
      </div>
    @else
      <div class="landing-doctor-results">
        @foreach($doctors as $doctor)
          @php($softClass = 'landing-soft-' . (($loop->index % 5) + 1))
          <article class="landing-doctor-result">
            <div class="landing-doctor-result-grid">
              <div>
                @if($doctor['image'])
                  <img src="{{ $doctor['image'] }}" alt="{{ $doctor['name'] }}" class="landing-avatar">
                @else
                  <div class="landing-avatar-fallback {{ $softClass }}"><i class="fa-solid fa-user-doctor"></i></div>
                @endif
              </div>

              <div>
                <div class="landing-doctor-head">
                  <div>
                    @if($doctor['featured'])
                      <div class="landing-badge mb-2"><i class="fa-solid fa-star"></i> Featured Doctor</div>
                    @endif
                    <h3 class="landing-doctor-name">{{ $doctor['name'] }}</h3>
                    <div class="landing-doctor-subtitle">
                      {{ $doctor['designation'] ?: 'Doctor' }}
                      @if($doctor['specialization']) • {{ $doctor['specialization'] }} @endif
                    </div>
                  </div>

                  <a href="{{ $doctor['href'] }}" class="landing-btn landing-btn-light">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>View Profile</span>
                  </a>
                </div>

                <div class="landing-metric-row">
                  @if($doctor['years_of_experience'])
                    <span class="landing-metric"><i class="fa-solid fa-briefcase-medical"></i>{{ $doctor['years_of_experience'] }} yrs exp</span>
                  @endif
                  <span class="landing-metric"><i class="fa-solid fa-star"></i>{{ $doctor['rating'] }} ({{ $doctor['review_count'] }})</span>
                  @if($doctor['consultation_fee'])
                    <span class="landing-metric"><i class="fa-solid fa-indian-rupee-sign"></i>{{ $doctor['consultation_fee'] }} consultation</span>
                  @endif
                  @if($doctor['online_consultation_available'])
                    <span class="landing-metric"><i class="fa-solid fa-video"></i>Online</span>
                  @endif
                  @if($doctor['in_person_consultation_available'])
                    <span class="landing-metric"><i class="fa-solid fa-user-check"></i>In Person</span>
                  @endif
                </div>

                <p class="landing-copy mt-3 mb-0">{{ $doctor['short_bio'] ?: 'Explore the profile for qualifications, clinic details, consultation options, and care background.' }}</p>

                <div class="landing-doctor-meta mt-3">
                  @if($doctor['hospital'])
                    <div class="landing-doctor-meta-item">
                      <i class="fa-solid fa-hospital"></i>
                      <span>{{ $doctor['hospital'] }}</span>
                    </div>
                  @endif
                  @if($doctor['department'])
                    <div class="landing-doctor-meta-item">
                      <i class="fa-solid fa-stethoscope"></i>
                      <span>{{ $doctor['department'] }}</span>
                    </div>
                  @endif
                </div>
              </div>

              <div class="landing-doctor-support">
                <div class="support-row">
                  <div>
                    <div class="landing-badge"><i class="fa-solid fa-calendar-check"></i> Patient Actions</div>
                    <div class="support-copy mt-2">Open the doctor page for richer details, or contact directly when a number is available.</div>
                  </div>
                </div>

                <div class="support-row">
                  <div>
                    <div class="support-copy">Consultation Starts</div>
                    <strong>{{ $doctor['consultation_fee'] ? ('₹' . $doctor['consultation_fee']) : 'Contact for fees' }}</strong>
                  </div>
                  <div class="text-end">
                    <div class="support-copy">Booking Status</div>
                    <strong>{{ $doctor['appointment_booking_available'] ? 'Open' : 'Profile Only' }}</strong>
                  </div>
                </div>

                <div class="landing-action-row">
                  <a href="{{ $doctor['appointment_href'] }}" class="landing-btn landing-btn-primary">
                    <i class="fa-solid fa-calendar-plus"></i>
                    <span>Book Appointment</span>
                  </a>

                  @if($doctor['call_href'])
                    <a href="{{ $doctor['call_href'] }}" class="landing-btn landing-btn-dark">
                      <i class="fa-solid fa-phone"></i>
                      <span>Call Now</span>
                    </a>
                  @endif
                </div>

                <div class="landing-doctor-meta">
                  @if($doctor['phone'])
                    <div class="landing-doctor-meta-item">
                      <i class="fa-solid fa-phone-volume"></i>
                      <span>{{ $doctor['phone'] }}</span>
                    </div>
                  @endif
                  @if($doctor['whatsapp'])
                    <div class="landing-doctor-meta-item">
                      <i class="fa-brands fa-whatsapp"></i>
                      <span>{{ $doctor['whatsapp'] }}</span>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </section>

  @if($relatedDepartments->isNotEmpty())
    <section class="landing-section">
      <div class="landing-section-head">
        <div>
          <span class="landing-badge">More Departments</span>
          <h2 class="mt-3 mb-0">Keep browsing adjacent care areas</h2>
        </div>
      </div>
      <div class="landing-department-showcase">
        @foreach($relatedDepartments as $item)
          @php($softClass = 'landing-soft-' . (($loop->index % 5) + 1))
          <a href="{{ $item['href'] }}" class="landing-department-link">
            <article class="landing-department-tile {{ $softClass }}">
              <div class="landing-department-visual {{ $softClass }}">
                <div class="landing-department-icon {{ $softClass }}">
                  <i class="fa-solid fa-stethoscope"></i>
                </div>
              </div>
              <h3 class="landing-department-title">{{ $item['name'] }}</h3>
              <div class="landing-department-cta">Consult Now</div>
            </article>
          </a>
        @endforeach
      </div>
    </section>
  @endif
</div>
@endsection
