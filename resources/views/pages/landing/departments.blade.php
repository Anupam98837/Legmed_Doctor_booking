@extends('pages.landing.layout')

@section('title', 'Browse Departments | LegMed Directory')
@section('meta_description', 'Explore all active departments and open their doctor lists.')

@section('content')
<div class="landing-shell">
  <div class="landing-page-bar">
    <div class="landing-breadcrumbs">
      <a href="{{ route('directory.home') }}">Home</a>
      <span>•</span>
      <span>Departments</span>
    </div>
    <div class="landing-page-meta">
      <span class="landing-badge"><i class="fa-solid fa-grid-2"></i>{{ $totalDepartments }} listed departments</span>
    </div>
  </div>
  <section class="landing-hero">
    <div class="landing-hero-grid">
      <div>
        <span class="landing-kicker"><i class="fa-solid fa-layer-group"></i> Departments</span>
        <h1 class="landing-display">Browse care by department, then open the doctors attached to each one.</h1>
        <p class="landing-lead">This page turns your live department records into a clean directory, making it easy to jump from specialty browsing into doctor discovery.</p>
      </div>
      <div class="landing-search-card">
        <form method="GET" action="{{ route('directory.departments.index') }}" class="landing-search-form">
          <input type="search" class="landing-search-input" name="q" value="{{ $search }}" placeholder="Search departments">
          <button type="submit" class="landing-btn landing-btn-primary landing-search-submit">Apply Search</button>
        </form>
        <div class="landing-copy mt-3">{{ $totalDepartments }} departments available{{ $search ? ' for your search' : '' }}.</div>
      </div>
    </div>
  </section>

  <section class="landing-section">
    @if($departments->isEmpty())
      <div class="landing-empty">
        <h3 class="mb-2">No departments matched this search.</h3>
        <p class="mb-0">Try a broader term or clear the search and browse everything.</p>
      </div>
    @else
      <div class="landing-department-showcase">
        @foreach($departments as $department)
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
    @endif
  </section>
</div>
@endsection
