@extends('pages.layout.structure')

@section('title', 'Dashboard')

@push('styles')
<style>
.dash-wrap{
  padding:2px 0;
}

.dash-head{
  padding:10px 12px;
  margin-bottom:12px;
}

.dash-head h1{
  margin:0;
  font-size:var(--fs-15);
  line-height:1.15;
  display:flex;
  align-items:center;
  gap:6px;
  flex-wrap:wrap;
}

.dash-head .seg-muted{
  color:var(--muted-color);
  font-weight:500;
}

.dash-head .seg-sep{
  color:var(--muted-color);
  opacity:.7;
}

.dash-card{
  background:linear-gradient(135deg, rgba(149,30,170,.08), rgba(15,23,42,.02));
  border:1px solid var(--line-strong);
  border-radius:18px;
  box-shadow:var(--shadow-1);
  padding:28px 22px;
}

.dash-card h2{
  margin:0 0 8px;
  font-size:28px;
  font-weight:800;
  color:var(--ink);
}

.dash-card p{
  margin:0;
  max-width:640px;
  color:var(--muted-color);
  font-size:14px;
  line-height:1.7;
}
</style>
@endpush

@section('content')
<div class="dash-wrap">
  <div class="panel dash-head">
    <h1>
      <span class="seg-muted">Home</span>
      <span class="seg-sep">/</span>
      <span>Dashboard</span>
    </h1>
  </div>

  <div class="dash-card">
    <h2>Welcome to Doctor Booking</h2>
    <p>Your dashboard is ready. Use the sidebar to open the pages you have access to.</p>
  </div>
</div>
@endsection
