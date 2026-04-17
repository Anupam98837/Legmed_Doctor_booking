{{-- resources/views/departments/manageDepartments.blade.php --}}
@extends('pages.layout.structure')
@section('title', 'Manage Departments')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

@push('styles')
<style>
/* ===== Shell ===== */
.dept-scope.cm-wrap{padding:2px 0;overflow:visible}
.dept-scope .dept-head{padding:10px 12px;margin-bottom:12px}
.dept-scope .dept-head-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.dept-scope .dept-head-copy{display:flex;align-items:center;min-height:30px}
.dept-scope .dept-head-copy h1{margin:0;font-size:var(--fs-15);line-height:1.15;display:flex;align-items:center;gap:6px}
.dept-scope .dept-head-copy .seg-muted{color:var(--muted-color);font-weight:500}
.dept-scope .dept-head-copy .seg-sep{color:var(--muted-color);opacity:.7}
.dept-scope .dept-head-actions{display:flex;align-items:center;gap:8px}
.dept-scope .dept-refresh-btn{min-width:30px;width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center}
.dept-scope .dept-refresh-btn.is-spinning i{animation:deptSpin .8s linear infinite}
@keyframes deptSpin{to{transform:rotate(360deg)}}

.dept-scope .panel{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:16px;
  box-shadow:var(--shadow-2);
  padding:12px;
}

.dept-scope .dept-toolbar{padding:10px;margin-bottom:12px}
.dept-scope .dept-search-wrap{position:relative;min-width:240px;flex:1 1 320px}
.dept-scope .dept-search-wrap .fa-search{
  position:absolute;left:10px;top:50%;transform:translateY(-50%);
  opacity:.55;pointer-events:none
}
.dept-scope .dept-search-wrap .form-control{padding-left:34px}
.dept-scope .dept-inline-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.dept-scope .dept-inline-actions .btn{white-space:nowrap}
.dept-scope .dept-toolbar .form-control,
.dept-scope .dept-toolbar .form-select,
.dept-scope .dept-toolbar .btn{min-height:40px}

