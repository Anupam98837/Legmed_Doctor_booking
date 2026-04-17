<style>
.docpage{min-height:100dvh;background:
  radial-gradient(circle at top left, rgba(14,165,233,.08), transparent 28%),
  radial-gradient(circle at top right, rgba(37,99,235,.08), transparent 24%),
  linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
}
.docpage-header{position:sticky;top:0;z-index:1200;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 22px;border-bottom:1px solid rgba(148,163,184,.22);background:rgba(255,255,255,.88);backdrop-filter:blur(16px)}
.docpage-header-left{display:flex;align-items:center;gap:10px;min-width:0}
.docpage-sidebar-badge{position:fixed;left:-10px;top:50%;transform:translateY(-50%);z-index:1206;width:46px;height:54px;display:none;align-items:center;justify-content:center;border:1px solid var(--line-strong);border-left:0;border-radius:0 18px 18px 0;background:var(--surface);color:var(--ink);box-shadow:0 14px 28px rgba(15,23,42,.16);transition:left .2s ease, transform .2s ease}
.docpage-sidebar-badge i{font-size:14px;transition:transform .18s ease}
.docpage-sidebar-badge.is-open{left:0}
.docpage-sidebar-badge.is-open i{transform:rotate(180deg)}
.docpage-header-right{display:flex;align-items:center;gap:10px;min-width:0}
.docpage-user-chip{display:flex;align-items:center;gap:10px;max-width:280px;padding:7px 12px;border:0;border-radius:999px;background:rgba(255,255,255,.72);box-shadow:0 10px 24px rgba(15,23,42,.08)}
.docpage-user-chip-avatar,.docpage-user-chip-fallback{width:34px;height:34px;border-radius:999px;flex:0 0 34px;border:0}
.docpage-user-chip-avatar{display:none;object-fit:cover}
.docpage-user-chip-fallback{display:flex;align-items:center;justify-content:center;background:var(--page);font-size:11px;font-weight:800;color:var(--muted-color)}
.docpage-user-chip-copy{min-width:0}
.docpage-user-chip-name{font-size:13px;font-weight:700;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.docpage-user-chip-role{font-size:11px;color:var(--muted-color);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.docpage-back-btn{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:8px 11px;font-size:12px;line-height:1;border:0;background:rgba(255,255,255,.72);box-shadow:0 10px 24px rgba(15,23,42,.08)}
.docpage-back-btn.docpage-back-btn-left{padding:8px 12px}
.docpage-main{padding:20px 22px 28px}
.docpage-overlay{position:fixed;inset:0;background:rgba(15,23,42,.48);opacity:0;visibility:hidden;pointer-events:none;transition:opacity .2s ease, visibility .2s ease;z-index:1190}
.docpage-overlay.is-open{opacity:1;visibility:visible;pointer-events:auto}
.docprof-wrap{padding:4px 0}
.docprof-shell{display:grid;grid-template-columns:280px minmax(0,1fr);gap:16px;align-items:start}
.docprof-panel{background:var(--surface);border:1px solid var(--line-strong);border-radius:18px;box-shadow:var(--shadow-2)}
.docprof-side{position:sticky;top:72px;padding:14px}
.docprof-user{display:flex;gap:12px;align-items:center;padding-bottom:12px;border-bottom:1px solid var(--line-strong);margin-bottom:12px}
.docprof-avatar,.docprof-avatar-fallback{width:58px;height:58px;border-radius:16px;flex:0 0 58px;border:1px solid var(--line-strong)}
.docprof-avatar{object-fit:cover;display:none}
.docprof-avatar-fallback{display:flex;align-items:center;justify-content:center;background:var(--page);font-weight:800;color:var(--muted-color)}
.docprof-user h2{margin:0;font-size:18px;color:var(--ink)}
.docprof-user p{margin:4px 0 0;color:var(--muted-color);font-size:12px}
.docprof-side-nav{display:grid;gap:6px}
.docprof-side-link{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;text-decoration:none;color:var(--text-color);font-size:13px}
.docprof-side-link:hover,.docprof-side-link.active{background:var(--page-hover);color:var(--ink)}
.docprof-main{display:grid;gap:16px}
.docprof-head{padding:16px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.docprof-head-copy h1{margin:0;font-size:22px;color:var(--ink)}
.docprof-head-copy p{margin:6px 0 0;color:var(--muted-color);font-size:13px}
.docprof-actions{display:flex;gap:10px;flex-wrap:wrap}
.docprof-save-state{display:none;align-items:center;gap:8px;padding:9px 12px;border-radius:999px;background:rgba(234,179,8,.14);color:#854d0e;font-size:12px;font-weight:700;box-shadow:0 10px 20px rgba(15,23,42,.06)}
.docprof-save-state.is-visible{display:inline-flex}
.docprof-section{padding:16px 18px}
.docprof-section + .docprof-section{margin-top:-8px}
.docprof-section-pane{display:none}
.docprof-section-pane.is-active{display:block}
.docprof-section-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px}
.docprof-section-head h3{margin:0;font-size:16px;color:var(--ink)}
.docprof-section-head p{margin:4px 0 0;font-size:12px;color:var(--muted-color)}
.docprof-grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:14px}
.docprof-col-12{grid-column:span 12}
.docprof-col-8{grid-column:span 8}
.docprof-col-6{grid-column:span 6}
.docprof-col-4{grid-column:span 4}
.docprof-col-3{grid-column:span 3}
.docprof-field label{display:block;font-size:12px;font-weight:600;color:var(--muted-color);margin-bottom:6px}
.docprof-field .form-control,.docprof-field .form-select,.docprof-field textarea{border-radius:12px;border:1px solid var(--line-strong)}
.docprof-field .form-control[readonly]{background:var(--page);color:var(--muted-color);cursor:not-allowed}
.docprof-upload{display:grid;grid-template-columns:64px minmax(0,1fr);gap:12px;align-items:center;padding:12px;border:1px dashed var(--line-strong);border-radius:14px;background:var(--page)}
.docprof-upload-preview,.docprof-upload-fallback{width:64px;height:64px;border-radius:14px;border:1px solid var(--line-strong)}
.docprof-upload-preview{display:none;object-fit:cover}
.docprof-upload-fallback{display:flex;align-items:center;justify-content:center;color:var(--muted-color);background:var(--surface)}
.docprof-switch-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.docprof-switch{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 12px;border:1px solid var(--line-strong);border-radius:14px;background:var(--page)}
.docprof-check-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.docprof-card{padding:12px;border:1px solid var(--line-strong);border-radius:16px;background:var(--surface)}
.docprof-card.is-selected{border-color:var(--accent-color);box-shadow:0 0 0 3px color-mix(in oklab, var(--accent-color) 14%, transparent)}
.docprof-card-top{display:flex;gap:10px;justify-content:space-between;align-items:flex-start}
.docprof-card-title{font-weight:700;color:var(--ink);font-size:14px}
.docprof-card-sub{margin-top:4px;font-size:12px;color:var(--muted-color)}
.docprof-card-body{margin-top:12px;display:grid;gap:10px}
.docprof-inline-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
.docprof-qual-list{display:grid;gap:12px}
.docprof-qual-card{padding:12px;border:1px solid var(--line-strong);border-radius:16px;background:var(--surface)}
.docprof-qual-actions{display:flex;justify-content:flex-end;margin-top:10px}
.docprof-empty{padding:18px;border:1px dashed var(--line-strong);border-radius:16px;background:var(--page);text-align:center;color:var(--muted-color)}
.docprof-sticky-save{position:sticky;bottom:12px;display:flex;justify-content:flex-end;z-index:10}
.docprof-toast-wrap{position:fixed;top:16px;right:16px;z-index:2100}
html.theme-dark .docprof-panel,html.theme-dark .docprof-card,html.theme-dark .docprof-qual-card{background:#0f172a;border-color:var(--line-strong)}
html.theme-dark .docprof-upload,html.theme-dark .docprof-switch,html.theme-dark .docprof-empty,html.theme-dark .docprof-avatar-fallback{background:#0b1220;border-color:var(--line-strong)}
html.theme-dark .docprof-save-state{background:rgba(250,204,21,.16);color:#facc15}
html.theme-dark .docpage{background:
  radial-gradient(circle at top left, rgba(14,165,233,.14), transparent 28%),
  radial-gradient(circle at top right, rgba(37,99,235,.14), transparent 24%),
  linear-gradient(180deg, #020617 0%, #0b1220 100%)}
html.theme-dark .docpage-header{background:rgba(2,6,23,.84);border-bottom-color:var(--line-strong)}
html.theme-dark .docpage-sidebar-badge,
html.theme-dark .docpage-user-chip,
html.theme-dark .docpage-back-btn{background:rgba(15,23,42,.9);border-color:transparent;color:#e5eefb}
html.theme-dark .docpage-user-chip-fallback{background:#111b30;color:#d9e4f7}
@media (max-width:1199.98px){.docprof-shell{grid-template-columns:1fr}.docprof-side{position:relative;top:0}.docprof-side-nav{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:991.98px){.docprof-check-grid,.docprof-switch-grid,.docprof-inline-grid{grid-template-columns:1fr}.docprof-col-8,.docprof-col-6,.docprof-col-4,.docprof-col-3{grid-column:span 12}}
@media (max-width:991.98px){
  .docpage-header{padding:12px 14px}
  .docpage-sidebar-badge{display:inline-flex}
  .docpage-main{padding:14px}
  .docprof-shell{display:block}
  .docprof-side{position:fixed;top:0;left:0;bottom:0;width:min(86vw,320px);max-width:320px;overflow-y:auto;padding:16px;z-index:1205;border-radius:0 18px 18px 0;transform:translateX(-104%);transition:transform .22s ease}
  .docprof-side.is-open{transform:translateX(0)}
  .docprof-side-nav{grid-template-columns:1fr}
}
@media (max-width:575.98px){.docpage-user-chip{max-width:150px;padding:6px 8px;gap:8px}.docpage-user-chip-role{display:none}.docpage-back-btn span{display:none}.docprof-upload{grid-template-columns:1fr}.docprof-upload-preview,.docprof-upload-fallback{margin:0 auto}.docprof-head{padding:14px}.docprof-section{padding:14px}}
</style>

<div class="docpage">
  <header class="docpage-header">
    <div class="docpage-header-left">
      <button type="button" class="btn btn-light btn-sm docpage-back-btn docpage-back-btn-left" id="doctorPageBackBtn">
        <i class="fa fa-arrow-left"></i><span>Back</span>
      </button>
    </div>
    <div class="docpage-header-right">
      <div class="docpage-user-chip">
        <img id="doctorPageUserAvatar" class="docpage-user-chip-avatar" alt="Doctor profile">
        <div id="doctorPageUserAvatarFallback" class="docpage-user-chip-fallback">DR</div>
        <div class="docpage-user-chip-copy">
          <div id="doctorPageUserName" class="docpage-user-chip-name">Doctor</div>
          <div id="doctorPageUserRole" class="docpage-user-chip-role">Profile workspace</div>
        </div>
      </div>
    </div>
  </header>

  <button type="button" class="docpage-sidebar-badge" id="doctorPageSidebarBadge" aria-label="Open doctor sections" aria-expanded="false">
    <i class="fa fa-chevron-right"></i>
  </button>

  <div class="docpage-overlay" id="doctorPageOverlay" aria-hidden="true"></div>
  <div id="doctorGlobalLoading" style="display:none;">
    @include('partials.overlay')
  </div>

  <div class="docpage-main">
    <div class="docprof-wrap">
      <div class="docprof-shell">
        <aside class="docprof-panel docprof-side" id="doctorProfileSidebar">
      <div class="docprof-user">
        <img id="doctorUserAvatar" class="docprof-avatar" alt="User avatar">
        <div id="doctorUserAvatarFallback" class="docprof-avatar-fallback">DR</div>
        <div>
          <h2 id="doctorUserName">Doctor Profile</h2>
          <p id="doctorUserMeta">Loading user details...</p>
        </div>
      </div>

      <nav class="docprof-side-nav" id="doctorProfileNav">
        <a href="#doctor-personal" data-section="doctor-personal" class="docprof-side-link active"><i class="fa fa-user"></i><span>Personal Details</span></a>
        <a href="#doctor-basics" data-section="doctor-basics" class="docprof-side-link"><i class="fa fa-user-doctor"></i><span>Basics</span></a>
        <a href="#doctor-bio" data-section="doctor-bio" class="docprof-side-link"><i class="fa fa-address-card"></i><span>Bio & SEO</span></a>
        <a href="#doctor-media" data-section="doctor-media" class="docprof-side-link"><i class="fa fa-image"></i><span>Media</span></a>
        <a href="#doctor-specializations" data-section="doctor-specializations" class="docprof-side-link"><i class="fa fa-stethoscope"></i><span>Specializations</span></a>
        <a href="#doctor-languages" data-section="doctor-languages" class="docprof-side-link"><i class="fa fa-language"></i><span>Languages</span></a>
        <a href="#doctor-services" data-section="doctor-services" class="docprof-side-link"><i class="fa fa-briefcase-medical"></i><span>Services</span></a>
        <a href="#doctor-qualifications" data-section="doctor-qualifications" class="docprof-side-link"><i class="fa fa-graduation-cap"></i><span>Qualifications</span></a>
        <a href="#doctor-clinics" data-section="doctor-clinics" class="docprof-side-link"><i class="fa fa-clinic-medical"></i><span>Clinics</span></a>
      </nav>
        </aside>

        <div class="docprof-main">
      <div class="docprof-panel docprof-head">
        <div class="docprof-head-copy">
          <h1>Doctor Profile Workspace</h1>
          <p>All doctor-linked tables are managed from this page for the selected user.</p>
        </div>
        <div class="docprof-actions">
          <div class="docprof-save-state" id="doctorProfileDirtyState"><i class="fa fa-circle-exclamation"></i><span>Changes not saved</span></div>
          <button type="button" class="btn btn-light" id="doctorProfileReloadBtn"><i class="fa fa-rotate-right me-1"></i>Reload</button>
          <button type="button" class="btn btn-primary" id="doctorProfileSaveTopBtn"><i class="fa fa-floppy-disk me-1"></i>Save Profile</button>
        </div>
      </div>

      <div class="docprof-panel docprof-section docprof-section-pane is-active" id="doctor-personal">
        <div class="docprof-section-head">
          <div>
            <h3>Personal Details</h3>
            <p>Base user account details used across the doctor workspace. Email stays locked here.</p>
          </div>
        </div>

        <div class="docprof-grid">
          <div class="docprof-col-6">
            <div class="docprof-upload">
              <div>
                <div id="doctorUserImageFallback" class="docprof-upload-fallback"><i class="fa fa-user"></i></div>
                <img id="doctorUserImagePreview" class="docprof-upload-preview" alt="Profile image preview">
              </div>
              <div>
                <div class="docprof-field mb-0">
                  <label>Profile Image</label>
                  <input id="doctorUserImageInput" class="form-control" type="file" accept="image/*">
                </div>
                <div id="doctorUserImageName" class="small text-muted mt-2">No file selected</div>
              </div>
            </div>
          </div>
          <div class="docprof-field docprof-col-6">
            <label>Email</label>
            <input id="doctorUserEmailInput" class="form-control" readonly>
            <div class="form-text">Email is shown here for reference and cannot be edited from this page.</div>
          </div>
          <div class="docprof-field docprof-col-6"><label>Full Name</label><input id="doctorUserNameInput" class="form-control"></div>
          <div class="docprof-field docprof-col-6"><label>Phone Number</label><input id="doctorUserPhoneInput" class="form-control"></div>
          <div class="docprof-field docprof-col-6"><label>Alternative Email</label><input id="doctorUserAltEmailInput" class="form-control" type="email"></div>
          <div class="docprof-field docprof-col-6"><label>Alternative Phone</label><input id="doctorUserAltPhoneInput" class="form-control"></div>
          <div class="docprof-field docprof-col-6"><label>WhatsApp Number</label><input id="doctorUserWhatsappInput" class="form-control"></div>
          <div class="docprof-field docprof-col-12"><label>Address</label><textarea id="doctorUserAddressInput" class="form-control" rows="3"></textarea></div>
        </div>
      </div>

      <div class="docprof-panel docprof-section docprof-section-pane" id="doctor-basics">
        <div class="docprof-section-head">
          <div>
            <h3>Doctor Basics</h3>
            <p>Core professional data, fees, flags, and linked primary masters.</p>
          </div>
        </div>

        <div class="docprof-grid">
          <div class="docprof-field docprof-col-4"><label>Doctor Code</label><input id="doctorCode" class="form-control"></div>
          <div class="docprof-field docprof-col-4"><label>Designation</label><select id="doctorDesignationId" class="form-select"></select></div>
          <div class="docprof-field docprof-col-4"><label>Registration Council</label><select id="doctorRegistrationCouncilId" class="form-select"></select></div>
          <div class="docprof-field docprof-col-4"><label>Primary Hospital</label><select id="doctorPrimaryHospitalId" class="form-select"></select></div>
          <div class="docprof-field docprof-col-4"><label>Primary Department</label><select id="doctorPrimaryDepartmentId" class="form-select"></select></div>
          <div class="docprof-field docprof-col-4"><label>Primary Specialization</label><select id="doctorPrimarySpecializationId" class="form-select"></select></div>
          <div class="docprof-field docprof-col-6"><label>Qualification Summary</label><input id="doctorQualificationSummary" class="form-control"></div>
          <div class="docprof-field docprof-col-3"><label>Experience (Years)</label><input id="doctorYearsOfExperience" class="form-control" type="number" min="0"></div>
          <div class="docprof-field docprof-col-3"><label>Registration Year</label><input id="doctorRegistrationYear" class="form-control" type="number" min="1900" max="2100"></div>
          <div class="docprof-field docprof-col-4"><label>Medical Registration Number</label><input id="doctorMedicalRegistrationNumber" class="form-control"></div>
          <div class="docprof-field docprof-col-2"><label>Status</label><select id="doctorStatus" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
          <div class="docprof-field docprof-col-3"><label>Verification</label><select id="doctorVerificationStatus" class="form-select"><option value="pending">Pending</option><option value="verified">Verified</option><option value="rejected">Rejected</option></select></div>
          <div class="docprof-field docprof-col-3"><label>Visibility</label><select id="doctorProfileVisibility" class="form-select"><option value="public">Public</option><option value="private">Private</option></select></div>
          <div class="docprof-field docprof-col-2"><label>Sort Order</label><input id="doctorSortOrder" class="form-control" type="number" min="0" value="0"></div>
          <div class="docprof-field docprof-col-2"><label>Completion %</label><input id="doctorProfileCompletionPercentage" class="form-control" type="number" readonly></div>
          <div class="docprof-field docprof-col-3"><label>Total Patients</label><input id="doctorTotalPatientsTreated" class="form-control" type="number" min="0" value="0"></div>
          <div class="docprof-field docprof-col-3"><label>Total Surgeries</label><input id="doctorTotalSurgeries" class="form-control" type="number" min="0" value="0"></div>
          <div class="docprof-field docprof-col-3"><label>Total Consultations</label><input id="doctorTotalConsultations" class="form-control" type="number" min="0" value="0"></div>
          <div class="docprof-field docprof-col-3"><label>Review Count</label><input id="doctorReviewCount" class="form-control" type="number" min="0" value="0"></div>
          <div class="docprof-field docprof-col-3"><label>Average Rating</label><input id="doctorAverageRating" class="form-control" type="number" min="0" max="5" step="0.01" value="0"></div>
          <div class="docprof-field docprof-col-3"><label>Consultation Fee</label><input id="doctorConsultationFee" class="form-control" type="number" min="0" step="0.01"></div>
          <div class="docprof-field docprof-col-3"><label>Follow-up Fee</label><input id="doctorFollowupFee" class="form-control" type="number" min="0" step="0.01"></div>
          <div class="docprof-field docprof-col-3"><label>Video Fee</label><input id="doctorVideoConsultationFee" class="form-control" type="number" min="0" step="0.01"></div>
          <div class="docprof-field docprof-col-3"><label>Home Visit Fee</label><input id="doctorHomeVisitFee" class="form-control" type="number" min="0" step="0.01"></div>
        </div>

        <div class="docprof-switch-grid mt-3">
          <label class="docprof-switch"><span>Featured Doctor</span><input id="doctorFeaturedStatus" class="form-check-input" type="checkbox"></label>
          <label class="docprof-switch"><span>Online Consultation</span><input id="doctorOnlineConsultationAvailable" class="form-check-input" type="checkbox"></label>
          <label class="docprof-switch"><span>In-person Consultation</span><input id="doctorInPersonConsultationAvailable" class="form-check-input" type="checkbox" checked></label>
          <label class="docprof-switch"><span>Home Visit Available</span><input id="doctorHomeVisitAvailable" class="form-check-input" type="checkbox"></label>
          <label class="docprof-switch"><span>Appointment Booking</span><input id="doctorAppointmentBookingAvailable" class="form-check-input" type="checkbox" checked></label>
        </div>
      </div>

      <div class="docprof-panel docprof-section docprof-section-pane" id="doctor-bio">
        <div class="docprof-section-head"><div><h3>Bio & SEO</h3><p>Short profile copy and search metadata.</p></div></div>
        <div class="docprof-grid">
          <div class="docprof-field docprof-col-12"><label>Short Bio</label><textarea id="doctorShortBio" class="form-control" rows="2"></textarea></div>
          <div class="docprof-field docprof-col-12"><label>About Doctor</label><textarea id="doctorAboutDoctor" class="form-control" rows="5"></textarea></div>
          <div class="docprof-field docprof-col-6"><label>SEO Title</label><input id="doctorSeoTitle" class="form-control"></div>
          <div class="docprof-field docprof-col-6"><label>SEO Description</label><textarea id="doctorSeoDescription" class="form-control" rows="2"></textarea></div>
          <div class="docprof-field docprof-col-12"><label>Metadata JSON</label><textarea id="doctorMetadata" class="form-control" rows="4" placeholder='{"featured_note":"Top doctor"}'></textarea></div>
        </div>
      </div>

      <div class="docprof-panel docprof-section docprof-section-pane" id="doctor-media">
        <div class="docprof-section-head"><div><h3>Media</h3><p>Doctor-specific media beyond the base user avatar.</p></div></div>
        <div class="docprof-grid">
          <div class="docprof-col-6">
            <div class="docprof-upload">
              <div>
                <div id="doctorCoverPhotoFallback" class="docprof-upload-fallback"><i class="fa fa-image"></i></div>
                <img id="doctorCoverPhotoPreview" class="docprof-upload-preview" alt="Cover photo preview">
              </div>
              <div>
                <div class="docprof-field mb-0">
                  <label>Cover Photo</label>
                  <input id="doctorCoverPhoto" class="form-control" type="file" accept="image/*">
                </div>
                <div id="doctorCoverPhotoName" class="small text-muted mt-2">No file selected</div>
              </div>
            </div>
          </div>
          <div class="docprof-field docprof-col-6"><label>Gallery URLs / Paths</label><textarea id="doctorGallery" class="form-control" rows="4" placeholder="One per line or comma separated"></textarea></div>
        </div>
      </div>

      <div class="docprof-panel docprof-section docprof-section-pane" id="doctor-specializations">
        <div class="docprof-section-head"><div><h3>Specializations</h3><p>Select all relevant specializations and mark the primary one.</p></div></div>
        <div id="doctorSpecializationsWrap" class="docprof-check-grid"></div>
      </div>

      <div class="docprof-panel docprof-section docprof-section-pane" id="doctor-languages">
        <div class="docprof-section-head"><div><h3>Languages</h3><p>Toggle languages and assign proficiency.</p></div></div>
        <div id="doctorLanguagesWrap" class="docprof-check-grid"></div>
      </div>

      <div class="docprof-panel docprof-section docprof-section-pane" id="doctor-services">
        <div class="docprof-section-head"><div><h3>Services</h3><p>Link services and override fee or duration where needed.</p></div></div>
        <div id="doctorServicesWrap" class="docprof-check-grid"></div>
      </div>

      <div class="docprof-panel docprof-section docprof-section-pane" id="doctor-qualifications">
        <div class="docprof-section-head">
          <div><h3>Qualifications</h3><p>Add as many qualifications as needed with institute and year details.</p></div>
          <button type="button" class="btn btn-light btn-sm" id="doctorAddQualificationBtn"><i class="fa fa-plus me-1"></i>Add Qualification</button>
        </div>
        <div id="doctorQualificationsList" class="docprof-qual-list"></div>
      </div>

      <div class="docprof-panel docprof-section docprof-section-pane" id="doctor-clinics">
        <div class="docprof-section-head"><div><h3>Clinics</h3><p>Select clinics, define the primary one, and manage clinic-specific fees.</p></div></div>
        <div id="doctorClinicsWrap" class="docprof-check-grid"></div>
      </div>

      <div class="docprof-sticky-save">
        <button type="button" class="btn btn-primary btn-lg" id="doctorProfileSaveBottomBtn"><i class="fa fa-floppy-disk me-1"></i>Save Doctor Profile</button>
      </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="docprof-toast-wrap">
  <div id="doctorProfileToastOk" class="toast text-bg-success border-0"><div class="d-flex"><div id="doctorProfileToastOkText" class="toast-body">Done</div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>
  <div id="doctorProfileToastErr" class="toast text-bg-danger border-0 mt-2"><div class="d-flex"><div id="doctorProfileToastErrText" class="toast-body">Something went wrong</div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.__DOCTOR_PROFILE_INIT__) return;
  window.__DOCTOR_PROFILE_INIT__ = true;

  const token = localStorage.getItem('token') || sessionStorage.getItem('token') || '';
  if (!token) { window.location.href = '/'; return; }

  const userUuid = window.location.pathname.split('/').filter(Boolean).pop() || '';
  if (!userUuid) { window.location.href = '/users/manage'; return; }

  const okToast = new bootstrap.Toast(document.getElementById('doctorProfileToastOk'));
  const errToast = new bootstrap.Toast(document.getElementById('doctorProfileToastErr'));
  const ok = message => { document.getElementById('doctorProfileToastOkText').textContent = message || 'Done'; okToast.show(); };
  const err = message => { document.getElementById('doctorProfileToastErrText').textContent = message || 'Something went wrong'; errToast.show(); };
  const PROFILE_CACHE_TTL_MS = 1000 * 60 * 15;
  const PROFILE_CACHE_KEY = `doctor-profile:${(token || '').slice(0, 16)}:${userUuid}`;

  const state = {
    payload: null,
    options: {},
    relations: { specializations: [], languages: [], services: [], qualifications: [], clinics: [] },
    isDirty: false,
    activeSectionId: (window.location.hash || '#doctor-personal').replace('#', '') || 'doctor-personal',
  };

  const els = {
    navLinks: Array.from(document.querySelectorAll('#doctorProfileNav .docprof-side-link')),
    sidebar: document.getElementById('doctorProfileSidebar'),
    sidebarToggle: document.getElementById('doctorPageSidebarBadge'),
    sidebarOverlay: document.getElementById('doctorPageOverlay'),
    globalLoading: document.getElementById('doctorGlobalLoading'),
    backBtn: document.getElementById('doctorPageBackBtn'),
    headerUserName: document.getElementById('doctorPageUserName'),
    headerUserRole: document.getElementById('doctorPageUserRole'),
    headerUserAvatar: document.getElementById('doctorPageUserAvatar'),
    headerUserAvatarFallback: document.getElementById('doctorPageUserAvatarFallback'),
    userName: document.getElementById('doctorUserName'),
    userMeta: document.getElementById('doctorUserMeta'),
    userAvatar: document.getElementById('doctorUserAvatar'),
    userAvatarFallback: document.getElementById('doctorUserAvatarFallback'),
    dirtyState: document.getElementById('doctorProfileDirtyState'),
    reloadBtn: document.getElementById('doctorProfileReloadBtn'),
    saveTopBtn: document.getElementById('doctorProfileSaveTopBtn'),
    saveBottomBtn: document.getElementById('doctorProfileSaveBottomBtn'),
    userImageInput: document.getElementById('doctorUserImageInput'),
    userImagePreview: document.getElementById('doctorUserImagePreview'),
    userImageFallback: document.getElementById('doctorUserImageFallback'),
    userImageName: document.getElementById('doctorUserImageName'),
    coverPhoto: document.getElementById('doctorCoverPhoto'),
    coverPhotoPreview: document.getElementById('doctorCoverPhotoPreview'),
    coverPhotoFallback: document.getElementById('doctorCoverPhotoFallback'),
    coverPhotoName: document.getElementById('doctorCoverPhotoName'),
    specializationsWrap: document.getElementById('doctorSpecializationsWrap'),
    languagesWrap: document.getElementById('doctorLanguagesWrap'),
    servicesWrap: document.getElementById('doctorServicesWrap'),
    qualificationsList: document.getElementById('doctorQualificationsList'),
    addQualificationBtn: document.getElementById('doctorAddQualificationBtn'),
    clinicsWrap: document.getElementById('doctorClinicsWrap'),
  };

  const authHeaders = (extra = {}) => Object.assign({ Authorization: 'Bearer ' + token, Accept: 'application/json' }, extra);
  const esc = value => String(value ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
  const optionHtml = (items, selected, placeholder = 'Select') => [`<option value="">${placeholder}</option>`].concat((items || []).map(item => `<option value="${esc(item.id)}" ${String(item.id) === String(selected ?? '') ? 'selected' : ''}>${esc(item.label || item.name || '')}</option>`)).join('');
  const yesNoChecked = value => value ? 'checked' : '';
  const isMobileSidebar = () => window.innerWidth <= 991.98;
  const sectionIds = ['doctor-personal','doctor-basics','doctor-bio','doctor-media','doctor-specializations','doctor-languages','doctor-services','doctor-qualifications','doctor-clinics'];

  const fieldIds = [
    'doctorUserNameInput','doctorUserEmailInput','doctorUserPhoneInput','doctorUserAltEmailInput','doctorUserAltPhoneInput','doctorUserWhatsappInput','doctorUserAddressInput',
    'doctorCode','doctorDesignationId','doctorPrimaryHospitalId','doctorPrimaryDepartmentId','doctorPrimarySpecializationId','doctorRegistrationCouncilId',
    'doctorQualificationSummary','doctorYearsOfExperience','doctorMedicalRegistrationNumber','doctorRegistrationYear','doctorShortBio','doctorAboutDoctor',
    'doctorConsultationFee','doctorFollowupFee','doctorVideoConsultationFee','doctorHomeVisitFee','doctorTotalPatientsTreated','doctorTotalSurgeries',
    'doctorTotalConsultations','doctorAverageRating','doctorReviewCount','doctorVerificationStatus','doctorProfileVisibility','doctorStatus','doctorSortOrder',
    'doctorProfileCompletionPercentage','doctorSeoTitle','doctorSeoDescription','doctorGallery','doctorMetadata'
  ];
  const fieldMap = Object.fromEntries(fieldIds.map(id => [id, document.getElementById(id)]));

  function readProfileCache() {
    try {
      const raw = sessionStorage.getItem(PROFILE_CACHE_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch (_) {
      return null;
    }
  }

  function writeProfileCache(data) {
    try {
      sessionStorage.setItem(PROFILE_CACHE_KEY, JSON.stringify({ cached_at: Date.now(), data }));
    } catch (_) {}
  }

  function isFreshCache(cache) {
    return !!(cache && cache.cached_at && (Date.now() - Number(cache.cached_at) <= PROFILE_CACHE_TTL_MS) && cache.data);
  }

  function normalizePayload(data) {
    if (!data || typeof data !== 'object') return {};
    if (data.profile && typeof data.profile === 'object') {
      return Object.assign({}, data.profile, {
        relations: data.relations || data.profile.relations || {},
        options: data.options || data.profile.options || {},
      });
    }
    return data;
  }

  function openSidebar() {
    if (!isMobileSidebar() || !els.sidebar) return;
    els.sidebar.classList.add('is-open');
    els.sidebarOverlay?.classList.add('is-open');
    els.sidebarToggle?.setAttribute('aria-expanded', 'true');
    els.sidebarToggle?.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    els.sidebar?.classList.remove('is-open');
    els.sidebarOverlay?.classList.remove('is-open');
    els.sidebarToggle?.setAttribute('aria-expanded', 'false');
    els.sidebarToggle?.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  function setProfileLoading(loading) {
    if (!els.globalLoading) return;
    els.globalLoading.style.display = loading ? 'block' : 'none';
  }

  function setDirty(dirty) {
    state.isDirty = !!dirty;
    els.dirtyState?.classList.toggle('is-visible', state.isDirty);
  }

  async function api(url, options = {}) {
    const response = await fetch(url, { ...options, headers: authHeaders(options.headers || {}) });
    const json = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(json.message || json.error || 'Request failed');
    return json;
  }

  function setImagePreview(imgEl, fallbackEl, nameEl, src, fileName) {
    if (src) {
      imgEl.src = src;
      imgEl.style.display = 'block';
      fallbackEl.style.display = 'none';
    } else {
      imgEl.src = '';
      imgEl.style.display = 'none';
      fallbackEl.style.display = 'flex';
    }
    if (nameEl) nameEl.textContent = fileName || 'No file selected';
  }

  function bindPreview(inputEl, imageEl, fallbackEl, nameEl) {
    inputEl.addEventListener('change', () => {
      const file = inputEl.files && inputEl.files[0] ? inputEl.files[0] : null;
      if (!file) return setImagePreview(imageEl, fallbackEl, nameEl, '', 'No file selected');
      const objectUrl = URL.createObjectURL(file);
      setImagePreview(imageEl, fallbackEl, nameEl, objectUrl, file.name);
    });
  }

  function showSection(sectionId, options = {}) {
    const targetId = sectionIds.includes(sectionId) ? sectionId : 'doctor-personal';
    state.activeSectionId = targetId;
    sectionIds.forEach(id => {
      document.getElementById(id)?.classList.toggle('is-active', id === targetId);
    });
    els.navLinks.forEach(link => link.classList.toggle('active', (link.dataset.section || '') === targetId));
    if (options.updateHash !== false) {
      window.history.replaceState(null, '', `#${targetId}`);
    }
  }

  function fillUser(user) {
    els.userName.textContent = user.name || 'Doctor Profile';
    els.userMeta.textContent = [user.email || 'No email', user.phone_number || 'No phone'].join(' • ');
    els.headerUserName.textContent = user.name || 'Doctor';
    els.headerUserRole.textContent = user.role ? String(user.role).replace(/_/g, ' ') : 'Profile workspace';

    const initials = (user.name || 'DR').trim().split(/\s+/).slice(0,2).map(v => v.charAt(0).toUpperCase()).join('') || 'DR';
    if (user.image) {
      els.userAvatar.src = user.image;
      els.userAvatar.style.display = 'block';
      els.userAvatarFallback.style.display = 'none';
      els.headerUserAvatar.src = user.image;
      els.headerUserAvatar.style.display = 'block';
      els.headerUserAvatarFallback.style.display = 'none';
    } else {
      els.userAvatar.style.display = 'none';
      els.userAvatarFallback.style.display = 'flex';
      els.userAvatarFallback.textContent = initials;
      els.headerUserAvatar.style.display = 'none';
      els.headerUserAvatarFallback.style.display = 'flex';
      els.headerUserAvatarFallback.textContent = initials;
    }

    setImagePreview(
      els.userImagePreview,
      els.userImageFallback,
      els.userImageName,
      user.image || '',
      (user.image_path || '').split('/').pop() || 'No file selected'
    );
  }

  function fillPersonalFields(user) {
    fieldMap.doctorUserNameInput.value = user.name || '';
    fieldMap.doctorUserEmailInput.value = user.email || '';
    fieldMap.doctorUserPhoneInput.value = user.phone_number || '';
    fieldMap.doctorUserAltEmailInput.value = user.alternative_email || '';
    fieldMap.doctorUserAltPhoneInput.value = user.alternative_phone_number || '';
    fieldMap.doctorUserWhatsappInput.value = user.whatsapp_number || '';
    fieldMap.doctorUserAddressInput.value = user.address || '';
  }

  function setSimpleFields(profile) {
    const doctor = profile.doctor || {};
    fieldMap.doctorCode.value = doctor.doctor_code || '';
    fieldMap.doctorQualificationSummary.value = doctor.qualification_summary || '';
    fieldMap.doctorYearsOfExperience.value = doctor.years_of_experience ?? 0;
    fieldMap.doctorMedicalRegistrationNumber.value = doctor.medical_registration_number || '';
    fieldMap.doctorRegistrationYear.value = doctor.registration_year || '';
    fieldMap.doctorShortBio.value = doctor.short_bio || '';
    fieldMap.doctorAboutDoctor.value = doctor.about_doctor || '';
    fieldMap.doctorConsultationFee.value = doctor.consultation_fee ?? '';
    fieldMap.doctorFollowupFee.value = doctor.followup_fee ?? '';
    fieldMap.doctorVideoConsultationFee.value = doctor.video_consultation_fee ?? '';
    fieldMap.doctorHomeVisitFee.value = doctor.home_visit_fee ?? '';
    fieldMap.doctorTotalPatientsTreated.value = doctor.total_patients_treated ?? 0;
    fieldMap.doctorTotalSurgeries.value = doctor.total_surgeries ?? 0;
    fieldMap.doctorTotalConsultations.value = doctor.total_consultations ?? 0;
    fieldMap.doctorAverageRating.value = doctor.average_rating ?? 0;
    fieldMap.doctorReviewCount.value = doctor.review_count ?? 0;
    fieldMap.doctorVerificationStatus.value = doctor.verification_status || 'pending';
    fieldMap.doctorProfileVisibility.value = doctor.profile_visibility || 'public';
    fieldMap.doctorStatus.value = doctor.status || 'active';
    fieldMap.doctorSortOrder.value = doctor.sort_order ?? 0;
    fieldMap.doctorProfileCompletionPercentage.value = doctor.profile_completion_percentage ?? 0;
    fieldMap.doctorSeoTitle.value = doctor.seo_title || '';
    fieldMap.doctorSeoDescription.value = doctor.seo_description || '';
    fieldMap.doctorGallery.value = Array.isArray(doctor.gallery) ? doctor.gallery.join('\n') : '';
    fieldMap.doctorMetadata.value = doctor.metadata ? JSON.stringify(doctor.metadata, null, 2) : '';
    document.getElementById('doctorFeaturedStatus').checked = !!doctor.featured_status;
    document.getElementById('doctorOnlineConsultationAvailable').checked = !!doctor.online_consultation_available;
    document.getElementById('doctorInPersonConsultationAvailable').checked = doctor.in_person_consultation_available !== false;
    document.getElementById('doctorHomeVisitAvailable').checked = !!doctor.home_visit_available;
    document.getElementById('doctorAppointmentBookingAvailable').checked = doctor.appointment_booking_available !== false;

    document.getElementById('doctorDesignationId').innerHTML = optionHtml(state.options.designations, doctor.designation_id, 'Select designation');
    document.getElementById('doctorPrimaryHospitalId').innerHTML = optionHtml(state.options.hospitals, doctor.primary_hospital_id, 'Select hospital');
    document.getElementById('doctorPrimaryDepartmentId').innerHTML = optionHtml(state.options.departments, doctor.primary_department_id, 'Select department');
    document.getElementById('doctorPrimarySpecializationId').innerHTML = optionHtml(state.options.specializations, doctor.primary_specialization_id, 'Select primary specialization');
    document.getElementById('doctorRegistrationCouncilId').innerHTML = optionHtml(state.options.registration_councils, doctor.registration_council_id, 'Select council');

    setImagePreview(els.coverPhotoPreview, els.coverPhotoFallback, els.coverPhotoName, doctor.cover_photo || '', (doctor.cover_photo_path || '').split('/').pop() || 'No file selected');
  }

  function renderSpecializations() {
    const selected = new Map((state.relations.specializations || []).map(item => [String(item.specialization_id), item]));
    els.specializationsWrap.innerHTML = (state.options.specializations || []).map((item, index) => {
      const row = selected.get(String(item.id));
      return `
        <div class="docprof-card ${row ? 'is-selected' : ''}" data-card-type="specialization">
          <div class="docprof-card-top">
            <label class="form-check m-0">
              <input class="form-check-input js-doc-spec-check" type="checkbox" value="${item.id}" ${row ? 'checked' : ''}>
              <span class="form-check-label ms-2">
                <span class="docprof-card-title">${esc(item.name)}</span>
                <span class="docprof-card-sub d-block">${esc(item.short_form || '')}</span>
              </span>
            </label>
            <label class="small text-muted d-flex align-items-center gap-2"><input class="form-check-input js-doc-spec-primary" name="doctorPrimarySpecializationCheck" type="radio" value="${item.id}" ${row && row.is_primary ? 'checked' : ''}>Primary</label>
          </div>
          <div class="docprof-card-body">
            <div class="docprof-field"><label>Sort Order</label><input class="form-control js-doc-spec-sort" type="number" min="0" value="${row ? row.sort_order : index}"></div>
          </div>
        </div>
      `;
    }).join('');
  }

  function renderLanguages() {
    const selected = new Map((state.relations.languages || []).map(item => [String(item.language_id), item]));
    els.languagesWrap.innerHTML = (state.options.languages || []).map((item, index) => {
      const row = selected.get(String(item.id));
      return `
        <div class="docprof-card ${row ? 'is-selected' : ''}" data-card-type="language">
          <div class="docprof-card-top">
            <label class="form-check m-0">
              <input class="form-check-input js-doc-lang-check" type="checkbox" value="${item.id}" ${row ? 'checked' : ''}>
              <span class="form-check-label ms-2">
                <span class="docprof-card-title">${esc(item.name)}</span>
                <span class="docprof-card-sub d-block">${esc(item.code || '')}</span>
              </span>
            </label>
          </div>
          <div class="docprof-card-body">
            <div class="docprof-inline-grid">
              <div class="docprof-field"><label>Proficiency</label><select class="form-select js-doc-lang-level"><option value="">Select</option><option value="basic" ${row?.proficiency_level === 'basic' ? 'selected' : ''}>Basic</option><option value="fluent" ${row?.proficiency_level === 'fluent' ? 'selected' : ''}>Fluent</option><option value="native" ${row?.proficiency_level === 'native' ? 'selected' : ''}>Native</option></select></div>
              <div class="docprof-field"><label>Sort Order</label><input class="form-control js-doc-lang-sort" type="number" min="0" value="${row ? row.sort_order : index}"></div>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  function renderServices() {
    const selected = new Map((state.relations.services || []).map(item => [String(item.service_id), item]));
    els.servicesWrap.innerHTML = (state.options.services || []).map((item, index) => {
      const row = selected.get(String(item.id));
      return `
        <div class="docprof-card ${row ? 'is-selected' : ''}" data-card-type="service">
          <div class="docprof-card-top">
            <label class="form-check m-0">
              <input class="form-check-input js-doc-service-check" type="checkbox" value="${item.id}" ${row ? 'checked' : ''}>
              <span class="form-check-label ms-2">
                <span class="docprof-card-title">${esc(item.name)}</span>
                <span class="docprof-card-sub d-block">Default fee: ${esc(item.default_price ?? '—')} | ${esc(item.default_duration_minutes ?? '—')} min</span>
              </span>
            </label>
          </div>
          <div class="docprof-card-body">
            <div class="docprof-inline-grid">
              <div class="docprof-field"><label>Custom Fee</label><input class="form-control js-doc-service-fee" type="number" min="0" step="0.01" value="${row?.custom_fee ?? ''}"></div>
              <div class="docprof-field"><label>Custom Duration</label><input class="form-control js-doc-service-duration" type="number" min="0" value="${row?.custom_duration_minutes ?? ''}"></div>
            </div>
            <div class="docprof-inline-grid">
              <div class="docprof-field"><label>Notes</label><textarea class="form-control js-doc-service-notes" rows="2">${esc(row?.notes || '')}</textarea></div>
              <div class="docprof-field"><label>Sort Order</label><input class="form-control js-doc-service-sort" type="number" min="0" value="${row ? row.sort_order : index}"></div>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  function qualificationRow(data = {}) {
    return `
      <div class="docprof-qual-card">
        <div class="docprof-grid">
          <div class="docprof-field docprof-col-4"><label>Qualification</label><select class="form-select js-doc-qual-id">${optionHtml(state.options.qualifications || [], data.qualification_id, 'Select qualification')}</select></div>
          <div class="docprof-field docprof-col-4"><label>Institute</label><input class="form-control js-doc-qual-institute" value="${esc(data.institute_name || '')}"></div>
          <div class="docprof-field docprof-col-4"><label>University</label><input class="form-control js-doc-qual-university" value="${esc(data.university_name || '')}"></div>
          <div class="docprof-field docprof-col-3"><label>Country</label><input class="form-control js-doc-qual-country" value="${esc(data.country || '')}"></div>
          <div class="docprof-field docprof-col-3"><label>Start Year</label><input class="form-control js-doc-qual-start" type="number" min="1900" max="2100" value="${esc(data.start_year || '')}"></div>
          <div class="docprof-field docprof-col-3"><label>End Year</label><input class="form-control js-doc-qual-end" type="number" min="1900" max="2100" value="${esc(data.end_year || '')}"></div>
          <div class="docprof-field docprof-col-3"><label>Sort Order</label><input class="form-control js-doc-qual-sort" type="number" min="0" value="${esc(data.sort_order ?? 0)}"></div>
          <div class="docprof-field docprof-col-12"><label>Description</label><textarea class="form-control js-doc-qual-description" rows="2">${esc(data.description || '')}</textarea></div>
        </div>
        <div class="docprof-qual-actions">
          <button type="button" class="btn btn-light btn-sm text-danger js-doc-qual-remove"><i class="fa fa-trash me-1"></i>Remove</button>
        </div>
      </div>
    `;
  }

  function renderQualifications() {
    const rows = state.relations.qualifications || [];
    if (!rows.length) {
      els.qualificationsList.innerHTML = `<div class="docprof-empty">No qualification rows added yet.</div>`;
      return;
    }

    els.qualificationsList.innerHTML = rows.map(row => qualificationRow(row)).join('');
  }

  function renderClinics() {
    const selected = new Map((state.relations.clinics || []).map(item => [String(item.clinic_id), item]));
    els.clinicsWrap.innerHTML = (state.options.clinics || []).map((item, index) => {
      const row = selected.get(String(item.id));
      return `
        <div class="docprof-card ${row ? 'is-selected' : ''}" data-card-type="clinic">
          <div class="docprof-card-top">
            <label class="form-check m-0">
              <input class="form-check-input js-doc-clinic-check" type="checkbox" value="${item.id}" ${row ? 'checked' : ''}>
              <span class="form-check-label ms-2">
                <span class="docprof-card-title">${esc(item.name)}</span>
                <span class="docprof-card-sub d-block">${esc(item.clinic_code || '')}</span>
              </span>
            </label>
            <label class="small text-muted d-flex align-items-center gap-2"><input class="form-check-input js-doc-clinic-primary" name="doctorPrimaryClinicCheck" type="radio" value="${item.id}" ${row && row.is_primary ? 'checked' : ''}>Primary</label>
          </div>
          <div class="docprof-card-body">
            <div class="docprof-inline-grid">
              <div class="docprof-field"><label>Consultation Fee</label><input class="form-control js-doc-clinic-fee" type="number" min="0" step="0.01" value="${row?.consultation_fee ?? ''}"></div>
              <div class="docprof-field"><label>Follow-up Fee</label><input class="form-control js-doc-clinic-followup" type="number" min="0" step="0.01" value="${row?.followup_fee ?? ''}"></div>
              <div class="docprof-field"><label>Video Fee</label><input class="form-control js-doc-clinic-video" type="number" min="0" step="0.01" value="${row?.video_consultation_fee ?? ''}"></div>
              <div class="docprof-field"><label>Room No</label><input class="form-control js-doc-clinic-room" value="${esc(row?.room_no || '')}"></div>
              <div class="docprof-field"><label>Visit Note</label><input class="form-control js-doc-clinic-note" value="${esc(row?.visit_note || '')}"></div>
              <div class="docprof-field"><label>Sort Order</label><input class="form-control js-doc-clinic-sort" type="number" min="0" value="${row ? row.sort_order : index}"></div>
            </div>
            <div class="docprof-switch-grid">
              <label class="docprof-switch"><span>Online Consultation</span><input class="form-check-input js-doc-clinic-online" type="checkbox" ${yesNoChecked(row?.online_consultation_available)}></label>
              <label class="docprof-switch"><span>In-person Consultation</span><input class="form-check-input js-doc-clinic-inperson" type="checkbox" ${row ? yesNoChecked(row.in_person_consultation_available) : 'checked'}></label>
              <label class="docprof-switch"><span>Appointment Booking</span><input class="form-check-input js-doc-clinic-booking" type="checkbox" ${row ? yesNoChecked(row.appointment_booking_available) : 'checked'}></label>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  function attachCardSelectionHandlers(wrap, checkboxSelector) {
    wrap.querySelectorAll(checkboxSelector).forEach(input => {
      input.addEventListener('change', function () {
        const card = this.closest('.docprof-card');
        if (card) card.classList.toggle('is-selected', this.checked);
      });
    });
  }

  function collectSpecializations() {
    return Array.from(els.specializationsWrap.querySelectorAll('.docprof-card')).map(card => {
      const check = card.querySelector('.js-doc-spec-check');
      if (!check.checked) return null;
      return {
        specialization_id: Number(check.value),
        is_primary: !!card.querySelector('.js-doc-spec-primary')?.checked,
        sort_order: Number(card.querySelector('.js-doc-spec-sort')?.value || 0),
      };
    }).filter(Boolean);
  }

  function collectLanguages() {
    return Array.from(els.languagesWrap.querySelectorAll('.docprof-card')).map(card => {
      const check = card.querySelector('.js-doc-lang-check');
      if (!check.checked) return null;
      return {
        language_id: Number(check.value),
        proficiency_level: card.querySelector('.js-doc-lang-level')?.value || '',
        sort_order: Number(card.querySelector('.js-doc-lang-sort')?.value || 0),
      };
    }).filter(Boolean);
  }

  function collectServices() {
    return Array.from(els.servicesWrap.querySelectorAll('.docprof-card')).map(card => {
      const check = card.querySelector('.js-doc-service-check');
      if (!check.checked) return null;
      return {
        service_id: Number(check.value),
        custom_fee: card.querySelector('.js-doc-service-fee')?.value || '',
        custom_duration_minutes: card.querySelector('.js-doc-service-duration')?.value || '',
        notes: card.querySelector('.js-doc-service-notes')?.value || '',
        sort_order: Number(card.querySelector('.js-doc-service-sort')?.value || 0),
      };
    }).filter(Boolean);
  }

  function collectQualifications() {
    const rows = Array.from(els.qualificationsList.querySelectorAll('.docprof-qual-card'));
    return rows.map((row, index) => ({
      qualification_id: Number(row.querySelector('.js-doc-qual-id')?.value || 0),
      institute_name: row.querySelector('.js-doc-qual-institute')?.value || '',
      university_name: row.querySelector('.js-doc-qual-university')?.value || '',
      country: row.querySelector('.js-doc-qual-country')?.value || '',
      start_year: row.querySelector('.js-doc-qual-start')?.value || '',
      end_year: row.querySelector('.js-doc-qual-end')?.value || '',
      description: row.querySelector('.js-doc-qual-description')?.value || '',
      sort_order: Number(row.querySelector('.js-doc-qual-sort')?.value || index),
    })).filter(row => row.qualification_id > 0);
  }

  function collectClinics() {
    return Array.from(els.clinicsWrap.querySelectorAll('.docprof-card')).map(card => {
      const check = card.querySelector('.js-doc-clinic-check');
      if (!check.checked) return null;
      return {
        clinic_id: Number(check.value),
        is_primary: !!card.querySelector('.js-doc-clinic-primary')?.checked,
        consultation_fee: card.querySelector('.js-doc-clinic-fee')?.value || '',
        followup_fee: card.querySelector('.js-doc-clinic-followup')?.value || '',
        video_consultation_fee: card.querySelector('.js-doc-clinic-video')?.value || '',
        online_consultation_available: !!card.querySelector('.js-doc-clinic-online')?.checked,
        in_person_consultation_available: !!card.querySelector('.js-doc-clinic-inperson')?.checked,
        appointment_booking_available: !!card.querySelector('.js-doc-clinic-booking')?.checked,
        room_no: card.querySelector('.js-doc-clinic-room')?.value || '',
        visit_note: card.querySelector('.js-doc-clinic-note')?.value || '',
        sort_order: Number(card.querySelector('.js-doc-clinic-sort')?.value || 0),
      };
    }).filter(Boolean);
  }

  function populate(data) {
    state.payload = data;
    state.options = data.options || {};
    state.relations = data.relations || { specializations: [], languages: [], services: [], qualifications: [], clinics: [] };

    fillUser(data.user || {});
    fillPersonalFields(data.user || {});
    setSimpleFields(data);
    renderSpecializations();
    renderLanguages();
    renderServices();
    renderQualifications();
    renderClinics();
    attachCardSelectionHandlers(els.specializationsWrap, '.js-doc-spec-check');
    attachCardSelectionHandlers(els.languagesWrap, '.js-doc-lang-check');
    attachCardSelectionHandlers(els.servicesWrap, '.js-doc-service-check');
    attachCardSelectionHandlers(els.clinicsWrap, '.js-doc-clinic-check');
    setDirty(false);
    requestAnimationFrame(() => showSection(state.activeSectionId, { updateHash: false }));
  }

  async function loadProfile(options = {}) {
    const forceRefresh = !!options.forceRefresh;
    const skipCachedRender = !!options.skipCachedRender;
    const cached = readProfileCache();

    if (cached?.data && !skipCachedRender) {
      populate(cached.data);
    }

    if (!forceRefresh && isFreshCache(cached)) {
      return;
    }

    setProfileLoading(true);
    try {
      const json = await api(`/api/doctors/profile/${encodeURIComponent(userUuid)}`);
      const payload = normalizePayload(json.data || {});
      writeProfileCache(payload);
      populate(payload);
    } finally {
      setProfileLoading(false);
    }
  }

  function appendJsonOrThrow(fd, key, value) {
    try {
      fd.append(key, JSON.stringify(value));
    } catch (_) {
      throw new Error(`Invalid ${key}`);
    }
  }

  async function saveProfile() {
    try {
      els.saveTopBtn.disabled = true;
      els.saveBottomBtn.disabled = true;

      const fd = new FormData();
      fd.append('user_name', fieldMap.doctorUserNameInput.value.trim());
      fd.append('user_phone_number', fieldMap.doctorUserPhoneInput.value.trim());
      fd.append('user_alternative_email', fieldMap.doctorUserAltEmailInput.value.trim());
      fd.append('user_alternative_phone_number', fieldMap.doctorUserAltPhoneInput.value.trim());
      fd.append('user_whatsapp_number', fieldMap.doctorUserWhatsappInput.value.trim());
      fd.append('user_address', fieldMap.doctorUserAddressInput.value.trim());
      fd.append('doctor_code', fieldMap.doctorCode.value.trim());
      fd.append('designation_id', document.getElementById('doctorDesignationId').value);
      fd.append('primary_hospital_id', document.getElementById('doctorPrimaryHospitalId').value);
      fd.append('primary_department_id', document.getElementById('doctorPrimaryDepartmentId').value);
      fd.append('primary_specialization_id', document.getElementById('doctorPrimarySpecializationId').value);
      fd.append('registration_council_id', document.getElementById('doctorRegistrationCouncilId').value);
      fd.append('qualification_summary', fieldMap.doctorQualificationSummary.value.trim());
      fd.append('years_of_experience', fieldMap.doctorYearsOfExperience.value || '0');
      fd.append('medical_registration_number', fieldMap.doctorMedicalRegistrationNumber.value.trim());
      fd.append('registration_year', fieldMap.doctorRegistrationYear.value.trim());
      fd.append('short_bio', fieldMap.doctorShortBio.value.trim());
      fd.append('about_doctor', fieldMap.doctorAboutDoctor.value.trim());
      fd.append('consultation_fee', fieldMap.doctorConsultationFee.value.trim());
      fd.append('followup_fee', fieldMap.doctorFollowupFee.value.trim());
      fd.append('video_consultation_fee', fieldMap.doctorVideoConsultationFee.value.trim());
      fd.append('home_visit_fee', fieldMap.doctorHomeVisitFee.value.trim());
      fd.append('total_patients_treated', fieldMap.doctorTotalPatientsTreated.value || '0');
      fd.append('total_surgeries', fieldMap.doctorTotalSurgeries.value || '0');
      fd.append('total_consultations', fieldMap.doctorTotalConsultations.value || '0');
      fd.append('average_rating', fieldMap.doctorAverageRating.value || '0');
      fd.append('review_count', fieldMap.doctorReviewCount.value || '0');
      fd.append('featured_status', document.getElementById('doctorFeaturedStatus').checked ? '1' : '0');
      fd.append('verification_status', fieldMap.doctorVerificationStatus.value);
      fd.append('profile_visibility', fieldMap.doctorProfileVisibility.value);
      fd.append('status', fieldMap.doctorStatus.value);
      fd.append('sort_order', fieldMap.doctorSortOrder.value || '0');
      fd.append('online_consultation_available', document.getElementById('doctorOnlineConsultationAvailable').checked ? '1' : '0');
      fd.append('in_person_consultation_available', document.getElementById('doctorInPersonConsultationAvailable').checked ? '1' : '0');
      fd.append('home_visit_available', document.getElementById('doctorHomeVisitAvailable').checked ? '1' : '0');
      fd.append('appointment_booking_available', document.getElementById('doctorAppointmentBookingAvailable').checked ? '1' : '0');
      fd.append('seo_title', fieldMap.doctorSeoTitle.value.trim());
      fd.append('seo_description', fieldMap.doctorSeoDescription.value.trim());
      if (fieldMap.doctorMetadata.value.trim()) {
        JSON.parse(fieldMap.doctorMetadata.value);
        fd.append('metadata', fieldMap.doctorMetadata.value.trim());
      }
      appendJsonOrThrow(fd, 'gallery', (fieldMap.doctorGallery.value || '').split(/[\n,]+/).map(v => v.trim()).filter(Boolean));
      appendJsonOrThrow(fd, 'specializations', collectSpecializations());
      appendJsonOrThrow(fd, 'languages', collectLanguages());
      appendJsonOrThrow(fd, 'services', collectServices());
      appendJsonOrThrow(fd, 'qualifications', collectQualifications());
      appendJsonOrThrow(fd, 'clinics', collectClinics());
      if (els.userImageInput.files && els.userImageInput.files[0]) fd.append('user_image', els.userImageInput.files[0]);
      if (els.coverPhoto.files && els.coverPhoto.files[0]) fd.append('cover_photo', els.coverPhoto.files[0]);

      const res = await fetch(`/api/doctors/profile/${encodeURIComponent(userUuid)}`, {
        method: 'POST',
        headers: authHeaders(),
        body: fd,
      });
      const json = await res.json().catch(() => ({}));
      if (!res.ok) {
        let message = json.message || 'Failed to save doctor profile';
        if (json.errors) {
          const firstKey = Object.keys(json.errors)[0];
          if (firstKey && json.errors[firstKey] && json.errors[firstKey][0]) message = json.errors[firstKey][0];
        }
        throw new Error(message);
      }

      ok(json.message || 'Doctor profile saved');
      if (json.data) {
        const payload = normalizePayload(json.data);
        writeProfileCache(payload);
        populate(payload);
      } else {
        await loadProfile({ forceRefresh: true, skipCachedRender: true });
      }
    } catch (error) {
      err(error.message || 'Failed to save doctor profile');
    } finally {
      els.saveTopBtn.disabled = false;
      els.saveBottomBtn.disabled = false;
    }
  }

  els.addQualificationBtn.addEventListener('click', () => {
    const emptyState = els.qualificationsList.querySelector('.docprof-empty');
    if (emptyState) els.qualificationsList.innerHTML = '';
    els.qualificationsList.insertAdjacentHTML('beforeend', qualificationRow({ sort_order: els.qualificationsList.querySelectorAll('.docprof-qual-card').length }));
    setDirty(true);
  });

  els.qualificationsList.addEventListener('click', function (event) {
    const removeBtn = event.target.closest('.js-doc-qual-remove');
    if (!removeBtn) return;
    const card = removeBtn.closest('.docprof-qual-card');
    if (card) card.remove();
    if (!els.qualificationsList.querySelector('.docprof-qual-card')) {
      els.qualificationsList.innerHTML = `<div class="docprof-empty">No qualification rows added yet.</div>`;
    }
    setDirty(true);
  });

  els.saveTopBtn.addEventListener('click', saveProfile);
  els.saveBottomBtn.addEventListener('click', saveProfile);
  els.reloadBtn.addEventListener('click', () => loadProfile({ forceRefresh: true, skipCachedRender: true }));
  bindPreview(els.userImageInput, els.userImagePreview, els.userImageFallback, els.userImageName);
  bindPreview(els.coverPhoto, els.coverPhotoPreview, els.coverPhotoFallback, els.coverPhotoName);
  document.querySelector('.docprof-main')?.addEventListener('input', () => setDirty(true), true);
  document.querySelector('.docprof-main')?.addEventListener('change', () => setDirty(true), true);
  els.navLinks.forEach(link => link.addEventListener('click', event => {
    event.preventDefault();
    showSection(link.dataset.section || 'doctor-personal');
    if (isMobileSidebar()) closeSidebar();
  }));
  els.sidebarToggle?.addEventListener('click', () => {
    if (els.sidebar?.classList.contains('is-open')) closeSidebar();
    else openSidebar();
  });
  els.sidebarOverlay?.addEventListener('click', closeSidebar);
  els.backBtn?.addEventListener('click', () => {
    if (window.history.length > 1) {
      window.history.back();
      return;
    }
    window.location.href = '/user/manage';
  });
  window.addEventListener('resize', () => {
    if (!isMobileSidebar()) closeSidebar();
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') closeSidebar();
  });
  window.addEventListener('beforeunload', event => {
    if (!state.isDirty) return;
    event.preventDefault();
    event.returnValue = '';
  });

  loadProfile().then(() => showSection(state.activeSectionId, { updateHash: false })).catch(error => err(error.message || 'Failed to load doctor profile'));
});
</script>
