{{-- resources/views/privileges/createPagePrivilege.blade.php --}}
@extends('pages.layout.structure')

@section('title','Create Page Privilege')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}"/>

@push('styles')
<style>
  .cp-wrap{max-width:1140px;margin:16px auto 40px}
  .panel{background:var(--surface);border:1px solid var(--line-strong);border-radius:16px;box-shadow:var(--shadow-2);padding:14px}

  .btn-primary{background:var(--primary-color);border:none}
  .btn-light{background:var(--surface);border:1px solid var(--line-strong)}
  .form-control,.form-select,textarea{border-radius:12px;border:1px solid var(--line-strong);background:#fff}
  html.theme-dark .form-control,html.theme-dark .form-select,html.theme-dark textarea{background:#0f172a;color:#e5e7eb;border-color:var(--line-strong)}
  .help{font-size:12.5px;color:var(--muted-color)}
  .req{color:var(--danger-color)}

  .field-row{display:flex;gap:.75rem;align-items:end;flex-wrap:wrap}
  .field-row .grow{flex:1 1 520px;min-width:280px}
  .field-row .btn{height:40px;border-radius:12px}
  .readonly{background:color-mix(in oklab, var(--muted-color) 6%, transparent)!important;cursor:pointer}
  .priv-list{display:flex;flex-direction:column;gap:12px}
  .priv-card{border:1px solid var(--line-strong);border-radius:16px;background:var(--surface);overflow:hidden}
  .priv-card-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 14px;border-bottom:1px solid var(--line-soft);background:var(--background-soft)}
  .priv-card-title{font-weight:700}
  .priv-grid{padding:14px}
  .priv-api-actions{display:flex;gap:.75rem;align-items:end;flex-wrap:wrap}
  .priv-api-actions .grow{flex:1 1 520px;min-width:280px}
  .priv-remove-btn{min-width:120px}
  .section-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}

  /* Modals */
  .modal-content{border-radius:16px;border:1px solid var(--line-strong);background:var(--surface)}
  .modal-header{border-bottom:1px solid var(--line-strong)}
  .modal-footer{border-top:1px solid var(--line-strong)}

  /* Tree */
  .tree-wrap{border:1px solid var(--line-strong);border-radius:14px;background:var(--surface);overflow:hidden}
  .tree-toolbar{padding:10px 12px;border-bottom:1px solid var(--line-soft);background:var(--background-soft)}
  .tree-toolbar .form-control{height:38px;border-radius:12px}
  .tree-body{max-height:60vh;overflow:auto;padding:8px 10px}

  .tree-node{display:block}
  .tree-row{display:flex;align-items:center;gap:.6rem;padding:8px 10px;border-radius:12px}
  .tree-row:hover{background:var(--page-hover)}
  .tree-left{display:flex;align-items:center;gap:.55rem;min-width:0;flex:1}
  .tree-title{font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .tree-sub{font-size:12px;color:var(--muted-color)}
  .tree-actions{display:flex;align-items:center;gap:.4rem;flex-shrink:0}
  .tree-actions .btn{height:34px;border-radius:10px}

  .tree-toggle{
    width:22px;height:22px;border-radius:8px;
    display:inline-flex;align-items:center;justify-content:center;
    border:1px solid var(--line-strong);background:var(--surface);color:var(--muted-color);
    flex-shrink:0;
  }
  .tree-toggle[aria-expanded="false"] i{transform:rotate(-90deg)}
  .tree-toggle i{transition:transform .15s ease}
  .tree-indent{width:14px;flex-shrink:0}
  .tree-children{margin-left:18px;padding-left:10px;border-left:1px dashed var(--line-soft);display:none}
  .tree-children.show{display:block}

  .pill{
    font-size:.72rem;border-radius:999px;padding:2px 10px;
    background:var(--background-soft);border:1px solid var(--line-soft);color:var(--muted-color)
  }

  /* API tree pills */
  .m-pill{
    display:inline-flex;align-items:center;justify-content:center;
    min-width:44px;padding:2px 10px;border-radius:999px;
    border:1px solid var(--line-soft);
    background:var(--background-soft);
    color:var(--muted-color);
    font-size:.72rem;font-weight:800;
  }

  .api-note{font-size:12.5px;color:var(--muted-color);margin-top:6px}

  html.theme-dark .tree-toolbar{background:#0b1220}
  html.theme-dark .tree-wrap{background:#0f172a}
  html.theme-dark .tree-row:hover{background:rgba(255,255,255,.03)}
</style>
@endpush

@section('content')
<div class="cp-wrap">

  <div class="panel mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <i class="fa-solid fa-shield-halved" style="opacity:.8"></i>
      <div>
        <div class="fw-semibold" id="pageHeading">Create Page Privilege</div>
        <div class="help" id="pageSubheading">Module and API are chosen via modals.</div>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button id="btnBack" class="btn btn-light"><i class="fa fa-arrow-left me-1"></i>Back</button>
      <button id="btnSave" class="btn btn-primary"><i class="fa fa-floppy-disk me-1"></i><span id="saveBtnTxt">Save</span></button>
    </div>
  </div>

  <div class="panel">
    <div class="row g-3">

      {{-- ✅ hidden key for edit mode --}}
      <input id="priv_key" type="hidden" value="">

      {{-- Module (via modal) --}}
      <div class="col-12">
        <label class="form-label mb-1">Module <span class="req">*</span></label>
        <div class="field-row">
          <div class="grow">
            <input id="module_label" class="form-control readonly" readonly placeholder="Select a module from dashboard menus…">
            <input id="module_id" type="hidden" value="">
          </div>
          <button id="btnPickModule" type="button" class="btn btn-light">
            <i class="fa fa-sitemap me-1"></i>Choose Module
          </button>
          <button id="btnClearModule" type="button" class="btn btn-light">
            <i class="fa fa-xmark me-1"></i>Clear
          </button>
        </div>
        <div class="help mt-1">Only non “dropdown head” menus can be selected.</div>
      </div>

      {{-- Action --}}
      <div class="col-12">
        <div class="section-head mb-2">
          <div>
            <label class="form-label mb-1">Privileges <span class="req">*</span></label>
            <div class="help">Add one or more privileges for the selected module. Use the plus button to keep adding rows.</div>
          </div>
          <button id="btnAddPrivilegeRow" type="button" class="btn btn-light">
            <i class="fa fa-plus me-1"></i>Add Privilege
          </button>
        </div>
        <div id="privilegeRows" class="priv-list"></div>
      </div>

      <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <button id="btnResetForm" class="btn btn-light" type="button"><i class="fa fa-rotate-left me-1"></i>Reset</button>
        <button id="btnSaveBottom" class="btn btn-primary" type="button"><i class="fa fa-floppy-disk me-1"></i><span id="saveBtnTxt2">Save</span></button>
      </div>

    </div>
  </div>
</div>

<template id="privilegeRowTemplate">
  <div class="priv-card js-priv-row">
    <div class="priv-card-head">
      <div>
        <div class="priv-card-title js-priv-row-title">Privilege 1</div>
        <div class="help">Action is required. API is optional.</div>
      </div>
      <button type="button" class="btn btn-light priv-remove-btn js-remove-priv-row">
        <i class="fa fa-trash me-1"></i>Remove
      </button>
    </div>

    <div class="priv-grid">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label mb-1">Action (unique per module) <span class="req">*</span></label>
          <select class="form-select js-action-select">
            <option value="">Select action…</option>
            <option value="add">Add</option>
            <option value="edit">Edit</option>
            <option value="delete">Delete</option>
            <option value="view">View</option>
            <option value="__other">Other…</option>
          </select>
          <input class="form-control mt-2 d-none js-action-other" maxlength="60" placeholder="Custom action, e.g. approve_enquiry">
        </div>

        <div class="col-md-6">
          <label class="form-label mb-1">Status</label>
          <select class="form-select js-status">
            <option value="active" selected>Active</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label mb-1">Description</label>
          <textarea class="form-control js-description" rows="3" placeholder="Optional description"></textarea>
        </div>

        <div class="col-12">
          <label class="form-label mb-1">API Endpoint / Route</label>
          <div class="priv-api-actions">
            <div class="grow">
              <input class="form-control readonly js-api-label" readonly placeholder="Choose API route from controller tree…">
              <input type="hidden" class="js-api-pattern" value="">
              <input type="hidden" class="js-api-controller" value="">
              <input type="hidden" class="js-api-function" value="">
            </div>
            <button type="button" class="btn btn-light js-pick-api">
              <i class="fa fa-code me-1"></i>Set API
            </button>
            <button type="button" class="btn btn-light js-clear-api">
              <i class="fa fa-xmark me-1"></i>Clear
            </button>
          </div>

          <div class="mt-2" style="max-width:360px">
            <label class="form-label mb-1">HTTP Method</label>
            <select class="form-select js-http-method" disabled>
              <option value="">Auto</option>
              <option value="GET">GET</option>
              <option value="POST">POST</option>
              <option value="PUT">PUT</option>
              <option value="PATCH">PATCH</option>
              <option value="DELETE">DELETE</option>
            </select>
            <div class="help mt-1">Auto-filled when you select a route from the API modal.</div>
          </div>

          <div class="help mt-2">Optional. Pick from controller tree if this privilege should point to a specific API route.</div>
        </div>
      </div>
    </div>
  </div>
</template>

{{-- ===================== MODULE PICKER MODAL ===================== --}}
<div class="modal fade" id="moduleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0"><i class="fa fa-sitemap me-2"></i>Select Module</h5>
          <div class="help">Choose from Dashboard Menus tree. Dropdown-head items can’t be selected.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="tree-wrap">
          <div class="tree-toolbar d-flex flex-wrap align-items-center gap-2">
            <div class="position-relative flex-grow-1" style="min-width:280px;">
              <input id="menuSearch" class="form-control ps-5" placeholder="Search menus…">
              <i class="fa fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);opacity:.6;"></i>
            </div>
            <div class="help">Selected: <span id="selectedModulePill" class="pill">—</span></div>
          </div>

          <div id="menuTree" class="tree-body">
            <div class="help p-2">Loading menus…</div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

{{-- ===================== API PICKER MODAL ===================== --}}
<div class="modal fade" id="apiModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0"><i class="fa fa-route me-2"></i>Pick API Route</h5>
          <div class="help">Select a method leaf to fill API endpoint + method automatically.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="tree-wrap">
          <div class="tree-toolbar d-flex flex-wrap align-items-center gap-2">
            <div class="position-relative flex-grow-1" style="min-width:320px;">
              <input id="apiSearch" class="form-control ps-5" placeholder="Search controller / path / method / function…">
              <i class="fa fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);opacity:.6;"></i>
            </div>

            <button id="btnReloadApi" type="button" class="btn btn-light btn-sm" style="height:38px;border-radius:12px">
              <i class="fa fa-rotate me-1"></i>Reload
            </button>

            <button id="btnClearApiPick" type="button" class="btn btn-light btn-sm" style="height:38px;border-radius:12px">
              <i class="fa fa-eraser me-1"></i>Clear selection
            </button>

            <div class="help">Selected: <span id="selectedApiPill" class="pill">—</span></div>
          </div>

          <div id="apiTree" class="tree-body">
            <div class="help p-2">Loading routes…</div>
          </div>
        </div>

        <div class="api-note">
          Source: <code>GET /api/privileges/index-of-api</code>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

{{-- Toasts --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:2100">
  <div id="okToast" class="toast text-bg-success border-0"><div class="d-flex">
    <div id="okMsg" class="toast-body">Done</div><button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button>
  </div></div>
  <div id="errToast" class="toast text-bg-danger border-0 mt-2"><div class="d-flex">
    <div id="errMsg" class="toast-body">Something went wrong</div><button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button>
  </div></div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function(){
  if (window.__PAGE_PRIV_CREATE_INIT__) return;
  window.__PAGE_PRIV_CREATE_INIT__ = true;

  const TOKEN = localStorage.getItem('token') || sessionStorage.getItem('token') || '';
  if(!TOKEN){
    Swal.fire('Login needed','Your session expired. Please login again.','warning')
      .then(()=> location.href='/');
    return;
  }

  // ✅ Endpoints
  const API_CREATE     = '/api/privileges';
  const API_SHOW_ONE   = (key)=> `/api/privileges/${encodeURIComponent(key)}`;
  const API_UPDATE_ONE = (key)=> `/api/privileges/${encodeURIComponent(key)}`;

  const API_MENUS_TREE = '/api/dashboard-menus/tree?only_active=0';
  const API_MENUS_LIST = '/api/dashboard-menus?per_page=2000';
  const API_API_MAP    = '/api/privileges/index-of-api';

  const okToast=new bootstrap.Toast(document.getElementById('okToast'));
  const errToast=new bootstrap.Toast(document.getElementById('errToast'));
  const ok=(m)=>{document.getElementById('okMsg').textContent=m||'Done'; okToast.show();};
  const err=(m)=>{document.getElementById('errMsg').textContent=m||'Something went wrong'; errToast.show();};

  const els = {
    heading: document.getElementById('pageHeading'),
    subheading: document.getElementById('pageSubheading'),
    saveTxtTop: document.getElementById('saveBtnTxt'),
    saveTxtBottom: document.getElementById('saveBtnTxt2'),

    back: document.getElementById('btnBack'),
    saveTop: document.getElementById('btnSave'),
    saveBottom: document.getElementById('btnSaveBottom'),
    reset: document.getElementById('btnResetForm'),

    privKey: document.getElementById('priv_key'),

    moduleId: document.getElementById('module_id'),
    moduleLabel: document.getElementById('module_label'),
    pickModule: document.getElementById('btnPickModule'),
    clearModule: document.getElementById('btnClearModule'),
    rowsWrap: document.getElementById('privilegeRows'),
    addRow: document.getElementById('btnAddPrivilegeRow'),
    rowTemplate: document.getElementById('privilegeRowTemplate'),

    moduleModalEl: document.getElementById('moduleModal'),
    apiModalEl: document.getElementById('apiModal'),

    // module tree
    menuTree: document.getElementById('menuTree'),
    menuSearch: document.getElementById('menuSearch'),
    selectedModulePill: document.getElementById('selectedModulePill'),

    // api tree
    apiTree: document.getElementById('apiTree'),
    apiSearch: document.getElementById('apiSearch'),
    reloadApi: document.getElementById('btnReloadApi'),
    clearApiPick: document.getElementById('btnClearApiPick'),
    selectedApiPill: document.getElementById('selectedApiPill'),
  };

  const moduleModal = new bootstrap.Modal(els.moduleModalEl);
  const apiModal = new bootstrap.Modal(els.apiModalEl);

  const esc = (s)=>{
    const m={'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;','`':'&#96;'};
    return (s==null?'':String(s)).replace(/[&<>\"'`]/g,ch=>m[ch]);
  };

  const tryParseJson = (v)=>{
    if(v == null) return null;
    if(typeof v === 'object') return v;
    if(typeof v === 'string'){
      const s=v.trim();
      if(!s) return null;
      try{ return JSON.parse(s); }catch{ return null; }
    }
    return null;
  };

  let _rowSeq = 0;
  let _activeApiRowId = null;

  function getPrivilegeRows(){
    return Array.from(els.rowsWrap.querySelectorAll('.js-priv-row'));
  }

  function getRowEls(row){
    return {
      actionSelect: row.querySelector('.js-action-select'),
      actionOther: row.querySelector('.js-action-other'),
      status: row.querySelector('.js-status'),
      desc: row.querySelector('.js-description'),
      apiLabel: row.querySelector('.js-api-label'),
      apiPattern: row.querySelector('.js-api-pattern'),
      httpMethod: row.querySelector('.js-http-method'),
      apiController: row.querySelector('.js-api-controller'),
      apiFunction: row.querySelector('.js-api-function'),
      rowTitle: row.querySelector('.js-priv-row-title'),
      removeBtn: row.querySelector('.js-remove-priv-row'),
    };
  }

  function setRowActionValue(row, actionStr){
    const nodes = getRowEls(row);
    const a = (actionStr || '').trim();
    if(!a){
      nodes.actionSelect.value = '';
      nodes.actionOther.value = '';
      nodes.actionOther.classList.add('d-none');
      return;
    }
    const builtIn = ['add','edit','delete','view'];
    if(builtIn.includes(a.toLowerCase())){
      nodes.actionSelect.value = a.toLowerCase();
      nodes.actionOther.value = '';
      nodes.actionOther.classList.add('d-none');
    }else{
      nodes.actionSelect.value = '__other';
      nodes.actionOther.classList.remove('d-none');
      nodes.actionOther.value = a;
    }
  }

  function setRowApiSelection(row, path, method, controllerKey, fnName){
    const nodes = getRowEls(row);
    const p = (path || '').trim();
    const m = (method || '').trim().toUpperCase();

    nodes.apiPattern.value = p;
    nodes.apiController.value = controllerKey || '';
    nodes.apiFunction.value = fnName || '';
    nodes.httpMethod.value = p ? (m || '') : '';
    nodes.httpMethod.disabled = !p;

    const label = p ? ((m ? `[${m}] ` : '') + p) : '';
    nodes.apiLabel.value = label;

    if(_activeApiRowId === row.dataset.rowId){
      els.selectedApiPill.textContent = label || '—';
    }
  }

  function createPrivilegeRow(data = {}){
    const fragment = els.rowTemplate.content.cloneNode(true);
    const row = fragment.querySelector('.js-priv-row');
    row.dataset.rowId = String(++_rowSeq);
    els.rowsWrap.appendChild(row);

    const nodes = getRowEls(row);
    setRowActionValue(row, data.action || '');
    nodes.status.value = data.status || 'active';
    nodes.desc.value = data.description || '';
    setRowApiSelection(row, data.apiPattern || '', data.httpMethod || '', data.apiController || '', data.apiFunction || '');

    updatePrivilegeRowMeta();
    return row;
  }

  function updatePrivilegeRowMeta(){
    const rows = getPrivilegeRows();
    rows.forEach((row, idx)=>{
      const nodes = getRowEls(row);
      if(nodes.rowTitle) nodes.rowTitle.textContent = `Privilege ${idx + 1}`;
      if(nodes.removeBtn) nodes.removeBtn.disabled = rows.length === 1 || isEdit;
    });
  }

  function resetCreateRows(rows = [{}]){
    els.rowsWrap.innerHTML = '';
    rows.forEach(data => createPrivilegeRow(data));
    updatePrivilegeRowMeta();
  }

  function pickApiAndMethodFromRecord(r){
    const metaObj = tryParseJson(r?.meta) || {};
    const assigned = tryParseJson(r?.assigned_apis);
    const assignedFirst = Array.isArray(assigned) ? (assigned[0] || '') : '';

    const method =
      (metaObj?.http_method ? String(metaObj.http_method).toUpperCase() : '') ||
      (r?.http_method ? String(r.http_method).toUpperCase() : '') ||
      (typeof r?.http_methods === 'string' ? r.http_methods : '') ||
      (Array.isArray(r?.http_methods) ? r.http_methods.join(',') : '');

    const api =
      (r?.api_pattern || r?.api || r?.endpoint || assignedFirst || '');

    return { api, method };
  }

  async function fetchJSON(url, opts={}){
    const res = await fetch(url, {
      method: opts.method || 'GET',
      headers: Object.assign({
        'Authorization':'Bearer '+TOKEN,
        'Accept':'application/json'
      }, opts.headers || {}),
      body: opts.body
    });
    const j = await res.json().catch(()=>({}));
    if(!res.ok) throw new Error(j?.message || ('HTTP '+res.status));
    return j;
  }

  /* ===================== MODE: CREATE OR EDIT ===================== */
  const usp = new URLSearchParams(window.location.search);
  const editKey = (usp.get('edit') || usp.get('id') || usp.get('uuid') || '').trim();
  let isEdit = !!editKey;
  let _editSnapshot = null;

  function setModeUI(){
    if(!isEdit){
      els.heading.textContent = 'Create Page Privilege';
      els.subheading.textContent = 'Module and API are chosen via modals.';
      els.saveTxtTop.textContent = 'Save';
      els.saveTxtBottom.textContent = 'Save';
      return;
    }
    els.heading.textContent = 'Edit Page Privilege';
    els.subheading.textContent = 'You are editing an existing privilege. Update and save changes.';
    els.saveTxtTop.textContent = 'Update';
    els.saveTxtBottom.textContent = 'Update';
  }

  /* ===================== MODULE MODAL + TREE ===================== */
  let _menuLoaded = false;
  let _roots = [];
  let _menuFlatCache = null;

  function normalizeMenusFromFlat(items){
    const nodes = (items||[]).map(m => ({
      id: m.id ?? null,
      uuid: m.uuid ?? null,
      parent_id: m.parent_id ?? null,
      parent_uuid: m.parent_uuid ?? null,
      name: m.name ?? m.title ?? ('#'+(m.id||'')),
      icon_class: m.icon_class ?? '',
      is_dropdown_head: Number(m.is_dropdown_head ?? 0),
      children: [],
      _order: Number(m.sort_order ?? m.position ?? m.order ?? m.id ?? 0)
    }));

    const byId = new Map();
    const byUuid = new Map();
    nodes.forEach(n=>{
      if(n.id != null) byId.set(String(n.id), n);
      if(n.uuid) byUuid.set(String(n.uuid), n);
    });

    const roots = [];
    nodes.forEach(n=>{
      let parent = null;
      if(n.parent_uuid && byUuid.has(String(n.parent_uuid))) parent = byUuid.get(String(n.parent_uuid));
      else if(n.parent_id != null && byId.has(String(n.parent_id))) parent = byId.get(String(n.parent_id));

      if(parent) parent.children.push(n);
      else roots.push(n);
    });

    const sortTree = (arr)=>{
      arr.sort((a,b)=>(a._order||0)-(b._order||0));
      arr.forEach(x=> sortTree(x.children||[]));
    };
    sortTree(roots);

    return roots;
  }

  function normalizeMenusFromTree(nodes){
    const walk = (n)=>({
      id: n.id ?? null,
      uuid: n.uuid ?? null,
      parent_id: n.parent_id ?? null,
      parent_uuid: n.parent_uuid ?? null,
      name: n.name ?? n.title ?? ('#'+(n.id||'')),
      icon_class: n.icon_class ?? '',
      is_dropdown_head: Number(n.is_dropdown_head ?? 0),
      children: Array.isArray(n.children) ? n.children.map(walk) : [],
      _order: Number(n.sort_order ?? n.position ?? n.order ?? n.id ?? 0)
    });
    const arr = Array.isArray(nodes) ? nodes.map(walk) : [];
    const sortTree = (a)=>{
      a.sort((x,y)=>(x._order||0)-(y._order||0));
      a.forEach(z=> sortTree(z.children||[]));
    };
    sortTree(arr);
    return arr;
  }

  function filterTree(roots, q){
    const query = (q||'').trim().toLowerCase();
    if(!query) return roots;

    const walk = (node)=>{
      const name = String(node.name||'').toLowerCase();
      const hit = name.includes(query);
      const kids = (node.children||[]).map(walk).filter(Boolean);
      if(hit || kids.length) return { ...node, children: kids };
      return null;
    };
    return roots.map(walk).filter(Boolean);
  }

  function renderMenuTree(container, roots){
    container.innerHTML = '';
    if(!roots.length){
      container.innerHTML = `<div class="help p-2">No menus found.</div>`;
      return;
    }

    const makeNode = (node, depth=0)=>{
      const wrap = document.createElement('div');
      wrap.className = 'tree-node';

      const row = document.createElement('div');
      row.className = 'tree-row';

      const left = document.createElement('div');
      left.className = 'tree-left';

      for(let i=0;i<depth;i++){
        const ind = document.createElement('span');
        ind.className = 'tree-indent';
        left.appendChild(ind);
      }

      const hasChildren = (node.children||[]).length > 0;
      let kids = null;

      if(hasChildren){
        const t = document.createElement('button');
        t.type='button';
        t.className='tree-toggle';
        t.setAttribute('aria-expanded','true');
        t.innerHTML = `<i class="fa fa-chevron-down"></i>`;
        left.appendChild(t);

        t.addEventListener('click', ()=>{
          const open = t.getAttribute('aria-expanded') === 'true';
          t.setAttribute('aria-expanded', open ? 'false' : 'true');
          if(kids) kids.classList.toggle('show', !open);
        });
      }else{
        const spacer = document.createElement('span');
        spacer.className='tree-toggle';
        spacer.style.visibility='hidden';
        spacer.innerHTML = `<i class="fa fa-chevron-down"></i>`;
        left.appendChild(spacer);
      }

      const icon = document.createElement('span');
      icon.innerHTML = node.icon_class
        ? `<i class="${esc(node.icon_class)}" style="opacity:.85"></i>`
        : `<i class="fa fa-circle-dot" style="opacity:.35"></i>`;
      left.appendChild(icon);

      const titleWrap = document.createElement('div');
      titleWrap.style.minWidth='0';
      titleWrap.innerHTML = `
        <div class="tree-title">${esc(node.name)}</div>
        <div class="tree-sub">${node.is_dropdown_head ? '<span class="pill">Dropdown head</span>' : '<span class="pill">Menu</span>'}</div>
      `;
      left.appendChild(titleWrap);

      const actions = document.createElement('div');
      actions.className='tree-actions';

      const selectBtn = document.createElement('button');
      selectBtn.type='button';
      selectBtn.className='btn btn-primary btn-sm';
      selectBtn.innerHTML = `<i class="fa fa-check me-1"></i>Select`;

      if(node.is_dropdown_head){
        selectBtn.style.display='none';
      }else{
        selectBtn.addEventListener('click', ()=>{
          const chosen = (node.id != null) ? String(node.id) : (node.uuid ? String(node.uuid) : '');
          els.moduleId.value = chosen;
          els.moduleLabel.value = node.name || '';
          els.selectedModulePill.textContent = node.name || '—';
          moduleModal.hide();
        });
      }

      actions.appendChild(selectBtn);

      row.appendChild(left);
      row.appendChild(actions);
      wrap.appendChild(row);

      kids = document.createElement('div');
      kids.className = 'tree-children' + (hasChildren ? ' show' : '');
      wrap.appendChild(kids);

      (node.children||[]).forEach(ch=> kids.appendChild(makeNode(ch, depth+1)));

      if(!hasChildren){
        kids.classList.remove('show');
        kids.style.display='none';
      }

      return wrap;
    };

    const frag = document.createDocumentFragment();
    roots.forEach(r=> frag.appendChild(makeNode(r, 0)));
    container.appendChild(frag);
  }

  async function ensureMenuFlatCache(){
    if(_menuFlatCache) return _menuFlatCache;
    try{
      const j = await fetchJSON(API_MENUS_LIST);
      const items = j.data || j.menus || j.dashboard_menus || (Array.isArray(j) ? j : []);
      _menuFlatCache = Array.isArray(items) ? items : [];
    }catch{
      _menuFlatCache = [];
    }
    return _menuFlatCache;
  }

  async function resolveModuleLabelByIdentifier(identifier){
    if(!identifier) return '';
    const idStr = String(identifier);
    const items = await ensureMenuFlatCache();
    const found = items.find(m =>
      String(m.id ?? '') === idStr || String(m.uuid ?? '') === idStr
    );
    return found?.name || found?.title || (found?.id ? ('#'+found.id) : '');
  }

  async function loadMenusOnce(){
    if(_menuLoaded) return;

    els.menuTree.innerHTML = `<div class="help p-2">Loading menus…</div>`;
    try{
      let jTree = null;
      try { jTree = await fetchJSON(API_MENUS_TREE); } catch (_) { jTree = null; }

      if(jTree){
        const nodes = jTree.data || jTree.menus || jTree.dashboard_menus || (Array.isArray(jTree) ? jTree : []);
        _roots = normalizeMenusFromTree(nodes);
      } else {
        const j = await fetchJSON(API_MENUS_LIST);
        const items = j.data || j.menus || j.dashboard_menus || (Array.isArray(j) ? j : []);
        _roots = normalizeMenusFromFlat(items);
      }

      _menuLoaded = true;

    }catch(e){
      els.menuTree.innerHTML = `<div class="text-danger small p-2">Failed to load menus: ${esc(e.message||'')}</div>`;
      throw e;
    }
  }

  function openModuleModal(){
    els.selectedModulePill.textContent = els.moduleLabel.value || '—';
    els.menuSearch.value = '';
    moduleModal.show();

    loadMenusOnce()
      .then(()=> renderMenuTree(els.menuTree, _roots))
      .catch((e)=> err(e.message || 'Failed to load menus'));
  }

  els.pickModule.addEventListener('click', openModuleModal);
  els.moduleLabel.addEventListener('click', openModuleModal);

  els.clearModule.addEventListener('click', ()=>{
    els.moduleId.value = '';
    els.moduleLabel.value = '';
    els.selectedModulePill.textContent = '—';
  });

  let _searchT = null;
  els.menuSearch.addEventListener('input', ()=>{
    clearTimeout(_searchT);
    _searchT = setTimeout(()=>{
      renderMenuTree(els.menuTree, filterTree(_roots, els.menuSearch.value));
    }, 150);
  });

  /* ===================== API MODAL (controller tree) ===================== */
  let _apiMapLoaded = false;
  let _apiControllers = {};

  function normalizeApiMapResponse(j){
    const root = (j && typeof j === 'object') ? j : {};
    if (root.Api && typeof root.Api === 'object') return root.Api;
    if (root.data && root.data.Api && typeof root.data.Api === 'object') return root.data.Api;
    if (root.data && typeof root.data === 'object') return root.data;
    return root;
  }

  function renderApiTree(container, apiControllers){
    container.innerHTML = '';

    const keys = Object.keys(apiControllers || {});
    if(!keys.length){
      container.innerHTML = `<div class="help p-2">No routes found.</div>`;
      return;
    }

    const frag = document.createDocumentFragment();

    const makeNode = ({titleText, subText, depth=0, hasChildren=true, startOpen=true})=>{
      const wrap = document.createElement('div');
      wrap.className = 'tree-node';

      const row = document.createElement('div');
      row.className = 'tree-row';

      const left = document.createElement('div');
      left.className = 'tree-left';

      for(let i=0;i<depth;i++){
        const ind = document.createElement('span');
        ind.className = 'tree-indent';
        left.appendChild(ind);
      }

      let kids = null;

      if(hasChildren){
        const t = document.createElement('button');
        t.type='button';
        t.className='tree-toggle';
        t.setAttribute('aria-expanded', startOpen ? 'true' : 'false');
        t.innerHTML = `<i class="fa fa-chevron-down"></i>`;
        left.appendChild(t);

        t.addEventListener('click', ()=>{
          const open = t.getAttribute('aria-expanded') === 'true';
          t.setAttribute('aria-expanded', open ? 'false' : 'true');
          if(kids) kids.classList.toggle('show', !open);
        });
      } else {
        const spacer = document.createElement('span');
        spacer.className='tree-toggle';
        spacer.style.visibility='hidden';
        spacer.innerHTML = `<i class="fa fa-chevron-down"></i>`;
        left.appendChild(spacer);
      }

      const titleWrap = document.createElement('div');
      titleWrap.style.minWidth='0';
      titleWrap.innerHTML = `
        <div class="tree-title">${esc(titleText||'-')}</div>
        ${subText ? `<div class="tree-sub">${subText}</div>` : `<div class="tree-sub"></div>`}
      `;
      left.appendChild(titleWrap);

      const actions = document.createElement('div');
      actions.className='tree-actions';

      row.appendChild(left);
      row.appendChild(actions);
      wrap.appendChild(row);

      kids = document.createElement('div');
      kids.className = 'tree-children' + (startOpen ? ' show' : '');
      wrap.appendChild(kids);

      return { wrap, kids, actions };
    };

    keys.sort().forEach((controllerKey)=>{
      const pathsObj = apiControllers[controllerKey] || {};
      const pathKeys = Object.keys(pathsObj || {});
      const c = makeNode({
        titleText: controllerKey,
        subText: `<span class="pill">${pathKeys.length} route${pathKeys.length===1?'':'s'}</span>`,
        depth: 0,
        hasChildren: pathKeys.length>0,
        startOpen: true
      });

      pathKeys.sort().forEach((path)=>{
        const methodsArr = Array.isArray(pathsObj[path]) ? pathsObj[path] : [];
        const p = makeNode({
          titleText: path,
          subText: methodsArr.length ? `<span class="pill">${methodsArr.length} method${methodsArr.length===1?'':'s'}</span>` : '',
          depth: 1,
          hasChildren: methodsArr.length>0,
          startOpen: false
        });

        methodsArr.forEach((mObj)=>{
          const method = String(mObj?.method || '').toUpperCase();
          const fnName = String(mObj?.functionName || '');

          const leaf = makeNode({
            titleText: `${method} ${path}`,
            subText: fnName ? `<span class="pill">fn: ${esc(fnName)}</span>` : '',
            depth: 2,
            hasChildren: false,
            startOpen: false
          });

          const titleEl = leaf.wrap.querySelector('.tree-title');
          if(titleEl){
            titleEl.innerHTML = `<span class="m-pill">${esc(method||'—')}</span> <span>${esc(path)}</span>`;
          }

          const selectBtn = document.createElement('button');
          selectBtn.type = 'button';
          selectBtn.className = 'btn btn-primary btn-sm';
          selectBtn.innerHTML = `<i class="fa fa-check me-1"></i>Select`;
          selectBtn.addEventListener('click', ()=>{
            const row = getPrivilegeRows().find(x => x.dataset.rowId === _activeApiRowId) || getPrivilegeRows()[0];
            if(row) setRowApiSelection(row, path, method, controllerKey, fnName);
            apiModal.hide();
          });

          leaf.actions.appendChild(selectBtn);
          p.kids.appendChild(leaf.wrap);
        });

        c.kids.appendChild(p.wrap);
      });

      frag.appendChild(c.wrap);
    });

    container.appendChild(frag);
  }

  function filterApiDom(container, q){
    const query = (q||'').trim().toLowerCase();
    const nodes = container.querySelectorAll('.tree-node');
    if(!query){ nodes.forEach(n=> n.style.display=''); return; }
    nodes.forEach(n=>{
      const txt = (n.textContent || '').toLowerCase();
      n.style.display = txt.includes(query) ? '' : 'none';
    });
  }

  async function loadApiMap(force=false){
    if(_apiMapLoaded && !force) return;

    els.apiTree.innerHTML = `<div class="help p-2">Loading routes…</div>`;
    try{
      const j = await fetchJSON(API_API_MAP);
      _apiControllers = normalizeApiMapResponse(j) || {};
      _apiMapLoaded = true;
      renderApiTree(els.apiTree, _apiControllers);
    }catch(e){
      els.apiTree.innerHTML = `<div class="text-danger small p-2">Failed to load API map: ${esc(e.message||'')}</div>`;
      throw e;
    }
  }

  function openApiPickerForRow(row){
    if(!row) return;
    _activeApiRowId = row.dataset.rowId || null;
    const nodes = getRowEls(row);
    els.apiSearch.value = '';
    els.selectedApiPill.textContent = nodes.apiLabel.value || '—';
    apiModal.show();
    loadApiMap(false).catch(e=> err(e.message || 'Failed to load API map'));
  }

  els.reloadApi.addEventListener('click', ()=>{
    loadApiMap(true).then(()=> ok('API map reloaded')).catch(e=> err(e.message||'Reload failed'));
  });

  els.clearApiPick.addEventListener('click', ()=>{
    const row = getPrivilegeRows().find(x => x.dataset.rowId === _activeApiRowId) || getPrivilegeRows()[0];
    if(row) setRowApiSelection(row, '', '', '', '');
  });

  let _apiSearchT = null;
  els.apiSearch.addEventListener('input', ()=>{
    clearTimeout(_apiSearchT);
    _apiSearchT = setTimeout(()=> filterApiDom(els.apiTree, els.apiSearch.value), 120);
  });

  /* ===================== PREFILL FOR EDIT ===================== */
  function safeJson(v){
    if(!v) return null;
    if(typeof v === 'object') return v;
    if(typeof v === 'string'){
      try { return JSON.parse(v); } catch(e){ return null; }
    }
    return null;
  }

  async function prefillEditMode(key){
    try{
      const j = await fetchJSON(API_SHOW_ONE(key));
      const r = j.privilege || j.data || j || {};
      if(!r || (!r.id && !r.uuid)){
        throw new Error('Privilege not found');
      }

      els.privKey.value = String(r.uuid || r.id);

      // module
      const moduleIdentifier =
        r.dashboard_menu_id || r.dashboard_menu_uuid ||
        r.module_id || r.module_uuid ||
        r.module || '';

      if(moduleIdentifier){
        els.moduleId.value = String(moduleIdentifier);
        const label = await resolveModuleLabelByIdentifier(moduleIdentifier);
        els.moduleLabel.value = label || (r.module_name || r.module || '');
        els.selectedModulePill.textContent = els.moduleLabel.value || '—';
      }

      resetCreateRows([{}]);
      const row = getPrivilegeRows()[0];
      const { api, method } = pickApiAndMethodFromRecord(r);
      setRowActionValue(row, r.action || '');
      const rowEls = getRowEls(row);
      rowEls.status.value = (r.status || 'active');
      rowEls.desc.value = (r.description || '');
      setRowApiSelection(row, api || '', method || '', r.api_controller || '', r.api_function || '');

      // snapshot for reset
      _editSnapshot = {
        privKey: els.privKey.value,
        moduleId: els.moduleId.value,
        moduleLabel: els.moduleLabel.value,
        rows: [{
          actionSelect: rowEls.actionSelect.value,
          actionOther: rowEls.actionOther.value,
          status: rowEls.status.value,
          desc: rowEls.desc.value,
          apiPattern: rowEls.apiPattern.value,
          apiLabel: rowEls.apiLabel.value,
          httpMethod: rowEls.httpMethod.value,
          apiController: rowEls.apiController.value,
          apiFunction: rowEls.apiFunction.value
        }]
      };

      ok('Loaded privilege for edit');
    }catch(e){
      err(e.message || 'Failed to load privilege');
      Swal.fire('Edit failed', e.message || 'Unable to load privilege for editing.', 'error')
        .then(()=> location.href = '/page-privilege/manage');
    }
  }

  /* ===================== SAVE (CREATE OR UPDATE) ===================== */
  function getFinalAction(row){
    const nodes = getRowEls(row);
    const sel = (nodes.actionSelect.value || '').trim();
    if(!sel) return { ok:false, msg:'Please select an action.' };
    if(sel === '__other'){
      const custom = (nodes.actionOther.value || '').trim();
      if(!custom) return { ok:false, msg:'Please type a custom action.' };
      return { ok:true, value: custom };
    }
    return { ok:true, value: sel };
  }

  function rowHasAnyValue(row){
    const nodes = getRowEls(row);
    return !!(
      (nodes.actionSelect.value || '').trim() ||
      (nodes.actionOther.value || '').trim() ||
      (nodes.desc.value || '').trim() ||
      (nodes.apiPattern.value || '').trim()
    );
  }

  function buildRowPayload(row, index){
    const nodes = getRowEls(row);
    const actionInfo = getFinalAction(row);

    if(!actionInfo.ok){
      if(rowHasAnyValue(row) || getPrivilegeRows().length === 1){
        return { ok:false, msg:`Privilege ${index + 1}: ${actionInfo.msg}` };
      }
      return { ok:true, skip:true };
    }

    const payload = {
      action: actionInfo.value,
      description: (nodes.desc.value || '').trim() || null,
      status: (nodes.status.value || 'active')
    };

    const api = (nodes.apiPattern.value || '').trim();
    const method = (nodes.httpMethod.value || '').trim().toUpperCase();

    if(api){
      payload.assigned_apis = [api];
    }
    if(api && method){
      payload.meta = { http_method: method };
    }

    return {
      ok:true,
      payload,
      summary: {
        action: actionInfo.value,
        apiLabel: nodes.apiLabel.value || api || 'Not set'
      }
    };
  }

  async function savePrivilege(){
    const moduleId = (els.moduleId.value || '').trim();
    if(!moduleId) return Swal.fire('Module required','Please choose a module first.','info');

    const isNowEdit = isEdit && !!(els.privKey.value || editKey);
    const keyToUse = (els.privKey.value || editKey || '').trim();
    const rows = getPrivilegeRows();
    const builtRows = [];

    for(let idx = 0; idx < rows.length; idx++){
      const built = buildRowPayload(rows[idx], idx);
      if(!built.ok){
        return Swal.fire('Action required', built.msg, 'info');
      }
      if(!built.skip){
        builtRows.push(built);
      }
    }

    if(!builtRows.length){
      return Swal.fire('Privilege required', 'Please add at least one privilege row with an action.', 'info');
    }

    const payload = isNowEdit
      ? Object.assign({ dashboard_menu_id: moduleId }, builtRows[0].payload)
      : {
          dashboard_menu_id: moduleId,
          privileges: builtRows.map(item => item.payload)
        };

    const previewHtml = builtRows.slice(0, 4).map((item, idx)=>
      `${idx + 1}. <b>${esc(item.summary.action)}</b> <span style="opacity:.7">(${esc(item.summary.apiLabel)})</span>`
    ).join('<br>');

    const {isConfirmed} = await Swal.fire({
      icon:'question',
      title: isNowEdit ? 'Update page privilege?' : `Create ${builtRows.length} page privilege${builtRows.length === 1 ? '' : 's'}?`,
      html:`Module: <b>${esc(els.moduleLabel.value||'-')}</b><br>${previewHtml}${builtRows.length > 4 ? `<br>...and ${builtRows.length - 4} more` : ''}`,
      showCancelButton:true,
      confirmButtonText: isNowEdit ? 'Update' : 'Create'
    });
    if(!isConfirmed) return;

    els.saveTop.disabled = true;
    els.saveBottom.disabled = true;

    try{
      let res, j;

      if(isNowEdit){
        res = await fetch(API_UPDATE_ONE(keyToUse), {
          method:'PATCH',
          headers:{
            'Authorization':'Bearer '+TOKEN,
            'Content-Type':'application/json',
            'Accept':'application/json'
          },
          body: JSON.stringify(payload)
        });
        j = await res.json().catch(()=>({}));
        if(!res.ok) throw new Error(j?.message || 'Update failed');

        ok('Privilege updated');
        Swal.fire({
          icon:'success',
          title:'Updated!',
          text:'Page privilege updated successfully.',
          confirmButtonText:'Go to Manage'
        }).then(()=> { location.href = '/page-privilege/manage'; });

      }else{
        res = await fetch(API_CREATE, {
          method:'POST',
          headers:{
            'Authorization':'Bearer '+TOKEN,
            'Content-Type':'application/json',
            'Accept':'application/json'
          },
          body: JSON.stringify(payload)
        });
        j = await res.json().catch(()=>({}));
        if(!res.ok) throw new Error(j?.message || 'Create failed');

        ok('Privileges created');
        Swal.fire({
          icon:'success',
          title:'Created!',
          text:`${builtRows.length} page privilege${builtRows.length === 1 ? '' : 's'} created successfully.`,
          confirmButtonText:'Go to Manage'
        }).then(()=> { location.href = '/page-privilege/manage'; });
      }

    }catch(e){
      err(e.message || (isEdit ? 'Update failed' : 'Create failed'));
    }finally{
      els.saveTop.disabled = false;
      els.saveBottom.disabled = false;
    }
  }

  /* ===================== RESET / BACK ===================== */
  function resetForm(){
    if(isEdit && _editSnapshot){
      els.privKey.value = _editSnapshot.privKey || '';
      els.moduleId.value = _editSnapshot.moduleId || '';
      els.moduleLabel.value = _editSnapshot.moduleLabel || '';
      els.selectedModulePill.textContent = els.moduleLabel.value || '—';
      resetCreateRows(_editSnapshot.rows || [{}]);
      const row = getPrivilegeRows()[0];
      if(row){
        const rowEls = getRowEls(row);
        rowEls.actionSelect.value = _editSnapshot.rows?.[0]?.actionSelect || '';
        rowEls.actionOther.value = _editSnapshot.rows?.[0]?.actionOther || '';
        rowEls.status.value = _editSnapshot.rows?.[0]?.status || 'active';
        rowEls.desc.value = _editSnapshot.rows?.[0]?.desc || '';
        if(rowEls.actionSelect.value === '__other') rowEls.actionOther.classList.remove('d-none');
        else rowEls.actionOther.classList.add('d-none');
        setRowApiSelection(
          row,
          _editSnapshot.rows?.[0]?.apiPattern || '',
          _editSnapshot.rows?.[0]?.httpMethod || '',
          _editSnapshot.rows?.[0]?.apiController || '',
          _editSnapshot.rows?.[0]?.apiFunction || ''
        );
      }
      ok('Reverted changes');
      return;
    }

    els.privKey.value = '';
    els.moduleId.value = '';
    els.moduleLabel.value = '';
    els.selectedModulePill.textContent = '—';
    resetCreateRows([{}]);
    els.selectedApiPill.textContent = '—';
  }

  els.rowsWrap.addEventListener('change', (e)=>{
    if(e.target.matches('.js-action-select')){
      const row = e.target.closest('.js-priv-row');
      const nodes = getRowEls(row);
      if(e.target.value === '__other'){
        nodes.actionOther.classList.remove('d-none');
        nodes.actionOther.focus();
      }else{
        nodes.actionOther.classList.add('d-none');
        nodes.actionOther.value = '';
      }
    }
  });

  els.rowsWrap.addEventListener('click', (e)=>{
    const row = e.target.closest('.js-priv-row');
    if(!row) return;

    if(e.target.closest('.js-pick-api') || e.target.classList.contains('js-api-label')){
      openApiPickerForRow(row);
      return;
    }

    if(e.target.closest('.js-clear-api')){
      setRowApiSelection(row, '', '', '', '');
      return;
    }

    if(e.target.closest('.js-remove-priv-row')){
      if(getPrivilegeRows().length === 1 || isEdit) return;
      row.remove();
      updatePrivilegeRowMeta();
    }
  });

  els.addRow.addEventListener('click', ()=>{
    createPrivilegeRow({});
  });

  els.reset.addEventListener('click', resetForm);
  els.back.addEventListener('click', ()=> location.href = '/page-privilege/manage');
  els.saveTop.addEventListener('click', savePrivilege);
  els.saveBottom.addEventListener('click', savePrivilege);

  // ✅ init
  setModeUI();
  resetCreateRows([{}]);
  if(isEdit){
    els.addRow.classList.add('d-none');
    prefillEditMode(editKey);
  }

})();
</script>
@endpush
