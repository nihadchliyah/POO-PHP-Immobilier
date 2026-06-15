<?php
/** @var array $annonces */
/** @var array $stats    */
require __DIR__ . '/../layout.php';

$allPhotos = [
    'appartement' => ['photo-1522708323590-d24dbb6b0267','photo-1502672260266-1c1ef2d93688','photo-1560448204-e02f11c3d0e2','photo-1493809842364-78817add7ffb','photo-1574362848149-11496d93a7c7'],
    'maison'      => ['photo-1564013799919-ab600027ffc6','photo-1570129477492-45c003edd2be','photo-1512917774080-9991f1c4c750','photo-1600585154340-be6161a56a0c','photo-1449844908441-8829872d2607'],
    'local'       => ['photo-1497366216548-37526070297c','photo-1441986300917-64674bd600d8','photo-1560179707-f14e90ef3623','photo-1604719312566-8912e9227c6a','photo-1604014237800-1c9102c219da'],
];
?>

<!-- ── HERO ANNONCES ──────────────────────────────────────────────────────────── -->
<div class="relative -mx-6 overflow-hidden mb-12" style="height:300px">
    <img src="https://images.unsplash.com/photo-1486325212027-8081e485255e?w=1800&h=500&fit=crop&auto=format&q=80"
         alt="Annonces" class="absolute inset-0 w-full h-full object-cover">
    <div class="absolute inset-0" style="background:linear-gradient(135deg,rgba(26,26,46,.88) 0%,rgba(26,26,46,.60) 60%,rgba(201,169,110,.15) 100%)"></div>
    <div class="absolute inset-0 flex flex-col justify-center px-10 md:px-16">
        <p class="text-[#c9a96e] text-[11px] font-bold uppercase tracking-[5px] mb-3">Portefeuille · Nina Immo</p>
        <h1 class="font-playfair text-4xl md:text-5xl font-bold text-white leading-tight mb-5">
            Annonces en ligne
        </h1>
        <!-- Recherche hero -->
        <form method="GET" action="" class="flex w-full max-w-md bg-white/10 backdrop-blur border border-white/20 rounded-2xl overflow-hidden">
            <input type="hidden" name="page" value="annonces">
            <input type="text" name="q" placeholder="Titre, ville, contact..."
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                   class="flex-1 px-5 py-3.5 text-sm text-white bg-transparent outline-none placeholder-white/40">
            <button type="submit" class="bg-[#c9a96e] hover:bg-[#b8935a] text-white px-6 font-bold text-sm transition-colors shrink-0">
                Filtrer
            </button>
        </form>
    </div>
    <!-- Stats dans le hero -->
    <div class="absolute bottom-5 left-10 md:left-16 flex gap-8">
        <div>
            <span class="text-2xl font-bold text-white counter" data-target="<?= $stats['total'] ?>">0</span>
            <span class="text-white/40 text-xs uppercase tracking-wide ml-1">annonces</span>
        </div>
        <div>
            <span class="text-2xl font-bold text-white counter" data-target="<?= $stats['actives'] ?>">0</span>
            <span class="text-white/40 text-xs uppercase tracking-wide ml-1">actives</span>
        </div>
        <div>
            <span class="text-2xl font-bold text-white counter" data-target="<?= $stats['ventes'] ?>">0</span>
            <span class="text-white/40 text-xs uppercase tracking-wide ml-1">ventes</span>
        </div>
    </div>
</div>

<!-- ── HEADER + FILTRES ───────────────────────────────────────────────────────── -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 fade-up">
    <div>
        <h2 class="font-playfair text-3xl font-bold text-[#1a1a2e]">
            Toutes les annonces
            <?php if (!empty($_GET['q'])): ?>
            <span class="text-base font-normal text-[#1a1a2e]/40 ml-2">— <?= count($annonces) ?> résultat(s)</span>
            <?php endif; ?>
        </h2>
    </div>
    <a href="?page=annonces&action=create"
       class="inline-flex items-center gap-2 bg-[#1a1a2e] text-white text-sm font-semibold px-5 py-2.5 rounded-full hover:bg-[#2d2d50] transition-all hover:shadow-lg hover:shadow-[#1a1a2e]/20 self-start">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Nouvelle annonce
    </a>
</div>

