<?php
/** @var array $biens  */
/** @var array $stats  */
require __DIR__ . '/../layout.php';
?>

<!-- ── HERO ──────────────────────────────────────────────────────────────────── -->
<div class="relative -mx-6 overflow-hidden mb-14" style="height:440px">
    <img src="https://images.unsplash.com/photo-1560185893-a55cbc8c57e8?w=1800&h=600&fit=crop&auto=format"
         alt="Nina Immo" class="absolute inset-0 w-full h-full object-cover" id="hero-bg">
    <!-- Gradient overlay -->
    <div class="absolute inset-0" style="background:linear-gradient(120deg,rgba(26,26,46,.92) 0%,rgba(26,26,46,.72) 55%,rgba(201,169,110,.18) 100%)"></div>

    <!-- Content -->
    <div class="absolute inset-0 flex flex-col justify-center px-10 md:px-16">
        <p class="text-[#c9a96e] text-[11px] font-bold uppercase tracking-[5px] mb-4">Agence premium · Depuis 2005</p>
        <h1 class="font-playfair text-4xl md:text-5xl lg:text-6xl font-bold leading-[1.1] mb-3">
            <span class="gradient-text">Votre bien idéal</span><br>
            <span class="text-white/90">vous attend.</span>
        </h1>
        <p class="text-white/50 text-sm mb-8 max-w-sm">
            Appartements, maisons et locaux d'exception dans les plus belles villes de France.
        </p>

        <!-- Hero search bar -->
        <form method="GET" action=""
              class="flex w-full max-w-lg bg-white rounded-2xl overflow-hidden"
              style="box-shadow:0 20px 60px rgba(0,0,0,.35)">
            <input type="hidden" name="page" value="biens">
            <div class="flex flex-1 items-center px-5 gap-3">
                <svg class="w-4 h-4 text-[#c9a96e] shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="q"
                       value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                       placeholder="Ville, référence, type de bien..."
                       class="flex-1 py-4 text-sm text-[#1a1a2e] outline-none placeholder-[#1a1a2e]/35">
            </div>
            <button type="submit"
                    class="bg-[#c9a96e] hover:bg-[#b8935a] text-white px-7 font-bold text-sm transition-colors shrink-0">
                Rechercher
            </button>
        </form>

        <!-- Quick stats in hero -->
        <div class="flex gap-8 mt-10">
            <?php
            $heroStats = [
                ['val' => $stats['total'],      'label' => 'biens'],
                ['val' => $stats['disponible'], 'label' => 'disponibles'],
                ['val' => $stats['vendu'],      'label' => 'vendus'],
            ];
            ?>
            <?php foreach ($heroStats as $i => $s): ?>
            <div>
                <span class="text-3xl font-bold text-white counter" data-target="<?= $s['val'] ?>">0</span>
                <span class="text-white/40 text-xs uppercase tracking-widest ml-1"><?= $s['label'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ── HEADER + ACTIONS ───────────────────────────────────────────────────────── -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 fade-up">
    <div>
        <p class="text-[#c9a96e] font-bold text-[11px] uppercase tracking-[4px] mb-1">Catalogue</p>
        <h2 class="font-playfair text-3xl font-bold text-[#1a1a2e]">
            Nos biens
            <?php if (!empty($_GET['q'])): ?>
            <span class="text-base font-normal text-[#1a1a2e]/40 ml-2">
                — <?= count($biens) ?> résultat(s) pour "<?= htmlspecialchars($_GET['q']) ?>"
            </span>
            <?php endif; ?>
        </h2>
    </div>
    <a href="?page=biens&action=create"
       class="inline-flex items-center gap-2 bg-[#1a1a2e] text-white text-sm font-semibold px-5 py-2.5 rounded-full hover:bg-[#2d2d50] transition-all hover:shadow-lg hover:shadow-[#1a1a2e]/20 self-start">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Nouveau bien
    </a>
</div>

<!-- ── STATS + FILTRES ────────────────────────────────────────────────────────── -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-8 fade-up delay-1">

    <!-- Filtres type -->
    <div class="flex items-center gap-2 flex-wrap">
        <?php
        $tabs = [
            ['id'=>'tous',         'label'=>'Tous',       'count'=> count($biens)],
            ['id'=>'appartement',  'label'=>'Appartements','count'=> count(array_filter($biens, fn($b)=>$b['type']==='appartement'))],
            ['id'=>'maison',       'label'=>'Maisons',    'count'=> count(array_filter($biens, fn($b)=>$b['type']==='maison'))],
            ['id'=>'local',        'label'=>'Locaux',     'count'=> count(array_filter($biens, fn($b)=>$b['type']==='local'))],
        ];
        foreach ($tabs as $tab):
            if ($tab['count'] === 0 && $tab['id'] !== 'tous') continue;
        ?>
        <button onclick="filterType('<?= $tab['id'] ?>')" id="tab-<?= $tab['id'] ?>"
                class="filter-tab <?= $tab['id']==='tous' ? 'active' : '' ?> flex items-center gap-1.5 text-xs font-semibold px-4 py-2 rounded-full border border-[#e8e4df] text-[#1a1a2e]/60 hover:text-[#1a1a2e]">
            <?= $tab['label'] ?>
            <span class="w-4 h-4 rounded-full bg-current/10 text-[10px] flex items-center justify-center leading-none font-bold">
                <?= $tab['count'] ?>
            </span>
        </button>
        <?php endforeach; ?>
    </div>

    <!-- Toggle grille / carte -->
    <div class="flex items-center gap-1 bg-white border border-[#e8e4df] rounded-xl p-1">
        <button onclick="setView('grid')" id="btn-grid"
                class="view-btn active flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                <rect x="14" y="14" width="7" height="7" rx="1.5"/>
            </svg>
            Grille
        </button>
        <button onclick="setView('map')" id="btn-map"
                class="view-btn flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg text-[#1a1a2e]/50 hover:text-[#1a1a2e]">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                <circle cx="12" cy="9" r="2.5"/>
            </svg>
            Carte
        </button>
    </div>
</div>

<!-- ── VUE CARTE ──────────────────────────────────────────────────────────────── -->
<div id="view-map" class="hidden mb-12 fade-up">
    <div class="bg-white rounded-3xl border border-[#e8e4df] p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-[#1a1a2e]">Localisation des biens</h3>
                <p id="map-status" class="text-xs text-[#1a1a2e]/40 mt-0.5">Chargement de la carte…</p>
            </div>
            <div class="flex items-center gap-4 text-xs text-[#1a1a2e]/50">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#1a1a2e] inline-block ring-2 ring-white shadow"></span>Appartement</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#c9a96e] inline-block ring-2 ring-white shadow"></span>Maison</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#64748b] inline-block ring-2 ring-white shadow"></span>Local</span>
            </div>
        </div>
        <div id="map-container"></div>
    </div>
</div>

<!-- ── VUE GRILLE ─────────────────────────────────────────────────────────────── -->
<div id="view-grid">
<?php
$labels  = ['appartement' => 'Appartements', 'maison' => 'Maisons', 'local' => 'Locaux commerciaux'];
$groupes = ['appartement' => [], 'maison' => [], 'local' => []];
foreach ($biens as $bien) {
    $groupes[$bien['type']][] = $bien;
}

$allPhotos = [
    'appartement' => ['photo-1522708323590-d24dbb6b0267','photo-1502672260266-1c1ef2d93688','photo-1560448204-e02f11c3d0e2','photo-1493809842364-78817add7ffb','photo-1574362848149-11496d93a7c7'],
    'maison'      => ['photo-1564013799919-ab600027ffc6','photo-1570129477492-45c003edd2be','photo-1512917774080-9991f1c4c750','photo-1600585154340-be6161a56a0c','photo-1449844908441-8829872d2607'],
    'local'       => ['photo-1497366216548-37526070297c','photo-1441986300917-64674bd600d8','photo-1560179707-f14e90ef3623','photo-1604719312566-8912e9227c6a','photo-1604014237800-1c9102c219da'],
];
$delay = 0;
?>

<?php foreach ($groupes as $type => $liste): ?>
<?php if (empty($liste)) continue; ?>

<div class="section-type mb-14 fade-up" data-type="<?= $type ?>">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-1 h-6 rounded-full bg-[#c9a96e]"></div>
        <h3 class="text-lg font-bold text-[#1a1a2e]"><?= $labels[$type] ?></h3>
        <span class="text-xs bg-[#c9a96e]/15 text-[#1a1a2e] font-bold px-2.5 py-0.5 rounded-full">
            <?= count($liste) ?>
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($liste as $bien):
            $d = json_decode($bien['details'] ?? '{}', true) ?? [];

            $statutStyle = match($bien['statut']) {
                'vendu'  => 'badge-vendu',
                'loue'   => 'badge-loue',
                default  => 'badge-dispo',
            };
            $statutLabel = match($bien['statut']) {
                'vendu' => 'Vendu', 'loue' => 'Loué', default => 'Disponible',
            };

            $pool     = $allPhotos[$bien['type']] ?? $allPhotos['appartement'];
            $photoId  = $pool[$bien['id'] % count($pool)];
            $photoUrl = 'https://images.unsplash.com/' . $photoId . '?w=700&h=500&fit=crop&auto=format&q=80';

            $nbAnn = (int)($bien['nb_annonces'] ?? 0);

            // Feature chips
            $chips = [];
            if ($bien['type'] === 'appartement') {
                if (!empty($d['typeAppartement'])) $chips[] = $d['typeAppartement'];
                if (isset($d['etage']))            $chips[] = 'Étage ' . $d['etage'];
                if (!empty($d['ascenseur']))       $chips[] = 'Ascenseur';
            } elseif ($bien['type'] === 'maison') {
                if (isset($d['nbChambres']))         $chips[] = $d['nbChambres'] . ' ch.';
                if (!empty($d['jardin']))            $chips[] = 'Jardin ' . ($d['surfaceJardin'] ?? 0) . 'm²';
                if (!empty($d['garage']))            $chips[] = $d['garage'];
            } elseif ($bien['type'] === 'local') {
                if (!empty($d['activite']))          $chips[] = $d['activite'];
            }
        ?>
        <div class="property-card bg-white rounded-2xl overflow-hidden border border-[#e8e4df] flex flex-col shadow-sm fade-up delay-<?= min(($delay++ % 3) + 1, 4) ?>">

            <!-- Photo -->
            <div class="relative overflow-hidden" style="height:240px">
                <img src="<?= $photoUrl ?>" alt="<?= htmlspecialchars($bien['ville']) ?>"
                     class="card-img absolute inset-0 w-full h-full object-cover">

                <!-- Overlay permanent dégradé -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent pointer-events-none"></div>

                <!-- Badges haut -->
                <div class="absolute top-3 left-3 flex gap-2">
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full <?= $statutStyle ?>">
                        <?= $statutLabel ?>
                    </span>
                    <?php if ($nbAnn > 0): ?>
                    <a href="?page=annonces&q=<?= urlencode($bien['reference']) ?>"
                       class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-[#c9a96e] text-white hover:bg-[#b8935a] transition-colors">
                        <?= $nbAnn ?> annonce<?= $nbAnn > 1 ? 's' : '' ?>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Référence -->
                <span class="absolute top-3 right-3 font-mono text-[10px] text-white/70 bg-black/30 backdrop-blur-sm px-2 py-0.5 rounded-lg">
                    <?= htmlspecialchars($bien['reference']) ?>
                </span>

                <!-- Prix permanent en bas de photo -->
                <div class="absolute bottom-0 left-0 right-0 p-4 transition-opacity duration-300">
                    <p class="text-xl font-bold text-white leading-none"><?= number_format($bien['prix'], 0, ',', ' ') ?> €</p>
                    <p class="text-white/55 text-xs mt-0.5"><?= number_format($bien['surface'], 0) ?> m² &middot; <?= number_format($bien['prix'] / max($bien['surface'],1), 0, ',', ' ') ?> €/m²</p>
                </div>

                <!-- Slide-up overlay au hover -->
                <div class="card-overlay absolute bottom-0 left-0 right-0 bg-[#1a1a2e]/95 backdrop-blur-sm p-4">
                    <p class="text-[#c9a96e] text-[10px] font-bold uppercase tracking-widest mb-1">Actions rapides</p>
                    <div class="flex gap-2">
                        <a href="?page=biens&action=edit&id=<?= $bien['id'] ?>"
                           class="flex-1 text-center text-xs font-semibold text-white bg-white/10 hover:bg-white/20 px-3 py-2 rounded-xl transition-colors">
                            ✏️ Modifier
                        </a>
                        <a href="?page=annonces&action=create&bien_id=<?= $bien['id'] ?>"
                           class="flex-1 text-center text-xs font-semibold text-[#c9a96e] border border-[#c9a96e]/40 hover:bg-[#c9a96e]/10 px-3 py-2 rounded-xl transition-colors">
                            📢 Annonce
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-5 flex flex-col flex-1">
                <h4 class="font-playfair text-xl font-bold text-[#1a1a2e] mb-2"><?= htmlspecialchars($bien['ville']) ?></h4>

                <!-- Feature chips -->
                <?php if (!empty($chips)): ?>
                <div class="flex flex-wrap gap-1.5 mb-4">
                    <?php foreach ($chips as $chip): ?>
                    <span class="chip"><?= htmlspecialchars($chip) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Actions bas -->
                <div class="flex gap-2 mt-auto pt-4 border-t border-[#e8e4df]">
                    <a href="?page=biens&action=edit&id=<?= $bien['id'] ?>"
                       class="flex-1 text-center text-sm font-semibold text-[#1a1a2e] bg-[#f8f6f3] border border-[#e8e4df] px-3 py-2.5 rounded-xl hover:bg-[#ede9e3] transition-colors">
                        Modifier
                    </a>
                    <form method="POST" action="?page=biens&action=delete&id=<?= $bien['id'] ?>"
                          onsubmit="return confirm('Supprimer ce bien définitivement ?')">
                        <button type="submit"
                                class="text-sm font-semibold text-red-500 bg-red-50 border border-red-100 px-3 py-2.5 rounded-xl hover:bg-red-100 transition-colors">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<?php if (empty($biens)): ?>
<div class="text-center py-24 fade-up">
    <div class="w-20 h-20 bg-[#1a1a2e]/5 rounded-3xl flex items-center justify-center mx-auto mb-5">
        <svg class="w-10 h-10 text-[#1a1a2e]/20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
        </svg>
    </div>
    <h3 class="font-playfair text-2xl font-bold text-[#1a1a2e]/30 mb-2">Aucun bien trouvé</h3>
    <p class="text-sm text-[#1a1a2e]/25">Essayez un autre terme de recherche ou créez un nouveau bien.</p>
    <a href="?page=biens&action=create"
       class="inline-flex items-center gap-2 mt-6 bg-[#1a1a2e] text-white text-sm font-semibold px-6 py-3 rounded-full hover:bg-[#2d2d50] transition-all">
        Créer un bien
    </a>
</div>
<?php endif; ?>
</div><!-- /view-grid -->

<!-- ── SCRIPTS ────────────────────────────────────────────────────────────────── -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── Filtres type ──────────────────────────────────────────────────────────
function filterType(type) {
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + type).classList.add('active');
    document.querySelectorAll('.section-type').forEach(s => {
        s.style.display = (type === 'tous' || s.dataset.type === type) ? '' : 'none';
    });
}

// ── Toggle grille / carte ─────────────────────────────────────────────────
let mapReady = false;
function setView(v) {
    const isMap = v === 'map';
    document.getElementById('view-grid').classList.toggle('hidden', isMap);
    document.getElementById('view-map').classList.toggle('hidden', !isMap);
    document.getElementById('btn-grid').classList.toggle('active', !isMap);
    document.getElementById('btn-map').classList.toggle('active',  isMap);
    if (isMap && !mapReady) { mapReady = true; initMap(); }
}

// ── Animated counters ─────────────────────────────────────────────────────
function animCounter(el) {
    const target = parseInt(el.dataset.target) || 0;
    if (!target) { el.textContent = 0; return; }
    const dur = 1400;
    const start = performance.now();
    const tick = now => {
        const p = Math.min((now - start) / dur, 1);
        el.textContent = Math.round((1 - Math.pow(1 - p, 3)) * target);
        if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

// ── Intersection Observer (scroll animations + counters) ──────────────────
const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (!e.isIntersecting) return;
        e.target.classList.add('visible');
        if (e.target.classList.contains('counter')) animCounter(e.target);
        obs.unobserve(e.target);
    });
}, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.fade-up, .counter').forEach(el => obs.observe(el));

// ── Leaflet map ───────────────────────────────────────────────────────────
const biensGeo = <?= json_encode(array_map(fn($b) => [
    'id'        => $b['id'],
    'ville'     => $b['ville'],
    'type'      => $b['type'],
    'reference' => $b['reference'],
    'prix'      => $b['prix'],
    'surface'   => $b['surface'],
    'statut'    => $b['statut'],
], $biens)) ?>;

function initMap() {
    const map = L.map('map-container').setView([46.6, 2.3], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 18
    }).addTo(map);

    const colors  = { appartement:'#1a1a2e', maison:'#c9a96e', local:'#64748b' };
    const typeLabels = { appartement:'Appartement', maison:'Maison', local:'Local' };
    const statutBadge = { disponible:'🟢 Disponible', loue:'🟡 Loué', vendu:'🔴 Vendu' };

    const byCity = {};
    biensGeo.forEach(b => { if (!byCity[b.ville]) byCity[b.ville] = []; byCity[b.ville].push(b); });

    const cities   = Object.keys(byCity);
    const markers  = [];
    const statusEl = document.getElementById('map-status');
    let done = 0;

    async function processCity(i) {
        if (i >= cities.length) {
            statusEl.textContent = markers.length
                ? `${markers.length} bien(s) localisé(s)`
                : 'Aucune localisation disponible.';
            if (markers.length) map.fitBounds(L.featureGroup(markers).getBounds().pad(0.25));
            return;
        }
        const ville = cities[i];
        const biens = byCity[ville];
        const coords = await geocode(ville);
        if (coords) {
            const c = colors[biens[0].type] ?? '#333';
            const icon = L.divIcon({
                html: `<div style="width:22px;height:22px;border-radius:50%;background:${c};border:3px solid white;box-shadow:0 3px 12px rgba(0,0,0,.35)"></div>`,
                iconSize: [22,22], iconAnchor: [11,11], className:''
            });
            const popup = biens.map(b => `
                <div style="min-width:190px;font-family:'DM Sans',sans-serif;padding:4px 0">
                    <div style="font-weight:700;color:#1a1a2e;font-size:14px">${b.ville}</div>
                    <div style="font-size:11px;color:#888;margin-bottom:6px">${typeLabels[b.type]??b.type} · <span style="font-family:monospace">${b.reference}</span></div>
                    <div style="font-size:18px;font-weight:800;color:#c9a96e;line-height:1">${Number(b.prix).toLocaleString('fr-FR')} €</div>
                    <div style="font-size:11px;color:#999;margin-top:2px">${b.surface} m² · ${statutBadge[b.statut]??b.statut}</div>
                    <a href="?page=biens&action=edit&id=${b.id}"
                       style="display:inline-block;margin-top:8px;font-size:12px;font-weight:600;color:#1a1a2e;padding:4px 12px;background:#f8f6f3;border-radius:8px;text-decoration:none">
                        Modifier →
                    </a>
                </div>`).join('<hr style="margin:10px 0;border:none;border-top:1px solid #eee">');

            const m = L.marker(coords, {icon}).addTo(map).bindPopup(popup, {maxWidth:260});
            markers.push(m);
        }
        done++;
        statusEl.textContent = done < cities.length ? `Géolocalisation… ${done}/${cities.length}` : '';
        setTimeout(() => processCity(i+1), 1100);
    }
    processCity(0);
}

async function geocode(ville) {
    const key    = 'nina_v2_' + ville.toLowerCase().trim();
    const cached = localStorage.getItem(key);
    if (cached) { try { return JSON.parse(cached); } catch(e){} }
    try {
        const r = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(ville+', France')}&format=json&limit=1`,
            {headers:{'Accept-Language':'fr'}});
        const d = await r.json();
        if (d.length) {
            const c = [parseFloat(d[0].lat), parseFloat(d[0].lon)];
            localStorage.setItem(key, JSON.stringify(c));
            return c;
        }
    } catch(e) {}
    return null;
}
</script>

<?php require __DIR__ . '/../layout_footer.php'; ?>
