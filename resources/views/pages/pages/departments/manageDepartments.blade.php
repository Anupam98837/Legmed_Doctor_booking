@extends('pages.layout.structure')

@section('title', 'Departments')
@section('header', 'Manage Departments')

@section('content')
  @include('modules.departments.manageDepartments')
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (!sessionStorage.getItem('token') && !localStorage.getItem('token')) {
      window.location.href = '/';
    }
  });
</script>
@endsection
