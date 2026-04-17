@extends('pages.layout.structure')

@section('title', 'Manage Languages')

@section('content')
  @include('modules.masters.manageSimpleMaster', [
    'master' => [
      'key' => 'languages',
      'page_path' => '/languages/manage',
      'api_base' => '/api/languages',
      'plural' => 'Languages',
      'singular' => 'Language',
      'icon' => 'fa-solid fa-language',
      'fields' => [
        'code' => true,
        'metadata' => true,
      ],
      'labels' => [
        'code' => 'Language Code',
      ],
    ],
  ])
@endsection
