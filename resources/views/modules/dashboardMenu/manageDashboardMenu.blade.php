{{-- resources/views/modules/dashboardMenu/manageDashboardMenu.blade.php --}}
@extends('pages.layout.structure')
@section('title', 'Manage Dashboard Menu')

@php
  $dmUid = 'dm_' . \Illuminate\Support\Str::random(8);

  // Web URLs
  $dmCreateUrl    = url('/dashboard-menu/create');
  $dmEditPattern  = url('/dashboard-menu/create') . '?edit={id}';

  // API URLs
  $apiBase  = url('/api/dashboard-menus');
  $apiTree  = url('/api/dashboard-menus/tree?only_active=1'); // Active tree
@endphp

@push('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

  <style>
    .mdm-wrap{
      padding:2px 0;
      overflow:visible;
    }

    .mdm-head{
      padding:10px 12px;
      margin-bottom:12px;
    }

    .mdm-head-row{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
    }

    .mdm-head-copy{
      display:flex;
      align-items:center;
      min-height:30px;
    }

    .mdm-head-copy h1{
      margin:0;
      font-size:var(--fs-15);
      line-height:1.15;
      display:flex;
      align-items:center;
      gap:6px;
    }

    .mdm-head-copy .seg-muted{
      color:var(--muted-color);
      font-weight:500;
    }

    .mdm-head-copy .seg-sep{
      color:var(--muted-color);
      opacity:.7;
    }

    .mdm-head-actions{
      display:flex;
      align-items:center;
      gap:8px;
    }

    .mdm-refresh-btn{
      min-width:30px;
      width:30px;
      height:30px;
      padding:0;
      display:inline-flex;
      align-items:center;
      justify-content:center;
    }

    .mdm-refresh-btn.is-spinning i{
      animation:mdmSpin .8s linear infinite;
    }

    @keyframes mdmSpin{
      to{ transform:rotate(360deg); }
    }

    .mdm-toolbar{
      padding:10px;
      margin-bottom:12px;
    }

    .mdm-search-wrap{
      position:relative;
    }

    .mdm-search-wrap .fa-search{
      position:absolute;
      left:10px;
      top:50%;
      transform:translateY(-50%);
      opacity:.55;
      pointer-events:none;
    }

    .mdm-search-wrap .form-control{
      padding-left:34px;
    }

    .mdm-inline-actions{
      display:flex;
      gap:8px;
      flex-wrap:wrap;
      align-items:center;
    }

    .mdm-inline-actions .btn{
      white-space:nowrap;
    }

    .panel{
      background:var(--surface);
      border:1px solid var(--line-strong);
      border-radius:16px;
      box-shadow:var(--shadow-2);
    }

    .mfa-toolbar .form-control{height:40px;border-radius:12px;border:1px solid var(--line-strong);background:var(--surface)}
    .mfa-toolbar .form-select{height:40px;border-radius:12px;border:1px solid var(--line-strong);background:var(--surface)}
    .mfa-toolbar .btn-light{background:var(--surface);border:1px solid var(--line-strong)}
    .mfa-toolbar .btn-primary{background:var(--primary-color);border:none}

    /* Card */
    .table-wrap.card{position:relative;border:1px solid var(--line-strong);border-radius:16px;background:var(--surface);box-shadow:var(--shadow-2);overflow:visible}
    .table-wrap .card-body{overflow:visible}
    .table-responsive{overflow:visible !important}
    .table{--bs-table-bg:transparent}
    .table thead th{font-weight:600;color:var(--muted-color);font-size:13px;border-bottom:1px solid(var(--line-strong));background:var(--surface)}
    .table thead.sticky-top{z-index:3}
    .table tbody tr{border-top:1px solid var(--line-soft)}
    .table tbody tr:hover{background:var(--page-hover)}
    .small{font-size:12.5px}

    /* Empty & loader */
    .empty{color:var(--muted-color)}
    .placeholder{background:linear-gradient(90deg,#00000010,#00000005,#00000010);border-radius:8px}

    /* Badges */
    .badge-soft{background:color-mix(in oklab, var(--muted-color) 12%, transparent);color:var(--ink)}
    .badge.badge-success{background:var(--success-color)!important;color:#fff!important}
    .badge.badge-secondary{background:#64748b!important;color:#fff!important}
    .badge.badge-warning{background:#f59e0b!important;color:#111827!important}

    /* ===== Tree (Unlimited hierarchy) ===== */
    .dm-tree{padding:14px}
    .dm-list{list-style:none;margin:0;padding:0}
    .dm-item{margin:0;padding:0}
    .dm-row{
      --level:0;
      display:flex;
      align-items:flex-start;
      gap:10px;
      padding:10px 12px;
      padding-left: calc(12px + (var(--level) * 18px));
      border:1px solid var(--line-soft);
      border-radius:14px;
      background:var(--surface);
      box-shadow:var(--shadow-1);
      margin-bottom:8px;
    }
    .dm-title{font-weight:600}
    .dm-meta{font-size:12px;color:var(--muted-color);margin-top:2px}
    .dm-actions{margin-left:auto;display:flex;gap:8px;align-items:center}
    .dm-actions .btn{height:34px;border-radius:10px}
    .dm-toggle{
      width:30px;height:30px;border-radius:10px;
      border:1px solid var(--line-strong);
      background:var(--surface);
      display:inline-flex;align-items:center;justify-content:center;
      flex:0 0 auto;
      cursor:pointer;
    }
    .dm-toggle i{transition:transform .15s ease}
    .dm-item.is-collapsed > .dm-children{display:none}
    .dm-item.is-collapsed > .dm-row .dm-toggle i{transform:rotate(-90deg)}
    .dm-toggle[disabled]{opacity:.45;cursor:default}

    .drag-handle{
      display:inline-flex;align-items:center;justify-content:center;
      width:26px;height:26px;border-radius:10px;
      color:#9ca3af;cursor:grab;flex:0 0 auto;
      border:1px dashed transparent;
    }
    .dm-reorder-on .drag-handle{border-color:var(--line-soft)}
    .drag-handle:active{cursor:grabbing}
    .drag-ghost{opacity:.55}
    .drag-chosen{box-shadow:var(--shadow-2)}
    .dm-reorder-note{display:none}
    .dm-reorder-on .dm-reorder-note{display:block}

    /* ✅ Save Order button spinner state */
    .dm-btn-loading{pointer-events:none; opacity:.95}
    .dm-btn-loading .btn-spinner{display:inline-block !important}
    .dm-btn-loading .btn-icon{display:none !important}

    .dm-dd{
      position:relative;
      margin-left:auto;
      flex:0 0 auto;
    }

    .dm-dd-btn{
      min-width:34px;
      width:34px;
      height:34px;
      padding:0;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border-radius:10px;
    }

    .dm-dd-btn.is-open{
      color:var(--primary-color);
      border-color:var(--primary-color);
      background:rgba(149,30,170,.08);
    }

    .dm-dd .dropdown-menu{
      border-radius:14px;
      border:1px solid var(--line-strong);
      box-shadow:var(--shadow-2);
      min-width:210px;
      padding:8px;
    }

    .dm-dd .dropdown-item{
      border-radius:10px;
      display:flex;
      align-items:center;
      gap:10px;
      padding:9px 10px;
      font-size:13px;
    }

    .dm-dd .dropdown-item i{
      width:16px;
      text-align:center;
      color:var(--muted-color);
    }

    .dm-dd .dropdown-item.text-danger i{
      color:inherit;
    }

    .dm-dd .dropdown-divider{
      margin:6px 0;
    }

    /* Dark tweaks */
    html.theme-dark .panel,
    html.theme-dark .table-wrap.card{background:#0f172a;border-color:var(--line-strong)}
    html.theme-dark .table thead th{background:#0f172a;border-color:var(--line-strong);color:#94a3b8}
    html.theme-dark .dm-row{background:#0f172a;border-color:var(--line-soft)}
    html.theme-dark .dm-toggle{background:#0f172a}
    html.theme-dark .dm-dd .dropdown-menu{background:#0f172a;border-color:var(--line-strong)}

    /* ============================
      FIX: prevent wrapper clipping / weird overflow
    ============================ */
    #{{ $dmUid }},
    #{{ $dmUid }} .table-responsive,
    #{{ $dmUid }} .table-wrap,
    #{{ $dmUid }} .card,
    #{{ $dmUid }} .panel,
    #{{ $dmUid }} .tab-content,
    #{{ $dmUid }} .tab-pane,
    #{{ $dmUid }} .dm-tree,
    #{{ $dmUid }} .dm-list,
    #{{ $dmUid }} .dm-item {
      overflow: visible !important;
      transform: none !important;
    }

    /* stop flex children from forcing overflow */
    #{{ $dmUid }} .dm-row { max-width: 100%; }
    #{{ $dmUid }} .dm-main{ min-width:0; flex:1 1 auto; }
    #{{ $dmUid }} .dm-title,
    #{{ $dmUid }} .dm-meta{ overflow-wrap:anywhere; word-break:break-word; }

    /* toolbar/search responsive */
    #{{ $dmUid }} .mfa-toolbar .position-relative{
      min-width: min(320px, 100%) !important;
      flex: 1 1 320px;
    }

    /* Mobile wrap */
    @media (max-width: 768px){
      #{{ $dmUid }} .mdm-head{
        padding:10px;
      }
      #{{ $dmUid }} .mdm-toolbar{
        padding:10px;
      }
      #{{ $dmUid }} .dm-row{ flex-wrap: wrap; }
      #{{ $dmUid }} .dm-actions{
        width: 100%;
        justify-content: flex-end;
      }
      #{{ $dmUid }} .mdm-inline-actions{
        width:100%;
      }
    }
  </style>
@endpush
@section('content')
  <div id="{{ $dmUid }}"
       class="mdm-wrap"
       data-create-url="{{ $dmCreateUrl }}"
       data-edit-pattern="{{ $dmEditPattern }}"
       data-api-base="{{ $apiBase }}"
       data-api-tree="{{ $apiTree }}">

    <div class="panel mdm-head">
      <div class="mdm-head-row">
        <div class="mdm-head-copy">
          <h1>
            <span class="seg-muted">Dashboard Menus</span>
            <span class="seg-sep">/</span>
            <span>Manage</span>
          </h1>
        </div>

        <div class="mdm-head-actions">
          <button type="button" class="btn btn-light mdm-refresh-btn js-refresh" title="Refresh" aria-label="Refresh">
            <i class="fa fa-rotate-right"></i>
          </button>
        </div>
      </div>
    </div>

    {{-- ===== Tabs ===== --}}
    @php
      $tabActive   = $dmUid.'_tab_active';
      $tabArchived = $dmUid.'_tab_archived';
      $tabBin      = $dmUid.'_tab_bin';
    @endphp

    <ul class="nav nav-tabs mb-3" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#{{ $tabActive }}" role="tab" aria-selected="true">
          <i class="fa-solid fa-bars me-2" aria-hidden="true"></i>
          Active Menus
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#{{ $tabArchived }}" role="tab" aria-selected="false">
          <i class="fa-solid fa-box-archive me-2" aria-hidden="true"></i>
          Archived
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#{{ $tabBin }}" role="tab" aria-selected="false">
          <i class="fa-solid fa-trash-can me-2" aria-hidden="true"></i>
          Bin
        </a>
      </li>
    </ul>

    <div class="tab-content mb-3">

      {{-- ========== ACTIVE (TREE, UNLIMITED HIERARCHY) ========== --}}
      <div class="tab-pane fade show active" id="{{ $tabActive }}" role="tabpanel">
        <div class="panel mdm-toolbar mfa-toolbar">
          <div class="row g-2 align-items-center">
            <div class="col-12 col-xl">
              <div class="mdm-inline-actions">
                <div class="d-flex align-items-center gap-2">
                  <label class="text-muted small mb-0">Per page (roots)</label>
                  <select class="form-select js-per-page" style="width:110px;">
                    <option>10</option><option>20</option><option selected>30</option><option>50</option><option>100</option>
                  </select>
                </div>

                <div class="mdm-search-wrap flex-grow-1" style="min-width:260px;">
                  <input type="text" class="form-control js-q" placeholder="Search name, href or status">
                  <i class="fa fa-search"></i>
                </div>

                <button class="btn btn-primary js-search"><i class="fa fa-search me-1"></i>Search</button>
                <button class="btn btn-light js-reset"><i class="fa fa-rotate-left me-1"></i>Reset</button>
              </div>
            </div>

            <div class="col-12 col-xl-auto">
              <div class="mdm-inline-actions justify-content-xl-end">
                <button class="btn btn-light js-reorder"><i class="fa fa-up-down-left-right me-1"></i>Reorder</button>
                <a href="{{ $dmCreateUrl }}" class="btn btn-primary"><i class="fa fa-plus me-1"></i>New Menu</a>
              </div>
            </div>
          </div>
        </div>

        <div class="card table-wrap">
          <div class="card-body p-0">
            <div class="dm-tree">
              <div class="dm-reorder-note p-2 mb-2 small text-muted js-reorder-note">
                Reorder mode is ON — drag using the handle. <b>Only sibling reordering is allowed (no parent changes).</b>
              </div>

              <div class="js-loader" style="display:none;">
                <div class="p-3">
                  <div class="placeholder-wave">
                    <div class="placeholder col-12 mb-2" style="height:18px;"></div>
                    <div class="placeholder col-12 mb-2" style="height:18px;"></div>
                    <div class="placeholder col-12 mb-2" style="height:18px;"></div>
                  </div>
                </div>
              </div>

              <div class="js-empty empty p-4 text-center" style="display:none;">
                <i class="fa fa-bars mb-2" style="font-size:32px; opacity:.6;"></i>
                <div>No dashboard menus found.</div>
              </div>

              <div class="js-tree"></div>

              <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 gap-2">
                <div class="text-muted small js-meta">—</div>
                <div class="d-flex align-items-center gap-2">
                  <button class="btn btn-primary btn-sm js-save-order" style="display:none;">
                    <span class="btn-spinner spinner-border spinner-border-sm me-1" style="display:none;" aria-hidden="true"></span>
                    <i class="fa fa-floppy-disk me-1 btn-icon"></i>
                    <span class="btn-text">Save Order</span>
                  </button>
                  <button class="btn btn-light btn-sm js-cancel-order" style="display:none;">Cancel</button>
                  <nav style="position:relative; z-index:1;">
                    <ul class="pagination mb-0 js-pager"></ul>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ========== ARCHIVED (TABLE) ========== --}}
      <div class="tab-pane fade" id="{{ $tabArchived }}" role="tabpanel">
        <div class="card table-wrap">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover table-borderless align-middle mb-0">
                <thead class="sticky-top">
                  <tr>
                    <th>NAME & HREF</th>
                    <th style="width:18%;">PARENT</th>
                    <th style="width:140px;">CREATED</th>
                    <th class="text-end" style="width:210px;">ACTIONS</th>
                  </tr>
                </thead>
                <tbody class="js-rows-archived">
                  <tr class="js-loader-archived" style="display:none;">
                    <td colspan="4" class="p-0">
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

            <div class="js-empty-archived empty p-4 text-center" style="display:none;">
              <i class="fa fa-box-archive mb-2" style="font-size:32px; opacity:.6;"></i>
              <div>No archived menus.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 gap-2">
              <div class="text-muted small js-meta-archived">—</div>
              <nav style="position:relative; z-index:1;"><ul class="pagination mb-0 js-pager-archived"></ul></nav>
            </div>
          </div>
        </div>
      </div>

      {{-- ========== BIN (TABLE) ========== --}}
      <div class="tab-pane fade" id="{{ $tabBin }}" role="tabpanel">
        <div class="card table-wrap">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover table-borderless align-middle mb-0">
                <thead class="sticky-top">
                  <tr>
                    <th>NAME & HREF</th>
                    <th style="width:18%;">PARENT</th>
                    <th style="width:140px;">DELETED AT</th>
                    <th class="text-end" style="width:250px;">ACTIONS</th>
                  </tr>
                </thead>
                <tbody class="js-rows-bin">
                  <tr class="js-loader-bin" style="display:none;">
                    <td colspan="4" class="p-0">
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

            <div class="js-empty-bin empty p-4 text-center" style="display:none;">
              <i class="fa fa-trash mb-2" style="font-size:32px; opacity:.6;"></i>
              <div>No items in Bin.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 gap-2">
              <div class="text-muted small js-meta-bin">—</div>
              <nav style="position:relative; z-index:1;"><ul class="pagination mb-0 js-pager-bin"></ul></nav>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- Toasts --}}
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:2100">
    <div class="toast js-ok-toast text-bg-success border-0">
      <div class="d-flex">
        <div class="toast-body js-ok-msg">Done</div>
        <button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button>
      </div>
    </div>
    <div class="toast js-err-toast text-bg-danger border-0 mt-2">
      <div class="d-flex">
        <div class="toast-body js-err-msg">Something went wrong</div>
        <button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

  <script>
    (function () {
      const ROOT = document.getElementById(@json($dmUid));
      if (!ROOT) return;

      // ✅ Prevent double init if scripts get injected twice
      if (ROOT.dataset.dmInit === '1') return;
      ROOT.dataset.dmInit = '1';

      const TOKEN =
        localStorage.getItem('token') ||
        sessionStorage.getItem('token') ||
        '';

      if (!TOKEN) {
        Swal.fire('Login needed', 'Your session expired. Please login again.', 'warning')
          .then(() => location.href = '/');
        return;
      }

      const API_BASE = ROOT.dataset.apiBase;
      const API_TREE = ROOT.dataset.apiTree;
      const EDIT_PATTERN = ROOT.dataset.editPattern;

      const qs  = (sel) => ROOT.querySelector(sel);
      const qsa = (sel) => Array.from(ROOT.querySelectorAll(sel));

      const esc = (s) => {
        const m = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;','`':'&#96;'};
        return (s==null?'':String(s)).replace(/[&<>\"'`]/g, ch => m[ch]);
      };

      const fmtDate = (iso) => {
        if (!iso) return '-';
        const d = new Date(iso);
        if (isNaN(d)) return esc(iso);
        return d.toLocaleString(undefined, {year:'numeric',month:'short',day:'2-digit',hour:'2-digit',minute:'2-digit'});
      };

      const editUrl = (id) => EDIT_PATTERN.replace('{id}', encodeURIComponent(id));

      function actionMenu(scope, row) {
        const id = Number(row?.id || 0);
        const name = esc(row?.name || '');
        const items = [];

        if (scope !== 'bin') {
          items.push(`
            <li>
              <a class="dropdown-item" href="${editUrl(id)}">
                <i class="fa fa-pen"></i>
                <span>Edit</span>
              </a>
            </li>
          `);
        }

        if (scope === 'active') {
          if (Array.isArray(row?.children) && row.children.length) {
            items.push(`
              <li>
                <button type="button" class="dropdown-item" data-act="reorder-children" data-id="${id}" data-name="${name}">
                  <i class="fa fa-up-down-left-right"></i>
                  <span>Reorder Children</span>
                </button>
              </li>
            `);
            items.push('<li><hr class="dropdown-divider"></li>');
          }
          items.push(`
            <li>
              <button type="button" class="dropdown-item" data-act="archive" data-id="${id}" data-name="${name}">
                <i class="fa fa-box-archive"></i>
                <span>Archive</span>
              </button>
            </li>
          `);
          items.push('<li><hr class="dropdown-divider"></li>');
          items.push(`
            <li>
              <button type="button" class="dropdown-item text-danger" data-act="delete" data-id="${id}" data-name="${name}">
                <i class="fa fa-trash"></i>
                <span>Delete</span>
              </button>
            </li>
          `);
        } else if (scope === 'archived') {
          items.push(`
            <li>
              <button type="button" class="dropdown-item" data-act="unarchive" data-id="${id}" data-name="${name}">
                <i class="fa fa-box-open"></i>
                <span>Unarchive</span>
              </button>
            </li>
          `);
          items.push('<li><hr class="dropdown-divider"></li>');
          items.push(`
            <li>
              <button type="button" class="dropdown-item text-danger" data-act="delete" data-id="${id}" data-name="${name}">
                <i class="fa fa-trash"></i>
                <span>Delete</span>
              </button>
            </li>
          `);
        } else {
          items.push(`
            <li>
              <button type="button" class="dropdown-item" data-act="restore" data-id="${id}" data-name="${name}">
                <i class="fa fa-rotate-left"></i>
                <span>Restore</span>
              </button>
            </li>
          `);
          items.push('<li><hr class="dropdown-divider"></li>');
          items.push(`
            <li>
              <button type="button" class="dropdown-item text-danger" data-act="force" data-id="${id}" data-name="${name}">
                <i class="fa fa-skull-crossbones"></i>
                <span>Delete Permanently</span>
              </button>
            </li>
          `);
        }

        return `
          <div class="dropdown dm-dd">
            <button class="btn btn-light btn-sm dm-dd-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
              <i class="fa fa-ellipsis-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              ${items.join('')}
            </ul>
          </div>
        `;
      }

      // Toasts live outside ROOT in many layouts, so query from document (not ROOT)
      const okToastEl  = document.querySelector('.js-ok-toast');
      const errToastEl = document.querySelector('.js-err-toast');
      const okMsgEl    = document.querySelector('.js-ok-msg');
      const errMsgEl   = document.querySelector('.js-err-msg');

      const okToast  = okToastEl  ? new bootstrap.Toast(okToastEl)  : null;
      const errToast = errToastEl ? new bootstrap.Toast(errToastEl) : null;

      const ok = (m) => {
        if (okMsgEl) okMsgEl.textContent = m || 'Done';
        if (okToast) okToast.show();
        else console.log('[OK]', m);
      };

      const err = (m) => {
        if (errMsgEl) errMsgEl.textContent = m || 'Something went wrong';
        if (errToast) errToast.show();
        else console.error('[ERR]', m);
      };

      async function fetchJSON(url, opts = {}) {
        const res = await fetch(url, {
          cache: 'no-store',
          ...opts,
          headers: {
            'Authorization': 'Bearer ' + TOKEN,
            'Accept': 'application/json',
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache',
            ...(opts.headers || {})
          }
        });

        const j = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(j?.error || j?.message || 'Request failed');
        return j;
      }

      /* =========================
        SMART REFRESH FLAGS
      ========================= */
      const loaded = { active:false, archived:false, bin:false };
      const dirty  = { active:false, archived:false, bin:false };

      const paneActive   = document.getElementById(@json($tabActive));
      const paneArchived = document.getElementById(@json($tabArchived));
      const paneBin      = document.getElementById(@json($tabBin));

      function isPaneShown(pane){
        return !!(pane && pane.classList.contains('show') && pane.classList.contains('active'));
      }

      function markDirty(keys){
        (keys || []).forEach(k => { if (k in dirty) dirty[k] = true; });
      }

      async function refreshVisible() {
        if (isPaneShown(paneActive) && (dirty.active || !loaded.active)) {
          await loadActiveTree();
        }
        if (isPaneShown(paneArchived) && (dirty.archived || !loaded.archived)) {
          await loadArchived();
        }
        if (isPaneShown(paneBin) && (dirty.bin || !loaded.bin)) {
          await loadBin();
        }
      }

      /* =========================
        ACTIVE TREE (unlimited)
      ========================= */
      const perPageSel  = qs('.js-per-page');
      const qInput      = qs('.js-q');
      const btnSearch   = qs('.js-search');
      const btnReset    = qs('.js-reset');
      const btnRefresh  = qs('.js-refresh');
      const btnReorder  = qs('.js-reorder');
      const btnSaveOrd  = qs('.js-save-order');
      const btnCancelOrd= qs('.js-cancel-order');

      const treeWrap    = qs('.js-tree');
      const loader      = qs('.js-loader');
      const empty       = qs('.js-empty');
      const meta        = qs('.js-meta');
      const pager       = qs('.js-pager');
      const reorderNote = qs('.js-reorder-note');

      let reorderTarget = null;
      let sortables = [];
      let treeAll = [];        // full tree from API
      let activePage = 1;      // root pagination
      let treeSearching = false;
      const expandedNodes = new Set();

      function runSearch() {
        activePage = 1;
        renderActiveTree();
      }

      function setRefreshLoading(on) {
        if (!btnRefresh) return;
        btnRefresh.disabled = !!on;
        btnRefresh.classList.toggle('is-spinning', !!on);
      }

      function setLoading(v) { loader.style.display = v ? '' : 'none'; }

      function setSaveBtnLoading(on) {
        if (!btnSaveOrd) return;
        const sp   = btnSaveOrd.querySelector('.btn-spinner');
        const icon = btnSaveOrd.querySelector('.btn-icon');
        const txt  = btnSaveOrd.querySelector('.btn-text');

        if (!btnSaveOrd.dataset.defaultText) {
          btnSaveOrd.dataset.defaultText = (txt?.textContent || 'Save Order').trim();
        }

        btnSaveOrd.classList.toggle('dm-btn-loading', !!on);
        btnSaveOrd.disabled = !!on;

        if (sp)   sp.style.display = on ? '' : 'none';
        if (icon) icon.style.display = on ? 'none' : '';
        if (txt)  txt.textContent = on ? 'Saving…' : (btnSaveOrd.dataset.defaultText || 'Save Order');

        if (btnCancelOrd) btnCancelOrd.disabled = !!on;
        if (btnReorder)   btnReorder.disabled   = !!on;
      }

      function statusBadge(status){
        const s = String(status || '').toLowerCase();
        if (s === 'active')   return `<span class="badge badge-success ms-2">Active</span>`;
        if (s === 'archived') return `<span class="badge badge-secondary ms-2">Archived</span>`;
        return `<span class="badge badge-warning ms-2">${esc(status || '-')}</span>`;
      }

      function isRootReorder() {
        return reorderTarget === 'root';
      }

      function isChildReorder() {
        return reorderTarget !== null && reorderTarget !== 'root';
      }

      function isListReorderTarget(parentIdRaw) {
        if (reorderTarget === null) return false;
        const normalized = (parentIdRaw === 'null' || parentIdRaw === '' || typeof parentIdRaw === 'undefined')
          ? null
          : Number(parentIdRaw);
        if (reorderTarget === 'root') return normalized === null;
        return normalized === Number(reorderTarget);
      }

      function setReorderState(target) {
        reorderTarget = target;
        const active = reorderTarget !== null;
        btnReorder.classList.toggle('btn-primary', reorderTarget === 'root');
        btnReorder.classList.toggle('btn-light', reorderTarget !== 'root');
        btnReorder.innerHTML = reorderTarget === 'root'
          ? '<i class="fa fa-check-double me-1"></i>Reorder Parents'
          : '<i class="fa fa-up-down-left-right me-1"></i>Reorder Parents';
        btnSaveOrd.style.display = active ? '' : 'none';
        btnCancelOrd.style.display = active ? '' : 'none';
        if (reorderNote) {
          reorderNote.innerHTML = reorderTarget === 'root'
            ? 'Reorder mode is ON for <b>top-level menus</b>. Drag only the parent rows shown on this page.'
            : isChildReorder()
              ? 'Reorder mode is ON for the selected parent branch. <b>Only that parent&apos;s direct children</b> can be reordered.'
              : 'Reorder mode is ON — drag using the handle. <b>Only sibling reordering is allowed (no parent changes).</b>';
        }
      }

      function filterTree(nodes, term) {
        if (!term) return nodes;
        const t = term.toLowerCase();

        function nodeMatches(n) {
          const hay = [
            n.name, n.href, n.status, n.icon_class, n.description
          ].filter(Boolean).join(' ').toLowerCase();
          return hay.includes(t);
        }

        function walk(list) {
          const out = [];
          list.forEach(n => {
            const kids = Array.isArray(n.children) ? walk(n.children) : [];
            if (nodeMatches(n) || kids.length) out.push({...n, children: kids});
          });
          return out;
        }
        return walk(nodes);
      }

      function countNodes(nodes) {
        let c = 0;
        (function walk(list){
          list.forEach(n => {
            c++;
            if (n.children && n.children.length) walk(n.children);
          });
        })(nodes || []);
        return c;
      }

      function destroySortables() {
        sortables.forEach(s => { try { s.destroy(); } catch(e){} });
        sortables = [];
      }

      function initSortables() {
        destroySortables();
        qsa('.dm-list[data-parent-id]').forEach(list => {
          if (!isListReorderTarget(list.dataset.parentId)) return;
          const s = new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            draggable: '.dm-item',
            ghostClass: 'drag-ghost',
            chosenClass: 'drag-chosen',
            group: { name: 'dm-siblings', put: false }, // no parent changes
            fallbackOnBody: true,
            swapThreshold: 0.65
          });
          sortables.push(s);
        });
      }

      function collectOrdersFromDOM() {
        const orders = [];
        qsa('.dm-list[data-parent-id]').forEach(list => {
          if (!isListReorderTarget(list.dataset.parentId)) return;
          const pidRaw = list.dataset.parentId;
          const parent_id = (pidRaw === 'null' || pidRaw === '' || typeof pidRaw === 'undefined') ? null : Number(pidRaw);

          Array.from(list.children).forEach((li, idx) => {
            const id = Number(li.dataset.id);
            if (!Number.isFinite(id)) return;
            orders.push({ id, position: idx, parent_id });
          });
        });
        return orders;
      }

      function buildPager(cur, pages) {
        const li = (dis, act, label, t) =>
          `<li class="page-item ${dis?'disabled':''} ${act?'active':''}">
            <a class="page-link" href="javascript:void(0)" data-page="${t||''}">${label}</a>
          </li>`;

        let html = '';
        html += li(cur<=1, false, 'Previous', cur-1);

        const w = 3;
        const s = Math.max(1, cur - w);
        const e = Math.min(pages, cur + w);

        if (s > 1) {
          html += li(false, false, 1, 1);
          if (s > 2) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }

        for (let i = s; i <= e; i++) html += li(false, i===cur, i, i);

        if (e < pages) {
          if (e < pages-1) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
          html += li(false, false, pages, pages);
        }

        html += li(cur>=pages, false, 'Next', cur+1);
        pager.innerHTML = html;

        pager.querySelectorAll('a.page-link[data-page]').forEach(a => {
          a.addEventListener('click', () => {
            const t = Number(a.dataset.page);
            if (!t || t === activePage) return;
            activePage = Math.max(1, t);
            renderActiveTree();
            window.scrollTo({top:0, behavior:'smooth'});
          });
        });
      }

      function renderNode(n, level) {
        const hasKids = Array.isArray(n.children) && n.children.length > 0;

        const li = document.createElement('li');
        li.className = 'dm-item';
        li.dataset.id = n.id;
        if (hasKids && !treeSearching && !expandedNodes.has(Number(n.id))) {
          li.classList.add('is-collapsed');
        }

        const row = document.createElement('div');
        row.className = 'dm-row';
        row.style.setProperty('--level', level);

        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'dm-toggle';
        toggleBtn.innerHTML = `<i class="fa fa-chevron-down"></i>`;
        if (!hasKids) toggleBtn.disabled = true;

        const drag = document.createElement('span');
        drag.className = 'drag-handle';
        drag.title = isListReorderTarget(level === 0 ? 'null' : String(n.parent_id ?? '')) ? 'Drag to reorder' : '';
        drag.innerHTML = `<i class="fa fa-grip-vertical"></i>`;
        drag.style.display = 'none';

        const main = document.createElement('div');
        main.className = 'dm-main';

        const hrefLine = n.href ? `<span class="d-block small text-muted mt-1"><i class="fa fa-link me-1 opacity-75"></i>${esc(n.href)}</span>` : '';

        main.innerHTML = `
          <div class="dm-title">
            ${esc(n.name || '-')}
            ${statusBadge(n.status)}
            ${level>0 ? `<span class="badge badge-soft ms-2">Level ${level+1}</span>` : ''}
            ${hasKids ? `<span class="badge badge-soft ms-2">${n.children.length} child</span>` : ''}
          </div>
          <div class="dm-meta">
            ${n.icon_class ? `icon: ${esc(n.icon_class)}` : '—'}
            ${n.description ? ` • ${esc(String(n.description).slice(0,80))}${String(n.description).length>80?'…':''}` : ''}
          </div>
          ${hrefLine}
        `;

        const actions = document.createElement('div');
        actions.className = 'dm-actions';
        actions.innerHTML = actionMenu('active', n);

        row.appendChild(toggleBtn);
        row.appendChild(drag);
        row.appendChild(main);
        row.appendChild(actions);

        li.appendChild(row);

        const kids = document.createElement('ul');
        kids.className = 'dm-list dm-children';
        kids.dataset.parentId = String(n.id);
        kids.setAttribute('data-parent-id', String(n.id)); // ✅ IMPORTANT for sibling sorting

        if (hasKids) n.children.forEach(ch => kids.appendChild(renderNode(ch, level+1)));

        if (isListReorderTarget(level === 0 ? 'null' : String(n.parent_id ?? ''))) {
          drag.style.display = '';
        }

        if (reorderTarget === Number(n.id)) {
          li.classList.remove('is-collapsed');
          expandedNodes.add(Number(n.id));
        }

        toggleBtn.addEventListener('click', () => {
          if (!hasKids) return;
          li.classList.toggle('is-collapsed');
          if (li.classList.contains('is-collapsed')) expandedNodes.delete(Number(n.id));
          else expandedNodes.add(Number(n.id));
        });

        li.appendChild(kids);
        return li;
      }

      function renderActiveTree() {
        const term = (qInput.value || '').trim();
        const per = Math.max(10, Number(perPageSel.value || 30));
        treeSearching = !!term;

        const filtered = filterTree(treeAll, term);
        const totalRoots = filtered.length;
        const totalNodes = countNodes(filtered);

        const pages = Math.max(1, Math.ceil(totalRoots / per));
        if (activePage > pages) activePage = pages;

        const startIdx = (activePage - 1) * per;
        const rootsPage = filtered.slice(startIdx, startIdx + per);

        treeWrap.innerHTML = '';
        empty.style.display = (totalRoots === 0) ? '' : 'none';

        const rootList = document.createElement('ul');
        rootList.className = 'dm-list';
        rootList.dataset.parentId = 'null';
        rootList.setAttribute('data-parent-id', 'null');

        rootsPage.forEach(n => rootList.appendChild(renderNode(n, 0)));

        treeWrap.appendChild(rootList);

        meta.textContent = `Roots: ${totalRoots} • Nodes: ${totalNodes} • Page ${activePage} of ${pages}`;
        buildPager(activePage, pages);

        ROOT.classList.toggle('dm-reorder-on', reorderTarget !== null);
        setReorderState(reorderTarget);

        if (reorderTarget !== null) initSortables();
        else destroySortables();
      }

      let activeLoadPromise = null;

      async function loadActiveTree() {
        if (activeLoadPromise) return activeLoadPromise;

        activeLoadPromise = (async () => {
          setLoading(true);
          try {
            const sep = API_TREE.includes('?') ? '&' : '?';
            const j = await fetchJSON(API_TREE + sep + '_ts=' + Date.now());

            // accept {data:[...]} or raw array
            treeAll = Array.isArray(j.data) ? j.data : (Array.isArray(j) ? j : []);
            renderActiveTree();

            loaded.active = true;
            dirty.active = false;
          } catch (e) {
            console.error(e);
            treeAll = [];
            treeWrap.innerHTML = '';
            empty.style.display = '';
            meta.textContent = 'Failed to load';
            err(e.message || 'Load error');
          } finally {
            setLoading(false);
            activeLoadPromise = null;
          }
        })();

        return activeLoadPromise;
      }

      /* =========================
        ARCHIVED + BIN TABLES
      ========================= */
      const rowsArchived = qs('.js-rows-archived');
      const rowsBin      = qs('.js-rows-bin');

      const loaderArchived = qs('.js-loader-archived');
      const loaderBin      = qs('.js-loader-bin');

      const emptyArchived  = qs('.js-empty-archived');
      const emptyBin       = qs('.js-empty-bin');

      const metaArchived   = qs('.js-meta-archived');
      const metaBin        = qs('.js-meta-bin');

      const pagerArchived  = qs('.js-pager-archived');
      const pagerBin       = qs('.js-pager-bin');

      const state = {
        archived: { page: 1 },
        bin: { page: 1 }
      };

      function clearRows(tbody, keepSelector) {
        Array.from(tbody.querySelectorAll('tr')).forEach(tr => {
          if (keepSelector && tr.matches(keepSelector)) return;
          tr.remove();
        });
      }

      function parentInfo(r) {
        return (r.parent_id ? `#${r.parent_id}` : 'Root');
      }

      function nameBits(r){
        const hrefLine = r.href
          ? `<span class="d-block mt-1 small text-muted"><i class="fa fa-link me-1 opacity-75"></i>${esc(r.href)}</span>`
          : '';
        const desc = r.description ? `<span class="d-block mt-1 small text-muted">${esc(String(r.description).slice(0,90))}${String(r.description).length>90?'…':''}</span>` : '';
        return `<div class="fw-semibold">${esc(r.name || '-')}</div>${hrefLine}${desc}`;
      }

      function archivedRow(r) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${nameBits(r)}</td>
          <td>${esc(parentInfo(r))}</td>
          <td>${fmtDate(r.created_at)}</td>
          <td class="text-end">
            ${actionMenu('archived', r)}
          </td>
        `;
        return tr;
      }

      function binRow(r) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${nameBits(r)}</td>
          <td>${esc(parentInfo(r))}</td>
          <td>${fmtDate(r.deleted_at)}</td>
          <td class="text-end">
            ${actionMenu('bin', r)}
          </td>
        `;
        return tr;
      }

      function buildPagerGeneric(pagerEl, cur, pages, onPage) {
        const li = (dis, act, label, t) =>
          `<li class="page-item ${dis?'disabled':''} ${act?'active':''}">
            <a class="page-link" href="javascript:void(0)" data-page="${t||''}">${label}</a>
          </li>`;

        let html = '';
        html += li(cur<=1, false, 'Previous', cur-1);

        const w = 3;
        const s = Math.max(1, cur - w);
        const e = Math.min(pages, cur + w);

        if (s > 1) {
          html += li(false, false, 1, 1);
          if (s > 2) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }

        for (let i = s; i <= e; i++) html += li(false, i===cur, i, i);

        if (e < pages) {
          if (e < pages-1) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
          html += li(false, false, pages, pages);
        }

        html += li(cur>=pages, false, 'Next', cur+1);
        pagerEl.innerHTML = html;

        pagerEl.querySelectorAll('a.page-link[data-page]').forEach(a => {
          a.addEventListener('click', () => {
            const t = Number(a.dataset.page);
            if (!t || t === cur) return;
            onPage(t);
            window.scrollTo({top:0, behavior:'smooth'});
          });
        });
      }

      async function loadArchived() {
        loaderArchived.style.display = '';
        emptyArchived.style.display = 'none';
        metaArchived.textContent = '—';
        pagerArchived.innerHTML = '';
        clearRows(rowsArchived, '.js-loader-archived');

        try {
          const per = 30;
          const usp = new URLSearchParams();
          usp.set('per_page', per);
          usp.set('page', state.archived.page);
          usp.set('status', 'Archived');
          usp.set('sort', 'created_at');
          usp.set('direction', 'desc');

          const j = await fetchJSON(API_BASE + '?' + usp.toString());
          const items = Array.isArray(j.data) ? j.data : [];
          const pag = j.pagination || {page:1, per_page: per, total: items.length};

          if (!items.length) emptyArchived.style.display = '';

          const frag = document.createDocumentFragment();
          items.forEach(r => frag.appendChild(archivedRow(r)));
          rowsArchived.appendChild(frag);

          const total = Number(pag.total || 0);
          const pages = Math.max(1, Math.ceil(total / Number(pag.per_page || per)));
          metaArchived.textContent = `Showing page ${pag.page} of ${pages} — ${total} result(s)`;

          buildPagerGeneric(pagerArchived, Number(pag.page||1), pages, (t)=>{
            state.archived.page = Math.max(1,t);
            loadArchived();
          });

          loaded.archived = true;
          dirty.archived = false;

        } catch(e) {
          console.error(e);
          emptyArchived.style.display = '';
          metaArchived.textContent = 'Failed to load';
          err(e.message || 'Load error');
        } finally {
          loaderArchived.style.display = 'none';
        }
      }

      async function loadBin() {
        loaderBin.style.display = '';
        emptyBin.style.display = 'none';
        metaBin.textContent = '—';
        pagerBin.innerHTML = '';
        clearRows(rowsBin, '.js-loader-bin');

        // Prefer /bin (your existing module used it), fallback to /trash if needed
        const per = 30;
        const usp = new URLSearchParams();
        usp.set('per_page', per);
        usp.set('page', state.bin.page);

        let j = null;
        try {
          j = await fetchJSON(API_BASE + '/bin?' + usp.toString());
        } catch(_e) {
          j = await fetchJSON(API_BASE + '/trash?' + usp.toString());
        }

        try {
          const items = Array.isArray(j.data) ? j.data : [];
          const pag = j.pagination || {page:1, per_page: per, total: items.length};

          if (!items.length) emptyBin.style.display = '';

          const frag = document.createDocumentFragment();
          items.forEach(r => frag.appendChild(binRow(r)));
          rowsBin.appendChild(frag);

          const total = Number(pag.total || 0);
          const pages = Math.max(1, Math.ceil(total / Number(pag.per_page || per)));
          metaBin.textContent = `Showing page ${pag.page} of ${pages} — ${total} result(s)`;

          buildPagerGeneric(pagerBin, Number(pag.page||1), pages, (t)=>{
            state.bin.page = Math.max(1,t);
            loadBin();
          });

          loaded.bin = true;
          dirty.bin = false;

        } catch(e) {
          console.error(e);
          emptyBin.style.display = '';
          metaBin.textContent = 'Failed to load';
          err(e.message || 'Load error');
        } finally {
          loaderBin.style.display = 'none';
        }
      }

      /* =========================
        EVENTS
      ========================= */
      btnSearch?.addEventListener('click', runSearch);

      qInput.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        runSearch();
      });

      perPageSel.addEventListener('change', () => {
        activePage = 1;
        renderActiveTree();
      });

      btnReset.addEventListener('click', () => {
        qInput.value = '';
        perPageSel.value = '30';
        activePage = 1;
        renderActiveTree();
      });

      btnRefresh?.addEventListener('click', async () => {
        setRefreshLoading(true);
        try {
          markDirty(['active','archived','bin']);
          await refreshVisible();
        } finally {
          setRefreshLoading(false);
        }
      });

      btnReorder.addEventListener('click', () => {
        setReorderState(reorderTarget === 'root' ? null : 'root');
        renderActiveTree();
      });

      btnCancelOrd.addEventListener('click', () => {
        setReorderState(null);
        renderActiveTree();
      });

      btnSaveOrd.addEventListener('click', async () => {
        const orders = collectOrdersFromDOM();
        if (!orders.length) {
          Swal.fire('Nothing to save', 'No items found to reorder.', 'info');
          return;
        }

        setSaveBtnLoading(true);

        try {
          // Prefer robust payload (orders), fallback to simple ids if backend expects that
          let res = await fetch(API_BASE + '/reorder', {
            method: 'POST',
            headers: {
              'Authorization': 'Bearer ' + TOKEN,
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ orders })
          });

          let j = await res.json().catch(() => ({}));

          if (!res.ok) {
            // fallback: {ids:[...]}
            if (!isRootReorder()) {
              throw new Error(j?.error || j?.message || 'Reorder failed');
            }
            const ids = orders.sort((a,b)=>a.position-b.position).map(o => o.id);

            res = await fetch(API_BASE + '/reorder', {
              method: 'POST',
              headers: {
                'Authorization': 'Bearer ' + TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({ ids })
            });
            j = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(j?.error || j?.message || 'Reorder failed');
          }

          ok('Order updated');

          setReorderState(null);

          markDirty(['active']);
          await refreshVisible();

        } catch(e) {
          console.error(e);
          err(e.message || 'Reorder failed');
        } finally {
          setSaveBtnLoading(false);
        }
      });

      // delegated actions
      ROOT.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-act]');
        if (!btn) return;

        const act = btn.dataset.act;
        const id = btn.dataset.id;
        const name = btn.dataset.name || 'this menu';

        if (!id) return;

        if (act === 'archive') {
          const {isConfirmed} = await Swal.fire({
            icon: 'question',
            title: 'Archive menu?',
            html: `"${esc(name)}" will move to Archived.`,
            showCancelButton: true,
            confirmButtonText: 'Archive',
            confirmButtonColor: '#8b5cf6'
          });
          if (!isConfirmed) return;

          try {
            await fetchJSON(API_BASE + '/' + encodeURIComponent(id) + '/archive', { method: 'POST' });
            ok('Archived');

            markDirty(['active','archived']);
            await refreshVisible();
          } catch(e2) {
            err(e2.message || 'Archive failed');
          }
          return;
        }

        if (act === 'reorder-children') {
          setReorderState(Number(id));
          expandedNodes.add(Number(id));
          renderActiveTree();
          ok(`Reorder mode enabled for "${name}" children`);
          return;
        }

        if (act === 'unarchive') {
          const {isConfirmed} = await Swal.fire({
            icon: 'question',
            title: 'Unarchive menu?',
            html: `"${esc(name)}" will move back to Active.`,
            showCancelButton: true,
            confirmButtonText: 'Unarchive',
            confirmButtonColor: '#10b981'
          });
          if (!isConfirmed) return;

          try {
            await fetchJSON(API_BASE + '/' + encodeURIComponent(id) + '/unarchive', { method: 'POST' });
            ok('Unarchived');

            markDirty(['active','archived']);
            await refreshVisible();
          } catch(e2) {
            err(e2.message || 'Unarchive failed');
          }
          return;
        }

        if (act === 'delete') {
          const {isConfirmed} = await Swal.fire({
            icon: 'warning',
            title: 'Delete menu?',
            html: `"${esc(name)}" will be moved to Bin.`,
            showCancelButton: true,
            confirmButtonText: 'Delete',
            confirmButtonColor: '#ef4444'
          });
          if (!isConfirmed) return;

          try {
            await fetchJSON(API_BASE + '/' + encodeURIComponent(id), { method: 'DELETE' });
            ok('Moved to Bin');

            markDirty(['active','archived','bin']);
            await refreshVisible();
          } catch(e2) {
            err(e2.message || 'Delete failed');
          }
          return;
        }

        if (act === 'restore') {
          try {
            await fetchJSON(API_BASE + '/' + encodeURIComponent(id) + '/restore', { method: 'POST' });
            ok('Restored');

            markDirty(['bin','active','archived']);
            await refreshVisible();
          } catch(e2) {
            err(e2.message || 'Restore failed');
          }
          return;
        }

        if (act === 'force') {
          const {isConfirmed} = await Swal.fire({
            icon:'warning',
            title:'Delete permanently?',
            html:`This cannot be undone.<br>"${esc(name)}"`,
            showCancelButton:true,
            confirmButtonText:'Delete permanently',
            confirmButtonColor:'#dc2626'
          });
          if (!isConfirmed) return;

          try {
            await fetchJSON(API_BASE + '/' + encodeURIComponent(id) + '/force', { method: 'DELETE' });
            ok('Permanently deleted');

            markDirty(['bin']);
            await refreshVisible();
          } catch(e2) {
            err(e2.message || 'Force delete failed');
          }
          return;
        }
      });

      // tab loads
      const tabA = ROOT.querySelector('a[href="#{{ $tabActive }}"]');
      const tabR = ROOT.querySelector('a[href="#{{ $tabArchived }}"]');
      const tabB = ROOT.querySelector('a[href="#{{ $tabBin }}"]');

      tabA?.addEventListener('shown.bs.tab', () => {
        if (loaded.active && !dirty.active) return renderActiveTree();
        loadActiveTree();
      });

      tabR?.addEventListener('shown.bs.tab', () => {
        if (!loaded.archived || dirty.archived) loadArchived();
      });

      tabB?.addEventListener('shown.bs.tab', () => {
        if (!loaded.bin || dirty.bin) loadBin();
      });

      document.addEventListener('show.bs.dropdown', (e) => {
        const btn = e.target.querySelector('.dm-dd-btn');
        if (btn) btn.classList.add('is-open');
      });

      document.addEventListener('hide.bs.dropdown', (e) => {
        const btn = e.target.querySelector('.dm-dd-btn');
        if (btn) btn.classList.remove('is-open');
      });

      // initial
      loadActiveTree();

    })();
  </script>
@endpush
