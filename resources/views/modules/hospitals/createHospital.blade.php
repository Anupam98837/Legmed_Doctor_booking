@push('styles')
<style>
.hosp-form-wrap{padding:2px 0}
.hosp-head{padding:10px 12px;margin-bottom:12px}
.hosp-head-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.hosp-head-copy{display:flex;align-items:center;min-height:30px}
.hosp-head-copy h1{margin:0;font-size:var(--fs-15);line-height:1.15;display:flex;align-items:center;gap:6px;flex-wrap:wrap}
.hosp-head-copy .seg-muted{color:var(--muted-color);font-weight:500}
.hosp-head-copy .seg-sep{color:var(--muted-color);opacity:.7}
.hosp-head-sub{margin-top:5px;color:var(--muted-color);font-size:12px}
.hosp-head-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.hosp-card,.hosp-panel{background:var(--surface);border:1px solid var(--line-strong);border-radius:16px;box-shadow:var(--shadow-2)}
.hosp-panel{padding:14px;margin-bottom:12px}
.hosp-section-title{font-size:14px;font-weight:700;color:var(--ink);margin:0 0 12px;display:flex;align-items:center;gap:8px}
.hosp-grid-note{font-size:12px;color:var(--muted-color)}
.hosp-upload-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.hosp-upload-card{display:flex;gap:12px;align-items:center;padding:12px;border:1px dashed var(--line-strong);border-radius:14px;background:var(--page)}
.hosp-upload-preview{width:72px;height:72px;border-radius:14px;object-fit:cover;border:1px solid var(--line-strong);background:var(--surface);display:none}
.hosp-upload-fallback{width:72px;height:72px;border-radius:14px;display:flex;align-items:center;justify-content:center;border:1px solid var(--line-strong);background:var(--surface);color:var(--muted-color);flex:0 0 72px}
.hosp-upload-meta{flex:1 1 auto;min-width:0}
.hosp-upload-name{font-size:12px;color:var(--muted-color);margin-top:6px;word-break:break-word}
.hosp-switch-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px 18px}
.hosp-switch-item{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 12px;border:1px solid var(--line-strong);border-radius:12px;background:var(--page)}
.hosp-json-help{font-size:12px;color:var(--muted-color);margin-top:4px}
.hosp-sticky-actions{position:sticky;bottom:12px;z-index:5;display:flex;justify-content:flex-end;gap:8px;flex-wrap:wrap;padding:12px 14px;background:color-mix(in oklab, var(--surface) 92%, transparent);backdrop-filter:blur(8px);border:1px solid var(--line-strong);border-radius:16px;box-shadow:var(--shadow-2)}
.hosp-mode-chip{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;border:1px solid var(--line-strong);background:var(--surface-2);font-size:12px;font-weight:700;color:var(--muted-color)}
.hosp-mode-chip.edit{color:var(--primary-color)}
.hosp-banner{display:none;padding:12px 14px;border:1px solid var(--line-strong);border-radius:14px;background:var(--surface-2);margin-bottom:12px}
.hosp-banner.show{display:block}
.hosp-banner strong{color:var(--ink)}
.hosp-form textarea{min-height:110px}
.hosp-form .form-control,.hosp-form .form-select,.hosp-form textarea{border-radius:12px;border:1px solid var(--line-strong);background:#fff}
.hosp-form .form-control:focus,.hosp-form .form-select:focus,.hosp-form textarea:focus{box-shadow:0 0 0 3px color-mix(in oklab, var(--accent-color) 20%, transparent);border-color:var(--accent-color)}
html.theme-dark .hosp-card,html.theme-dark .hosp-panel{background:#0f172a;border-color:var(--line-strong)}
html.theme-dark .hosp-upload-card,html.theme-dark .hosp-upload-fallback,html.theme-dark .hosp-switch-item,html.theme-dark .hosp-banner{background:#0b1220;border-color:var(--line-strong)}
html.theme-dark .hosp-form .form-control,html.theme-dark .hosp-form .form-select,html.theme-dark .hosp-form textarea{background:#0f172a;color:#e5e7eb;border-color:var(--line-strong)}
@media (max-width:991.98px){.hosp-upload-grid,.hosp-switch-grid{grid-template-columns:1fr}}
@media (max-width:767.98px){.hosp-head-row,.hosp-sticky-actions{flex-direction:column;align-items:flex-start!important}.hosp-head-actions,.hosp-sticky-actions .btn{width:100%}}
</style>
@endpush

<div class="hosp-form-wrap">
  <div class="panel hosp-head">
    <div class="hosp-head-row">
      <div>
        <div class="hosp-head-copy">
          <h1>
            <span class="seg-muted">Hospital</span>
            <span class="seg-sep">/</span>
            <span id="hospitalPageHeading">Create</span>
          </h1>
        </div>
        <div class="hosp-head-sub">Create a hospital once, then continue editing the same record from this page.</div>
      </div>

      <div class="hosp-head-actions">
        <span class="hosp-mode-chip" id="hospitalModeChip"><i class="fa fa-plus"></i><span>Create Mode</span></span>
        <a href="/hospital/manage" class="btn btn-light" id="btnGoManageHospitals">
          <i class="fa fa-table-list me-1"></i>Manage Hospitals
        </a>
        <button type="button" class="w3-icon-btn" id="btnRefreshHospitalPage" title="Refresh current data" aria-label="Refresh current data">
          <i class="fa fa-rotate-right"></i>
        </button>
      </div>
    </div>
  </div>

  <div class="hosp-banner" id="hospitalStateBanner"></div>

  <form id="hospitalForm" class="hosp-form" enctype="multipart/form-data">
    <input type="hidden" id="hospitalId">
    <input type="hidden" id="hospitalUuid">

    <div class="hosp-panel">
      <h3 class="hosp-section-title"><i class="fa fa-hospital"></i>Basic Information</h3>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Hospital Name <span class="text-danger">*</span></label>
          <input id="hospitalName" class="form-control" maxlength="255" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Short Name</label>
          <input id="hospitalShortName" class="form-control" maxlength="120">
        </div>
        <div class="col-md-3">
          <label class="form-label">Hospital Code</label>
          <input id="hospitalCode" class="form-control" maxlength="80">
        </div>
        <div class="col-md-3">
          <label class="form-label">Registration No.</label>
          <input id="hospitalRegistrationNumber" class="form-control" maxlength="120">
        </div>
        <div class="col-md-3">
          <label class="form-label">License No.</label>
          <input id="hospitalLicenseNumber" class="form-control" maxlength="120">
        </div>
        <div class="col-md-2">
          <label class="form-label">Established Year</label>
          <input id="hospitalEstablishedYear" class="form-control" type="number" min="1800" max="2100">
        </div>
        <div class="col-md-2">
          <label class="form-label">Status</label>
          <select id="hospitalStatus" class="form-select">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Sort Order</label>
          <input id="hospitalSortOrder" class="form-control" type="number" min="0" value="0">
        </div>
        <div class="col-md-3">
          <label class="form-label">Hospital Type</label>
          <input id="hospitalType" class="form-control" maxlength="100" placeholder="Multi-speciality, Clinic">
        </div>
        <div class="col-md-3">
          <label class="form-label">Ownership Type</label>
          <input id="hospitalOwnershipType" class="form-control" maxlength="100" placeholder="Private, Trust, Govt">
        </div>
        <div class="col-md-3">
          <label class="form-label">Beds</label>
          <input id="hospitalBedCount" class="form-control" type="number" min="0" value="0">
        </div>
      </div>
    </div>

    <div class="hosp-panel">
      <h3 class="hosp-section-title"><i class="fa fa-address-card"></i>Contact</h3>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Email</label>
          <input id="hospitalEmail" class="form-control" type="email" maxlength="255">
        </div>
        <div class="col-md-4">
          <label class="form-label">Phone Number</label>
          <input id="hospitalPhoneNumber" class="form-control" maxlength="32">
        </div>
        <div class="col-md-4">
          <label class="form-label">Alternative Phone</label>
          <input id="hospitalAlternativePhoneNumber" class="form-control" maxlength="32">
        </div>
        <div class="col-md-4">
          <label class="form-label">WhatsApp</label>
          <input id="hospitalWhatsappNumber" class="form-control" maxlength="32">
        </div>
        <div class="col-md-4">
          <label class="form-label">Emergency Contact</label>
          <input id="hospitalEmergencyContactNumber" class="form-control" maxlength="32">
        </div>
        <div class="col-md-4">
          <label class="form-label">Website</label>
          <input id="hospitalWebsite" class="form-control" type="url" maxlength="255" placeholder="https://">
        </div>
      </div>
    </div>

    <div class="hosp-panel">
      <h3 class="hosp-section-title"><i class="fa fa-location-dot"></i>Location</h3>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Address Line 1</label>
          <input id="hospitalAddressLine1" class="form-control" maxlength="255">
        </div>
        <div class="col-md-6">
          <label class="form-label">Address Line 2</label>
          <input id="hospitalAddressLine2" class="form-control" maxlength="255">
        </div>
        <div class="col-md-4">
          <label class="form-label">Landmark</label>
          <input id="hospitalLandmark" class="form-control" maxlength="255">
        </div>
        <div class="col-md-4">
          <label class="form-label">Area</label>
          <input id="hospitalArea" class="form-control" maxlength="150">
        </div>
        <div class="col-md-4">
          <label class="form-label">Pincode</label>
          <input id="hospitalPincode" class="form-control" maxlength="20">
        </div>
        <div class="col-md-3">
          <label class="form-label">City</label>
          <input id="hospitalCity" class="form-control" maxlength="120">
        </div>
        <div class="col-md-3">
          <label class="form-label">State</label>
          <input id="hospitalState" class="form-control" maxlength="120">
        </div>
        <div class="col-md-3">
          <label class="form-label">Country</label>
          <input id="hospitalCountry" class="form-control" maxlength="120" value="India">
        </div>
        <div class="col-md-3">
          <label class="form-label">Map URL</label>
          <input id="hospitalMapUrl" class="form-control" maxlength="500" placeholder="https://maps.google.com/...">
        </div>
        <div class="col-md-3">
          <label class="form-label">Latitude</label>
          <input id="hospitalLatitude" class="form-control" type="number" step="0.0000001">
        </div>
        <div class="col-md-3">
          <label class="form-label">Longitude</label>
          <input id="hospitalLongitude" class="form-control" type="number" step="0.0000001">
        </div>
      </div>
    </div>

    <div class="hosp-panel">
      <h3 class="hosp-section-title"><i class="fa fa-image"></i>Media & SEO</h3>
      <div class="hosp-upload-grid mb-3">
        <div class="hosp-upload-card">
          <div id="hospitalLogoFallback" class="hosp-upload-fallback"><i class="fa fa-hospital"></i></div>
          <img id="hospitalLogoPreview" class="hosp-upload-preview" alt="Logo preview">
          <div class="hosp-upload-meta">
            <div class="fw-semibold">Logo</div>
            <div class="hosp-grid-note">Upload PNG, JPG, WEBP, GIF or SVG</div>
            <input id="hospitalLogo" class="form-control mt-2" type="file" accept="image/*">
            <div id="hospitalLogoName" class="hosp-upload-name">No file selected</div>
          </div>
        </div>
        <div class="hosp-upload-card">
          <div id="hospitalImageFallback" class="hosp-upload-fallback"><i class="fa fa-image"></i></div>
          <img id="hospitalImagePreview" class="hosp-upload-preview" alt="Main image preview">
          <div class="hosp-upload-meta">
            <div class="fw-semibold">Main Image</div>
            <div class="hosp-grid-note">Primary display image for cards/details</div>
            <input id="hospitalImage" class="form-control mt-2" type="file" accept="image/*">
            <div id="hospitalImageName" class="hosp-upload-name">No file selected</div>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea id="hospitalDescription" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Short Description</label>
          <textarea id="hospitalShortDescription" class="form-control" style="min-height:90px"></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Gallery URLs / Paths</label>
          <textarea id="hospitalGallery" class="form-control" placeholder="One per line or comma separated"></textarea>
          <div class="hosp-json-help">Store existing gallery item paths/URLs as a JSON array behind the scenes.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">SEO Title</label>
          <input id="hospitalSeoTitle" class="form-control" maxlength="255">
        </div>
        <div class="col-md-6">
          <label class="form-label">SEO Description</label>
          <textarea id="hospitalSeoDescription" class="form-control" style="min-height:90px"></textarea>
        </div>
      </div>
    </div>

    <div class="hosp-panel">
      <h3 class="hosp-section-title"><i class="fa fa-stethoscope"></i>Services & Tags</h3>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Departments</label>
          <textarea id="hospitalDepartments" class="form-control" placeholder="Cardiology, Neurology or one per line"></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Services</label>
          <textarea id="hospitalServices" class="form-control" placeholder="OPD, Surgery, Diagnostics"></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Facilities</label>
          <textarea id="hospitalFacilities" class="form-control" placeholder="ICU, Ambulance, Pharmacy"></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Specialities</label>
          <textarea id="hospitalSpecialities" class="form-control" placeholder="Cancer Care, Orthopaedics"></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Accreditations</label>
          <textarea id="hospitalAccreditations" class="form-control" placeholder="NABH, NABL, ISO"></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Insurances Accepted</label>
          <textarea id="hospitalInsurancesAccepted" class="form-control" placeholder="Star Health, Niva Bupa"></textarea>
        </div>
      </div>
    </div>

    <div class="hosp-panel">
      <h3 class="hosp-section-title"><i class="fa fa-sliders"></i>Availability & Metadata</h3>
      <div class="row g-3">
        <div class="col-lg-6">
          <div class="hosp-switch-grid">
            <label class="hosp-switch-item"><span>Ambulance Available</span><input id="hospitalAmbulanceAvailable" class="form-check-input" type="checkbox"></label>
            <label class="hosp-switch-item"><span>Emergency Available</span><input id="hospitalEmergencyAvailable" class="form-check-input" type="checkbox"></label>
            <label class="hosp-switch-item"><span>Pharmacy Available</span><input id="hospitalPharmacyAvailable" class="form-check-input" type="checkbox"></label>
            <label class="hosp-switch-item"><span>Lab Available</span><input id="hospitalLabAvailable" class="form-check-input" type="checkbox"></label>
            <label class="hosp-switch-item"><span>ICU Available</span><input id="hospitalIcuAvailable" class="form-check-input" type="checkbox"></label>
            <label class="hosp-switch-item"><span>Cashless Available</span><input id="hospitalCashlessAvailable" class="form-check-input" type="checkbox"></label>
            <label class="hosp-switch-item"><span>Online Consultation</span><input id="hospitalOnlineConsultationAvailable" class="form-check-input" type="checkbox"></label>
            <label class="hosp-switch-item"><span>Appointment Booking</span><input id="hospitalAppointmentBookingAvailable" class="form-check-input" type="checkbox" checked></label>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Timings JSON</label>
              <textarea id="hospitalTimings" class="form-control" placeholder='{"opd":"9 AM - 6 PM","emergency":"24x7"}'></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Social Links JSON</label>
              <textarea id="hospitalSocialLinks" class="form-control" placeholder='{"facebook":"https://...","instagram":"https://..."}'></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Metadata JSON</label>
              <textarea id="hospitalMetadata" class="form-control" placeholder='{"theme":"primary","featured":true}'></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="hosp-sticky-actions">
      <button type="button" class="btn btn-light" id="btnResetHospitalForm">
        <i class="fa fa-rotate-left me-1"></i>Reset
      </button>
      <button type="submit" class="btn btn-primary" id="btnSaveHospital">
        <i class="fa fa-save me-1"></i><span class="btn-label">Save Hospital</span>
      </button>
    </div>
  </form>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080">
  <div id="hospitalToastSuccess" class="toast align-items-center text-bg-success border-0">
    <div class="d-flex">
      <div class="toast-body" id="hospitalToastSuccessText">Done</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
  <div id="hospitalToastError" class="toast align-items-center text-bg-danger border-0 mt-2">
    <div class="d-flex">
      <div class="toast-body" id="hospitalToastErrorText">Something went wrong</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.__HOSPITAL_CREATE_INIT__) return;
  window.__HOSPITAL_CREATE_INIT__ = true;

  const token = sessionStorage.getItem('token') || localStorage.getItem('token') || '';
  if (!token) {
    window.location.href = '/';
    return;
  }

  const API = '/api/hospitals';
  const PAGE_CREATE = '/hospital/create';
  const PAGE_MANAGE = '/hospital/manage';

  const toastOk = new bootstrap.Toast(document.getElementById('hospitalToastSuccess'));
  const toastErr = new bootstrap.Toast(document.getElementById('hospitalToastError'));
  const okText = document.getElementById('hospitalToastSuccessText');
  const errText = document.getElementById('hospitalToastErrorText');
  const params = new URLSearchParams(window.location.search);

  const els = {
    form: document.getElementById('hospitalForm'),
    pageHeading: document.getElementById('hospitalPageHeading'),
    modeChip: document.getElementById('hospitalModeChip'),
    stateBanner: document.getElementById('hospitalStateBanner'),
    refreshBtn: document.getElementById('btnRefreshHospitalPage'),
    resetBtn: document.getElementById('btnResetHospitalForm'),
    saveBtn: document.getElementById('btnSaveHospital'),
    id: document.getElementById('hospitalId'),
    uuid: document.getElementById('hospitalUuid'),
    name: document.getElementById('hospitalName'),
    shortName: document.getElementById('hospitalShortName'),
    code: document.getElementById('hospitalCode'),
    registration: document.getElementById('hospitalRegistrationNumber'),
    license: document.getElementById('hospitalLicenseNumber'),
    year: document.getElementById('hospitalEstablishedYear'),
    type: document.getElementById('hospitalType'),
    ownership: document.getElementById('hospitalOwnershipType'),
    status: document.getElementById('hospitalStatus'),
    sortOrder: document.getElementById('hospitalSortOrder'),
    bedCount: document.getElementById('hospitalBedCount'),
    email: document.getElementById('hospitalEmail'),
    phone: document.getElementById('hospitalPhoneNumber'),
    altPhone: document.getElementById('hospitalAlternativePhoneNumber'),
    whatsapp: document.getElementById('hospitalWhatsappNumber'),
    emergencyPhone: document.getElementById('hospitalEmergencyContactNumber'),
    website: document.getElementById('hospitalWebsite'),
    address1: document.getElementById('hospitalAddressLine1'),
    address2: document.getElementById('hospitalAddressLine2'),
    landmark: document.getElementById('hospitalLandmark'),
    area: document.getElementById('hospitalArea'),
    city: document.getElementById('hospitalCity'),
    state: document.getElementById('hospitalState'),
    country: document.getElementById('hospitalCountry'),
    pincode: document.getElementById('hospitalPincode'),
    latitude: document.getElementById('hospitalLatitude'),
    longitude: document.getElementById('hospitalLongitude'),
    mapUrl: document.getElementById('hospitalMapUrl'),
    logo: document.getElementById('hospitalLogo'),
    logoPreview: document.getElementById('hospitalLogoPreview'),
    logoFallback: document.getElementById('hospitalLogoFallback'),
    logoName: document.getElementById('hospitalLogoName'),
    image: document.getElementById('hospitalImage'),
    imagePreview: document.getElementById('hospitalImagePreview'),
    imageFallback: document.getElementById('hospitalImageFallback'),
    imageName: document.getElementById('hospitalImageName'),
    description: document.getElementById('hospitalDescription'),
    shortDescription: document.getElementById('hospitalShortDescription'),
    gallery: document.getElementById('hospitalGallery'),
    seoTitle: document.getElementById('hospitalSeoTitle'),
    seoDescription: document.getElementById('hospitalSeoDescription'),
    departments: document.getElementById('hospitalDepartments'),
    services: document.getElementById('hospitalServices'),
    facilities: document.getElementById('hospitalFacilities'),
    specialities: document.getElementById('hospitalSpecialities'),
    accreditations: document.getElementById('hospitalAccreditations'),
    insurances: document.getElementById('hospitalInsurancesAccepted'),
    timings: document.getElementById('hospitalTimings'),
    socialLinks: document.getElementById('hospitalSocialLinks'),
    metadata: document.getElementById('hospitalMetadata'),
    ambulance: document.getElementById('hospitalAmbulanceAvailable'),
    emergency: document.getElementById('hospitalEmergencyAvailable'),
    pharmacy: document.getElementById('hospitalPharmacyAvailable'),
    lab: document.getElementById('hospitalLabAvailable'),
    icu: document.getElementById('hospitalIcuAvailable'),
    cashless: document.getElementById('hospitalCashlessAvailable'),
    onlineConsultation: document.getElementById('hospitalOnlineConsultationAvailable'),
    booking: document.getElementById('hospitalAppointmentBookingAvailable'),
  };

  const state = {
    mode: 'create',
    currentId: null,
    currentUuid: '',
    originalPayload: null,
    permissionsByPath: new Map(),
    allAccess: false,
  };

  function ok(message) {
    okText.textContent = message || 'Done';
    toastOk.show();
  }

  function err(message) {
    errText.textContent = message || 'Something went wrong';
    toastErr.show();
  }

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
        return;
      }

      (Array.isArray(js.tree) ? js.tree : []).forEach(header => {
        (Array.isArray(header.children) ? header.children : []).forEach(page => {
          const href = normalizePath(page.href || '');
          if (!href) return;
          const actionSet = new Set((Array.isArray(page.actions) ? page.actions : []).map(v => String(v || '').toLowerCase()).filter(Boolean));
          state.permissionsByPath.set(href, actionSet);
        });
      });
    } catch (e) {
      state.permissionsByPath = new Map();
    }
  }

  function setButtonLoading(button, loading, label) {
    if (!button) return;
    if (loading) {
      if (!button.dataset.originalHtml) button.dataset.originalHtml = button.innerHTML;
      button.disabled = true;
      button.innerHTML = `<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>${label || 'Loading...'}`;
      return;
    }
    button.disabled = false;
    if (button.dataset.originalHtml) button.innerHTML = button.dataset.originalHtml;
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
    if (nameEl) nameEl.textContent = fileName || 'No file selected';
  }

  function filePreviewListener(inputEl, imageEl, fallbackEl, nameEl) {
    inputEl?.addEventListener('change', function () {
      const file = this.files && this.files[0] ? this.files[0] : null;
      if (!file) {
        setPreview(imageEl, fallbackEl, nameEl, '', 'No file selected');
        return;
      }
      const reader = new FileReader();
      reader.onload = ev => setPreview(imageEl, fallbackEl, nameEl, ev.target.result || '', file.name);
      reader.readAsDataURL(file);
    });
  }

  function listText(value) {
    const items = String(value || '')
      .split(/[\n,]+/)
      .map(v => v.trim())
      .filter(Boolean);
    return Array.from(new Set(items));
  }

  function prettyJson(value) {
    if (!value) return '';
    try {
      return JSON.stringify(value, null, 2);
    } catch (e) {
      return '';
    }
  }

  function setBanner(message, variant) {
    if (!message) {
      els.stateBanner.className = 'hosp-banner';
      els.stateBanner.innerHTML = '';
      return;
    }
    els.stateBanner.className = 'hosp-banner show';
    els.stateBanner.innerHTML = `<strong>${variant || 'Info'}:</strong> ${message}`;
  }

  function applyModeUi() {
    const canCreate = hasPageAction(PAGE_CREATE, 'create');
    const canUpdate = hasPageAction(PAGE_CREATE, 'update', 'edit');
    const canView = hasPageAction(PAGE_CREATE, 'view') || canCreate || canUpdate;
    const viewOnly = state.mode === 'edit' ? !canUpdate && canView : !canCreate;

    els.pageHeading.textContent = state.mode === 'edit' ? 'Edit' : 'Create';
    els.modeChip.className = 'hosp-mode-chip' + (state.mode === 'edit' ? ' edit' : '');
    els.modeChip.innerHTML = state.mode === 'edit'
      ? '<i class="fa fa-pen"></i><span>Edit Mode</span>'
      : '<i class="fa fa-plus"></i><span>Create Mode</span>';

    const allowSave = state.mode === 'edit' ? canUpdate : canCreate;
    els.saveBtn.style.display = allowSave ? '' : 'none';

    Array.from(els.form.querySelectorAll('input,select,textarea,button')).forEach(el => {
      if (el === els.saveBtn || el === els.resetBtn || el === els.refreshBtn) return;
      if (el.type === 'file') {
        el.disabled = viewOnly;
      } else {
        el.disabled = false;
        el.readOnly = viewOnly && el.tagName !== 'SELECT';
        if (el.tagName === 'SELECT' || el.type === 'checkbox') {
          el.disabled = viewOnly;
        }
      }
    });

    if (state.mode === 'edit' && state.currentUuid) {
      if (viewOnly) {
        setBanner('You can view this hospital, but you do not currently have update permission for the create/edit page.', 'View Only');
      } else {
        setBanner(`Editing hospital UUID: <code>${state.currentUuid}</code>`, 'Editing');
      }
    } else if (!canCreate) {
      setBanner('You do not currently have create permission for the hospital form page.', 'Restricted');
    } else {
      setBanner('', '');
    }
  }

  function resetFormVisuals() {
    setPreview(els.logoPreview, els.logoFallback, els.logoName, '', 'No file selected');
    setPreview(els.imagePreview, els.imageFallback, els.imageName, '', 'No file selected');
  }

  function resetForm() {
    els.form.reset();
    els.id.value = '';
    els.uuid.value = '';
    els.sortOrder.value = '0';
    els.bedCount.value = '0';
    els.country.value = 'India';
    els.status.value = 'active';
    els.booking.checked = true;
    state.mode = 'create';
    state.currentId = null;
    state.currentUuid = '';
    state.originalPayload = null;
    resetFormVisuals();
    applyModeUi();
  }

  function fillForm(row) {
    state.originalPayload = row;
    state.currentId = row.id || null;
    state.currentUuid = row.uuid || '';
    state.mode = 'edit';

    els.id.value = row.id || '';
    els.uuid.value = row.uuid || '';
    els.name.value = row.name || '';
    els.shortName.value = row.short_name || '';
    els.code.value = row.hospital_code || '';
    els.registration.value = row.registration_number || '';
    els.license.value = row.license_number || '';
    els.year.value = row.established_year || '';
    els.type.value = row.hospital_type || '';
    els.ownership.value = row.ownership_type || '';
    els.status.value = row.status || 'active';
    els.sortOrder.value = row.sort_order ?? 0;
    els.bedCount.value = row.bed_count ?? 0;
    els.email.value = row.email || '';
    els.phone.value = row.phone_number || '';
    els.altPhone.value = row.alternative_phone_number || '';
    els.whatsapp.value = row.whatsapp_number || '';
    els.emergencyPhone.value = row.emergency_contact_number || '';
    els.website.value = row.website || '';
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
    els.description.value = row.description || '';
    els.shortDescription.value = row.short_description || '';
    els.gallery.value = Array.isArray(row.gallery) ? row.gallery.join('\n') : '';
    els.seoTitle.value = row.seo_title || '';
    els.seoDescription.value = row.seo_description || '';
    els.departments.value = Array.isArray(row.departments) ? row.departments.join('\n') : '';
    els.services.value = Array.isArray(row.services) ? row.services.join('\n') : '';
    els.facilities.value = Array.isArray(row.facilities) ? row.facilities.join('\n') : '';
    els.specialities.value = Array.isArray(row.specialities) ? row.specialities.join('\n') : '';
    els.accreditations.value = Array.isArray(row.accreditations) ? row.accreditations.join('\n') : '';
    els.insurances.value = Array.isArray(row.insurances_accepted) ? row.insurances_accepted.join('\n') : '';
    els.timings.value = prettyJson(row.timings);
    els.socialLinks.value = prettyJson(row.social_links);
    els.metadata.value = prettyJson(row.metadata);
    els.ambulance.checked = !!row.ambulance_available;
    els.emergency.checked = !!row.emergency_available;
    els.pharmacy.checked = !!row.pharmacy_available;
    els.lab.checked = !!row.lab_available;
    els.icu.checked = !!row.icu_available;
    els.cashless.checked = !!row.cashless_available;
    els.onlineConsultation.checked = !!row.online_consultation_available;
    els.booking.checked = !!row.appointment_booking_available;

    setPreview(els.logoPreview, els.logoFallback, els.logoName, row.logo || '', (row.logo_path || row.logo || '').split('/').pop() || 'Current logo');
    setPreview(els.imagePreview, els.imageFallback, els.imageName, row.image || '', (row.image_path || row.image || '').split('/').pop() || 'Current image');
    applyModeUi();
  }

  async function fetchHospital(identifier, silent) {
    const res = await fetch(`${API}/${encodeURIComponent(identifier)}`, { headers: authHeaders() });
    const js = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(js.message || 'Failed to load hospital');
    const row = js.hospital || js.data || null;
    if (!row && !silent) throw new Error('Hospital not found');
    return row;
  }

  async function loadExistingHospital() {
    const identifier = params.get('uuid') || params.get('hospital_uuid') || params.get('id') || '';
    if (!identifier) {
      resetForm();
      return;
    }

    try {
      setButtonLoading(els.refreshBtn, true, 'Loading');
      const row = await fetchHospital(identifier, false);
      fillForm(row);
    } catch (e) {
      err(e.message || 'Failed to load hospital');
      resetForm();
    } finally {
      setButtonLoading(els.refreshBtn, false);
    }
  }

  function appendJsonOrThrow(fd, key, value) {
    const raw = String(value || '').trim();
    if (!raw) return;
    try {
      const parsed = JSON.parse(raw);
      fd.append(key, JSON.stringify(parsed));
    } catch (e) {
      throw new Error(`${key.replace(/_/g, ' ')} must be valid JSON`);
    }
  }

  function buildFormData() {
    const fd = new FormData();
    if (state.mode === 'edit' && state.currentUuid) {
      fd.append('_method', 'PATCH');
    }

    fd.append('name', els.name.value.trim());
    fd.append('short_name', els.shortName.value.trim());
    fd.append('hospital_code', els.code.value.trim());
    fd.append('registration_number', els.registration.value.trim());
    fd.append('license_number', els.license.value.trim());
    fd.append('established_year', els.year.value.trim());
    fd.append('hospital_type', els.type.value.trim());
    fd.append('ownership_type', els.ownership.value.trim());
    fd.append('status', els.status.value);
    fd.append('sort_order', String(Number(els.sortOrder.value || 0)));
    fd.append('bed_count', String(Number(els.bedCount.value || 0)));
    fd.append('email', els.email.value.trim());
    fd.append('phone_number', els.phone.value.trim());
    fd.append('alternative_phone_number', els.altPhone.value.trim());
    fd.append('whatsapp_number', els.whatsapp.value.trim());
    fd.append('emergency_contact_number', els.emergencyPhone.value.trim());
    fd.append('website', els.website.value.trim());
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
    fd.append('description', els.description.value.trim());
    fd.append('short_description', els.shortDescription.value.trim());
    fd.append('seo_title', els.seoTitle.value.trim());
    fd.append('seo_description', els.seoDescription.value.trim());

    fd.append('departments', JSON.stringify(listText(els.departments.value)));
    fd.append('services', JSON.stringify(listText(els.services.value)));
    fd.append('facilities', JSON.stringify(listText(els.facilities.value)));
    fd.append('specialities', JSON.stringify(listText(els.specialities.value)));
    fd.append('accreditations', JSON.stringify(listText(els.accreditations.value)));
    fd.append('insurances_accepted', JSON.stringify(listText(els.insurances.value)));
    fd.append('gallery', JSON.stringify(listText(els.gallery.value)));

    appendJsonOrThrow(fd, 'timings', els.timings.value);
    appendJsonOrThrow(fd, 'social_links', els.socialLinks.value);
    appendJsonOrThrow(fd, 'metadata', els.metadata.value);

    fd.append('ambulance_available', els.ambulance.checked ? '1' : '0');
    fd.append('emergency_available', els.emergency.checked ? '1' : '0');
    fd.append('pharmacy_available', els.pharmacy.checked ? '1' : '0');
    fd.append('lab_available', els.lab.checked ? '1' : '0');
    fd.append('icu_available', els.icu.checked ? '1' : '0');
    fd.append('cashless_available', els.cashless.checked ? '1' : '0');
    fd.append('online_consultation_available', els.onlineConsultation.checked ? '1' : '0');
    fd.append('appointment_booking_available', els.booking.checked ? '1' : '0');

    if (els.logo.files && els.logo.files[0]) fd.append('logo', els.logo.files[0]);
    if (els.image.files && els.image.files[0]) fd.append('image', els.image.files[0]);

    return fd;
  }

  async function handleSubmit(e) {
    e.preventDefault();

    if (!els.name.value.trim()) {
      els.name.focus();
      return err('Hospital name is required');
    }

    const canSave = state.mode === 'edit'
      ? hasPageAction(PAGE_CREATE, 'update', 'edit')
      : hasPageAction(PAGE_CREATE, 'create');

    if (!canSave) {
      return err('You do not have permission to save from this page');
    }

    try {
      setButtonLoading(els.saveBtn, true, state.mode === 'edit' ? 'Updating...' : 'Saving...');
      const fd = buildFormData();
      const url = state.mode === 'edit' && state.currentUuid
        ? `${API}/${encodeURIComponent(state.currentUuid)}`
        : API;

      const res = await fetch(url, {
        method: 'POST',
        headers: authHeaders(),
        body: fd
      });
      const js = await res.json().catch(() => ({}));
      if (!res.ok) {
        let message = js.message || 'Failed to save hospital';
        if (js.errors) {
          const firstKey = Object.keys(js.errors)[0];
          if (firstKey && js.errors[firstKey] && js.errors[firstKey][0]) {
            message = js.errors[firstKey][0];
          }
        }
        throw new Error(message);
      }

      const hospital = js.hospital || js.data || null;
      if (!hospital) throw new Error('Hospital response is incomplete');

      fillForm(hospital);

      if (state.mode === 'edit' && hospital.uuid) {
        const nextUrl = `${PAGE_CREATE}?uuid=${encodeURIComponent(hospital.uuid)}`;
        history.replaceState({}, '', nextUrl);
      }

      ok(js.message || 'Hospital saved');
    } catch (e2) {
      err(e2.message || 'Failed to save hospital');
    } finally {
      setButtonLoading(els.saveBtn, false);
    }
  }

  els.resetBtn.addEventListener('click', function () {
    if (state.mode === 'edit' && state.originalPayload) {
      fillForm(state.originalPayload);
    } else {
      resetForm();
    }
  });

  els.refreshBtn.addEventListener('click', async function () {
    if (!state.currentUuid) {
      resetForm();
      return;
    }
    try {
      setButtonLoading(els.refreshBtn, true, 'Refreshing...');
      const row = await fetchHospital(state.currentUuid, false);
      fillForm(row);
      ok('Hospital data refreshed');
    } catch (e) {
      err(e.message || 'Failed to refresh hospital');
    } finally {
      setButtonLoading(els.refreshBtn, false);
    }
  });

  filePreviewListener(els.logo, els.logoPreview, els.logoFallback, els.logoName);
  filePreviewListener(els.image, els.imagePreview, els.imageFallback, els.imageName);
  els.form.addEventListener('submit', handleSubmit);

  (async function init() {
    await loadPermissions();
    await loadExistingHospital();
    applyModeUi();
  })();
});
</script>
@endpush
