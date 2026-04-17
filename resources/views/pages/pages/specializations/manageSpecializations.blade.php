@extends('pages.layout.structure')

@section('title', 'Manage Specializations')

@section('content')
  @include('modules.masters.manageSimpleMaster', [
    'master' => [
      'key' => 'specializations',
      'page_path' => '/specializations/manage',
      'api_base' => '/api/specializations',
      'plural' => 'Specializations',
      'singular' => 'Specialization',
      'icon' => 'fa-solid fa-stethoscope',
      'fields' => [
        'short_form' => true,
        'icon_upload' => true,
        'image_upload' => true,
        'metadata' => true,
      ],
    ],
  ])
@endsection
