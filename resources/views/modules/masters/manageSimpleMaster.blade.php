@php
  $master = array_merge([
      'key' => 'master',
      'page_path' => '/masters/manage',
      'api_base' => '/api/masters',
      'plural' => 'Masters',
      'singular' => 'Master',
      'icon' => 'fa-solid fa-layer-group',
      'fields' => [],
      'labels' => [],
  ], $master ?? []);

  $fields = array_merge([
      'short_form' => false,
      'code' => false,
      'type' => false,
      'country' => false,
      'state' => false,
      'website' => false,
      'default_price' => false,
      'default_duration_minutes' => false,
      'icon_upload' => false,
      'image_upload' => false,
      'metadata' => true,
  ], $master['fields'] ?? []);

  $labels = array_merge([
      'short_form' => 'Short Form',
      'code' => 'Code',
      'type' => 'Type',
      'country' => 'Country',
      'state' => 'State',
      'website' => 'Website',
      'default_price' => 'Default Price',
      'default_duration_minutes' => 'Default Duration (Minutes)',
      'metadata' => 'Metadata JSON',
  ], $master['labels'] ?? []);

  $clientConfig = [
      'key' => $master['key'],
      'pagePath' => $master['page_path'],
      'apiBase' => $master['api_base'],
      'plural' => $master['plural'],
      'singular' => $master['singular'],
      'icon' => $master['icon'],
      'fields' => $fields,
      'labels' => $labels,
  ];
@endphp

