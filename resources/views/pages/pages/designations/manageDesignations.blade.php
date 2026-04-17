@extends('pages.layout.structure')

@section('title', 'Manage Designations')

@section('content')
  @include('modules.masters.manageSimpleMaster', [
    'master' => [
      'key' => 'designations',
      'page_path' => '/designations/manage',
      'api_base' => '/api/designations',
      'plural' => 'Designations',
      'singular' => 'Designation',
      'icon' => 'fa-solid fa-user-tie',
      'fields' => [
        'short_form' => true,
        'metadata' => true,
      ],
    ],
  ])
@endsection
