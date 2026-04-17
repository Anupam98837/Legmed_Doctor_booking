@push('styles')
<style>
.hm-wrap{padding:2px 0;overflow:visible}
.hm-head{padding:10px 12px;margin-bottom:12px}
.hm-head-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.hm-head-copy{display:flex;align-items:center;min-height:30px}
.hm-head-copy h1{margin:0;font-size:var(--fs-15);line-height:1.15;display:flex;align-items:center;gap:6px}
.hm-head-copy .seg-muted{color:var(--muted-color);font-weight:500}
.hm-head-copy .seg-sep{color:var(--muted-color);opacity:.7}
.hm-head-actions{display:flex;align-items:center;gap:8px}
.hm-refresh-btn{min-width:30px;width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center}
.hm-refresh-btn.is-spinning i{animation:hmSpin .8s linear infinite}
@keyframes hmSpin{to{transform:rotate(360deg)}}
.hm-panel,.hm-table-card{background:var(--surface);border:1px solid var(--line-strong);border-radius:16px;box-shadow:var(--shadow-2)}
.hm-panel{padding:12px;margin-bottom:12px}
.hm-toolbar-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.hm-search-wrap{position:relative;min-width:260px;flex:1 1 340px}
.hm-search-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted-color)}
.hm-search-wrap .form-control{padding-left:36px}
.hm-table-card .card-body{padding:0}
.hm-table th{font-weight:600;color:var(--muted-color);font-size:13px;white-space:nowrap;border-bottom:1px solid var(--line-strong);background:var(--surface)}
.hm-table tbody tr{border-top:1px solid var(--line-soft)}
.hm-table tbody tr:hover{background:var(--page-hover)}
.hm-logo{width:42px;height:42px;border-radius:12px;object-fit:cover;border:1px solid var(--line-strong);background:var(--page)}
.hm-logo-fallback{width:42px;height:42px;border-radius:12px;border:1px solid var(--line-strong);display:inline-flex;align-items:center;justify-content:center;background:var(--page);color:var(--muted-color)}
.hm-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;border:1px solid var(--line-strong);background:var(--page);font-size:12px;font-weight:600}
.hm-badge.active{background:color-mix(in oklab, var(--success-color) 14%, transparent)}
.hm-badge.inactive{background:color-mix(in oklab, var(--danger-color) 10%, transparent)}
.hm-empty{padding:30px 16px;text-align:center;color:var(--muted-color)}
.hm-empty i{font-size:32px;opacity:.6;margin-bottom:10px}
html.theme-dark .hm-panel,html.theme-dark .hm-table-card{background:#0f172a;border-color:var(--line-strong)}
html.theme-dark .hm-table th{background:#0f172a;border-color:var(--line-strong);color:#94a3b8}
@media (max-width:767.98px){.hm-head-row,.hm-toolbar-row{flex-direction:column;align-items:flex-start!important}.hm-head-actions,.hm-toolbar-row .btn{width:100%}}
</style>
@endpush

<div class="hm-wrap">
  <div class="panel hm-head">
    <div class="hm-head-row">
      <div class="hm-head-copy">
        <h1>
          <span class="seg-muted">Hospital</span>
          <span class="seg-sep">/</span>
          <span>Manage</span>
        </h1>
      </div>
      <div class="hm-head-actions">
        <a href="/hospital/create" class="btn btn-primary" id="btnHospitalCreatePage">
          <i class="fa fa-plus me-1"></i>Create Hospital
        </a>
        <button type="button" class="w3-icon-btn hm-refresh-btn" id="btnHospitalRefresh" title="Refresh" aria-label="Refresh">
          <i class="fa fa-rotate-right"></i>
        </button>
      </div>
    </div>
  </div>

  <ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" data-bs-toggle="tab" href="#tab-hospital-active" role="tab">
        <i class="fa-solid fa-hospital me-2"></i>Active
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#tab-hospital-inactive" role="tab">
        <i class="fa-solid fa-power-off me-2"></i>Inactive
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#tab-hospital-bin" role="tab">
        <i class="fa-solid fa-trash-can me-2"></i>Bin
      </a>
    </li>
  </ul>

  <div class="hm-panel">
    <div class="hm-toolbar-row">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <label class="text-muted small mb-0">Per page</label>
        <select id="hospitalPerPage" class="form-select" style="width:96px">
          <option>10</option>
          <option selected>20</option>
          <option>30</option>
          <option>50</option>
          <option>100</option>
        </select>
      </div>

      <div class="hm-search-wrap">
        <i class="fa fa-search"></i>
        <input id="hospitalSearch" type="text" class="form-control" placeholder="Search by hospital, code, city, state or email">
      </div>

      <div class="d-flex gap-2 flex-wrap">
        <button id="btnHospitalSearch" class="btn btn-primary"><i class="fa fa-search me-1"></i>Search</button>
        <button id="btnHospitalReset" class="btn btn-light"><i class="fa fa-rotate-left me-1"></i>Reset</button>
      </div>
    </div>
  </div>

  <div class="tab-content">
    @foreach (['active' => 'rows-hospital-active', 'inactive' => 'rows-hospital-inactive', 'bin' => 'rows-hospital-bin'] as $scope => $tbodyId)
      <div class="tab-pane fade {{ $scope === 'active' ? 'show active' : '' }}" id="tab-hospital-{{ $scope }}" role="tabpanel">
        <div class="card hm-table-card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover table-borderless align-middle mb-0 hm-table">
                <thead class="sticky-top">
                  <tr>
                    <th>Hospital</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th class="text-end" style="width:200px">Actions</th>
                  </tr>
                </thead>
                <tbody id="{{ $tbodyId }}"></tbody>
              </table>
            </div>

            <div id="empty-hospital-{{ $scope }}" class="hm-empty" style="display:none">
              <i class="fa {{ $scope === 'bin' ? 'fa-trash-can' : ($scope === 'inactive' ? 'fa-power-off' : 'fa-hospital') }}"></i>
              <div>No hospitals in this tab.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 gap-2">
              <div class="text-muted small" id="meta-hospital-{{ $scope }}">—</div>
              <nav><ul id="pager-hospital-{{ $scope }}" class="pagination mb-0"></ul></nav>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080">
  <div id="hospitalManageToastSuccess" class="toast align-items-center text-bg-success border-0">
    <div class="d-flex">
      <div class="toast-body" id="hospitalManageToastSuccessText">Done</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
  <div id="hospitalManageToastError" class="toast align-items-center text-bg-danger border-0 mt-2">
    <div class="d-flex">
      <div class="toast-body" id="hospitalManageToastErrorText">Something went wrong</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.__HOSPITAL_MANAGE_INIT__) return;
  window.__HOSPITAL_MANAGE_INIT__ = true;

  const token = sessionStorage.getItem('token') || localStorage.getItem('token') || '';
  if (!token) {
    window.location.href = '/';
    return;
  }

  const API = '/api/hospitals';
  const PAGE_CREATE = '/hospital/create';
  const PAGE_MANAGE = '/hospital/manage';

  const toastOk = new bootstrap.Toast(document.getElementById('hospitalManageToastSuccess'));
  const toastErr = new bootstrap.Toast(document.getElementById('hospitalManageToastError'));
  const ok = message => {
    document.getElementById('hospitalManageToastSuccessText').textContent = message || 'Done';
    toastOk.show();
  };
  const err = message => {
    document.getElementById('hospitalManageToastErrorText').textContent = message || 'Something went wrong';
    toastErr.show();
  };

  const els = {
    search: document.getElementById('hospitalSearch'),
    perPage: document.getElementById('hospitalPerPage'),
    btnSearch: document.getElementById('btnHospitalSearch'),
    btnReset: document.getElementById('btnHospitalReset'),
    btnRefresh: document.getElementById('btnHospitalRefresh'),
    btnCreate: document.getElementById('btnHospitalCreatePage'),
    rows: {
      active: document.getElementById('rows-hospital-active'),
      inactive: document.getElementById('rows-hospital-inactive'),
      bin: document.getElementById('rows-hospital-bin'),
    },
    pager: {
      active: document.getElementById('pager-hospital-active'),
      inactive: document.getElementById('pager-hospital-inactive'),
      bin: document.getElementById('pager-hospital-bin'),
    },
    meta: {
      active: document.getElementById('meta-hospital-active'),
      inactive: document.getElementById('meta-hospital-inactive'),
      bin: document.getElementById('meta-hospital-bin'),
    },
    empty: {
      active: document.getElementById('empty-hospital-active'),
      inactive: document.getElementById('empty-hospital-inactive'),
      bin: document.getElementById('empty-hospital-bin'),
    },
  };

  const state = {
    activeScope: 'active',
    q: '',
    perPage: 20,
    permissionsByPath: new Map(),
    allAccess: false,
    pages: { active: 1, inactive: 1, bin: 1 },
  };

  function authHeaders(extra = {}) {
    return Object.assign({
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json'
    }, extra);
  }

  function normalizePath(path) {
    const raw = String(path || '').trim();
    if (!raw) return '';
    try {
      const u = new URL(raw, window.location.origin);
      return (u.pathname || '/').replace(/\/+$/, '') || '/';
    } catch (e) {
      return ('/' + raw.replace(/^\/+/, '')).replace(/\/+$/, '') || '/';
    }
  }

  function hasPageAction(path, ...actions) {
    if (state.allAccess) return true;
    const set = state.permissionsByPath.get(normalizePath(path));
    if (!set) return false;
    return actions.some(action => set.has(String(action).toLowerCase()));
  }

  async function loadPermissions() {
    state.permissionsByPath = new Map();
    state.allAccess = false;

    try {
      const res = await fetch('/api/my/sidebar-menus?with_actions=1', { headers: authHeaders() });
      const js = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(js.message || 'Failed to load permissions');

      if (js.tree === 'all') {
        state.allAccess = true;
      } else {
        (Array.isArray(js.tree) ? js.tree : []).forEach(header => {
          (Array.isArray(header.children) ? header.children : []).forEach(page => {
            const href = normalizePath(page.href || '');
            if (!href) return;
            state.permissionsByPath.set(
              href,
              new Set((Array.isArray(page.actions) ? page.actions : []).map(v => String(v || '').toLowerCase()).filter(Boolean))
            );
          });
        });
      }
    } catch (e) {
      state.permissionsByPath = new Map();
    }

    els.btnCreate.style.display = hasPageAction(PAGE_CREATE, 'create', 'view', 'update', 'edit') ? '' : 'none';
  }

  function setRefreshLoading(on) {
    els.btnRefresh.disabled = !!on;
    els.btnRefresh.classList.toggle('is-spinning', !!on);
  }

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function statusBadge(status) {
    const s = String(status || 'active').toLowerCase();
    return `<span class="hm-badge ${escapeHtml(s)}"><i class="fa ${s === 'active' ? 'fa-circle-check' : 'fa-circle-xmark'}"></i>${escapeHtml(s)}</span>`;
  }

  function logoCell(row) {
    if (row.logo) {
      return `<img src="${escapeHtml(row.logo)}" alt="${escapeHtml(row.name || 'Hospital')}" class="hm-logo">`;
    }
    const letters = String(row.short_name || row.name || 'H').trim().substring(0, 2).toUpperCase();
    return `<span class="hm-logo-fallback">${escapeHtml(letters)}</span>`;
  }

  function actionButtons(scope, row) {
    const canOpen = hasPageAction(PAGE_CREATE, 'view', 'update', 'edit', 'create');
    const canEdit = hasPageAction(PAGE_CREATE, 'update', 'edit') || hasPageAction(PAGE_MANAGE, 'update', 'edit');
    const canDelete = hasPageAction(PAGE_MANAGE, 'delete', 'destroy');
    const canRestore = hasPageAction(PAGE_MANAGE, 'restore');
    const canForceDelete = hasPageAction(PAGE_MANAGE, 'force_delete', 'force-delete');

    if (scope === 'bin') {
      const items = [];
      if (canRestore) items.push(`<button type="button" class="btn btn-light btn-sm js-hospital-restore" data-id="${row.id}" title="Restore"><i class="fa fa-rotate-left"></i></button>`);
      if (canForceDelete) items.push(`<button type="button" class="btn btn-light btn-sm text-danger js-hospital-force" data-id="${row.id}" title="Delete permanently"><i class="fa fa-skull-crossbones"></i></button>`);
      return items.join('') || '<span class="text-muted small">No actions</span>';
    }

    const items = [];
    if (canOpen) items.push(`<a class="btn btn-light btn-sm" href="${PAGE_CREATE}?uuid=${encodeURIComponent(row.uuid)}${canEdit ? '' : '&view=1'}" title="${canEdit ? 'Edit' : 'View'}"><i class="fa ${canEdit ? 'fa-pen' : 'fa-eye'}"></i></a>`);
    if (canDelete) items.push(`<button type="button" class="btn btn-light btn-sm text-danger js-hospital-delete" data-id="${row.id}" title="Delete"><i class="fa fa-trash"></i></button>`);
    return items.join('') || '<span class="text-muted small">No actions</span>';
  }

  function rowHtml(scope, row) {
    const contact = row.phone_number || row.email || '—';
    const location = [row.city, row.state].filter(Boolean).join(', ') || '—';

    return `
      <tr>
        <td>
          <div class="d-flex align-items-center gap-3">
            ${logoCell(row)}
            <div>
              <div class="fw-semibold">${escapeHtml(row.name || '—')}</div>
              <div class="small text-muted">${escapeHtml(row.hospital_code || row.uuid || '—')}</div>
            </div>
          </div>
        </td>
        <td>
          <div>${escapeHtml(row.hospital_type || '—')}</div>
          <div class="small text-muted">${escapeHtml(row.ownership_type || '')}</div>
        </td>
        <td>${escapeHtml(location)}</td>
        <td>${escapeHtml(contact)}</td>
        <td>${statusBadge(row.status)}</td>
        <td class="text-end">
          <div class="d-flex justify-content-end gap-1 flex-wrap">
            ${actionButtons(scope, row)}
          </div>
        </td>
      </tr>
    `;
  }

  function clearScope(scope) {
    els.rows[scope].innerHTML = '';
    els.empty[scope].style.display = 'none';
    els.meta[scope].textContent = '—';
  }

  function renderPager(scope, meta) {
    const pager = els.pager[scope];
    pager.innerHTML = '';

    const total = Number(meta.total || 0);
    const perPage = Number(meta.per_page || state.perPage || 20);
    const page = Number(meta.page || state.pages[scope] || 1);
    const totalPages = Math.max(1, Math.ceil(total / perPage));
    state.pages[scope] = page;

    const item = (disabled, active, label, target) => `
      <li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}">
        <a class="page-link" href="javascript:void(0)" data-scope="${scope}" data-page="${target || ''}">${label}</a>
      </li>
    `;

    let html = '';
    html += item(page <= 1, false, 'Previous', page - 1);
    for (let p = Math.max(1, page - 2); p <= Math.min(totalPages, page + 2); p++) {
      html += item(false, p === page, p, p);
    }
    html += item(page >= totalPages, false, 'Next', page + 1);
    pager.innerHTML = html;

    pager.querySelectorAll('a.page-link[data-page]').forEach(link => {
      link.addEventListener('click', function () {
        const target = Number(this.dataset.page || 0);
        if (!target || target < 1 || target === state.pages[scope]) return;
        state.pages[scope] = target;
        load(scope);
      });
    });

    els.meta[scope].textContent = total
      ? `Showing page ${page} of ${totalPages} — ${total} result(s)`
      : '0 result(s)';
  }

  function currentUrl(scope) {
    const params = new URLSearchParams();
    params.set('page', String(state.pages[scope] || 1));
    params.set('per_page', String(state.perPage || 20));
    if (state.q) params.set('q', state.q);

    if (scope === 'bin') return `${API}/bin?${params.toString()}`;
    params.set('status', scope);
    return `${API}?${params.toString()}`;
  }

  async function load(scope) {
    clearScope(scope);

    try {
      const res = await fetch(currentUrl(scope), { headers: authHeaders() });
      const js = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(js.message || 'Failed to load hospitals');

      const rows = Array.isArray(js.data) ? js.data : [];
      if (!rows.length) {
        els.empty[scope].style.display = '';
      } else {
        els.rows[scope].innerHTML = rows.map(row => rowHtml(scope, row)).join('');
      }

      renderPager(scope, js.meta || {});
    } catch (e) {
      els.empty[scope].style.display = '';
      els.meta[scope].textContent = 'Failed to load';
      err(e.message || 'Failed to load hospitals');
    }
  }

  async function deleteHospital(id) {
    const confirm = await Swal.fire({
      icon: 'warning',
      title: 'Delete hospital?',
      text: 'This will move the hospital to Bin.',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      confirmButtonColor: '#ef4444'
    });
    if (!confirm.isConfirmed) return;

    try {
      const res = await fetch(`${API}/${encodeURIComponent(id)}`, { method: 'DELETE', headers: authHeaders() });
      const js = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(js.message || 'Failed to delete hospital');
      ok(js.message || 'Hospital deleted');
      await load(state.activeScope);
      await load('bin');
    } catch (e) {
      err(e.message || 'Failed to delete hospital');
    }
  }

  async function restoreHospital(id) {
    const confirm = await Swal.fire({
      icon: 'question',
      title: 'Restore hospital?',
      showCancelButton: true,
      confirmButtonText: 'Restore'
    });
    if (!confirm.isConfirmed) return;

    try {
      const res = await fetch(`${API}/${encodeURIComponent(id)}/restore`, { method: 'POST', headers: authHeaders() });
      const js = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(js.message || 'Failed to restore hospital');
      ok(js.message || 'Hospital restored');
      await load('bin');
      await load('active');
    } catch (e) {
      err(e.message || 'Failed to restore hospital');
    }
  }

  async function forceDeleteHospital(id) {
    const confirm = await Swal.fire({
      icon: 'warning',
      title: 'Delete permanently?',
      text: 'This action cannot be undone.',
      showCancelButton: true,
      confirmButtonText: 'Delete permanently',
      confirmButtonColor: '#dc2626'
    });
    if (!confirm.isConfirmed) return;

    try {
      const res = await fetch(`${API}/${encodeURIComponent(id)}/force`, { method: 'DELETE', headers: authHeaders() });
      const js = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(js.message || 'Failed to permanently delete hospital');
      ok(js.message || 'Hospital permanently deleted');
      await load('bin');
    } catch (e) {
      err(e.message || 'Failed to permanently delete hospital');
    }
  }

  ['active', 'inactive', 'bin'].forEach(scope => {
    els.rows[scope].addEventListener('click', function (e) {
      const deleteBtn = e.target.closest('.js-hospital-delete');
      const restoreBtn = e.target.closest('.js-hospital-restore');
      const forceBtn = e.target.closest('.js-hospital-force');

      if (deleteBtn) deleteHospital(deleteBtn.dataset.id);
      if (restoreBtn) restoreHospital(restoreBtn.dataset.id);
      if (forceBtn) forceDeleteHospital(forceBtn.dataset.id);
    });
  });

  els.btnSearch.addEventListener('click', function () {
    state.q = els.search.value.trim();
    state.pages[state.activeScope] = 1;
    load(state.activeScope);
  });

  els.search.addEventListener('keydown', function (e) {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    state.q = els.search.value.trim();
    state.pages[state.activeScope] = 1;
    load(state.activeScope);
  });

  els.perPage.addEventListener('change', function () {
    state.perPage = Number(this.value || 20);
    state.pages[state.activeScope] = 1;
    load(state.activeScope);
  });

  els.btnReset.addEventListener('click', function () {
    els.search.value = '';
    els.perPage.value = '20';
    state.q = '';
    state.perPage = 20;
    state.pages = { active: 1, inactive: 1, bin: 1 };
    load(state.activeScope);
  });

  els.btnRefresh.addEventListener('click', async function () {
    setRefreshLoading(true);
    try {
      await load(state.activeScope);
    } finally {
      setTimeout(() => setRefreshLoading(false), 250);
    }
  });

  document.querySelector('a[href="#tab-hospital-active"]')?.addEventListener('shown.bs.tab', () => {
    state.activeScope = 'active';
    load('active');
  });
  document.querySelector('a[href="#tab-hospital-inactive"]')?.addEventListener('shown.bs.tab', () => {
    state.activeScope = 'inactive';
    load('inactive');
  });
  document.querySelector('a[href="#tab-hospital-bin"]')?.addEventListener('shown.bs.tab', () => {
    state.activeScope = 'bin';
    load('bin');
  });

  (async function init() {
    await loadPermissions();
    await load('active');
  })();
});
</script>
@endpush
