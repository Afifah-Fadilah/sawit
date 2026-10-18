@php
    $user = auth()->user();
    $userName = $user->name ?? 'Mandor';
    $pageTitle = $title ?? 'Dashboard';
@endphp

<style>
    .kp-topcard {
        margin: 0px 16px 0;
        padding: 18px 20px 22px;
        border-radius: 22px;
        background: linear-gradient(135deg, #a9b388 0%, #5f6f52 100%);
        color: #fefae0;
        box-shadow: 0 8px 18px rgba(95, 111, 82, .28);
    }
    .kp-topcard__row {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .kp-topcard__title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
    }
    .kp-topcard__avatar {
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #fefae0;
        color: #5f6f52;
        font-weight: 700;
        font-size: 15px;
        overflow: hidden;
    }
    .kp-topcard__greet {
        margin: 14px 0 2px;
        font-size: 15px;
        font-weight: 700;
    }
    .kp-topcard__sub {
        margin: 0;
        font-size: 12.5px;
        opacity: .9;
    }

    .kp-topcard__hamburger {
        display: none;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        padding: 0;
        border: 0;
        border-radius: 8px;
        background: rgba(255,255,255,.18);
        color: #fefae0;
        cursor: pointer;
    }
    .kp-topcard__hamburger:hover { background: rgba(255,255,255,.3); }

    @media (max-width: 768px) {
        .kp-topcard__hamburger { display: grid; }
    }
</style>

<div class="kp-topcard">
    <div class="kp-topcard__row">
        <div style="display:flex; align-items:center; gap:10px;">
            <button type="button" id="btnBukaMenuMandor" class="kp-topcard__hamburger" aria-label="Buka menu">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" x2="20" y1="7" y2="7"/>
                    <line x1="4" x2="20" y1="12" y2="12"/>
                    <line x1="4" x2="20" y1="17" y2="17"/>
                </svg>
            </button>
            <h1 class="kp-topcard__title">{{ $pageTitle }}</h1>
        </div>
        <span class="kp-topcard__avatar">{{ strtoupper(mb_substr($userName, 0, 1)) }}</span>
    </div>

    @if ($pageTitle === 'Dashboard')
        <p class="kp-topcard__greet">Selamat Datang {{ strtoupper($userName) }}!</p>
        <p class="kp-topcard__sub">Yuk, catat progres lapangan hari ini.</p>
    @endif
</div>