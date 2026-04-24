@extends('pages.landing.layout')

@section('title', $doctor['name'] . ' | LegMed Directory')
@section('meta_description', $doctor['seo_description'] ?: ($doctor['short_bio'] ?: ('View the public profile for ' . $doctor['name'] . '.')))

@section('content')
@php
  $callClinicName = config('app.name', 'LegMed');
  $callClinicLocation = $doctor['hospital_location'] ?: 'Asansol';
  $callPhoneNumber = '12345678';
  $callExtension = '510';
  $callSupportEmail = config('mail.from.address', 'support@legmed.test');
  $callLogo = asset('/assets/media/images/web/logo.png');
@endphp
<div class="landing-shell">
  <div class="landing-page-bar">
    <div class="landing-breadcrumbs">
      <a href="{{ route('directory.home') }}">Home</a>
      <span>&gt;</span>
      <a href="{{ route('directory.departments.index') }}">Departments</a>
      @if($doctor['department'] && $doctor['department_slug'])
        <span>&gt;</span>
        <a href="{{ route('directory.departments.show', ['slug' => $doctor['department_slug']]) }}">{{ $doctor['department'] }}</a>
      @endif
      <span>&gt;</span>
      <span>{{ $doctor['name'] }}</span>
    </div>
    <div class="landing-page-meta-minimal">
      {{ $doctor['hospital'] ?: 'Doctor profile' }}
    </div>
  </div>

  <section class="landing-section">
    <div class="landing-profile-layout">
      <aside class="landing-profile-rail landing-sticky">
        <article class="landing-profile-card landing-profile-identity">
          <div class="landing-profile-head">
            @if($doctor['image'])
              <img src="{{ $doctor['image'] }}" alt="{{ $doctor['name'] }}" class="landing-avatar">
            @else
              <div class="landing-avatar-fallback"><i class="fa-solid fa-user-doctor"></i></div>
            @endif

            <div>
              <h1 class="landing-profile-name">{{ $doctor['name'] }}</h1>
              <div class="landing-profile-subtitle">
                {{ $doctor['designation'] ?: 'Doctor' }}
                @if($doctor['department']) • {{ $doctor['department'] }} @endif
              </div>
              <div class="landing-profile-subtitle-accent">
                {{ $doctor['years_of_experience'] ? ($doctor['years_of_experience'] . ' years of experience') : 'Experience to be updated' }}
              </div>
            </div>
          </div>

          <div class="landing-profile-divider"></div>
          <div class="landing-profile-meta">
            <div class="landing-profile-meta-row">
              <div class="landing-profile-meta-line">
                <strong>Reviews</strong>
                <span>{{ $doctor['average_rating'] }} • {{ $doctor['review_count'] }}</span>
              </div>
              <div class="landing-profile-meta-line">
                <strong>Patients Treated</strong>
                <span>{{ $doctor['total_patients_treated'] ? number_format($doctor['total_patients_treated']) : '—' }}</span>
              </div>
            </div>
          </div>

          <div class="landing-profile-mini-grid">
            <div class="landing-profile-mini-item">
              <i class="fa-solid fa-graduation-cap"></i>
              <div>
                <strong>{{ $doctor['qualification_summary'] ?: '—' }}</strong>
                <span>Qualification summary</span>
              </div>
            </div>
          </div>

          <div class="landing-profile-actions">
            <button type="button" class="landing-profile-book-trigger js-open-book-modal" aria-label="Book now" title="Book now">
              <i class="fa-solid fa-calendar-plus"></i>
              <span>Book Now</span>
            </button>
            <button type="button" class="landing-profile-call-trigger js-open-call-modal" aria-label="Call now" title="Call now">
              <i class="fa-solid fa-phone-volume"></i>
              <span>Call Now</span>
            </button>
          </div>
        </article>

        <article class="landing-doctor-summary-card">
          <h3>Practice Snapshot</h3>
          <div class="landing-info-grid">
            @if($doctor['hospital'])
              <div class="landing-info-row"><i class="fa-solid fa-hospital"></i><span>{{ $doctor['hospital'] }}{{ $doctor['hospital_location'] ? ' • ' . $doctor['hospital_location'] : '' }}</span></div>
            @endif
            @if($doctor['department'])
              <div class="landing-info-row"><i class="fa-solid fa-stethoscope"></i><span>{{ $doctor['department'] }}</span></div>
            @endif
            @if($doctor['specialization'])
              <div class="landing-info-row"><i class="fa-solid fa-shield-heart"></i><span>{{ $doctor['specialization'] }}</span></div>
            @endif
            @if($doctor['phone'])
              <div class="landing-info-row"><i class="fa-solid fa-phone"></i><span>{{ $doctor['phone'] }}</span></div>
            @endif
            @if($doctor['email'])
              <div class="landing-info-row"><i class="fa-solid fa-envelope"></i><span>{{ $doctor['email'] }}</span></div>
            @endif
          </div>
        </article>

        <article class="landing-doctor-summary-card">
          <h3>Availability</h3>
          <div class="landing-pills">
            @if($doctor['online_consultation_available']) <span class="landing-pill"><i class="fa-solid fa-video"></i>Online Consultation</span> @endif
            @if($doctor['in_person_consultation_available']) <span class="landing-pill"><i class="fa-solid fa-user-check"></i>Clinic Visit</span> @endif
            @if($doctor['home_visit_available']) <span class="landing-pill"><i class="fa-solid fa-house-medical"></i>Home Visit</span> @endif
            @if($doctor['appointment_booking_available']) <span class="landing-pill"><i class="fa-solid fa-calendar-check"></i>Booking Open</span> @endif
          </div>
        </article>
      </aside>

      <div class="landing-profile-content">
        <section class="landing-tab-shell">
          <div class="landing-tab-nav" role="tablist" aria-label="Doctor profile tabs">
            <button type="button" class="landing-tab-btn is-active" data-tab-target="tab-overview">Overview</button>
            <button type="button" class="landing-tab-btn" data-tab-target="tab-specializations">Specializations</button>
            <button type="button" class="landing-tab-btn" data-tab-target="tab-experience">Experience & Credentials</button>
            <button type="button" class="landing-tab-btn" data-tab-target="tab-clinics">Clinics</button>
            @if($languages->isNotEmpty())
              <button type="button" class="landing-tab-btn" data-tab-target="tab-languages">Languages</button>
            @endif
          </div>

          <div id="tab-overview" class="landing-tab-panel is-active">
            <article class="landing-slab">
              <div class="landing-section-head mb-0">
                <div>
                  <span class="landing-badge">Overview</span>
                  <h2 class="mt-3 mb-2">About this doctor</h2>
                </div>
              </div>
              <p class="landing-copy mb-0">{{ $doctor['about_doctor'] ?: ($doctor['short_bio'] ?: 'Detailed biography will appear here once it is added in the admin profile workspace.') }}</p>
            </article>
          </div>

          <div id="tab-specializations" class="landing-tab-panel">
            <article class="landing-slab">
              <div class="landing-section-head mb-0">
                <div>
                  <span class="landing-badge">Specializations</span>
                  <h2 class="mt-3 mb-2">Areas of expertise and services</h2>
                </div>
              </div>
              <div class="landing-pills mb-3">
                @forelse($specializations as $specialization)
                  <span class="landing-pill"><i class="fa-solid fa-stethoscope"></i>{{ $specialization['name'] }}@if($specialization['is_primary']) • Primary @endif</span>
                @empty
                  <span class="landing-copy">No specialization records added yet.</span>
                @endforelse
              </div>

              @if($services->isNotEmpty())
                <div class="landing-list">
                  @foreach($services as $service)
                    <div class="landing-list-card">
                      <div class="d-flex justify-content-between gap-3 flex-wrap">
                        <div>
                          <h4 class="mb-1">{{ $service['name'] }}</h4>
                          @if($service['notes']) <div class="landing-copy">{{ $service['notes'] }}</div> @endif
                        </div>
                        <div class="text-md-end">
                          @if($service['fee'] !== null)<div class="fw-bold">From ₹{{ number_format((float) $service['fee'], 0) }}</div>@endif
                          @if($service['duration'])<div class="landing-muted">{{ $service['duration'] }} min</div>@endif
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif
            </article>
          </div>

          <div id="tab-experience" class="landing-tab-panel">
            <article class="landing-slab">
              <div class="landing-section-head mb-0">
                <div>
                  <span class="landing-badge">Experience</span>
                  <h2 class="mt-3 mb-2">Education, registration, and professional background</h2>
                </div>
              </div>
              <div class="landing-metric-row mb-3">
                @if($doctor['qualification_summary']) <span class="landing-metric"><i class="fa-solid fa-graduation-cap"></i>{{ $doctor['qualification_summary'] }}</span> @endif
                @if($doctor['medical_registration_number']) <span class="landing-metric"><i class="fa-solid fa-id-card"></i>{{ $doctor['medical_registration_number'] }}</span> @endif
                @if($doctor['registration_council']) <span class="landing-metric"><i class="fa-solid fa-shield-halved"></i>{{ $doctor['registration_council'] }}</span> @endif
                @if($doctor['years_of_experience']) <span class="landing-metric"><i class="fa-solid fa-briefcase-medical"></i>{{ $doctor['years_of_experience'] }} years experience</span> @endif
              </div>

              <div class="landing-timeline">
                @forelse($qualifications as $qualification)
                  <div class="landing-timeline-item">
                    <h4 class="mb-1">{{ $qualification['name'] }}</h4>
                    <div class="landing-muted">
                      {{ $qualification['institute_name'] ?: $qualification['university_name'] ?: 'Qualification details' }}
                      @if($qualification['start_year'] || $qualification['end_year'])
                        • {{ $qualification['start_year'] ?: '—' }} - {{ $qualification['end_year'] ?: '—' }}
                      @endif
                    </div>
                    @if($qualification['description']) <p class="landing-copy mb-0 mt-2">{{ $qualification['description'] }}</p> @endif
                  </div>
                @empty
                  <p class="landing-copy mb-0">Qualification rows will appear here once they are added in admin.</p>
                @endforelse
              </div>
            </article>
          </div>

          <div id="tab-clinics" class="landing-tab-panel">
            <div class="landing-clinic-grid">
              @if($doctor['hospital'])
                <article class="landing-slab">
                  <div class="d-flex gap-3 align-items-start">
                    @if($doctor['hospital_image'])
                      <img src="{{ $doctor['hospital_image'] }}" alt="{{ $doctor['hospital'] }}" class="landing-avatar">
                    @else
                      <div class="landing-avatar-fallback"><i class="fa-solid fa-hospital"></i></div>
                    @endif
                    <div>
                      <div class="landing-badge mb-2"><i class="fa-solid fa-hospital"></i>Primary Hospital</div>
                      <h4 class="mb-1">{{ $doctor['hospital'] }}</h4>
                      @if($doctor['hospital_location']) <div class="landing-copy mb-0">{{ $doctor['hospital_location'] }}</div> @endif
                    </div>
                  </div>
                </article>
              @endif

              @forelse($clinics as $clinic)
                <article class="landing-slab">
                  <div class="d-flex justify-content-between gap-3 flex-wrap">
                    <div>
                      @if($clinic['is_primary']) <div class="landing-badge mb-2"><i class="fa-solid fa-location-dot"></i>Primary Clinic</div> @endif
                      <h4 class="mb-1">{{ $clinic['name'] }}</h4>
                      @if($clinic['location']) <div class="landing-muted">{{ $clinic['location'] }}</div> @endif
                      @if($clinic['address_line_1']) <p class="landing-copy mt-2 mb-0">{{ $clinic['address_line_1'] }}</p> @endif
                    </div>
                    <div class="text-md-end">
                      @if($clinic['consultation_fee'] !== null)<div class="fw-bold">Clinic fee ₹{{ number_format((float) $clinic['consultation_fee'], 0) }}</div>@endif
                      @if($clinic['room_no'])<div class="landing-muted">Room {{ $clinic['room_no'] }}</div>@endif
                    </div>
                  </div>
                </article>
              @empty
                <article class="landing-slab">
                  <p class="landing-copy mb-0">No clinic records added yet.</p>
                </article>
              @endforelse
            </div>
          </div>

          @if($languages->isNotEmpty())
            <div id="tab-languages" class="landing-tab-panel">
              <article class="landing-slab">
                <span class="landing-badge">Languages</span>
                <h3 class="mt-3">Communication profile</h3>
                <div class="landing-pills mt-3">
                  @foreach($languages as $language)
                    <span class="landing-pill">{{ $language['name'] }}@if($language['proficiency']) • {{ \Illuminate\Support\Str::headline($language['proficiency']) }} @endif</span>
                  @endforeach
                </div>
              </article>
            </div>
          @endif
        </section>
      </div>
    </div>
  </section>

  <div class="landing-call-modal" id="doctorCallModal" aria-hidden="true">
    <div class="landing-call-card" role="dialog" aria-modal="true" aria-labelledby="doctorCallModalTitle">
      <div class="landing-call-card-head">
        <div class="landing-call-brand">
          <img src="{{ $callLogo }}" alt="{{ $callClinicName }}">
          <div>
            <strong id="doctorCallModalTitle">{{ $callClinicName }}</strong>
            <span>{{ $callClinicLocation }}</span>
          </div>
        </div>
        <button type="button" class="landing-call-close js-close-call-modal" aria-label="Close">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="landing-call-card-body">
        <div class="landing-call-detail">
          <strong>Phone Number</strong>
          <div class="landing-call-number-row">
            <div class="landing-call-number" id="doctorCallNumber">{{ $callPhoneNumber }}</div>
            <button type="button" class="landing-call-copy" id="doctorCallCopyBtn">Copy</button>
          </div>
        </div>

        <div class="landing-call-detail">
          <strong>Dial The Extension Given Below After The Call Connects</strong>
          <span>Ext. {{ $callExtension }}</span>
        </div>

        <div class="landing-call-actions">
          <a href="tel:{{ $callPhoneNumber }}" class="landing-btn landing-btn-primary">
            <i class="fa-solid fa-phone"></i>
            <span>Call Now</span>
          </a>
        </div>

        <p class="landing-call-note">
          By calling this number, you agree to the Terms &amp; Conditions. If you could not connect with the center, please write to
          <a href="mailto:{{ $callSupportEmail }}">{{ $callSupportEmail }}</a>.
        </p>
      </div>
    </div>
  </div>

  <div class="landing-call-modal" id="doctorBookingModal" aria-hidden="true">
    <div class="landing-call-card" role="dialog" aria-modal="true" aria-labelledby="doctorBookingModalTitle">
      <div class="landing-call-card-head">
        <div class="landing-call-brand">
          <img src="{{ $callLogo }}" alt="{{ $doctor['name'] }}">
          <div>
            <strong id="doctorBookingModalTitle">Book {{ $doctor['name'] }}</strong>
            <span>Booking module status</span>
          </div>
        </div>
        <button type="button" class="landing-call-close js-close-book-modal" aria-label="Close booking modal">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="landing-call-card-body">
        <div class="landing-book-success is-visible" id="doctorBookingSuccess">
          <div class="landing-book-success-icon">
            <i class="fa-solid fa-circle-check"></i>
          </div>
          <div class="landing-book-alert is-success is-visible">Login active. Registration already completed.</div>
          <div>
            <h3>Booking screen coming soon</h3>
            <p>Your account session is active, so this doctor is ready for the next booking step as soon as the full appointment screen is connected.</p>
          </div>
          <button type="button" class="landing-btn landing-btn-light js-close-book-modal">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to doctor profile</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  @if($similarDoctors->isNotEmpty())
    <section class="landing-section">
      <div class="landing-section-head">
        <div>
          <span class="landing-badge">More Doctors</span>
          <h2 class="mt-3 mb-0">Similar profiles from the same care flow</h2>
        </div>
      </div>
      <div class="landing-grid doctors">
        @foreach($similarDoctors as $item)
          <a href="{{ $item['href'] }}" class="landing-card-link">
            <article class="landing-card">
              <div class="d-flex gap-3 align-items-start">
                @if($item['image'])
                  <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="landing-avatar">
                @else
                  <div class="landing-avatar-fallback"><i class="fa-solid fa-user-doctor"></i></div>
                @endif
                <div class="flex-grow-1">
                  <h3 class="mb-1">{{ $item['name'] }}</h3>
                  <div class="landing-muted">{{ $item['designation'] ?: 'Doctor' }}</div>
                </div>
              </div>
              <div class="landing-metric-row">
                <span class="landing-metric"><i class="fa-solid fa-star"></i>{{ $item['rating'] }}</span>
                @if($item['consultation_fee']) <span class="landing-metric"><i class="fa-solid fa-indian-rupee-sign"></i>{{ $item['consultation_fee'] }}</span> @endif
              </div>
            </article>
          </a>
        @endforeach
      </div>
    </section>
  @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const tabButtons = Array.from(document.querySelectorAll('.landing-tab-btn'));
  const tabPanels = Array.from(document.querySelectorAll('.landing-tab-panel'));
  const callModal = document.getElementById('doctorCallModal');
  const bookingModal = document.getElementById('doctorBookingModal');
  const openCallBtn = document.querySelector('.js-open-call-modal');
  const closeCallBtn = document.querySelector('.js-close-call-modal');
  const openBookBtns = Array.from(document.querySelectorAll('.js-open-book-modal'));
  const closeBookBtns = Array.from(document.querySelectorAll('.js-close-book-modal'));
  const copyBtn = document.getElementById('doctorCallCopyBtn');
  const callNumberEl = document.getElementById('doctorCallNumber');
  const authCheckUrl = '{{ url('/api/auth/check') }}';
  const currentUrl = new URL(window.location.href);
  const bookReturnUrl = currentUrl.pathname + '?book=1';

  function activateTab(targetId) {
    tabButtons.forEach((btn) => {
      btn.classList.toggle('is-active', btn.dataset.tabTarget === targetId);
    });

    tabPanels.forEach((panel) => {
      panel.classList.toggle('is-active', panel.id === targetId);
    });
  }

  tabButtons.forEach((btn) => {
    btn.addEventListener('click', function () {
      activateTab(btn.dataset.tabTarget);
    });
  });

  function syncBodyLock() {
    const hasOpenModal = [callModal, bookingModal].some((modal) => modal?.classList.contains('is-open'));
    document.body.style.overflow = hasOpenModal ? 'hidden' : '';
  }

  function openCallModal() {
    if (!callModal) return;
    callModal.classList.add('is-open');
    callModal.setAttribute('aria-hidden', 'false');
    syncBodyLock();
  }

  function closeCallModal() {
    if (!callModal) return;
    callModal.classList.remove('is-open');
    callModal.setAttribute('aria-hidden', 'true');
    syncBodyLock();
  }

  function openBookingModal() {
    if (!bookingModal) return;
    bookingModal.classList.add('is-open');
    bookingModal.setAttribute('aria-hidden', 'false');
    syncBodyLock();
  }

  function closeBookingModal() {
    if (!bookingModal) return;
    bookingModal.classList.remove('is-open');
    bookingModal.setAttribute('aria-hidden', 'true');
    syncBodyLock();
  }

  function getAuthToken() {
    return sessionStorage.getItem('token') || localStorage.getItem('token') || '';
  }

  function redirectToRegister() {
    window.location.assign('/register?redirect=' + encodeURIComponent(bookReturnUrl));
  }

  async function verifyActiveSession() {
    const token = getAuthToken();
    if (!token) return false;

    try {
      const response = await fetch(authCheckUrl, {
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ' + token,
        },
      });

      if (!response.ok) {
        sessionStorage.removeItem('token');
        sessionStorage.removeItem('role');
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        return false;
      }

      return true;
    } catch (error) {
      return false;
    }
  }

  openCallBtn?.addEventListener('click', openCallModal);
  closeCallBtn?.addEventListener('click', closeCallModal);
  openBookBtns.forEach((btn) => btn.addEventListener('click', async function () {
    const isActive = await verifyActiveSession();
    if (!isActive) {
      redirectToRegister();
      return;
    }

    openBookingModal();
  }));
  closeBookBtns.forEach((btn) => btn.addEventListener('click', closeBookingModal));

  callModal?.addEventListener('click', function (event) {
    if (event.target === callModal) closeCallModal();
  });

  bookingModal?.addEventListener('click', function (event) {
    if (event.target === bookingModal) closeBookingModal();
  });

  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') return;
    closeCallModal();
    closeBookingModal();
  });

  copyBtn?.addEventListener('click', async function () {
    const value = callNumberEl?.textContent?.trim() || '';
    if (!value) return;

    try {
      await navigator.clipboard.writeText(value);
      copyBtn.textContent = 'Copied';
      copyBtn.classList.add('is-copied');
      window.setTimeout(() => {
        copyBtn.textContent = 'Copy';
        copyBtn.classList.remove('is-copied');
      }, 1400);
    } catch (error) {
      copyBtn.textContent = 'Copy failed';
      window.setTimeout(() => {
        copyBtn.textContent = 'Copy';
      }, 1400);
    }
  });

  if (currentUrl.searchParams.get('book') === '1') {
    verifyActiveSession().then((isActive) => {
      if (isActive) {
        openBookingModal();
        currentUrl.searchParams.delete('book');
        const nextUrl = currentUrl.pathname + (currentUrl.search ? currentUrl.search : '');
        window.history.replaceState({}, '', nextUrl);
      }
    });
  }
});
</script>
@endsection