<!-- Filtres pills -->
<div class="flex flex-wrap gap-2 mb-8 fade-up delay-1">
    <?php
    $txActif = $_GET['transaction'] ?? '';
    $stActif = $_GET['statut'] ?? '';
    $base    = '?page=annonces' . (!empty($_GET['q']) ? '&q='.urlencode($_GET['q']) : '');
    $filters = [
        ['label'=>'Toutes',    'tx'=>'', 'st'=>'', 'count'=>$stats['total']],
        ['label'=>'Vente',     'tx'=>'vente','st'=>'','count'=>$stats['ventes']],
        ['label'=>'Location',  'tx'=>'location','st'=>'','count'=>$stats['locations']],
        ['label'=>'Actives',   'tx'=>'', 'st'=>'active','count'=>$stats['actives']],
        ['label'=>'Archivées', 'tx'=>'', 'st'=>'archivee','count'=>$stats['archivees']],
    ];
    foreach ($filters as $f):
        $active = ($txActif === $f['tx'] && $stActif === $f['st']);
        $url    = $base . ($f['tx'] ? '&transaction='.$f['tx'] : '') . ($f['st'] ? '&statut='.$f['st'] : '');
    ?>
    <a href="<?= $url ?>"
       class="inline-flex items-center gap-1.5 text-xs font-bold px-4 py-2 rounded-full border transition-all
              <?= $active ? 'bg-[#1a1a2e] text-white border-[#1a1a2e] shadow-sm' : 'border-[#e8e4df] text-[#1a1a2e]/55 hover:text-[#1a1a2e] hover:bg-[#f0ede8]' ?>">
        <?= $f['label'] ?>
        <span class="<?= $active ? 'bg-white/20' : 'bg-[#1a1a2e]/8' ?> text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">
            <?= $f['count'] ?>
        </span>
    </a>
    <?php endforeach; ?>
</div>

<!-- ── GRILLE ANNONCES ────────────────────────────────────────────────────────── -->
<?php if (empty($annonces)): ?>
<div class="text-center py-24 fade-up">
    <div class="w-20 h-20 bg-[#1a1a2e]/5 rounded-3xl flex items-center justify-center mx-auto mb-5">
        <svg class="w-10 h-10 text-[#1a1a2e]/20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
    </div>
    <h3 class="font-playfair text-2xl font-bold text-[#1a1a2e]/30 mb-2">Aucune annonce</h3>
    <p class="text-sm text-[#1a1a2e]/25">Créez votre première annonce depuis un bien du catalogue.</p>
    <a href="?page=annonces&action=create"
       class="inline-flex items-center gap-2 mt-6 bg-[#1a1a2e] text-white text-sm font-semibold px-6 py-3 rounded-full hover:bg-[#2d2d50] transition-all">
        Créer une annonce
    </a>
</div>

<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
<?php
$delay = 0;
foreach ($annonces as $a):
    $pool     = $allPhotos[$a['bien_type']] ?? $allPhotos['appartement'];
    $photoId  = $pool[$a['bien_id'] % count($pool)];
    $photoUrl = 'https://images.unsplash.com/' . $photoId . '?w=700&h=500&fit=crop&auto=format&q=80';

    $txLabel  = $a['type_transaction'] === 'vente' ? 'Vente' : 'Location';
    $isVente  = $a['type_transaction'] === 'vente';
    $isActive = $a['statut'] === 'active';

    $prix = (float)($a['prix_demande'] ?? $a['prix_bien']);
    $d    = json_decode($a['details'] ?? '{}', true) ?? [];
