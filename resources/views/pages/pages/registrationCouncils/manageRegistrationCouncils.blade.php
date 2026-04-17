@extends('pages.layout.structure')

@section('title', 'Manage Registration Councils')

@section('content')
  @include('modules.masters.manageSimpleMaster', [
    'master' => [
      'key' => 'registration_councils',
      'page_path' => '/registration-councils/manage',
      'api_base' => '/api/registration-councils',
      'plural' => 'Registration Councils',
      'singular' => 'Registration Council',
      'icon' => 'fa-solid fa-id-card',
      'fields' => [
        'short_form' => true,
        'country' => true,
        'state' => true,
        'website' => true,
        'metadata' => true,
      ],
    ],
  ])
@endsection
