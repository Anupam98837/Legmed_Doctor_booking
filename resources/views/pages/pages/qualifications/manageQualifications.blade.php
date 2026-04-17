@extends('pages.layout.structure')

@section('title', 'Manage Qualifications')

@section('content')
  @include('modules.masters.manageSimpleMaster', [
    'master' => [
      'key' => 'qualifications',
      'page_path' => '/qualifications/manage',
      'api_base' => '/api/qualifications',
      'plural' => 'Qualifications',
      'singular' => 'Qualification',
      'icon' => 'fa-solid fa-graduation-cap',
      'fields' => [
        'short_form' => true,
        'type' => true,
        'metadata' => true,
      ],
      'labels' => [
        'type' => 'Qualification Type',
      ],
    ],
  ])
@endsection