/* ===== Table Card ===== */
.dept-scope .table-wrap.card{
  position:relative;
  border:1px solid var(--line-strong);
  border-radius:16px;
  background:var(--surface);
  box-shadow:var(--shadow-2);
  overflow:visible;
}
.dept-scope .table-wrap .card-body{overflow:visible}
.dept-scope .table-responsive{overflow:visible !important}
.dept-scope .table{--bs-table-bg:transparent}
.dept-scope .table thead th{
  font-weight:600;
  color:var(--muted-color);
  font-size:13px;
  border-bottom:1px solid var(--line-strong);
  background:var(--surface);
  white-space:nowrap;
}
.dept-scope .table thead.sticky-top{z-index:3}
.dept-scope .table tbody tr{border-top:1px solid var(--line-soft)}
.dept-scope .table tbody tr:hover{background:var(--page-hover)}
.dept-scope .small{font-size:12.5px}
.dept-scope .dept-name{min-width:220px}
.dept-scope .dept-thumb{
  width:42px;height:42px;border-radius:12px;object-fit:cover;
  border:1px solid var(--line-strong);background:var(--page)
}
.dept-scope .dept-thumb-fallback{
  width:42px;height:42px;border-radius:12px;
  display:inline-flex;align-items:center;justify-content:center;
  border:1px solid var(--line-strong);background:var(--page);color:var(--muted-color)
}
.dept-scope .dept-desc{
  max-width:360px;
  white-space:normal;
  color:var(--muted-color);
}
.dept-scope .dept-code{
  font-size:12px;
  padding:2px 8px;
  border-radius:999px;
  border:1px solid var(--line-strong);
  background:var(--page);
}
.dept-scope .dept-badge{
  display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;
  border:1px solid var(--line-strong);background:var(--page);font-size:12px;font-weight:600;
}
.dept-scope .dept-badge.active{
  background:color-mix(in oklab, var(--success-color) 14%, transparent);
}
.dept-scope .dept-badge.inactive{
  background:color-mix(in oklab, var(--danger-color) 10%, transparent);
}
.dept-scope .dept-badge.archived{
  background:color-mix(in oklab, var(--warning-color, #f59e0b) 16%, transparent);
}

/* ===== Buttons ===== */
.dept-scope .icon-btn{
  display:inline-flex;align-items:center;justify-content:center;height:34px;min-width:34px;
  padding:0 10px;border:1px solid var(--line-strong);background:var(--surface);border-radius:10px
}
.dept-scope .icon-btn:hover{box-shadow:var(--shadow-1)}

/* ===== Empty / Loader ===== */
.dept-scope .empty{color:var(--muted-color)}
.dept-scope .placeholder{background:linear-gradient(90deg,#00000010,#00000005,#00000010);border-radius:8px}

/* ===== Modals ===== */
.dept-scope .modal-content{border-radius:16px;border:1px solid var(--line-strong);background:var(--surface)}
.dept-scope .modal-header{border-bottom:1px solid var(--line-strong)}
.dept-scope .modal-footer{border-top:1px solid var(--line-strong)}
.dept-scope .form-control,
.dept-scope .form-select,
.dept-scope textarea{
  border-radius:12px;border:1px solid var(--line-strong);background:#fff;
}
.dept-scope .form-control:focus,
.dept-scope .form-select:focus,
.dept-scope textarea:focus{
  box-shadow:0 0 0 3px color-mix(in oklab, var(--accent-color) 20%, transparent);
  border-color:var(--accent-color);
}

.dept-scope .dept-upload-card{
  display:flex;
  gap:12px;
  align-items:center;
  padding:10px;
  border:1px dashed var(--line-strong);
  border-radius:14px;
  background:var(--page);
}
.dept-scope .dept-upload-preview{
  width:72px;
  height:72px;
  border-radius:14px;
  object-fit:cover;
  border:1px solid var(--line-strong);
  background:var(--surface);
  display:none;
}
.dept-scope .dept-upload-fallback{
  width:72px;
  height:72px;
  border-radius:14px;
  display:flex;
  align-items:center;
  justify-content:center;
  border:1px solid var(--line-strong);
  background:var(--surface);
  color:var(--muted-color);
  flex:0 0 72px;
}
.dept-scope .dept-upload-meta{
  flex:1 1 auto;
  min-width:0;
}
.dept-scope .dept-upload-name{
  font-size:12px;
  color:var(--muted-color);
  margin-top:6px;
  word-break:break-word;
}

.dept-scope .table-responsive,
.dept-scope .table-wrap,
.dept-scope .card,
.dept-scope .panel,
.dept-scope.cm-wrap {
  overflow: visible !important;
  transform: none !important;
}

/* ===== Dark tweaks ===== */
html.theme-dark .dept-scope .panel,
html.theme-dark .dept-scope .table-wrap.card,
html.theme-dark .dept-scope .modal-content{
  background:#0f172a;border-color:var(--line-strong)
}
html.theme-dark .dept-scope .table thead th{
  background:#0f172a;border-color:var(--line-strong);color:#94a3b8
}
html.theme-dark .dept-scope .table tbody tr{border-color:var(--line-soft)}
html.theme-dark .dept-scope .form-control,
html.theme-dark .dept-scope .form-select,
html.theme-dark .dept-scope textarea{
  background:#0f172a;color:#e5e7eb;border-color:var(--line-strong)
}
html.theme-dark .dept-scope .dept-thumb-fallback,
html.theme-dark .dept-scope .dept-code,
html.theme-dark .dept-scope .dept-badge,
html.theme-dark .dept-scope .dept-upload-card,
html.theme-dark .dept-scope .dept-upload-fallback{
  background:#0b1220;border-color:var(--line-strong)
}
</style>
@endpush

@section('content')
<div class="cm-wrap dept-scope">

  <div class="panel dept-head">
    <div class="dept-head-row">
      <div class="dept-head-copy">
        <h1>
          <span class="seg-muted">Departments</span>
          <span class="seg-sep">/</span>
          <span>Manage</span>
        </h1>
      </div>

      <div class="dept-head-actions">
        <button type="button" class="btn btn-light dept-refresh-btn" id="btnDeptRefresh" title="Refresh" aria-label="Refresh">
          <i class="fa fa-rotate-right"></i>
        </button>
      </div>
    </div>
  </div>

  <ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" data-bs-toggle="tab" href="#tab-active" role="tab" aria-selected="true">
        <i class="fa-solid fa-hospital-user me-2"></i>Departments
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#tab-archived" role="tab" aria-selected="false">
        <i class="fa-solid fa-box-archive me-2"></i>Archived
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#tab-bin" role="tab" aria-selected="false">
        <i class="fa-solid fa-trash-can me-2"></i>Bin
      </a>
    </li>
  </ul>

  <div class="tab-content mb-3">

    <div class="tab-pane fade show active" id="tab-active" role="tabpanel">
      <div class="panel dept-toolbar">
        <div class="row g-2 align-items-center">
          <div class="col-12 col-xl">
            <div class="dept-inline-actions">
              <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Per page</label>
                <select id="deptPerPage" class="form-select" style="width:96px;">
                  <option>10</option>
                  <option selected>20</option>
                  <option>30</option>
                  <option>50</option>
                  <option>100</option>
                </select>
              </div>

              <div class="dept-search-wrap">
                <input id="deptSearch" type="text" class="form-control" placeholder="Search by name, short form or slug">
                <i class="fa fa-search"></i>
              </div>

              <button id="btnDeptSearch" class="btn btn-primary">
                <i class="fa fa-search me-1"></i>Search
              </button>
              <button id="btnDeptReset" class="btn btn-light">
                <i class="fa fa-rotate-left me-1"></i>Reset
              </button>
            </div>
          </div>

          <div class="col-12 col-xl-auto">
            <div class="dept-inline-actions justify-content-xl-end">
              <button id="btnDeptCreate" class="btn btn-primary">
                <i class="fa fa-plus me-1"></i>New Department
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card table-wrap">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover table-borderless align-middle mb-0">
              <thead class="sticky-top">
                <tr>
                  <th>Department</th>
                  <th>Short Form</th>
                  <th>Slug</th>
                  <th>Status</th>
                  <th>Sort</th>
                  <th>Description</th>
                  <th class="text-end" style="width:160px;">Actions</th>
                </tr>
              </thead>
              <tbody id="rows-active">
                <tr id="loaderRow-active" style="display:none;">
                  <td colspan="7" class="p-0">
                    <div class="p-4">
                      <div class="placeholder-wave">
                        <div class="placeholder col-12 mb-2" style="height:18px;"></div>
                        <div class="placeholder col-12 mb-2" style="height:18px;"></div>
                        <div class="placeholder col-12 mb-2" style="height:18px;"></div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div id="empty-active" class="empty p-4 text-center" style="display:none;">
            <i class="fa fa-hospital-user mb-2" style="font-size:32px; opacity:.6;"></i>
            <div>No departments found.</div>
          </div>

          <div class="d-flex flex-wrap align-items-center justify-content-between p-3 gap-2">
            <div class="text-muted small" id="metaTxt-active">—</div>
            <nav style="position:relative; z-index:1;"><ul id="pager-active" class="pagination mb-0"></ul></nav>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="tab-archived" role="tabpanel">
      <div class="card table-wrap">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover table-borderless align-middle mb-0">
              <thead class="sticky-top">
                <tr>
                  <th>Department</th>
                  <th>Short Form</th>
                  <th>Slug</th>
                  <th>Status</th>
                  <th>Sort</th>
                  <th>Description</th>
                  <th class="text-end" style="width:160px;">Actions</th>
                </tr>
              </thead>
              <tbody id="rows-archived">
                <tr id="loaderRow-archived" style="display:none;">
                  <td colspan="7" class="p-0">
                    <div class="p-4">
                      <div class="placeholder-wave">
                        <div class="placeholder col-12 mb-2" style="height:18px;"></div>
                        <div class="placeholder col-12 mb-2" style="height:18px;"></div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div id="empty-archived" class="empty p-4 text-center" style="display:none;">
            <i class="fa fa-box-archive mb-2" style="font-size:32px; opacity:.6;"></i>
            <div>No archived departments.</div>
          </div>

          <div class="d-flex flex-wrap align-items-center justify-content-between p-3 gap-2">
            <div class="text-muted small" id="metaTxt-archived">—</div>
            <nav style="position:relative; z-index:1;"><ul id="pager-archived" class="pagination mb-0"></ul></nav>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="tab-bin" role="tabpanel">
      <div class="card table-wrap">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover table-borderless align-middle mb-0">
              <thead class="sticky-top">
                <tr>
                  <th>Department</th>
                  <th>Short Form</th>
                  <th>Slug</th>
                  <th>Status</th>
                  <th>Sort</th>
                  <th>Description</th>
                  <th class="text-end" style="width:180px;">Actions</th>
                </tr>
              </thead>
              <tbody id="rows-bin">
                <tr id="loaderRow-bin" style="display:none;">
                  <td colspan="7" class="p-0">
                    <div class="p-4">
                      <div class="placeholder-wave">
                        <div class="placeholder col-12 mb-2" style="height:18px;"></div>
                        <div class="placeholder col-12 mb-2" style="height:18px;"></div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div id="empty-bin" class="empty p-4 text-center" style="display:none;">
            <i class="fa fa-trash-can mb-2" style="font-size:32px; opacity:.6;"></i>
            <div>No items in Bin.</div>
          </div>

          <div class="d-flex flex-wrap align-items-center justify-content-between p-3 gap-2">
            <div class="text-muted small" id="metaTxt-bin">—</div>
            <nav style="position:relative; z-index:1;"><ul id="pager-bin" class="pagination mb-0"></ul></nav>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="modal fade" id="deptModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deptModalTitle">
          <i class="fa fa-hospital-user me-2"></i>Add Department
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="deptForm" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" id="deptId">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Department Name <span class="text-danger">*</span></label>
              <input id="deptName" class="form-control" type="text" maxlength="150" required>
            </div>

            <div class="col-md-3">
              <label class="form-label">Short Form</label>
              <input id="deptShortForm" class="form-control" type="text" maxlength="20">
            </div>

            <div class="col-md-3">
              <label class="form-label">Sort Order</label>
              <input id="deptSortOrder" class="form-control" type="number" min="0" value="0">
            </div>

            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select id="deptStatus" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="archived">Archived</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Department Image</label>
              <input id="deptImage" class="form-control" type="file" accept="image/*">
            </div>

            <div class="col-12">
              <div class="dept-upload-card">
                <div id="deptImageFallback" class="dept-upload-fallback">
                  <i class="fa fa-image"></i>
                </div>
                <img id="deptImagePreview" class="dept-upload-preview" alt="Department image preview">
                <div class="dept-upload-meta">
                  <div class="fw-semibold">Image preview</div>
                  <div class="small text-muted">Upload JPG, PNG, WEBP, GIF or SVG</div>
                  <div id="deptImageName" class="dept-upload-name">No image selected</div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea id="deptDescription" class="form-control" rows="4"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="deptSubmitBtn">
            <i class="fa fa-save me-1"></i>Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:2100">
  <div id="okToast" class="toast text-bg-success border-0">
    <div class="d-flex">
      <div id="okMsg" class="toast-body">Done</div>
      <button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button>
    </div>
  </div>

  <div id="errToast" class="toast text-bg-danger border-0 mt-2">
    <div class="d-flex">
      <div id="errMsg" class="toast-body">Something went wrong</div>
      <button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function () {
  if (window.__DEPARTMENTS_MANAGE_INIT__) return;
  window.__DEPARTMENTS_MANAGE_INIT__ = true;

  const API = '/api/departments';
  const PAGE_MANAGE = '/departments';
  const PAGE_CREATE = '/departments/create';
  const TOKEN = localStorage.getItem('token') || sessionStorage.getItem('token') || '';

  if (!window.bootstrap || !window.bootstrap.Modal) {
    console.error('Bootstrap JS not loaded');
    return;
  }

  const okToast = new bootstrap.Toast(document.getElementById('okToast'));
  const errToast = new bootstrap.Toast(document.getElementById('errToast'));
  const ok = (m) => {
    document.getElementById('okMsg').textContent = m || 'Done';
    okToast.show();
  };
  const err = (m) => {
    document.getElementById('errMsg').textContent = m || 'Something went wrong';
    errToast.show();
  };

  const state = {
    active: { page: 1, rows: [] },
    archived: { page: 1, rows: [] },
    bin: { page: 1, rows: [] },
    q: '',
    perPage: 20,
    permissions: { all: false, actions: new Set() },
    activeScope: 'active',
  };

  const els = {
    search: document.getElementById('deptSearch'),
    perPage: document.getElementById('deptPerPage'),
    btnSearch: document.getElementById('btnDeptSearch'),
    btnReset: document.getElementById('btnDeptReset'),
    btnRefresh: document.getElementById('btnDeptRefresh'),
    btnCreate: document.getElementById('btnDeptCreate'),

    activeRows: document.getElementById('rows-active'),
    archivedRows: document.getElementById('rows-archived'),
    binRows: document.getElementById('rows-bin'),

    activePager: document.getElementById('pager-active'),
    archivedPager: document.getElementById('pager-archived'),
    binPager: document.getElementById('pager-bin'),

    activeMeta: document.getElementById('metaTxt-active'),
    archivedMeta: document.getElementById('metaTxt-archived'),
    binMeta: document.getElementById('metaTxt-bin'),

    activeEmpty: document.getElementById('empty-active'),
    archivedEmpty: document.getElementById('empty-archived'),
    binEmpty: document.getElementById('empty-bin'),

    activeLoader: document.getElementById('loaderRow-active'),
    archivedLoader: document.getElementById('loaderRow-archived'),
    binLoader: document.getElementById('loaderRow-bin'),

    modalTitle: document.getElementById('deptModalTitle'),
    form: document.getElementById('deptForm'),
    id: document.getElementById('deptId'),
    name: document.getElementById('deptName'),
    shortForm: document.getElementById('deptShortForm'),
    sortOrder: document.getElementById('deptSortOrder'),
    status: document.getElementById('deptStatus'),
    image: document.getElementById('deptImage'),
    imagePreview: document.getElementById('deptImagePreview'),
    imageFallback: document.getElementById('deptImageFallback'),
    imageName: document.getElementById('deptImageName'),
    description: document.getElementById('deptDescription'),
    submitBtn: document.getElementById('deptSubmitBtn'),
  };

  const deptModalEl = document.getElementById('deptModal');
  const deptModal = new bootstrap.Modal(deptModalEl);

  function tokenHeaders(extra = {}) {
    const headers = { Accept: 'application/json', ...extra };
    if (TOKEN) headers.Authorization = `Bearer ${TOKEN}`;
    return headers;
  }

  async function api(url, opts = {}) {
    const res = await fetch(url, {
      ...opts,
      headers: tokenHeaders(opts.headers || {}),
    });

    let json = {};
    try { json = await res.json(); } catch (e) {}

    if (!res.ok) {
      throw new Error(json.message || json.error || 'Request failed');
    }

    return json;
  }

  function esc(v) {
    return String(v ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function hasAction(...names) {
    if (state.permissions.all) return true;
    return names.some(name => state.permissions.actions.has(String(name).toLowerCase()));
  }

  async function loadPermissions() {
    try {
      const res = await api('/api/my/sidebar-menus?with_actions=1');

      if (res.tree === 'all') {
        state.permissions.all = true;
        state.permissions.actions = new Set(['view','create','update','edit','delete','destroy','restore','force_delete','force-delete']);
        return;
      }

      let path = window.location.pathname.replace(/\/+$/, '') || '/';
      if (path === PAGE_CREATE) path = PAGE_MANAGE;
      const actions = new Set();

      (Array.isArray(res.tree) ? res.tree : []).forEach(header => {
        (header.children || []).forEach(page => {
          const href = String(page.href || '').replace(/\/+$/, '') || '/';
          if (href === path) {
            (page.actions || []).forEach(action => actions.add(String(action).toLowerCase()));
          }
        });
      });

      state.permissions.actions = actions;
    } catch (e) {
      state.permissions.actions = new Set();
    }
  }

  function setRefreshLoading(on) {
    els.btnRefresh.disabled = !!on;
    els.btnRefresh.classList.toggle('is-spinning', !!on);
  }

  function statusBadge(status) {
    const s = String(status || 'active').toLowerCase();
    return `<span class="dept-badge ${esc(s)}">${esc(s)}</span>`;
  }

  function imageCell(row) {
    if (row.image) {
      return `<img src="${esc(row.image)}" alt="${esc(row.name)}" class="dept-thumb">`;
    }

    const short = (row.short_form || row.name || 'D').substring(0, 2).toUpperCase();
    return `<span class="dept-thumb-fallback">${esc(short)}</span>`;
  }

  function actionsHtml(scope, row) {
    const key = row.id;

    if (scope === 'bin') {
      const arr = [];
      if (hasAction('restore')) {
        arr.push(`<button class="btn btn-light btn-sm js-restore" data-id="${key}" type="button" title="Restore"><i class="fa fa-rotate-left"></i></button>`);
      }
      if (hasAction('force_delete', 'force-delete')) {
        arr.push(`<button class="btn btn-light btn-sm text-danger js-force" data-id="${key}" type="button" title="Delete permanently"><i class="fa fa-skull-crossbones"></i></button>`);
      }
      return arr.join('') || '<span class="text-muted small">No actions</span>';
    }

    const arr = [];
    if (hasAction('update', 'edit')) {
      arr.push(`<button class="btn btn-light btn-sm js-edit" data-id="${key}" type="button" title="Edit"><i class="fa fa-pen"></i></button>`);
    }
    if (hasAction('delete', 'destroy')) {
      arr.push(`<button class="btn btn-light btn-sm text-danger js-delete" data-id="${key}" type="button" title="Delete"><i class="fa fa-trash"></i></button>`);
    }
    return arr.join('') || '<span class="text-muted small">No actions</span>';
  }

  function rowHtml(scope, row) {
    return `
      <tr>
        <td class="dept-name">
          <div class="d-flex align-items-center gap-3">
            ${imageCell(row)}
            <div>
              <div class="fw-semibold">${esc(row.name || '-')}</div>
              <div class="small text-muted">${esc(row.uuid || '-')}</div>
            </div>
          </div>
        </td>
        <td>${row.short_form ? `<span class="dept-code">${esc(row.short_form)}</span>` : '-'}</td>
        <td><code>${esc(row.slug || '-')}</code></td>
        <td>${statusBadge(row.status)}</td>
        <td>${Number(row.sort_order || 0)}</td>
        <td class="dept-desc">${esc(row.description || '-')}</td>
        <td class="text-end">
          <div class="d-flex justify-content-end gap-1 flex-wrap">
            ${actionsHtml(scope, row)}
          </div>
        </td>
      </tr>
    `;
  }

  function clearRows(scope) {
    const tbody = scope === 'active' ? els.activeRows : scope === 'archived' ? els.archivedRows : els.binRows;
    tbody.querySelectorAll('tr:not([id^="loaderRow"])').forEach(el => el.remove());
  }

  function showLoader(scope, show) {
    const el = scope === 'active' ? els.activeLoader : scope === 'archived' ? els.archivedLoader : els.binLoader;
    el.style.display = show ? '' : 'none';
  }

  function showEmpty(scope, show) {
    const el = scope === 'active' ? els.activeEmpty : scope === 'archived' ? els.archivedEmpty : els.binEmpty;
    el.style.display = show ? '' : 'none';
  }

  function setMeta(scope, text) {
    const el = scope === 'active' ? els.activeMeta : scope === 'archived' ? els.archivedMeta : els.binMeta;
    el.textContent = text;
  }

  function pagerEl(scope) {
    return scope === 'active' ? els.activePager : scope === 'archived' ? els.archivedPager : els.binPager;
  }

  function bodyEl(scope) {
    return scope === 'active' ? els.activeRows : scope === 'archived' ? els.archivedRows : els.binRows;
  }

  function currentUrl(scope) {
    const params = new URLSearchParams();
    params.set('per_page', String(state.perPage));
    params.set('page', String(state[scope].page || 1));

    if (scope === 'active') {
      if (state.q) params.set('q', state.q);
      params.set('status', 'active');
      return `${API}?${params.toString()}`;
    }

    if (scope === 'archived') {
      if (state.q) params.set('q', state.q);
      return `${API}/archived?${params.toString()}`;
    }

    if (state.q) params.set('q', state.q);
    return `${API}/bin?${params.toString()}`;
  }

  function renderPager(scope, meta) {
    const pager = pagerEl(scope);
    pager.innerHTML = '';

    const total = Number(meta.total || 0);
    const per = Number(meta.per_page || state.perPage || 20);
    const page = Number(meta.page || state[scope].page || 1);
    const totalPages = Math.max(1, Math.ceil(total / per));

    const li = (disabled, active, label, target) => `
      <li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}">
        <a class="page-link" href="javascript:void(0)" data-page="${target || ''}">${label}</a>
      </li>
    `;

    let html = '';
    html += li(page <= 1, false, 'Previous', page - 1);

    const w = 3;
    const s = Math.max(1, page - w);
    const e = Math.min(totalPages, page + w);

    if (s > 1) {
      html += li(false, false, 1, 1);
      if (s > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }

    for (let i = s; i <= e; i++) {
      html += li(false, i === page, i, i);
    }

    if (e < totalPages) {
      if (e < totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
      html += li(false, false, totalPages, totalPages);
    }

    html += li(page >= totalPages, false, 'Next', page + 1);

    pager.innerHTML = html;

    pager.querySelectorAll('a.page-link[data-page]').forEach(a => {
      a.addEventListener('click', () => {
        const target = Number(a.dataset.page);
        if (!target || target === state[scope].page) return;
        state[scope].page = Math.max(1, target);
        load(scope);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });

    setMeta(scope, `Showing page ${page} of ${totalPages} — ${total} result(s)`);
  }

  async function load(scope) {
    clearRows(scope);
    showEmpty(scope, false);
    setMeta(scope, '—');
    showLoader(scope, true);

    try {
      const res = await api(currentUrl(scope));
      const items = Array.isArray(res.data) ? res.data : [];
      const meta = res.meta || {
        page: 1,
        per_page: state.perPage,
        total: items.length
      };

      state[scope].rows = items;

      if (!items.length) {
        showEmpty(scope, true);
        renderPager(scope, meta);
        return;
      }

      const tbody = bodyEl(scope);
      tbody.insertAdjacentHTML('beforeend', items.map(row => rowHtml(scope, row)).join(''));
      renderPager(scope, meta);
    } catch (e) {
      showEmpty(scope, true);
      setMeta(scope, 'Failed to load');
      err(e.message || 'Failed to load departments');
    } finally {
      showLoader(scope, false);
    }
  }

  function setImagePreview(src = '', fileName = 'No image selected') {
    if (src) {
      els.imagePreview.src = src;
      els.imagePreview.style.display = 'block';
      els.imageFallback.style.display = 'none';
    } else {
      els.imagePreview.src = '';
      els.imagePreview.style.display = 'none';
      els.imageFallback.style.display = 'flex';
    }

    els.imageName.textContent = fileName || 'No image selected';
  }

  function resetForm() {
    els.form.reset();
    els.id.value = '';
    els.sortOrder.value = '0';
    els.status.value = 'active';
    els.modalTitle.innerHTML = '<i class="fa fa-hospital-user me-2"></i>Add Department';
    els.submitBtn.innerHTML = '<i class="fa fa-save me-1"></i>Save';
    setImagePreview('', 'No image selected');
  }

  function fillForm(row) {
    els.id.value = row.id || '';
    els.name.value = row.name || '';
    els.shortForm.value = row.short_form || '';
    els.sortOrder.value = Number(row.sort_order || 0);
    els.status.value = row.status || 'active';
    els.image.value = '';
    els.description.value = row.description || '';
    els.modalTitle.innerHTML = '<i class="fa fa-pen me-2"></i>Edit Department';
    els.submitBtn.innerHTML = '<i class="fa fa-floppy-disk me-1"></i>Update';

    if (row.image) {
      const fileName = (row.image_path || row.image || '').split('/').pop() || 'Current image';
      setImagePreview(row.image, fileName);
    } else {
      setImagePreview('', 'No image selected');
    }
  }

  async function openEdit(id) {
    try {
      const res = await api(`${API}/${encodeURIComponent(id)}`);
      const row = res.department || res.data || {};
      resetForm();
      fillForm(row);
      deptModal.show();
    } catch (e) {
      err(e.message || 'Failed to load department');
    }
  }

  function buildFormData(isEdit) {
    const fd = new FormData();
    fd.append('name', els.name.value.trim());
    fd.append('short_form', els.shortForm.value.trim());
    fd.append('sort_order', String(Number(els.sortOrder.value || 0)));
    fd.append('status', els.status.value);
    fd.append('description', els.description.value.trim());

    if (els.image.files && els.image.files[0]) {
      fd.append('image', els.image.files[0]);
    }

    if (isEdit) {
      fd.append('_method', 'PATCH');
    }

    return fd;
  }

  async function submitForm(ev) {
    ev.preventDefault();

    const name = els.name.value.trim();
    if (!name) {
      return err('Department name is required');
    }

    const id = els.id.value.trim();
    const isEdit = !!id;
    const formData = buildFormData(isEdit);

    els.submitBtn.disabled = true;

    try {
      const res = await fetch(isEdit ? `${API}/${encodeURIComponent(id)}` : API, {
        method: 'POST',
        headers: tokenHeaders(),
        body: formData,
      });

      let json = {};
      try { json = await res.json(); } catch (e) {}

      if (!res.ok) {
        throw new Error(json.message || json.error || 'Failed to save department');
      }

      deptModal.hide();
      resetForm();
      ok(isEdit ? 'Department updated' : 'Department created');
      await load(state.activeScope);
      if (state.activeScope !== 'active') await load('active');
    } catch (e) {
      err(e.message || 'Failed to save department');
    } finally {
      els.submitBtn.disabled = false;
    }
  }

  async function softDelete(id) {
    const yes = await Swal.fire({
      icon: 'warning',
      title: 'Delete department?',
      text: 'This will move the department to Bin.',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      confirmButtonColor: '#ef4444'
    });

    if (!yes.isConfirmed) return;

    try {
      await api(`${API}/${encodeURIComponent(id)}`, { method: 'DELETE' });
      ok('Department moved to Bin');
      await load(state.activeScope);
      await load('bin');
    } catch (e) {
      err(e.message || 'Failed to delete department');
    }
  }

  async function restoreRow(id) {
    const yes = await Swal.fire({
      icon: 'question',
      title: 'Restore department?',
      showCancelButton: true,
      confirmButtonText: 'Restore'
    });

    if (!yes.isConfirmed) return;

    try {
      await api(`${API}/${encodeURIComponent(id)}/restore`, { method: 'POST' });
      ok('Department restored');
      await load('bin');
      await load('active');
    } catch (e) {
      err(e.message || 'Failed to restore department');
    }
  }

  async function forceDelete(id) {
    const yes = await Swal.fire({
      icon: 'warning',
      title: 'Delete permanently?',
      text: 'This action cannot be undone.',
      showCancelButton: true,
      confirmButtonText: 'Delete permanently',
      confirmButtonColor: '#dc2626'
    });

    if (!yes.isConfirmed) return;

    try {
      await api(`${API}/${encodeURIComponent(id)}/force`, { method: 'DELETE' });
      ok('Department permanently deleted');
      await load('bin');
    } catch (e) {
      err(e.message || 'Failed to permanently delete department');
    }
  }

  function bindTableActions(scope) {
    bodyEl(scope).addEventListener('click', (ev) => {
      const editBtn = ev.target.closest('.js-edit');
      const deleteBtn = ev.target.closest('.js-delete');
      const restoreBtn = ev.target.closest('.js-restore');
      const forceBtn = ev.target.closest('.js-force');

      if (editBtn) openEdit(editBtn.dataset.id);
      if (deleteBtn) softDelete(deleteBtn.dataset.id);
      if (restoreBtn) restoreRow(restoreBtn.dataset.id);
      if (forceBtn) forceDelete(forceBtn.dataset.id);
    });
  }

  function bindEvents() {
    els.btnCreate.addEventListener('click', () => {
      resetForm();
      deptModal.show();
    });

    els.btnSearch.addEventListener('click', () => {
      state.q = els.search.value.trim();
      state[state.activeScope].page = 1;
      load(state.activeScope);
    });

    els.search.addEventListener('keydown', (e) => {
      if (e.key !== 'Enter') return;
      e.preventDefault();
      state.q = els.search.value.trim();
      state[state.activeScope].page = 1;
      load(state.activeScope);
    });

    els.btnReset.addEventListener('click', () => {
      els.search.value = '';
      els.perPage.value = '20';
      state.q = '';
      state.perPage = 20;
      state.active.page = 1;
      state.archived.page = 1;
      state.bin.page = 1;
      load(state.activeScope);
    });

    els.perPage.addEventListener('change', () => {
      state.perPage = Number(els.perPage.value || 20);
      state[state.activeScope].page = 1;
      load(state.activeScope);
    });

    els.btnRefresh.addEventListener('click', async () => {
      setRefreshLoading(true);
      try {
        await load(state.activeScope);
      } finally {
        setTimeout(() => setRefreshLoading(false), 300);
      }
    });

    els.image.addEventListener('change', () => {
      const file = els.image.files && els.image.files[0] ? els.image.files[0] : null;
      if (!file) {
        setImagePreview('', 'No image selected');
        return;
      }

      const objectUrl = URL.createObjectURL(file);
      setImagePreview(objectUrl, file.name);
    });

    els.form.addEventListener('submit', submitForm);

    deptModalEl.addEventListener('hidden.bs.modal', () => {
      const url = new URL(window.location.href);
      const currentPath = url.pathname.replace(/\/+$/, '') || '/';
      const createFromRoute = currentPath === PAGE_CREATE || ['1', 'true', 'yes'].includes(String(url.searchParams.get('create') || '').toLowerCase());
      if (createFromRoute) {
        history.replaceState({}, '', PAGE_MANAGE);
      }
      resetForm();
    });

    document.querySelector('a[href="#tab-active"]')?.addEventListener('shown.bs.tab', () => {
      state.activeScope = 'active';
      load('active');
    });

    document.querySelector('a[href="#tab-archived"]')?.addEventListener('shown.bs.tab', () => {
      state.activeScope = 'archived';
      load('archived');
    });

    document.querySelector('a[href="#tab-bin"]')?.addEventListener('shown.bs.tab', () => {
      state.activeScope = 'bin';
      load('bin');
    });

    bindTableActions('active');
    bindTableActions('archived');
    bindTableActions('bin');
  }

  function openCreateFromRouteIfNeeded() {
    const url = new URL(window.location.href);
    const currentPath = url.pathname.replace(/\/+$/, '') || '/';
    const createFromRoute = currentPath === PAGE_CREATE || ['1', 'true', 'yes'].includes(String(url.searchParams.get('create') || '').toLowerCase());
    if (!createFromRoute || !hasAction('create', 'store')) return;

    resetForm();
    deptModal.show();
  }

  async function init() {
    await loadPermissions();
    els.btnCreate.hidden = !hasAction('create', 'store');
    bindEvents();
    await load('active');
    openCreateFromRouteIfNeeded();
  }

  init();
})();
</script>
@endpush