@push('styles')
<style>
.master-wrap{padding:2px 0}
.master-head{padding:10px 12px;margin-bottom:12px}
.master-head-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.master-head-copy{display:flex;align-items:center;min-height:30px}
.master-head-copy h1{margin:0;font-size:var(--fs-15);line-height:1.15;display:flex;align-items:center;gap:6px}
.master-head-copy .seg-muted{color:var(--muted-color);font-weight:500}
.master-head-copy .seg-sep{color:var(--muted-color);opacity:.7}
.master-head-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.master-panel,.master-table-card{background:var(--surface);border:1px solid var(--line-strong);border-radius:16px;box-shadow:var(--shadow-2)}
.master-panel{padding:12px;margin-bottom:12px}
.master-toolbar-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.master-search-wrap{position:relative;min-width:260px;flex:1 1 340px}
.master-search-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted-color)}
.master-search-wrap .form-control{padding-left:36px}
.master-table-card .card-body{padding:0}
.master-table th{font-weight:600;color:var(--muted-color);font-size:13px;white-space:nowrap;border-bottom:1px solid var(--line-strong);background:var(--surface)}
.master-table tbody tr{border-top:1px solid var(--line-soft)}
.master-table tbody tr:hover{background:var(--page-hover)}
.master-avatar{width:42px;height:42px;border-radius:12px;object-fit:cover;border:1px solid var(--line-strong);background:var(--page)}
.master-avatar-fallback{width:42px;height:42px;border-radius:12px;border:1px solid var(--line-strong);display:inline-flex;align-items:center;justify-content:center;background:var(--page);color:var(--muted-color)}
.master-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;border:1px solid var(--line-strong);background:var(--page);font-size:12px;font-weight:600}
.master-badge.active{background:color-mix(in oklab, var(--success-color) 14%, transparent)}
.master-badge.inactive{background:color-mix(in oklab, var(--danger-color) 10%, transparent)}
.master-empty{padding:30px 16px;text-align:center;color:var(--muted-color)}
.master-empty i{font-size:32px;opacity:.6;margin-bottom:10px}
.master-modal .modal-dialog{margin:1rem auto}
.master-modal .modal-content{border-radius:16px;border:1px solid var(--line-strong);background:var(--surface);max-height:calc(100vh - 2rem)}
.master-modal .modal-header{border-bottom:1px solid var(--line-strong)}
.master-modal .modal-footer{border-top:1px solid var(--line-strong)}
.master-modal .modal-body{overflow-y:auto;max-height:calc(100vh - 165px);-webkit-overflow-scrolling:touch}
.master-modal .form-control,.master-modal .form-select,.master-modal textarea{border-radius:12px;border:1px solid var(--line-strong);background:#fff}
.master-modal .form-control:focus,.master-modal .form-select:focus,.master-modal textarea:focus{box-shadow:0 0 0 3px color-mix(in oklab, var(--accent-color) 20%, transparent);border-color:var(--accent-color)}
.master-upload-inline{display:grid;grid-template-columns:56px minmax(0,1fr);gap:12px;align-items:center;padding:10px 12px;border:1px dashed var(--line-strong);border-radius:14px;background:var(--page)}
.master-upload-preview{width:56px;height:56px;border-radius:12px;object-fit:cover;border:1px solid var(--line-strong);background:var(--surface);display:none}
.master-upload-fallback{width:56px;height:56px;border-radius:12px;display:flex;align-items:center;justify-content:center;border:1px solid var(--line-strong);background:var(--surface);color:var(--muted-color);flex:0 0 56px}
.master-upload-meta{min-width:0}
.master-upload-label{display:block;font-size:12px;font-weight:600;color:var(--muted-color);margin-bottom:6px}
.master-upload-name{font-size:12px;color:var(--muted-color);margin-top:6px;word-break:break-word}
.master-form-grid{align-items:start}
html.theme-dark .master-panel,html.theme-dark .master-table-card,html.theme-dark .master-modal .modal-content{background:#0f172a;border-color:var(--line-strong)}
html.theme-dark .master-table th{background:#0f172a;border-color:var(--line-strong);color:#94a3b8}
html.theme-dark .master-modal .form-control,html.theme-dark .master-modal .form-select,html.theme-dark .master-modal textarea{background:#0f172a;color:#e5e7eb;border-color:var(--line-strong)}
html.theme-dark .master-avatar-fallback,html.theme-dark .master-badge,html.theme-dark .master-upload-card,html.theme-dark .master-upload-fallback{background:#0b1220;border-color:var(--line-strong)}
@media (max-width:575.98px){.master-upload-inline{grid-template-columns:1fr}.master-upload-fallback,.master-upload-preview{margin:0 auto}.master-upload-meta{text-align:left}}
@media (max-width:767.98px){.master-head-row,.master-toolbar-row{flex-direction:column;align-items:flex-start!important}.master-head-actions,.master-toolbar-row .btn{width:100%}.master-modal .modal-dialog{margin:0}.master-modal .modal-content{max-height:100vh;border-radius:0}.master-modal .modal-body{max-height:calc(100vh - 140px)}}
</style>
@endpush

<div class="master-wrap">
  <div class="panel master-head">
    <div class="master-head-row">
      <div class="master-head-copy">
        <h1>
          <span class="seg-muted">{{ $master['plural'] }}</span>
          <span class="seg-sep">/</span>
          <span>Manage</span>
        </h1>
      </div>
      <div class="master-head-actions">
        <button type="button" class="btn btn-primary" id="btnMasterCreate">
          <i class="fa fa-plus me-1"></i>Add {{ $master['singular'] }}
        </button>
        <button type="button" class="w3-icon-btn" id="btnMasterRefresh" title="Refresh" aria-label="Refresh">
          <i class="fa fa-rotate-right"></i>
        </button>
      </div>
    </div>
  </div>

  <ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-master-active" role="tab"><i class="fa fa-circle-check me-2"></i>Active</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-master-inactive" role="tab"><i class="fa fa-circle-xmark me-2"></i>Inactive</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-master-bin" role="tab"><i class="fa fa-trash-can me-2"></i>Bin</a></li>
  </ul>

  <div class="master-panel">
    <div class="master-toolbar-row">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <label class="text-muted small mb-0">Per page</label>
        <select id="masterPerPage" class="form-select" style="width:96px">
          <option>10</option>
          <option selected>20</option>
          <option>30</option>
          <option>50</option>
          <option>100</option>
        </select>
      </div>

      <div class="master-search-wrap">
        <i class="fa fa-search"></i>
        <input id="masterSearch" type="text" class="form-control" placeholder="Search {{ strtolower($master['plural']) }}">
      </div>

      <div class="d-flex gap-2 flex-wrap">
        <button id="btnMasterSearch" class="btn btn-primary"><i class="fa fa-search me-1"></i>Search</button>
        <button id="btnMasterReset" class="btn btn-light"><i class="fa fa-rotate-left me-1"></i>Reset</button>
      </div>
    </div>
  </div>

  <div class="tab-content">
    @foreach (['active', 'inactive', 'bin'] as $scope)
      <div class="tab-pane fade {{ $scope === 'active' ? 'show active' : '' }}" id="tab-master-{{ $scope }}" role="tabpanel">
        <div class="card master-table-card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover table-borderless align-middle mb-0 master-table">
                <thead class="sticky-top">
                  <tr>
                    <th>{{ $master['singular'] }}</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Sort</th>
                    <th>Description</th>
                    <th class="text-end" style="width:200px">Actions</th>
                  </tr>
                </thead>
                <tbody id="rows-master-{{ $scope }}"></tbody>
              </table>
            </div>
            <div id="empty-master-{{ $scope }}" class="master-empty" style="display:none">
              <i class="{{ $master['icon'] }}"></i>
              <div>No {{ strtolower($master['plural']) }} in this tab.</div>
            </div>
            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 gap-2">
              <div class="text-muted small" id="meta-master-{{ $scope }}">—</div>
              <nav><ul id="pager-master-{{ $scope }}" class="pagination mb-0"></ul></nav>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>

<div class="modal fade master-modal" id="masterModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-fullscreen-md-down modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="masterModalTitle"><i class="{{ $master['icon'] }} me-2"></i>Add {{ $master['singular'] }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="masterForm" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" id="masterId">
          <div class="row g-3 master-form-grid">
            <div class="col-lg-5 col-md-6">
              <label class="form-label">{{ $master['singular'] }} Name <span class="text-danger">*</span></label>
              <input id="masterName" class="form-control" maxlength="180" required>
            </div>

            @if ($fields['short_form'])
              <div class="col-lg-3 col-md-6">
                <label class="form-label">{{ $labels['short_form'] }}</label>
                <input id="masterShortForm" class="form-control" maxlength="40">
              </div>
            @endif

            @if ($fields['code'])
              <div class="col-lg-3 col-md-6">
                <label class="form-label">{{ $labels['code'] }}</label>
                <input id="masterCode" class="form-control" maxlength="40">
              </div>
            @endif

            @if ($fields['type'])
              <div class="col-lg-4 col-md-6">
                <label class="form-label">{{ $labels['type'] }}</label>
                <input id="masterType" class="form-control" maxlength="100">
              </div>
            @endif

            @if ($fields['country'])
              <div class="col-lg-4 col-md-6">
                <label class="form-label">{{ $labels['country'] }}</label>
                <input id="masterCountry" class="form-control" maxlength="120" value="India">
              </div>
            @endif

            @if ($fields['state'])
              <div class="col-lg-4 col-md-6">
                <label class="form-label">{{ $labels['state'] }}</label>
                <input id="masterState" class="form-control" maxlength="120">
              </div>
            @endif

            @if ($fields['website'])
              <div class="col-lg-6 col-md-6">
                <label class="form-label">{{ $labels['website'] }}</label>
                <input id="masterWebsite" class="form-control" maxlength="255" type="url" placeholder="https://">
              </div>
            @endif

            @if ($fields['default_price'])
              <div class="col-lg-3 col-md-6">
                <label class="form-label">{{ $labels['default_price'] }}</label>
                <input id="masterDefaultPrice" class="form-control" type="number" min="0" step="0.01">
              </div>
            @endif

            @if ($fields['default_duration_minutes'])
              <div class="col-lg-3 col-md-6">
                <label class="form-label">{{ $labels['default_duration_minutes'] }}</label>
                <input id="masterDefaultDurationMinutes" class="form-control" type="number" min="0">
              </div>
            @endif

            <div class="col-lg-2 col-md-6">
              <label class="form-label">Status</label>
              <select id="masterStatus" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>

            <div class="col-lg-2 col-md-6">
              <label class="form-label">Sort Order</label>
              <input id="masterSortOrder" class="form-control" type="number" min="0" value="0">
            </div>

            @if ($fields['icon_upload'])
              <div class="col-md-6">
                <div class="master-upload-inline">
                  <div>
                    <div id="masterIconFallback" class="master-upload-fallback"><i class="{{ $master['icon'] }}"></i></div>
                    <img id="masterIconPreview" class="master-upload-preview" alt="Icon preview">
                  </div>
                  <div class="master-upload-meta">
                    <label class="master-upload-label" for="masterIcon">Icon</label>
                    <input id="masterIcon" class="form-control" type="file" accept="image/*">
                    <div id="masterIconName" class="master-upload-name">No icon selected</div>
                  </div>
                </div>
              </div>
            @endif

            @if ($fields['image_upload'])
              <div class="col-md-6">
                <div class="master-upload-inline">
                  <div>
                    <div id="masterImageFallback" class="master-upload-fallback"><i class="fa fa-image"></i></div>
                    <img id="masterImagePreview" class="master-upload-preview" alt="Image preview">
                  </div>
                  <div class="master-upload-meta">
                    <label class="master-upload-label" for="masterImage">Image</label>
                    <input id="masterImage" class="form-control" type="file" accept="image/*">
                    <div id="masterImageName" class="master-upload-name">No image selected</div>
                  </div>
                </div>
              </div>
            @endif

            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea id="masterDescription" class="form-control" rows="4"></textarea>
            </div>

            @if ($fields['metadata'])
              <div class="col-12">
                <label class="form-label">{{ $labels['metadata'] }}</label>
                <textarea id="masterMetadata" class="form-control" rows="4" placeholder='{"featured":true}'></textarea>
              </div>
            @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="masterSubmitBtn">
            <i class="fa fa-save me-1"></i>Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:2100">
  <div id="masterToastOk" class="toast text-bg-success border-0">
    <div class="d-flex">
      <div id="masterToastOkText" class="toast-body">Done</div>
      <button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button>
    </div>
  </div>
  <div id="masterToastErr" class="toast text-bg-danger border-0 mt-2">
    <div class="d-flex">
      <div id="masterToastErrText" class="toast-body">Something went wrong</div>
      <button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const CONFIG = @json($clientConfig);
  window.__MASTER_INIT_KEYS__ = window.__MASTER_INIT_KEYS__ || {};
  if (window.__MASTER_INIT_KEYS__[CONFIG.key]) return;
  window.__MASTER_INIT_KEYS__[CONFIG.key] = true;

  const token = localStorage.getItem('token') || sessionStorage.getItem('token') || '';
  if (!token) {
    window.location.href = '/';
    return;
  }

  const okToast = new bootstrap.Toast(document.getElementById('masterToastOk'));
  const errToast = new bootstrap.Toast(document.getElementById('masterToastErr'));
  const ok = message => {
    document.getElementById('masterToastOkText').textContent = message || 'Done';
    okToast.show();
  };
  const err = message => {
    document.getElementById('masterToastErrText').textContent = message || 'Something went wrong';
    errToast.show();
  };

  const state = {
    activeScope: 'active',
    q: '',
    perPage: 20,
    permissions: { all: false, actions: new Set() },
    pages: { active: 1, inactive: 1, bin: 1 }
  };

  const els = {
    btnCreate: document.getElementById('btnMasterCreate'),
    btnRefresh: document.getElementById('btnMasterRefresh'),
    btnSearch: document.getElementById('btnMasterSearch'),
    btnReset: document.getElementById('btnMasterReset'),
    search: document.getElementById('masterSearch'),
    perPage: document.getElementById('masterPerPage'),
    modalEl: document.getElementById('masterModal'),
    form: document.getElementById('masterForm'),
    modalTitle: document.getElementById('masterModalTitle'),
    submitBtn: document.getElementById('masterSubmitBtn'),
    id: document.getElementById('masterId'),
    name: document.getElementById('masterName'),
    shortForm: document.getElementById('masterShortForm'),
    code: document.getElementById('masterCode'),
    type: document.getElementById('masterType'),
    country: document.getElementById('masterCountry'),
    state: document.getElementById('masterState'),
    website: document.getElementById('masterWebsite'),
    defaultPrice: document.getElementById('masterDefaultPrice'),
    defaultDurationMinutes: document.getElementById('masterDefaultDurationMinutes'),
    status: document.getElementById('masterStatus'),
    sortOrder: document.getElementById('masterSortOrder'),
    description: document.getElementById('masterDescription'),
    metadata: document.getElementById('masterMetadata'),
    icon: document.getElementById('masterIcon'),
    image: document.getElementById('masterImage'),
    iconPreview: document.getElementById('masterIconPreview'),
    imagePreview: document.getElementById('masterImagePreview'),
    iconFallback: document.getElementById('masterIconFallback'),
    imageFallback: document.getElementById('masterImageFallback'),
    iconName: document.getElementById('masterIconName'),
    imageName: document.getElementById('masterImageName'),
    rows: {
      active: document.getElementById('rows-master-active'),
      inactive: document.getElementById('rows-master-inactive'),
      bin: document.getElementById('rows-master-bin')
    },
    pager: {
      active: document.getElementById('pager-master-active'),
      inactive: document.getElementById('pager-master-inactive'),
      bin: document.getElementById('pager-master-bin')
    },
    meta: {
      active: document.getElementById('meta-master-active'),
      inactive: document.getElementById('meta-master-inactive'),
      bin: document.getElementById('meta-master-bin')
    },
    empty: {
      active: document.getElementById('empty-master-active'),
      inactive: document.getElementById('empty-master-inactive'),
      bin: document.getElementById('empty-master-bin')
    }
  };

  const modal = new bootstrap.Modal(els.modalEl);

  function authHeaders(extra = {}) {
    return Object.assign({ Authorization: 'Bearer ' + token, Accept: 'application/json' }, extra);
  }

  function hasAction(...names) {
    if (state.permissions.all) return true;
    return names.some(name => state.permissions.actions.has(String(name).toLowerCase()));
  }

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  async function api(url, opts = {}) {
    const res = await fetch(url, {
      ...opts,
      headers: authHeaders(opts.headers || {})
    });

    let json = {};
    try { json = await res.json(); } catch (_) {}
    if (!res.ok) throw new Error(json.message || json.error || 'Request failed');
    return json;
  }

  async function loadPermissions() {
    try {
      const res = await api('/api/my/sidebar-menus?with_actions=1');
      if (res.tree === 'all') {
        state.permissions.all = true;
        state.permissions.actions = new Set(['view','create','update','edit','delete','destroy','restore','force_delete','force-delete']);
      } else {
        const actions = new Set();
        const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
        (Array.isArray(res.tree) ? res.tree : []).forEach(header => {
          (header.children || []).forEach(page => {
            const href = String(page.href || '').replace(/\/+$/, '') || '/';
            if (href === currentPath) {
              (page.actions || []).forEach(action => actions.add(String(action).toLowerCase()));
            }
          });
        });
        state.permissions.actions = actions;
      }
    } catch (_) {
      state.permissions.actions = new Set();
    }

    els.btnCreate.style.display = hasAction('create', 'store') ? '' : 'none';
  }

  function setRefreshLoading(on) {
    els.btnRefresh.disabled = !!on;
    els.btnRefresh.classList.toggle('is-spinning', !!on);
  }

  function setPreview(imageEl, fallbackEl, nameEl, src, fileName) {
    if (!imageEl || !fallbackEl || !nameEl) return;
    if (src) {
      imageEl.src = src;
      imageEl.style.display = 'block';
      fallbackEl.style.display = 'none';
    } else {
      imageEl.src = '';
      imageEl.style.display = 'none';
      fallbackEl.style.display = 'flex';
    }
    nameEl.textContent = fileName || 'No file selected';
  }

  function wirePreview(inputEl, imageEl, fallbackEl, nameEl) {
    inputEl?.addEventListener('change', () => {
      const file = inputEl.files && inputEl.files[0] ? inputEl.files[0] : null;
      if (!file) {
        setPreview(imageEl, fallbackEl, nameEl, '', 'No file selected');
        return;
      }
      const objectUrl = URL.createObjectURL(file);
      setPreview(imageEl, fallbackEl, nameEl, objectUrl, file.name);
    });
  }

  function statusBadge(status) {
    const s = String(status || 'active').toLowerCase();
    return `<span class="master-badge ${escapeHtml(s)}"><i class="fa ${s === 'active' ? 'fa-circle-check' : 'fa-circle-xmark'}"></i>${escapeHtml(s)}</span>`;
  }

  function avatarHtml(row) {
    const src = row.image || row.icon || '';
    if (src) {
      return `<img src="${escapeHtml(src)}" alt="${escapeHtml(row.name || CONFIG.singular)}" class="master-avatar">`;
    }
    const letters = String(row.short_form || row.code || row.name || CONFIG.singular).trim().substring(0, 2).toUpperCase();
    return `<span class="master-avatar-fallback">${escapeHtml(letters || 'NA')}</span>`;
  }

  function detailsHtml(row) {
    const bits = [];
    if (row.short_form) bits.push(`<span>${escapeHtml(CONFIG.labels.short_form)}: ${escapeHtml(row.short_form)}</span>`);
    if (row.code) bits.push(`<span>${escapeHtml(CONFIG.labels.code)}: ${escapeHtml(row.code)}</span>`);
    if (row.qualification_type) bits.push(`<span>${escapeHtml(CONFIG.labels.type)}: ${escapeHtml(row.qualification_type)}</span>`);
    if (row.country || row.state) bits.push(`<span>${escapeHtml([row.country, row.state].filter(Boolean).join(', '))}</span>`);
    if (row.website) bits.push(`<a href="${escapeHtml(row.website)}" target="_blank" rel="noopener">Website</a>`);
    if (row.default_price !== null && row.default_price !== undefined && row.default_price !== '') bits.push(`<span>${escapeHtml(CONFIG.labels.default_price)}: ${escapeHtml(row.default_price)}</span>`);
    if (row.default_duration_minutes) bits.push(`<span>${escapeHtml(CONFIG.labels.default_duration_minutes)}: ${escapeHtml(row.default_duration_minutes)}</span>`);
    bits.push(`<code>${escapeHtml(row.slug || '')}</code>`);
    return bits.join('<br>');
  }

  function actionButtons(scope, row) {
    if (scope === 'bin') {
      const items = [];
      if (hasAction('restore')) items.push(`<button type="button" class="btn btn-light btn-sm js-master-restore" data-id="${row.id}" title="Restore"><i class="fa fa-rotate-left"></i></button>`);
      if (hasAction('force_delete', 'force-delete')) items.push(`<button type="button" class="btn btn-light btn-sm text-danger js-master-force" data-id="${row.id}" title="Delete permanently"><i class="fa fa-skull-crossbones"></i></button>`);
      return items.join('') || '<span class="text-muted small">No actions</span>';
    }

    const items = [];
    if (hasAction('update', 'edit')) items.push(`<button type="button" class="btn btn-light btn-sm js-master-edit" data-id="${row.id}" title="Edit"><i class="fa fa-pen"></i></button>`);
    if (hasAction('delete', 'destroy')) items.push(`<button type="button" class="btn btn-light btn-sm text-danger js-master-delete" data-id="${row.id}" title="Delete"><i class="fa fa-trash"></i></button>`);
    return items.join('') || '<span class="text-muted small">No actions</span>';
  }

  function rowHtml(scope, row) {
    return `
      <tr>
        <td>
          <div class="d-flex align-items-center gap-3">
            ${avatarHtml(row)}
            <div>
              <div class="fw-semibold">${escapeHtml(row.name || '—')}</div>
              <div class="small text-muted">${escapeHtml(row.uuid || '—')}</div>
            </div>
          </div>
        </td>
        <td>${detailsHtml(row)}</td>
        <td>${statusBadge(row.status)}</td>
        <td>${Number(row.sort_order || 0)}</td>
        <td class="text-muted">${escapeHtml(row.description || '—')}</td>
        <td class="text-end">
          <div class="d-flex justify-content-end gap-1 flex-wrap">${actionButtons(scope, row)}</div>
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

    els.meta[scope].textContent = total ? `Showing page ${page} of ${totalPages} — ${total} result(s)` : '0 result(s)';
  }

  function currentUrl(scope) {
    const params = new URLSearchParams();
    params.set('page', String(state.pages[scope] || 1));
    params.set('per_page', String(state.perPage || 20));
    if (state.q) params.set('q', state.q);
    if (scope === 'bin') return `${CONFIG.apiBase}/bin?${params.toString()}`;
    params.set('status', scope);
    return `${CONFIG.apiBase}?${params.toString()}`;
  }

  async function load(scope) {
    clearScope(scope);
    try {
      const res = await api(currentUrl(scope));
      const rows = Array.isArray(res.data) ? res.data : [];
      if (!rows.length) {
        els.empty[scope].style.display = '';
      } else {
        els.rows[scope].innerHTML = rows.map(row => rowHtml(scope, row)).join('');
      }
      renderPager(scope, res.meta || {});
    } catch (e) {
      els.empty[scope].style.display = '';
      els.meta[scope].textContent = 'Failed to load';
      err(e.message || `Failed to load ${CONFIG.plural.toLowerCase()}`);
    }
  }

  function resetForm() {
    els.form.reset();
    els.id.value = '';
    if (els.sortOrder) els.sortOrder.value = '0';
    if (els.status) els.status.value = 'active';
    if (els.country) els.country.value = 'India';
    els.modalTitle.innerHTML = `<i class="${CONFIG.icon} me-2"></i>Add ${CONFIG.singular}`;
    els.submitBtn.innerHTML = '<i class="fa fa-save me-1"></i>Save';
    setPreview(els.iconPreview, els.iconFallback, els.iconName, '', 'No icon selected');
    setPreview(els.imagePreview, els.imageFallback, els.imageName, '', 'No image selected');
  }

  function fillForm(row) {
    els.id.value = row.id || '';
    els.name.value = row.name || '';
    if (els.shortForm) els.shortForm.value = row.short_form || '';
    if (els.code) els.code.value = row.code || '';
    if (els.type) els.type.value = row.qualification_type || row.type || '';
    if (els.country) els.country.value = row.country || 'India';
    if (els.state) els.state.value = row.state || '';
    if (els.website) els.website.value = row.website || '';
    if (els.defaultPrice) els.defaultPrice.value = row.default_price ?? '';
    if (els.defaultDurationMinutes) els.defaultDurationMinutes.value = row.default_duration_minutes ?? '';
    els.status.value = row.status || 'active';
    els.sortOrder.value = Number(row.sort_order || 0);
    els.description.value = row.description || '';
    if (els.metadata) els.metadata.value = row.metadata ? JSON.stringify(row.metadata, null, 2) : '';
    els.modalTitle.innerHTML = `<i class="fa fa-pen me-2"></i>Edit ${CONFIG.singular}`;
    els.submitBtn.innerHTML = '<i class="fa fa-floppy-disk me-1"></i>Update';
    setPreview(els.iconPreview, els.iconFallback, els.iconName, row.icon || '', ((row.icon_path || row.icon || '').split('/').pop()) || 'Current icon');
    setPreview(els.imagePreview, els.imageFallback, els.imageName, row.image || '', ((row.image_path || row.image || '').split('/').pop()) || 'Current image');
  }

  async function openEdit(id) {
    try {
      const res = await api(`${CONFIG.apiBase}/${encodeURIComponent(id)}`);
      const row = res.data || res.record || {};
      resetForm();
      fillForm(row);
      modal.show();
    } catch (e) {
      err(e.message || `Failed to load ${CONFIG.singular.toLowerCase()}`);
    }
  }

  function appendJsonOrThrow(fd, key, value) {
    if (!value || !String(value).trim()) return;
    try {
      fd.append(key, JSON.stringify(JSON.parse(String(value))));
    } catch (_) {
      throw new Error(`${key.replace(/_/g, ' ')} must be valid JSON`);
    }
  }

  function buildFormData() {
    const isEdit = !!els.id.value.trim();
    const fd = new FormData();
    if (isEdit) fd.append('_method', 'PATCH');
    fd.append('name', els.name.value.trim());
    if (els.shortForm) fd.append('short_form', els.shortForm.value.trim());
    if (els.code) fd.append('code', els.code.value.trim());
    if (els.type) {
      const key = CONFIG.key === 'qualifications' ? 'qualification_type' : 'type';
      fd.append(key, els.type.value.trim());
    }
    if (els.country) fd.append('country', els.country.value.trim() || 'India');
    if (els.state) fd.append('state', els.state.value.trim());
    if (els.website) fd.append('website', els.website.value.trim());
    if (els.defaultPrice) fd.append('default_price', els.defaultPrice.value.trim());
    if (els.defaultDurationMinutes) fd.append('default_duration_minutes', els.defaultDurationMinutes.value.trim());
    fd.append('status', els.status.value);
    fd.append('sort_order', String(Number(els.sortOrder.value || 0)));
    fd.append('description', els.description.value.trim());
    if (els.metadata) appendJsonOrThrow(fd, 'metadata', els.metadata.value);
    if (els.icon && els.icon.files && els.icon.files[0]) fd.append('icon', els.icon.files[0]);
    if (els.image && els.image.files && els.image.files[0]) fd.append('image', els.image.files[0]);
    return fd;
  }

  async function submitForm(ev) {
    ev.preventDefault();
    if (!els.name.value.trim()) return err(`${CONFIG.singular} name is required`);
    if (!hasAction(els.id.value ? 'update' : 'create', els.id.value ? 'edit' : 'store')) return err('You do not have permission to save from this page');

    try {
      els.submitBtn.disabled = true;
      const isEdit = !!els.id.value.trim();
      const res = await fetch(isEdit ? `${CONFIG.apiBase}/${encodeURIComponent(els.id.value.trim())}` : CONFIG.apiBase, {
        method: 'POST',
        headers: authHeaders(),
        body: buildFormData()
      });
      const json = await res.json().catch(() => ({}));
      if (!res.ok) {
        let message = json.message || `Failed to save ${CONFIG.singular.toLowerCase()}`;
        if (json.errors) {
          const firstKey = Object.keys(json.errors)[0];
          if (firstKey && json.errors[firstKey] && json.errors[firstKey][0]) message = json.errors[firstKey][0];
        }
        throw new Error(message);
      }

      modal.hide();
      resetForm();
      ok(json.message || `${CONFIG.singular} saved`);
      await load(state.activeScope);
      if (state.activeScope !== 'active') await load('active');
    } catch (e) {
      err(e.message || `Failed to save ${CONFIG.singular.toLowerCase()}`);
    } finally {
      els.submitBtn.disabled = false;
    }
  }

  async function softDelete(id) {
    const confirm = await Swal.fire({ icon: 'warning', title: `Delete ${CONFIG.singular.toLowerCase()}?`, text: `This will move the ${CONFIG.singular.toLowerCase()} to Bin.`, showCancelButton: true, confirmButtonText: 'Delete', confirmButtonColor: '#ef4444' });
    if (!confirm.isConfirmed) return;

    try {
      await api(`${CONFIG.apiBase}/${encodeURIComponent(id)}`, { method: 'DELETE' });
      ok(`${CONFIG.singular} moved to Bin`);
      await load(state.activeScope);
      await load('bin');
    } catch (e) {
      err(e.message || `Failed to delete ${CONFIG.singular.toLowerCase()}`);
    }
  }

  async function restoreRow(id) {
    const confirm = await Swal.fire({ icon: 'question', title: `Restore ${CONFIG.singular.toLowerCase()}?`, showCancelButton: true, confirmButtonText: 'Restore' });
    if (!confirm.isConfirmed) return;

    try {
      await api(`${CONFIG.apiBase}/${encodeURIComponent(id)}/restore`, { method: 'POST' });
      ok(`${CONFIG.singular} restored`);
      await load('bin');
      await load('active');
    } catch (e) {
      err(e.message || `Failed to restore ${CONFIG.singular.toLowerCase()}`);
    }
  }

  async function forceDelete(id) {
    const confirm = await Swal.fire({ icon: 'warning', title: 'Delete permanently?', text: 'This action cannot be undone.', showCancelButton: true, confirmButtonText: 'Delete permanently', confirmButtonColor: '#dc2626' });
    if (!confirm.isConfirmed) return;

    try {
      await api(`${CONFIG.apiBase}/${encodeURIComponent(id)}/force`, { method: 'DELETE' });
      ok(`${CONFIG.singular} permanently deleted`);
      await load('bin');
    } catch (e) {
      err(e.message || `Failed to permanently delete ${CONFIG.singular.toLowerCase()}`);
    }
  }

  ['active', 'inactive', 'bin'].forEach(scope => {
    els.rows[scope].addEventListener('click', function (e) {
      const editBtn = e.target.closest('.js-master-edit');
      const deleteBtn = e.target.closest('.js-master-delete');
      const restoreBtn = e.target.closest('.js-master-restore');
      const forceBtn = e.target.closest('.js-master-force');
      if (editBtn) openEdit(editBtn.dataset.id);
      if (deleteBtn) softDelete(deleteBtn.dataset.id);
      if (restoreBtn) restoreRow(restoreBtn.dataset.id);
      if (forceBtn) forceDelete(forceBtn.dataset.id);
    });
  });

  els.btnCreate.addEventListener('click', () => {
    if (!hasAction('create', 'store')) return;
    resetForm();
    modal.show();
  });

  els.btnSearch.addEventListener('click', () => {
    state.q = els.search.value.trim();
    state.pages[state.activeScope] = 1;
    load(state.activeScope);
  });

  els.search.addEventListener('keydown', e => {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    state.q = els.search.value.trim();
    state.pages[state.activeScope] = 1;
    load(state.activeScope);
  });

  els.btnReset.addEventListener('click', () => {
    els.search.value = '';
    els.perPage.value = '20';
    state.q = '';
    state.perPage = 20;
    state.pages = { active: 1, inactive: 1, bin: 1 };
    load(state.activeScope);
  });

  els.perPage.addEventListener('change', () => {
    state.perPage = Number(els.perPage.value || 20);
    state.pages[state.activeScope] = 1;
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

  els.form.addEventListener('submit', submitForm);
  els.modalEl.addEventListener('hidden.bs.modal', resetForm);
  wirePreview(els.icon, els.iconPreview, els.iconFallback, els.iconName);
  wirePreview(els.image, els.imagePreview, els.imageFallback, els.imageName);

  document.querySelector('a[href="#tab-master-active"]')?.addEventListener('shown.bs.tab', () => {
    state.activeScope = 'active';
    load('active');
  });
  document.querySelector('a[href="#tab-master-inactive"]')?.addEventListener('shown.bs.tab', () => {
    state.activeScope = 'inactive';
    load('inactive');
  });
  document.querySelector('a[href="#tab-master-bin"]')?.addEventListener('shown.bs.tab', () => {
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
