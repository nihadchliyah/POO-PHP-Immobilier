<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nina Immo — Agence Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        :root {
            --navy: #1a1a2e;
            --gold: #c9a96e;
            --cream: #f8f6f3;
            --border: #e8e4df;
        }
        * { -webkit-font-smoothing: antialiased; }
        body { font-family: 'DM Sans', sans-serif; background-color: var(--cream); }
        .font-playfair { font-family: 'Playfair Display', serif; }

        /* NAV */
        .site-nav {
            transition: background 0.4s ease, box-shadow 0.4s ease, border-color 0.4s ease;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .site-nav.scrolled {
            background: rgba(248,246,243,0.98) !important;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        }

        /* CARDS */
        .property-card {
            transition: transform 0.35s cubic-bezier(.22,.68,0,1.2), box-shadow 0.35s ease, border-color 0.35s ease;
        }
        .property-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 32px 64px rgba(26,26,46,0.14);
            border-color: rgba(201,169,110,0.35) !important;
        }
        .card-img { transition: transform 0.6s cubic-bezier(.22,.68,0,1.2); }
        .property-card:hover .card-img { transform: scale(1.07); }

        /* SLIDE-UP OVERLAY */
        .card-overlay {
            transform: translateY(100%);
            transition: transform 0.35s cubic-bezier(.22,.68,0,1.2);
        }
        .property-card:hover .card-overlay { transform: translateY(0); }

        /* SCROLL ANIMATIONS */
        .fade-up {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.65s ease, transform 0.65s ease;
        }
        .fade-up.visible { opacity: 1; transform: translateY(0); }
        .fade-up.delay-1 { transition-delay: 0.1s; }
        .fade-up.delay-2 { transition-delay: 0.2s; }
        .fade-up.delay-3 { transition-delay: 0.3s; }
        .fade-up.delay-4 { transition-delay: 0.4s; }

        /* FILTER TABS */
        .filter-tab { transition: all .25s ease; }
        .filter-tab.active { background: var(--navy); color: #fff; border-color: var(--navy); }
        .view-btn { transition: all .25s ease; }
        .view-btn.active { background: var(--navy); color: #fff; }

        /* INPUTS */
        input:focus, select:focus, textarea:focus {
            outline: none;
            ring: 2px solid var(--gold);
            border-color: var(--gold) !important;
            box-shadow: 0 0 0 3px rgba(201,169,110,0.15);
        }

        /* CHIPS */
        .chip { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 999px; background: var(--cream); color: rgba(26,26,46,0.55); }

        /* MAP */
        #map-container { height: 540px; border-radius: 1rem; overflow: hidden; }
        .leaflet-popup-content-wrapper { border-radius: 14px; box-shadow: 0 12px 40px rgba(0,0,0,0.15); font-family: 'DM Sans', sans-serif; }
        .leaflet-popup-content { margin: 12px 16px; }

        /* GRADIENT TEXT */
        .gradient-text { background: linear-gradient(135deg, #fff 0%, #c9a96e 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

        /* SCROLLBAR */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--cream); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--gold); }

        /* ANNONCE BADGE */
        .badge-vente    { background:#1a1a2e; color:#fff; }
        .badge-location { background:#0369a1; color:#fff; }
        .badge-active   { background:#d1fae5; color:#065f46; }
        .badge-archivee { background:rgba(26,26,46,0.08); color:rgba(26,26,46,0.4); }
        .badge-dispo    { background:#d1fae5; color:#065f46; }
        .badge-loue     { background:#fef3c7; color:#92400e; }
        .badge-vendu    { background:#fee2e2; color:#991b1b; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

<?php $currentPage = $_GET['page'] ?? 'biens'; ?>

<!-- ── Navigation ───────────────────────────────────────────────────────────── -->
<nav class="site-nav fixed top-0 left-0 right-0 z-50 bg-[#f8f6f3]/85 border-b border-[#e8e4df]">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center gap-6">

        <!-- Logo -->
        <a href="?page=biens" class="flex items-center gap-2.5 shrink-0">
            <span class="w-8 h-8 rounded-xl bg-[#1a1a2e] flex items-center justify-center">
                <svg class="w-4 h-4 text-[#c9a96e]" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
            </span>
            <span class="font-playfair text-[#1a1a2e] font-bold text-xl tracking-tight">Nina Immo</span>
        </a>

        <!-- Search -->
        <?php
            $searchPlaceholder = match($currentPage) {
                'proprietaires' => 'Nom, email...',
                'annonces'      => 'Titre, ville, contact...',
                default         => 'Ville, référence, type...',
            };
        ?>
        <form method="GET" action="" id="search-form" class="flex-1 max-w-sm hidden md:block">
            <input type="hidden" name="page" value="<?= htmlspecialchars($currentPage) ?>">
            <div class="flex items-center bg-white border border-[#e8e4df] rounded-xl px-4 py-2.5 gap-2 shadow-sm focus-within:border-[#c9a96e] focus-within:shadow-[0_0_0_3px_rgba(201,169,110,0.15)] transition-all">
                <svg class="w-3.5 h-3.5 text-[#c9a96e] shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="q" id="search-input"
                       value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                       placeholder="<?= $searchPlaceholder ?>"
                       class="flex-1 text-sm text-[#1a1a2e] bg-transparent outline-none placeholder-[#1a1a2e]/35">
                <?php if (!empty($_GET['q'])): ?>
                <a href="?page=<?= htmlspecialchars($currentPage) ?>" class="text-[#1a1a2e]/30 hover:text-[#1a1a2e] text-xs leading-none">✕</a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Nav links -->
        <div class="flex items-center gap-1 ml-auto text-sm font-medium shrink-0">
            <a href="?page=biens"
               class="px-4 py-2 rounded-xl transition-all <?= $currentPage === 'biens' ? 'bg-[#1a1a2e] text-white' : 'text-[#1a1a2e]/60 hover:text-[#1a1a2e] hover:bg-[#1a1a2e]/5' ?>">
                Catalogue
            </a>
            <a href="?page=annonces"
               class="px-4 py-2 rounded-xl transition-all <?= $currentPage === 'annonces' ? 'bg-[#1a1a2e] text-white' : 'text-[#1a1a2e]/60 hover:text-[#1a1a2e] hover:bg-[#1a1a2e]/5' ?>">
                Annonces
            </a>
            <a href="?page=proprietaires"
               class="px-4 py-2 rounded-xl transition-all <?= $currentPage === 'proprietaires' ? 'bg-[#1a1a2e] text-white' : 'text-[#1a1a2e]/60 hover:text-[#1a1a2e] hover:bg-[#1a1a2e]/5' ?>">
                Propriétaires
            </a>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-6 pt-24 pb-16 flex-1 w-full">

<script>
// Nav scroll effect
window.addEventListener('scroll', () => {
    document.querySelector('.site-nav').classList.toggle('scrolled', window.scrollY > 20);
}, { passive: true });

// Nav search debounce
const searchInput = document.getElementById('search-input');
if (searchInput) {
    let timer;
    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => document.getElementById('search-form').submit(), 480);
    });
}
</script>
