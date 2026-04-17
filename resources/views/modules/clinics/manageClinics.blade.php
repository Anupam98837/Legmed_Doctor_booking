@push('styles')
<style>
.clinic-wrap{padding:2px 0}
.clinic-head{padding:10px 12px;margin-bottom:12px}
.clinic-head-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.clinic-head-copy{display:flex;align-items:center;min-height:30px}
.clinic-head-copy h1{margin:0;font-size:var(--fs-15);line-height:1.15;display:flex;align-items:center;gap:6px}
.clinic-head-copy .seg-muted{color:var(--muted-color);font-weight:500}
.clinic-head-copy .seg-sep{color:var(--muted-color);opacity:.7}
.clinic-head-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.clinic-panel,.clinic-table-card{background:var(--surface);border:1px solid var(--line-strong);border-radius:16px;box-shadow:var(--shadow-2)}
.clinic-panel{padding:12px;margin-bottom:12px}
.clinic-toolbar-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.clinic-search-wrap{position:relative;min-width:260px;flex:1 1 340px}
.clinic-search-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted-color)}
.clinic-search-wrap .form-control{padding-left:36px}
.clinic-table-card .card-body{padding:0}
.clinic-table th{font-weight:600;color:var(--muted-color);font-size:13px;white-space:nowrap;border-bottom:1px solid var(--line-strong);background:var(--surface)}
.clinic-table tbody tr{border-top:1px solid var(--line-soft)}
.clinic-table tbody tr:hover{background:var(--page-hover)}
.clinic-logo{width:42px;height:42px;border-radius:12px;object-fit:cover;border:1px solid var(--line-strong);background:var(--page)}
.clinic-logo-fallback{width:42px;height:42px;border-radius:12px;border:1px solid var(--line-strong);display:inline-flex;align-items:center;justify-content:center;background:var(--page);color:var(--muted-color)}
.clinic-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;border:1px solid var(--line-strong);background:var(--page);font-size:12px;font-weight:600}
.clinic-badge.active{background:color-mix(in oklab, var(--success-color) 14%, transparent)}
.clinic-badge.inactive{background:color-mix(in oklab, var(--danger-color) 10%, transparent)}
.clinic-empty{padding:30px 16px;text-align:center;color:var(--muted-color)}
.clinic-empty i{font-size:32px;opacity:.6;margin-bottom:10px}
.clinic-modal .modal-dialog{margin:1rem auto}
.clinic-modal .modal-content{border-radius:16px;border:1px solid var(--line-strong);background:var(--surface);max-height:calc(100vh - 2rem)}
.clinic-modal .modal-header{border-bottom:1px solid var(--line-strong)}
.clinic-modal .modal-footer{border-top:1px solid var(--line-strong)}
.clinic-modal .modal-body{overflow-y:auto;max-height:calc(100vh - 165px);-webkit-overflow-scrolling:touch}
.clinic-modal .form-control,.clinic-modal .form-select,.clinic-modal textarea{border-radius:12px;border:1px solid var(--line-strong);background:#fff}
.clinic-modal .form-control:focus,.clinic-modal .form-select:focus,.clinic-modal textarea:focus{box-shadow:0 0 0 3px color-mix(in oklab, var(--accent-color) 20%, transparent);border-color:var(--accent-color)}
.clinic-upload-inline{display:grid;grid-template-columns:56px minmax(0,1fr);gap:12px;align-items:center;padding:10px 12px;border:1px dashed var(--line-strong);border-radius:14px;background:var(--page)}
.clinic-upload-preview{width:56px;height:56px;border-radius:12px;object-fit:cover;border:1px solid var(--line-strong);background:var(--surface);display:none}
.clinic-upload-fallback{width:56px;height:56px;border-radius:12px;display:flex;align-items:center;justify-content:center;border:1px solid var(--line-strong);background:var(--surface);color:var(--muted-color);flex:0 0 56px}
.clinic-upload-meta{min-width:0}
.clinic-upload-label{display:block;font-size:12px;font-weight:600;color:var(--muted-color);margin-bottom:6px}
.clinic-upload-name{font-size:12px;color:var(--muted-color);margin-top:6px;word-break:break-word}
.clinic-switch-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px 16px}
.clinic-switch-item{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 12px;border:1px solid var(--line-strong);border-radius:12px;background:var(--page)}
html.theme-dark .clinic-panel,html.theme-dark .clinic-table-card,html.theme-dark .clinic-modal .modal-content{background:#0f172a;border-color:var(--line-strong)}
html.theme-dark .clinic-table th{background:#0f172a;border-color:var(--line-strong);color:#94a3b8}
html.theme-dark .clinic-modal .form-control,html.theme-dark .clinic-modal .form-select,html.theme-dark .clinic-modal textarea{background:#0f172a;color:#e5e7eb;border-color:var(--line-strong)}
html.theme-dark .clinic-upload-inline,html.theme-dark .clinic-upload-fallback,html.theme-dark .clinic-switch-item,html.theme-dark .clinic-logo-fallback,html.theme-dark .clinic-badge{background:#0b1220;border-color:var(--line-strong)}
@media (max-width:991.98px){.clinic-switch-grid{grid-template-columns:1fr}}
@media (max-width:575.98px){.clinic-upload-inline{grid-template-columns:1fr}.clinic-upload-fallback,.clinic-upload-preview{margin:0 auto}.clinic-upload-meta{text-align:left}}
@media (max-width:767.98px){.clinic-head-row,.clinic-toolbar-row{flex-direction:column;align-items:flex-start!important}.clinic-head-actions,.clinic-toolbar-row .btn{width:100%}.clinic-modal .modal-dialog{margin:0}.clinic-modal .modal-content{max-height:100vh;border-radius:0}.clinic-modal .modal-body{max-height:calc(100vh - 140px)}}
</style>
@endpush

<div class="clinic-wrap">
  <div class="panel clinic-head">
    <div class="clinic-head-row">
      <div class="clinic-head-copy">
        <h1>
          <span class="seg-muted">Clinics</span>
          <span class="seg-sep">/</span>
          <span>Manage</span>
        </h1>
      </div>
      <div class="clinic-head-actions">
        <button type="button" class="btn btn-primary" id="btnClinicCreate"><i class="fa fa-plus me-1"></i>Add Clinic</button>
        <button type="button" class="w3-icon-btn" id="btnClinicRefresh" title="Refresh" aria-label="Refresh"><i class="fa fa-rotate-right"></i></button>
      </div>
    </div>
  </div>

  <ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-clinic-active" role="tab"><i class="fa fa-circle-check me-2"></i>Active</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-clinic-inactive" role="tab"><i class="fa fa-circle-xmark me-2"></i>Inactive</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-clinic-bin" role="tab"><i class="fa fa-trash-can me-2"></i>Bin</a></li>
  </ul>

  <div class="clinic-panel">
    <div class="clinic-toolbar-row">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <label class="text-muted small mb-0">Per page</label>
        <select id="clinicPerPage" class="form-select" style="width:96px">
          <option>10</option>
          <option selected>20</option>
          <option>30</option>
          <option>50</option>
          <option>100</option>
        </select>
      </div>
      <div class="clinic-search-wrap">
        <i class="fa fa-search"></i>
        <input id="clinicSearch" type="text" class="form-control" placeholder="Search clinics, code, city, state or contact">
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <button id="btnClinicSearch" class="btn btn-primary"><i class="fa fa-search me-1"></i>Search</button>
        <button id="btnClinicReset" class="btn btn-light"><i class="fa fa-rotate-left me-1"></i>Reset</button>
      </div>
    </div>
  </div>

  <div class="tab-content">
    @foreach (['active', 'inactive', 'bin'] as $scope)
      <div class="tab-pane fade {{ $scope === 'active' ? 'show active' : '' }}" id="tab-clinic-{{ $scope }}" role="tabpanel">
        <div class="card clinic-table-card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover table-borderless align-middle mb-0 clinic-table">
                <thead class="sticky-top">
                  <tr>
                    <th>Clinic</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th class="text-end" style="width:200px">Actions</th>
                  </tr>
                </thead>
                <tbody id="rows-clinic-{{ $scope }}"></tbody>
              </table>
            </div>
            <div id="empty-clinic-{{ $scope }}" class="clinic-empty" style="display:none">
              <i class="fa-solid fa-clinic-medical"></i>
              <div>No clinics in this tab.</div>
            </div>
            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 gap-2">
              <div class="text-muted small" id="meta-clinic-{{ $scope }}">—</div>
              <nav><ul id="pager-clinic-{{ $scope }}" class="pagination mb-0"></ul></nav>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>

<div class="modal fade clinic-modal" id="clinicModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-fullscreen-md-down modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clinicModalTitle"><i class="fa-solid fa-clinic-medical me-2"></i>Add Clinic</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="clinicForm" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" id="clinicId">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Clinic Name <span class="text-danger">*</span></label><input id="clinicName" class="form-control" maxlength="255" required></div>
            <div class="col-md-3"><label class="form-label">Short Name</label><input id="clinicShortName" class="form-control" maxlength="120"></div>
            <div class="col-md-3"><label class="form-label">Clinic Code</label><input id="clinicCode" class="form-control" maxlength="80"></div>
            <div class="col-md-4"><label class="form-label">Clinic Type</label><input id="clinicType" class="form-control" maxlength="100" placeholder="Chamber, OPD, Branch"></div>
            <div class="col-md-2"><label class="form-label">Status</label><select id="clinicStatus" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="col-md-2"><label class="form-label">Sort Order</label><input id="clinicSortOrder" class="form-control" type="number" min="0" value="0"></div>
            <div class="col-md-4"><label class="form-label">Website</label><input id="clinicWebsite" class="form-control" maxlength="255" type="url" placeholder="https://"></div>

            <div class="col-md-4"><label class="form-label">Email</label><input id="clinicEmail" class="form-control" type="email" maxlength="255"></div>
            <div class="col-md-4"><label class="form-label">Phone Number</label><input id="clinicPhoneNumber" class="form-control" maxlength="32"></div>
            <div class="col-md-4"><label class="form-label">Alternative Phone</label><input id="clinicAlternativePhoneNumber" class="form-control" maxlength="32"></div>
            <div class="col-md-4"><label class="form-label">WhatsApp</label><input id="clinicWhatsappNumber" class="form-control" maxlength="32"></div>

            <div class="col-md-6"><label class="form-label">Address Line 1</label><input id="clinicAddressLine1" class="form-control" maxlength="255"></div>
            <div class="col-md-6"><label class="form-label">Address Line 2</label><input id="clinicAddressLine2" class="form-control" maxlength="255"></div>
            <div class="col-md-4"><label class="form-label">Landmark</label><input id="clinicLandmark" class="form-control" maxlength="255"></div>
            <div class="col-md-4"><label class="form-label">Area</label><input id="clinicArea" class="form-control" maxlength="150"></div>
            <div class="col-md-4"><label class="form-label">Pincode</label><input id="clinicPincode" class="form-control" maxlength="20"></div>
            <div class="col-md-3"><label class="form-label">City</label><input id="clinicCity" class="form-control" maxlength="120"></div>
            <div class="col-md-3"><label class="form-label">State</label><input id="clinicState" class="form-control" maxlength="120"></div>
            <div class="col-md-3"><label class="form-label">Country</label><input id="clinicCountry" class="form-control" maxlength="120" value="India"></div>
            <div class="col-md-3"><label class="form-label">Map URL</label><input id="clinicMapUrl" class="form-control" maxlength="500" placeholder="https://maps.google.com/..."></div>
            <div class="col-md-3"><label class="form-label">Latitude</label><input id="clinicLatitude" class="form-control" type="number" step="0.0000001"></div>
            <div class="col-md-3"><label class="form-label">Longitude</label><input id="clinicLongitude" class="form-control" type="number" step="0.0000001"></div>

            <div class="col-md-6">
              <div class="clinic-upload-inline">
                <div>
                  <div id="clinicLogoFallback" class="clinic-upload-fallback"><i class="fa-solid fa-clinic-medical"></i></div>
                  <img id="clinicLogoPreview" class="clinic-upload-preview" alt="Logo preview">
                </div>
                <div class="clinic-upload-meta">
                  <label class="clinic-upload-label" for="clinicLogo">Logo</label>
                  <input id="clinicLogo" class="form-control" type="file" accept="image/*">
                  <div id="clinicLogoName" class="clinic-upload-name">No logo selected</div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="clinic-upload-inline">
                <div>
                  <div id="clinicImageFallback" class="clinic-upload-fallback"><i class="fa fa-image"></i></div>
                  <img id="clinicImagePreview" class="clinic-upload-preview" alt="Image preview">
                </div>
                <div class="clinic-upload-meta">
                  <label class="clinic-upload-label" for="clinicImage">Image</label>
                  <input id="clinicImage" class="form-control" type="file" accept="image/*">
                  <div id="clinicImageName" class="clinic-upload-name">No image selected</div>
                </div>
              </div>
            </div>

            <div class="col-12"><label class="form-label">Description</label><textarea id="clinicDescription" class="form-control" rows="3"></textarea></div>
            <div class="col-12"><label class="form-label">Short Description</label><textarea id="clinicShortDescription" class="form-control" rows="2"></textarea></div>
            <div class="col-md-6"><label class="form-label">Gallery URLs / Paths</label><textarea id="clinicGallery" class="form-control" rows="3" placeholder="One per line or comma separated"></textarea></div>
            <div class="col-md-6"><label class="form-label">Facilities</label><textarea id="clinicFacilities" class="form-control" rows="3" placeholder="MRI, Pharmacy, Waiting Lounge"></textarea></div>
            <div class="col-md-6"><label class="form-label">Timings JSON</label><textarea id="clinicTimings" class="form-control" rows="4" placeholder='{"monday":"10 AM - 7 PM"}'></textarea></div>
            <div class="col-md-6"><label class="form-label">Social Links JSON</label><textarea id="clinicSocialLinks" class="form-control" rows="4" placeholder='{"facebook":"https://..."}'></textarea></div>
            <div class="col-12"><label class="form-label">Metadata JSON</label><textarea id="clinicMetadata" class="form-control" rows="4" placeholder='{"featured":true}'></textarea></div>

            <div class="col-12">
              <div class="clinic-switch-grid">
                <label class="clinic-switch-item"><span>Online Consultation Available</span><input id="clinicOnlineConsultationAvailable" class="form-check-input" type="checkbox"></label>
                <label class="clinic-switch-item"><span>Appointment Booking Available</span><input id="clinicAppointmentBookingAvailable" class="form-check-input" type="checkbox" checked></label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="clinicSubmitBtn"><i class="fa fa-save me-1"></i>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:2100">
  <div id="clinicToastOk" class="toast text-bg-success border-0"><div class="d-flex"><div id="clinicToastOkText" class="toast-body">Done</div><button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button></div></div>
  <div id="clinicToastErr" class="toast text-bg-danger border-0 mt-2"><div class="d-flex"><div id="clinicToastErrText" class="toast-body">Something went wrong</div><button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button></div></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.__CLINICS_MANAGE_INIT__) return;
  window.__CLINICS_MANAGE_INIT__ = true;

  const token = localStorage.getItem('token') || sessionStorage.getItem('token') || '';
  if (!token) { window.location.href = '/'; return; }

  const API = '/api/clinics';
  const PAGE = '/clinics/manage';
  const okToast = new bootstrap.Toast(document.getElementById('clinicToastOk'));
  const errToast = new bootstrap.Toast(document.getElementById('clinicToastErr'));
  const ok = m => { document.getElementById('clinicToastOkText').textContent = m || 'Done'; okToast.show(); };
  const err = m => { document.getElementById('clinicToastErrText').textContent = m || 'Something went wrong'; errToast.show(); };

  const state = { activeScope: 'active', q: '', perPage: 20, permissions: { all: false, actions: new Set() }, pages: { active: 1, inactive: 1, bin: 1 } };
  const modalEl = document.getElementById('clinicModal');
  const modal = new bootstrap.Modal(modalEl);
  const els = {
    btnCreate: document.getElementById('btnClinicCreate'),
    btnRefresh: document.getElementById('btnClinicRefresh'),
    btnSearch: document.getElementById('btnClinicSearch'),
    btnReset: document.getElementById('btnClinicReset'),
    search: document.getElementById('clinicSearch'),
    perPage: document.getElementById('clinicPerPage'),
    form: document.getElementById('clinicForm'),
    modalTitle: document.getElementById('clinicModalTitle'),
    submitBtn: document.getElementById('clinicSubmitBtn'),
    id: document.getElementById('clinicId'),
    name: document.getElementById('clinicName'),
    shortName: document.getElementById('clinicShortName'),
    code: document.getElementById('clinicCode'),
    type: document.getElementById('clinicType'),
    email: document.getElementById('clinicEmail'),
    phone: document.getElementById('clinicPhoneNumber'),
    altPhone: document.getElementById('clinicAlternativePhoneNumber'),
    whatsapp: document.getElementById('clinicWhatsappNumber'),
    website: document.getElementById('clinicWebsite'),
    description: document.getElementById('clinicDescription'),
    shortDescription: document.getElementById('clinicShortDescription'),
    address1: document.getElementById('clinicAddressLine1'),
    address2: document.getElementById('clinicAddressLine2'),
    landmark: document.getElementById('clinicLandmark'),
    area: document.getElementById('clinicArea'),
    city: document.getElementById('clinicCity'),
    state: document.getElementById('clinicState'),
    country: document.getElementById('clinicCountry'),
    pincode: document.getElementById('clinicPincode'),
    latitude: document.getElementById('clinicLatitude'),
    longitude: document.getElementById('clinicLongitude'),
    mapUrl: document.getElementById('clinicMapUrl'),
    gallery: document.getElementById('clinicGallery'),
    facilities: document.getElementById('clinicFacilities'),
    timings: document.getElementById('clinicTimings'),
    socialLinks: document.getElementById('clinicSocialLinks'),
    metadata: document.getElementById('clinicMetadata'),
    onlineConsultation: document.getElementById('clinicOnlineConsultationAvailable'),
    appointmentBooking: document.getElementById('clinicAppointmentBookingAvailable'),
    status: document.getElementById('clinicStatus'),
    sortOrder: document.getElementById('clinicSortOrder'),
    logo: document.getElementById('clinicLogo'),
    image: document.getElementById('clinicImage'),
    logoPreview: document.getElementById('clinicLogoPreview'),
    imagePreview: document.getElementById('clinicImagePreview'),
    logoFallback: document.getElementById('clinicLogoFallback'),
    imageFallback: document.getElementById('clinicImageFallback'),
    logoName: document.getElementById('clinicLogoName'),
    imageName: document.getElementById('clinicImageName'),
    rows: { active: document.getElementById('rows-clinic-active'), inactive: document.getElementById('rows-clinic-inactive'), bin: document.getElementById('rows-clinic-bin') },
    pager: { active: document.getElementById('pager-clinic-active'), inactive: document.getElementById('pager-clinic-inactive'), bin: document.getElementById('pager-clinic-bin') },
    meta: { active: document.getElementById('meta-clinic-active'), inactive: document.getElementById('meta-clinic-inactive'), bin: document.getElementById('meta-clinic-bin') },
    empty: { active: document.getElementById('empty-clinic-active'), inactive: document.getElementById('empty-clinic-inactive'), bin: document.getElementById('empty-clinic-bin') }
  };

  const authHeaders = (extra = {}) => Object.assign({ Authorization: 'Bearer ' + token, Accept: 'application/json' }, extra);
  const hasAction = (...names) => state.permissions.all || names.some(name => state.permissions.actions.has(String(name).toLowerCase()));
  const esc = value => String(value ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');

  async function api(url, opts = {}) {
    const res = await fetch(url, { ...opts, headers: authHeaders(opts.headers || {}) });
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
            if (href === currentPath) (page.actions || []).forEach(action => actions.add(String(action).toLowerCase()));
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
    inputEl.addEventListener('change', () => {
      const file = inputEl.files && inputEl.files[0] ? inputEl.files[0] : null;
      if (!file) return setPreview(imageEl, fallbackEl, nameEl, '', 'No file selected');
      const objectUrl = URL.createObjectURL(file);
      setPreview(imageEl, fallbackEl, nameEl, objectUrl, file.name);
    });
  }

  const statusBadge = status => {
    const s = String(status || 'active').toLowerCase();
    return `<span class="clinic-badge ${esc(s)}"><i class="fa ${s === 'active' ? 'fa-circle-check' : 'fa-circle-xmark'}"></i>${esc(s)}</span>`;
  };

  function logoHtml(row) {
    if (row.logo) return `<img src="${esc(row.logo)}" alt="${esc(row.name || 'Clinic')}" class="clinic-logo">`;
    const letters = String(row.short_name || row.name || 'C').trim().substring(0, 2).toUpperCase();
    return `<span class="clinic-logo-fallback">${esc(letters)}</span>`;
  }

  function actionButtons(scope, row) {
    if (scope === 'bin') {
      const items = [];
      if (hasAction('restore')) items.push(`<button type="button" class="btn btn-light btn-sm js-clinic-restore" data-id="${row.id}" title="Restore"><i class="fa fa-rotate-left"></i></button>`);
      if (hasAction('force_delete', 'force-delete')) items.push(`<button type="button" class="btn btn-light btn-sm text-danger js-clinic-force" data-id="${row.id}" title="Delete permanently"><i class="fa fa-skull-crossbones"></i></button>`);
      return items.join('') || '<span class="text-muted small">No actions</span>';
    }

    const items = [];
    if (hasAction('update', 'edit')) items.push(`<button type="button" class="btn btn-light btn-sm js-clinic-edit" data-id="${row.id}" title="Edit"><i class="fa fa-pen"></i></button>`);
    if (hasAction('delete', 'destroy')) items.push(`<button type="button" class="btn btn-light btn-sm text-danger js-clinic-delete" data-id="${row.id}" title="Delete"><i class="fa fa-trash"></i></button>`);
    return items.join('') || '<span class="text-muted small">No actions</span>';
  }

  function rowHtml(scope, row) {
    const location = [row.city, row.state].filter(Boolean).join(', ') || '—';
    const contact = row.phone_number || row.email || '—';
    return `
      <tr>
        <td><div class="d-flex align-items-center gap-3">${logoHtml(row)}<div><div class="fw-semibold">${esc(row.name || '—')}</div><div class="small text-muted">${esc(row.clinic_code || row.uuid || '—')}</div></div></div></td>
        <td><div>${esc(row.clinic_type || '—')}</div><div class="small text-muted">${esc(row.short_name || '')}</div></td>
        <td>${esc(location)}</td>
        <td>${esc(contact)}</td>
        <td>${statusBadge(row.status)}</td>
        <td class="text-end"><div class="d-flex justify-content-end gap-1 flex-wrap">${actionButtons(scope, row)}</div></td>
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
    const item = (disabled, active, label, target) => `<li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" data-scope="${scope}" data-page="${target || ''}">${label}</a></li>`;
    let html = '';
    html += item(page <= 1, false, 'Previous', page - 1);
    for (let p = Math.max(1, page - 2); p <= Math.min(totalPages, page + 2); p++) html += item(false, p === page, p, p);
    html += item(page >= totalPages, false, 'Next', page + 1);
    pager.innerHTML = html;
    pager.querySelectorAll('a.page-link[data-page]').forEach(link => link.addEventListener('click', function () {
      const target = Number(this.dataset.page || 0);
      if (!target || target < 1 || target === state.pages[scope]) return;
      state.pages[scope] = target;
      load(scope);
    }));
    els.meta[scope].textContent = total ? `Showing page ${page} of ${totalPages} — ${total} result(s)` : '0 result(s)';
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
      err(e.message || 'Failed to load clinics');
    }
  }

  function resetForm() {
    els.form.reset();
    els.id.value = '';
    els.sortOrder.value = '0';
    els.country.value = 'India';
    els.status.value = 'active';
    els.appointmentBooking.checked = true;
    els.modalTitle.innerHTML = '<i class="fa-solid fa-clinic-medical me-2"></i>Add Clinic';
    els.submitBtn.innerHTML = '<i class="fa fa-save me-1"></i>Save';
    setPreview(els.logoPreview, els.logoFallback, els.logoName, '', 'No logo selected');
    setPreview(els.imagePreview, els.imageFallback, els.imageName, '', 'No image selected');
  }

  function fillForm(row) {
    els.id.value = row.id || '';
    els.name.value = row.name || '';
    els.shortName.value = row.short_name || '';
    els.code.value = row.clinic_code || '';
    els.type.value = row.clinic_type || '';
    els.email.value = row.email || '';
    els.phone.value = row.phone_number || '';
    els.altPhone.value = row.alternative_phone_number || '';
    els.whatsapp.value = row.whatsapp_number || '';
    els.website.value = row.website || '';
    els.description.value = row.description || '';
    els.shortDescription.value = row.short_description || '';
    els.address1.value = row.address_line_1 || '';
    els.address2.value = row.address_line_2 || '';
    els.landmark.value = row.landmark || '';
    els.area.value = row.area || '';
    els.city.value = row.city || '';
    els.state.value = row.state || '';
    els.country.value = row.country || 'India';
    els.pincode.value = row.pincode || '';
    els.latitude.value = row.latitude ?? '';
    els.longitude.value = row.longitude ?? '';
    els.mapUrl.value = row.map_url || '';
    els.gallery.value = Array.isArray(row.gallery) ? row.gallery.join('\n') : '';
    els.facilities.value = Array.isArray(row.facilities) ? row.facilities.join('\n') : '';
    els.timings.value = row.timings ? JSON.stringify(row.timings, null, 2) : '';
    els.socialLinks.value = row.social_links ? JSON.stringify(row.social_links, null, 2) : '';
    els.metadata.value = row.metadata ? JSON.stringify(row.metadata, null, 2) : '';
    els.onlineConsultation.checked = !!row.online_consultation_available;
    els.appointmentBooking.checked = !!row.appointment_booking_available;
    els.status.value = row.status || 'active';
    els.sortOrder.value = Number(row.sort_order || 0);
    els.modalTitle.innerHTML = '<i class="fa fa-pen me-2"></i>Edit Clinic';
    els.submitBtn.innerHTML = '<i class="fa fa-floppy-disk me-1"></i>Update';
    setPreview(els.logoPreview, els.logoFallback, els.logoName, row.logo || '', ((row.logo_path || row.logo || '').split('/').pop()) || 'Current logo');
    setPreview(els.imagePreview, els.imageFallback, els.imageName, row.image || '', ((row.image_path || row.image || '').split('/').pop()) || 'Current image');
  }

  async function openEdit(id) {
    try {
      const res = await api(`${API}/${encodeURIComponent(id)}`);
      const row = res.data || {};
      resetForm();
      fillForm(row);
      modal.show();
    } catch (e) {
      err(e.message || 'Failed to load clinic');
    }
  }

  function appendJsonOrThrow(fd, key, value) {
    const raw = String(value || '').trim();
    if (!raw) return;
    try {
      fd.append(key, JSON.stringify(JSON.parse(raw)));
    } catch (_) {
      throw new Error(`${key.replace(/_/g, ' ')} must be valid JSON`);
    }
  }

  function listText(value) {
    return Array.from(new Set(String(value || '').split(/[\n,]+/).map(v => v.trim()).filter(Boolean)));
  }

  function buildFormData() {
    const isEdit = !!els.id.value.trim();
    const fd = new FormData();
    if (isEdit) fd.append('_method', 'PATCH');
    fd.append('name', els.name.value.trim());
    fd.append('short_name', els.shortName.value.trim());
    fd.append('clinic_code', els.code.value.trim());
    fd.append('clinic_type', els.type.value.trim());
    fd.append('email', els.email.value.trim());
    fd.append('phone_number', els.phone.value.trim());
    fd.append('alternative_phone_number', els.altPhone.value.trim());
    fd.append('whatsapp_number', els.whatsapp.value.trim());
    fd.append('website', els.website.value.trim());
    fd.append('description', els.description.value.trim());
    fd.append('short_description', els.shortDescription.value.trim());
    fd.append('address_line_1', els.address1.value.trim());
    fd.append('address_line_2', els.address2.value.trim());
    fd.append('landmark', els.landmark.value.trim());
    fd.append('area', els.area.value.trim());
    fd.append('city', els.city.value.trim());
    fd.append('state', els.state.value.trim());
    fd.append('country', els.country.value.trim() || 'India');
    fd.append('pincode', els.pincode.value.trim());
    fd.append('latitude', els.latitude.value.trim());
    fd.append('longitude', els.longitude.value.trim());
    fd.append('map_url', els.mapUrl.value.trim());
    fd.append('gallery', JSON.stringify(listText(els.gallery.value)));
    fd.append('facilities', JSON.stringify(listText(els.facilities.value)));
    appendJsonOrThrow(fd, 'timings', els.timings.value);
    appendJsonOrThrow(fd, 'social_links', els.socialLinks.value);
    appendJsonOrThrow(fd, 'metadata', els.metadata.value);
    fd.append('online_consultation_available', els.onlineConsultation.checked ? '1' : '0');
    fd.append('appointment_booking_available', els.appointmentBooking.checked ? '1' : '0');
    fd.append('status', els.status.value);
    fd.append('sort_order', String(Number(els.sortOrder.value || 0)));
    if (els.logo.files && els.logo.files[0]) fd.append('logo', els.logo.files[0]);
    if (els.image.files && els.image.files[0]) fd.append('image', els.image.files[0]);
    return fd;
  }

  async function submitForm(ev) {
    ev.preventDefault();
    if (!els.name.value.trim()) return err('Clinic name is required');
    if (!hasAction(els.id.value ? 'update' : 'create', els.id.value ? 'edit' : 'store')) return err('You do not have permission to save from this page');

    try {
      els.submitBtn.disabled = true;
      const isEdit = !!els.id.value.trim();
      const res = await fetch(isEdit ? `${API}/${encodeURIComponent(els.id.value.trim())}` : API, {
        method: 'POST',
        headers: authHeaders(),
        body: buildFormData()
      });
      const json = await res.json().catch(() => ({}));
      if (!res.ok) {
        let message = json.message || 'Failed to save clinic';
        if (json.errors) {
          const firstKey = Object.keys(json.errors)[0];
          if (firstKey && json.errors[firstKey] && json.errors[firstKey][0]) message = json.errors[firstKey][0];
        }
        throw new Error(message);
      }

      modal.hide();
      resetForm();
      ok(json.message || 'Clinic saved');
      await load(state.activeScope);
      if (state.activeScope !== 'active') await load('active');
    } catch (e) {
      err(e.message || 'Failed to save clinic');
    } finally {
      els.submitBtn.disabled = false;
    }
  }

  async function softDelete(id) {
    const confirm = await Swal.fire({ icon: 'warning', title: 'Delete clinic?', text: 'This will move the clinic to Bin.', showCancelButton: true, confirmButtonText: 'Delete', confirmButtonColor: '#ef4444' });
    if (!confirm.isConfirmed) return;
    try {
      await api(`${API}/${encodeURIComponent(id)}`, { method: 'DELETE' });
      ok('Clinic moved to Bin');
      await load(state.activeScope);
      await load('bin');
    } catch (e) {
      err(e.message || 'Failed to delete clinic');
    }
  }

  async function restoreRow(id) {
    const confirm = await Swal.fire({ icon: 'question', title: 'Restore clinic?', showCancelButton: true, confirmButtonText: 'Restore' });
    if (!confirm.isConfirmed) return;
    try {
      await api(`${API}/${encodeURIComponent(id)}/restore`, { method: 'POST' });
      ok('Clinic restored');
      await load('bin');
      await load('active');
    } catch (e) {
      err(e.message || 'Failed to restore clinic');
    }
  }

  async function forceDelete(id) {
    const confirm = await Swal.fire({ icon: 'warning', title: 'Delete permanently?', text: 'This action cannot be undone.', showCancelButton: true, confirmButtonText: 'Delete permanently', confirmButtonColor: '#dc2626' });
    if (!confirm.isConfirmed) return;
    try {
      await api(`${API}/${encodeURIComponent(id)}/force`, { method: 'DELETE' });
      ok('Clinic permanently deleted');
      await load('bin');
    } catch (e) {
      err(e.message || 'Failed to permanently delete clinic');
    }
  }

  ['active','inactive','bin'].forEach(scope => {
    els.rows[scope].addEventListener('click', function (e) {
      const editBtn = e.target.closest('.js-clinic-edit');
      const deleteBtn = e.target.closest('.js-clinic-delete');
      const restoreBtn = e.target.closest('.js-clinic-restore');
      const forceBtn = e.target.closest('.js-clinic-force');
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
  els.btnSearch.addEventListener('click', () => { state.q = els.search.value.trim(); state.pages[state.activeScope] = 1; load(state.activeScope); });
  els.search.addEventListener('keydown', e => { if (e.key !== 'Enter') return; e.preventDefault(); state.q = els.search.value.trim(); state.pages[state.activeScope] = 1; load(state.activeScope); });
  els.btnReset.addEventListener('click', () => { els.search.value = ''; els.perPage.value = '20'; state.q = ''; state.perPage = 20; state.pages = { active: 1, inactive: 1, bin: 1 }; load(state.activeScope); });
  els.perPage.addEventListener('change', () => { state.perPage = Number(els.perPage.value || 20); state.pages[state.activeScope] = 1; load(state.activeScope); });
  els.btnRefresh.addEventListener('click', async () => { setRefreshLoading(true); try { await load(state.activeScope); } finally { setTimeout(() => setRefreshLoading(false), 300); } });
  els.form.addEventListener('submit', submitForm);
  modalEl.addEventListener('hidden.bs.modal', resetForm);
  wirePreview(els.logo, els.logoPreview, els.logoFallback, els.logoName);
  wirePreview(els.image, els.imagePreview, els.imageFallback, els.imageName);
  document.querySelector('a[href="#tab-clinic-active"]')?.addEventListener('shown.bs.tab', () => { state.activeScope = 'active'; load('active'); });
  document.querySelector('a[href="#tab-clinic-inactive"]')?.addEventListener('shown.bs.tab', () => { state.activeScope = 'inactive'; load('inactive'); });
  document.querySelector('a[href="#tab-clinic-bin"]')?.addEventListener('shown.bs.tab', () => { state.activeScope = 'bin'; load('bin'); });

  (async function init() {
    await loadPermissions();
    await load('active');
  })();
});
</script>
@endpush
