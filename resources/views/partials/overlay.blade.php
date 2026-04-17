{{-- resources/views/partials/overlay.blade.php --}}
@php
  $appName = config('app.name', env('APP_NAME', 'Application'));
@endphp

<div id="pageOverlay" class="w3-loader-overlay">
    <div class="w3-loader-backdrop"></div>

    <div class="w3-loader-inner">
        <div class="w3-loader-main">
            <div class="w3-loader-up">
                <div class="w3-loaders">
                    <div class="w3-loader-stick"></div>
                    <div class="w3-loader-stick"></div>
                    <div class="w3-loader-stick"></div>
                    <div class="w3-loader-stick"></div>
                    <div class="w3-loader-stick"></div>
                    <div class="w3-loader-stick"></div>
                    <div class="w3-loader-stick"></div>
                    <div class="w3-loader-stick"></div>
                    <div class="w3-loader-stick"></div>
                </div>

                <div class="w3-loaders-b">
                    <div class="w3-loader-a"><div class="w3-ball w3-ball-0"></div></div>
                    <div class="w3-loader-a"><div class="w3-ball w3-ball-1"></div></div>
                    <div class="w3-loader-a"><div class="w3-ball w3-ball-2"></div></div>
                    <div class="w3-loader-a"><div class="w3-ball w3-ball-3"></div></div>
                    <div class="w3-loader-a"><div class="w3-ball w3-ball-4"></div></div>
                    <div class="w3-loader-a"><div class="w3-ball w3-ball-5"></div></div>
                    <div class="w3-loader-a"><div class="w3-ball w3-ball-6"></div></div>
                    <div class="w3-loader-a"><div class="w3-ball w3-ball-7"></div></div>
                    <div class="w3-loader-a"><div class="w3-ball w3-ball-8"></div></div>
                </div>
            </div>
        </div>

        <div class="w3-loader-text">
            <h2>{{ $appName }}</h2>
            <p>Loading your workspace…</p>
        </div>
    </div>
</div>