?>
<div class="property-card bg-white rounded-2xl overflow-hidden border border-[#e8e4df] flex flex-col shadow-sm fade-up delay-<?= min(($delay++ % 3)+1, 4) ?>">

    <!-- Photo -->
    <div class="relative overflow-hidden" style="height:220px">
        <img src="<?= $photoUrl ?>" alt="<?= htmlspecialchars($a['ville']) ?>"
             class="card-img absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent pointer-events-none"></div>

        <!-- Badges haut -->
        <div class="absolute top-3 left-3 flex gap-2">
            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full <?= $isVente ? 'badge-vente' : 'badge-location' ?>">
                <?= $txLabel ?>
            </span>
            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full <?= $isActive ? 'badge-active' : 'badge-archivee' ?>">
                <?= $isActive ? 'Active' : 'Archivée' ?>
            </span>
        </div>

        <!-- Ref -->
        <span class="absolute top-3 right-3 font-mono text-[10px] text-white/65 bg-black/30 backdrop-blur-sm px-2 py-0.5 rounded-lg">
            <?= htmlspecialchars($a['reference']) ?>
        </span>

        <!-- Prix bottom -->
        <div class="absolute bottom-0 left-0 right-0 p-4">
            <p class="text-xl font-bold text-white leading-none"><?= number_format($prix, 0, ',', ' ') ?> €<?= !$isVente ? '<span class="text-white/50 text-sm font-normal">/mois</span>' : '' ?></p>
            <p class="text-white/50 text-xs mt-0.5"><?= number_format($a['surface'], 0) ?> m² · <?= ucfirst($a['bien_type']) ?></p>
        </div>

        <!-- Slide-up overlay -->
        <div class="card-overlay absolute bottom-0 left-0 right-0 bg-[#1a1a2e]/95 backdrop-blur-sm p-4">
            <p class="text-[#c9a96e] text-[10px] font-bold uppercase tracking-widest mb-1">Contact</p>
            <p class="text-white text-sm font-semibold"><?= htmlspecialchars($a['contact_nom']) ?></p>
            <p class="text-white/50 text-xs"><?= htmlspecialchars($a['contact_email']) ?></p>
            <?php if (!empty($a['contact_telephone'])): ?>
            <p class="text-white/50 text-xs"><?= htmlspecialchars($a['contact_telephone']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Content -->
    <div class="p-5 flex flex-col flex-1">
        <!-- Titre + ville -->
        <h4 class="font-playfair text-lg font-bold text-[#1a1a2e] mb-1 leading-snug line-clamp-2">
            <?= htmlspecialchars($a['titre']) ?>
        </h4>
        <p class="text-sm text-[#1a1a2e]/45 mb-3">
            <?= htmlspecialchars($a['ville']) ?> &middot; <?= number_format($a['surface'], 0) ?> m²
        </p>

        <!-- Description -->
        <?php if (!empty($a['description'])): ?>
        <p class="text-sm text-[#1a1a2e]/55 leading-relaxed mb-4 line-clamp-2">
            <?= htmlspecialchars($a['description']) ?>
        </p>
        <?php endif; ?>

        <!-- Contact info -->
        <div class="bg-[#f8f6f3] rounded-xl px-4 py-3 mb-4 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-[#1a1a2e] flex items-center justify-center shrink-0 text-[#c9a96e] text-xs font-bold">
                <?= mb_strtoupper(mb_substr($a['contact_nom'], 0, 1)) ?>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-[#1a1a2e] truncate"><?= htmlspecialchars($a['contact_nom']) ?></p>
                <p class="text-xs text-[#1a1a2e]/40 truncate"><?= htmlspecialchars($a['contact_email']) ?></p>
            </div>
        </div>

        <!-- Date + actions -->
        <div class="mt-auto pt-4 border-t border-[#e8e4df]">
            <!-- Bouton Détails (CTA principal) -->
            <a href="?page=annonces&action=show&id=<?= $a['id'] ?>"
               class="flex items-center justify-center gap-2 w-full bg-[#1a1a2e] hover:bg-[#2d2d50] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all hover:shadow-md mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Voir le détail
            </a>
            <div class="flex items-center justify-between">
                <span class="text-xs text-[#1a1a2e]/30">
                    <?= date('d/m/Y', strtotime($a['created_at'])) ?>
                </span>
                <div class="flex gap-2">
                    <a href="?page=annonces&action=edit&id=<?= $a['id'] ?>"
                       class="text-xs font-semibold text-[#1a1a2e] bg-[#f8f6f3] border border-[#e8e4df] px-3 py-1.5 rounded-lg hover:bg-[#ede9e3] transition-colors">
                        Modifier
                    </a>
                    <form method="POST" action="?page=annonces&action=delete&id=<?= $a['id'] ?>"
                          onsubmit="return confirm('Supprimer cette annonce ?')">
                        <button type="submit"
                                class="text-xs font-semibold text-red-500 bg-red-50 border border-red-100 px-3 py-1.5 rounded-lg hover:bg-red-100 transition-colors">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<script>
// Counter + scroll animations
const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (!e.isIntersecting) return;
        e.target.classList.add('visible');
        if (e.target.classList.contains('counter')) {
            const target = parseInt(e.target.dataset.target) || 0;
            const dur = 1300, start = performance.now();
            const tick = now => {
                const p = Math.min((now-start)/dur, 1);
                e.target.textContent = Math.round((1-Math.pow(1-p,3))*target);
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        }
        obs.unobserve(e.target);
    });
}, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.fade-up, .counter').forEach(el => obs.observe(el));
</script>

<?php require __DIR__ . '/../layout_footer.php'; ?>
