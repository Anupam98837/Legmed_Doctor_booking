@extends('pages.layout.structure')

@section('title', 'Manage Services')

@section('content')
  @include('modules.masters.manageSimpleMaster', [
    'master' => [
      'key' => 'services',
      'page_path' => '/services/manage',
      'api_base' => '/api/services',
      'plural' => 'Services',
      'singular' => 'Service',
      'icon' => 'fa-solid fa-briefcase-medical',
      'fields' => [
        'short_form' => true,
        'icon_upload' => true,
        'image_upload' => true,
        'default_price' => true,
        'default_duration_minutes' => true,
        'metadata' => true,
      ],
    ],
  ])
@endsection