<style>
/* ================== Global Overlay ================== */
.w3-loader-overlay{
  position: fixed;
  inset: 0;
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-sans);
  background:
    radial-gradient(circle at top, rgba(148,163,255,0.14), transparent 55%),
    linear-gradient(135deg,
      color-mix(in oklab, var(--bg-body, #0b1220) 80%, #020617 20%),
      color-mix(in oklab, var(--bg-body, #0b1220) 90%, #020617 10%)
    );
  backdrop-filter: blur(14px);
}

.w3-loader-backdrop{
  position: absolute;
  inset: 0;
  pointer-events: none;
  background:
    radial-gradient(circle at bottom, rgba(15,23,42,0.7), transparent 55%);
  opacity: .9;
}

.w3-loader-inner{
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 420px;
  padding: 28px 22px 22px;
  border-radius: 22px;
  border: 1px solid var(--line-strong, rgba(148,163,184,0.5));
  background:
    radial-gradient(circle at top left,
      color-mix(in oklab, var(--t-primary, rgba(59,130,246,0.16)) 70%, transparent),
      transparent 60%),
    var(--surface, #ffffff);
  box-shadow: var(--shadow-3, 0 22px 45px rgba(15,23,42,0.55));
  color: var(--ink, #0f172a);
  text-align: center;
  overflow: hidden;
}

/* ================== Loader ================== */
.w3-loader-main{
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 260px;
}

.w3-loader-up{
  position: relative;
  width: 240px;
  height: 240px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.w3-loaders,
.w3-loaders-b{
  display: flex;
  align-items: center;
  justify-content: center;
}

.w3-loader-stick{
  position: absolute;
  width: 1.15em;
  height: 13em;
  border-radius: 50px;
  background: #e0e0e0;
}

.w3-loader-stick::after{
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  width: 1.15em;
  height: 5em;
  background: #e0e0e0;
  border-radius: 50px;
  border: 1px solid #e2e2e2;
  box-shadow:
    inset 5px 5px 15px #d3d2d2ab,
    inset -5px -5px 15px #e9e9e9ab;
  -webkit-mask-image: linear-gradient(to bottom, black calc(100% - 48px), transparent 100%);
  mask-image: linear-gradient(to bottom, black calc(100% - 48px), transparent 100%);
}

.w3-loader-stick::before{
  content: "";
  position: absolute;
  bottom: 0;
  right: 0;
  width: 1.15em;
  height: 4.5em;
  background: #e0e0e0;
  border-radius: 50px;
  border: 1px solid #e2e2e2;
  box-shadow:
    inset 5px 5px 15px #d3d2d2ab,
    inset -5px -5px 15px #e9e9e9ab;
  -webkit-mask-image: linear-gradient(to top, black calc(100% - 48px), transparent 100%);
  mask-image: linear-gradient(to top, black calc(100% - 48px), transparent 100%);
}

.w3-loader-a{
  position: absolute;
  width: 1.15em;
  height: 13em;
  border-radius: 50px;
  background: transparent;
}

.w3-ball{
  width: 1.15em;
  height: 1.15em;
  border-radius: 50%;
  transition: transform 800ms cubic-bezier(1, -0.4, 0, 1.4);
  background-color: rgba(232, 232, 232, 1);
  box-shadow:
    rgba(0, 0, 0, 0.17) 0px -10px 10px 0px inset,
    rgba(0, 0, 0, 0.15) 0px -15px 15px 0px inset,
    rgba(0, 0, 0, 0.1) 0px -40px 20px 0px inset,
    rgba(0, 0, 0, 0.06) 0px 2px 1px,
    rgba(0, 0, 0, 0.09) 0px 4px 2px,
    rgba(0, 0, 0, 0.09) 0px 8px 4px,
    rgba(0, 0, 0, 0.09) 0px 16px 8px,
    rgba(0, 0, 0, 0.09) 0px 32px 16px,
    0px -1px 15px -8px rgba(0, 0, 0, 0.09);
  animation: w3-loader-move 3.63s ease-in-out infinite;
}

/* Sticks rotation */
.w3-loader-stick:nth-child(2){ transform: rotate(20deg); }
.w3-loader-stick:nth-child(3){ transform: rotate(40deg); }
.w3-loader-stick:nth-child(4){ transform: rotate(60deg); }
.w3-loader-stick:nth-child(5){ transform: rotate(80deg); }
.w3-loader-stick:nth-child(6){ transform: rotate(100deg); }
.w3-loader-stick:nth-child(7){ transform: rotate(120deg); }
.w3-loader-stick:nth-child(8){ transform: rotate(140deg); }
.w3-loader-stick:nth-child(9){ transform: rotate(160deg); }

/* Ball tracks rotation */
.w3-loader-a:nth-child(2){ transform: rotate(20deg); }
.w3-loader-a:nth-child(3){ transform: rotate(40deg); }
.w3-loader-a:nth-child(4){ transform: rotate(60deg); }
.w3-loader-a:nth-child(5){ transform: rotate(80deg); }
.w3-loader-a:nth-child(6){ transform: rotate(100deg); }
.w3-loader-a:nth-child(7){ transform: rotate(120deg); }
.w3-loader-a:nth-child(8){ transform: rotate(140deg); }
.w3-loader-a:nth-child(9){ transform: rotate(160deg); }

/* Animation delays */
.w3-ball-1{ animation-delay: 0.2s; }
.w3-ball-2{ animation-delay: 0.4s; }
.w3-ball-3{ animation-delay: 0.6s; }
.w3-ball-4{ animation-delay: 0.8s; }
.w3-ball-5{ animation-delay: 1s; }
.w3-ball-6{ animation-delay: 1.2s; }
.w3-ball-7{ animation-delay: 1.4s; }
.w3-ball-8{ animation-delay: 1.6s; }

@keyframes w3-loader-move{
  0%{ transform: translateY(0em); }
  50%{ transform: translateY(12em); }
  100%{ transform: translateY(0em); }
}

/* ================== Text ================== */
.w3-loader-text h2{
  font-family: var(--font-head);
  font-size: 1.2rem;
  margin: 0 0 4px;
}

.w3-loader-text p{
  margin: 0;
  font-size: var(--fs-13, 0.85rem);
  color: var(--muted-color, #6b7280);
}

/* ================== Dark mode ================== */
html.theme-dark .w3-loader-overlay{
  background:
    radial-gradient(circle at top, rgba(56,189,248,0.14), transparent 55%),
    linear-gradient(135deg, #020617, #020617);
}

html.theme-dark .w3-loader-inner{
  background:
    radial-gradient(circle at top left,
      color-mix(in oklab, var(--t-primary, rgba(59,130,246,0.20)) 80%, transparent),
      transparent 60%),
    #020617;
  color: #e5e7eb;
  border-color: rgba(148,163,184,0.6);
}

html.theme-dark .w3-loader-text p{
  color: #9ca3af;
}

html.theme-dark .w3-loader-stick{
  background: #1f2937;
}

html.theme-dark .w3-loader-stick::after,
html.theme-dark .w3-loader-stick::before{
  background: #1f2937;
  border-color: #334155;
  box-shadow:
    inset 5px 5px 15px rgba(2, 6, 23, 0.55),
    inset -5px -5px 15px rgba(51, 65, 85, 0.35);
}

html.theme-dark .w3-ball{
  background-color: #cbd5e1;
}
</style>