<?php
/**
 * @var array      $annonce       Annonce + données bien (colonnes jointes)
 * @var array|null $proprietaire  Propriétaire du bien, ou null
 * @var array      $autresAnnonces Autres annonces sur ce bien
 * @var array      $similaires    Annonces similaires (même type)
 */
require __DIR__ . '/../layout.php';

$d = json_decode($annonce['details'] ?? '{}', true) ?? [];

$isVente  = $annonce['type_transaction'] === 'vente';
$isActive = $annonce['statut'] === 'active';
$prix     = (float)($annonce['prix_demande'] ?? $annonce['prix_bien']);

// Photo Unsplash selon type
$allPhotos = [
    'appartement' => ['photo-1522708323590-d24dbb6b0267','photo-1502672260266-1c1ef2d93688','photo-1560448204-e02f11c3d0e2','photo-1493809842364-78817add7ffb','photo-1574362848149-11496d93a7c7'],
    'maison'      => ['photo-1564013799919-ab600027ffc6','photo-1570129477492-45c003edd2be','photo-1512917774080-9991f1c4c750','photo-1600585154340-be6161a56a0c','photo-1449844908441-8829872d2607'],
    'local'       => ['photo-1497366216548-37526070297c','photo-1441986300917-64674bd600d8','photo-1560179707-f14e90ef3623','photo-1604719312566-8912e9227c6a','photo-1604014237800-1c9102c219da'],
];
$pool       = $allPhotos[$annonce['bien_type']] ?? $allPhotos['appartement'];
$photoMain  = $pool[$annonce['bien_id'] % count($pool)];
$photoAlt1  = $pool[($annonce['bien_id'] + 1) % count($pool)];
$photoAlt2  = $pool[($annonce['bien_id'] + 2) % count($pool)];
$urlMain    = 'https://images.unsplash.com/' . $photoMain . '?w=1200&h=700&fit=crop&auto=format&q=85';
$urlAlt1    = 'https://images.unsplash.com/' . $photoAlt1 . '?w=600&h=400&fit=crop&auto=format&q=80';
$urlAlt2    = 'https://images.unsplash.com/' . $photoAlt2 . '?w=600&h=400&fit=crop&auto=format&q=80';

// Chips caractéristiques du bien
$chips = [];
if ($annonce['bien_type'] === 'appartement') {
    if (!empty($d['typeAppartement'])) $chips[] = ['icon'=>'🏠','label'=>$d['typeAppartement']];
    if (isset($d['etage']))            $chips[] = ['icon'=>'🏢','label'=>'Étage '.$d['etage']];
    if (!empty($d['ascenseur']))       $chips[] = ['icon'=>'🛗','label'=>'Ascenseur'];
} elseif ($annonce['bien_type'] === 'maison') {
    if (!empty($d['nbChambres']))      $chips[] = ['icon'=>'🛏','label'=>$d['nbChambres'].' chambres'];
    if (!empty($d['jardin']))          $chips[] = ['icon'=>'🌿','label'=>'Jardin '.($d['surfaceJardin']??0).' m²'];
    if (!empty($d['garage']))          $chips[] = ['icon'=>'🚗','label'=>$d['garage']];
} elseif ($annonce['bien_type'] === 'local') {
    if (!empty($d['activite']))        $chips[] = ['icon'=>'🏪','label'=>$d['activite']];
}
$chips[] = ['icon'=>'📐','label'=>number_format($annonce['surface'],0).' m²'];
$chips[] = ['icon'=>'📍','label'=>$annonce['ville']];

$statutBienLabel = match($annonce['statut_bien']) {
    'vendu'  => 'Vendu',
    'loue'   => 'Loué',
    default  => 'Disponible',
};
$statutBienStyle = match($annonce['statut_bien']) {
    'vendu'  => 'badge-vendu',
    'loue'   => 'badge-loue',
    default  => 'badge-dispo',
};
?>

<!-- ── BREADCRUMB ──────────────────────────────────────────────────────────────── -->
<div class="flex items-center gap-2 text-xs text-[#1a1a2e]/40 mb-6 fade-up">
    <a href="?page=annonces" class="hover:text-[#c9a96e] transition-colors">Annonces</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
    <span class="text-[#1a1a2e]/60 truncate max-w-xs"><?= htmlspecialchars($annonce['titre']) ?></span>
</div>

<!-- ── GALERIE PHOTOS ─────────────────────────────────────────────────────────── -->
<div class="grid grid-cols-3 grid-rows-2 gap-3 rounded-3xl overflow-hidden mb-10 fade-up" style="height:460px">
    <!-- Photo principale -->
    <div class="col-span-2 row-span-2 relative overflow-hidden group cursor-pointer">
        <img src="<?= $urlMain ?>" alt="<?= htmlspecialchars($annonce['ville']) ?>"
             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>

        <!-- Badges sur la photo principale -->
        <div class="absolute top-4 left-4 flex gap-2">
            <span class="text-xs font-bold px-3 py-1.5 rounded-full <?= $isVente ? 'badge-vente' : 'badge-location' ?>">
                <?= $isVente ? 'Vente' : 'Location' ?>
            </span>
            <span class="text-xs font-bold px-3 py-1.5 rounded-full <?= $isActive ? 'badge-active' : 'badge-archivee' ?>">
                <?= $isActive ? '● Active' : '● Archivée' ?>
            </span>
        </div>

        <!-- Réf en bas à gauche -->
        <div class="absolute bottom-4 left-4">
            <span class="font-mono text-[11px] text-white/60 bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded-lg">
                <?= htmlspecialchars($annonce['reference']) ?>
            </span>
        </div>
    </div>

    <!-- Photos secondaires -->
    <div class="relative overflow-hidden group cursor-pointer">
        <img src="<?= $urlAlt1 ?>" alt="" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-108">
        <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
    </div>
    <div class="relative overflow-hidden group cursor-pointer">
        <img src="<?= $urlAlt2 ?>" alt="" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-108">
        <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20">
            <span class="text-white text-xs font-semibold bg-black/40 backdrop-blur px-3 py-1.5 rounded-full">Voir toutes les photos</span>
        </div>
    </div>
</div>

<!-- ── CONTENU PRINCIPAL ──────────────────────────────────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-14">

    <!-- Colonne principale (2/3) -->
    <div class="lg:col-span-2 space-y-8">

        <!-- Titre + prix -->
        <div class="fade-up">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                <div class="flex-1 min-w-0">
                    <h1 class="font-playfair text-3xl md:text-4xl font-bold text-[#1a1a2e] leading-tight mb-2">
                        <?= htmlspecialchars($annonce['titre']) ?>
                    </h1>
                    <p class="text-[#1a1a2e]/50 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-[#c9a96e] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        <?= htmlspecialchars($annonce['ville']) ?>, France
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-4xl font-bold text-[#c9a96e] leading-none">
                        <?= number_format($prix, 0, ',', ' ') ?> €
                    </p>
                    <?php if (!$isVente): ?>
                    <p class="text-[#1a1a2e]/40 text-sm mt-1">par mois</p>
                    <?php else: ?>
                    <p class="text-[#1a1a2e]/40 text-sm mt-1">
                        <?= number_format($prix / max($annonce['surface'],1), 0, ',', ' ') ?> €/m²
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Chips caractéristiques -->
            <div class="flex flex-wrap gap-2">
                <?php foreach ($chips as $chip): ?>
                <span class="inline-flex items-center gap-1.5 bg-white border border-[#e8e4df] text-[#1a1a2e]/70 text-sm font-medium px-4 py-2 rounded-xl shadow-sm">
                    <span><?= $chip['icon'] ?></span>
                    <?= htmlspecialchars($chip['label']) ?>
                </span>
                <?php endforeach; ?>
                <span class="inline-flex items-center gap-1.5 border border-[#e8e4df] text-sm font-medium px-4 py-2 rounded-xl <?= $statutBienStyle ?>">
                    Bien <?= $statutBienLabel ?>
                </span>
            </div>
        </div>

        <!-- Séparateur -->
        <div class="border-t border-[#e8e4df] fade-up"></div>

        <!-- Description -->
        <div class="fade-up">
            <h2 class="font-playfair text-xl font-bold text-[#1a1a2e] mb-4">Description</h2>
            <?php if (!empty($annonce['description'])): ?>
            <div class="text-[#1a1a2e]/65 leading-relaxed text-[15px] space-y-3" id="desc-content">
                <?php foreach (explode("\n", nl2br(htmlspecialchars($annonce['description']))) as $line): ?>
                <p><?= $line ?></p>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-[#1a1a2e]/30 italic text-sm">Aucune description disponible.</p>
            <?php endif; ?>
        </div>

        <!-- Détails du bien -->
        <div class="bg-white rounded-2xl border border-[#e8e4df] p-6 shadow-sm fade-up">
            <h2 class="font-playfair text-xl font-bold text-[#1a1a2e] mb-5">Caractéristiques du bien</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">

                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Type</p>
                    <p class="text-sm font-semibold text-[#1a1a2e]"><?= ucfirst($annonce['bien_type']) ?></p>
                </div>

                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Surface</p>
                    <p class="text-sm font-semibold text-[#1a1a2e]"><?= number_format($annonce['surface'], 0) ?> m²</p>
                </div>

                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Prix bien</p>
                    <p class="text-sm font-semibold text-[#1a1a2e]"><?= number_format($annonce['prix_bien'], 0, ',', ' ') ?> €</p>
                </div>

                <?php if ($annonce['bien_type'] === 'appartement'): ?>
                <?php if (isset($d['etage'])): ?>
                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Étage</p>
                    <p class="text-sm font-semibold text-[#1a1a2e]"><?= $d['etage'] ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($d['typeAppartement'])): ?>
                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Catégorie</p>
                    <p class="text-sm font-semibold text-[#1a1a2e]"><?= htmlspecialchars($d['typeAppartement']) ?></p>
                </div>
                <?php endif; ?>
                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Ascenseur</p>
                    <p class="text-sm font-semibold text-[#1a1a2e]"><?= !empty($d['ascenseur']) ? '✅ Oui' : '❌ Non' ?></p>
                </div>

                <?php elseif ($annonce['bien_type'] === 'maison'): ?>
                <?php if (isset($d['nbChambres'])): ?>
                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Chambres</p>
                    <p class="text-sm font-semibold text-[#1a1a2e]"><?= $d['nbChambres'] ?></p>
                </div>
                <?php endif; ?>
                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Jardin</p>
                    <p class="text-sm font-semibold text-[#1a1a2e]">
                        <?= !empty($d['jardin']) ? '✅ '.($d['surfaceJardin']??0).' m²' : '❌ Non' ?>
                    </p>
                </div>
                <?php if (!empty($d['garage'])): ?>
                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Garage</p>
                    <p class="text-sm font-semibold text-[#1a1a2e]"><?= htmlspecialchars($d['garage']) ?></p>
                </div>
                <?php endif; ?>

                <?php elseif ($annonce['bien_type'] === 'local'): ?>
                <?php if (!empty($d['activite'])): ?>
                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Activité</p>
                    <p class="text-sm font-semibold text-[#1a1a2e]"><?= htmlspecialchars($d['activite']) ?></p>
                </div>
                <?php endif; ?>
                <?php endif; ?>

                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Transaction</p>
                    <p class="text-sm font-semibold text-[#1a1a2e]"><?= $isVente ? '🏷 Vente' : '🔑 Location' ?></p>
                </div>

                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Référence</p>
                    <p class="text-sm font-semibold font-mono text-[#1a1a2e]"><?= htmlspecialchars($annonce['reference']) ?></p>
                </div>

                <div class="bg-[#f8f6f3] rounded-xl p-4">
                    <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-1">Statut bien</p>
                    <p class="text-sm font-semibold text-[#1a1a2e]"><?= $statutBienLabel ?></p>
                </div>
            </div>
        </div>

        <!-- Carte localisation -->
        <div class="bg-white rounded-2xl border border-[#e8e4df] p-6 shadow-sm fade-up">
            <h2 class="font-playfair text-xl font-bold text-[#1a1a2e] mb-1">Localisation</h2>
            <p class="text-sm text-[#1a1a2e]/40 mb-4">
                <?= htmlspecialchars($annonce['ville']) ?>, France
            </p>
            <div id="detail-map" style="height:280px;border-radius:12px;overflow:hidden"></div>
        </div>

        <!-- Autres annonces sur ce bien -->
        <?php if (!empty($autresAnnonces)): ?>
        <div class="fade-up">
            <h2 class="font-playfair text-xl font-bold text-[#1a1a2e] mb-4">
                Autres annonces sur ce bien
            </h2>
            <div class="space-y-3">
                <?php foreach ($autresAnnonces as $autre):
                    $autreIsVente = $autre['type_transaction'] === 'vente';
                ?>
                <a href="?page=annonces&action=show&id=<?= $autre['id'] ?>"
                   class="flex items-center justify-between bg-white border border-[#e8e4df] rounded-xl px-5 py-4 hover:border-[#c9a96e]/40 hover:shadow-md transition-all group">
                    <div>
                        <p class="text-sm font-semibold text-[#1a1a2e] group-hover:text-[#c9a96e] transition-colors line-clamp-1">
                            <?= htmlspecialchars($autre['titre']) ?>
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold <?= $autreIsVente ? 'badge-vente' : 'badge-location' ?>">
                                <?= $autreIsVente ? 'Vente' : 'Location' ?>
                            </span>
                            <span class="text-xs text-[#1a1a2e]/40">
                                <?= $autre['statut'] === 'active' ? '● Active' : '● Archivée' ?>
                            </span>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-4">
                        <p class="text-base font-bold text-[#c9a96e]">
                            <?= $autre['prix_demande'] ? number_format($autre['prix_demande'], 0, ',', ' ').' €' : '—' ?>
                        </p>
                        <svg class="w-4 h-4 text-[#1a1a2e]/20 mt-1 ml-auto group-hover:text-[#c9a96e] transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /col principale -->

    <!-- ── SIDEBAR DROITE (1/3) ─────────────────────────────────────────────── -->
    <div class="space-y-5">

        <!-- Carte contact principale -->
        <div class="bg-white rounded-2xl border border-[#e8e4df] p-6 shadow-sm sticky top-28 fade-up">
            <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-4">Contact agence</p>

            <!-- Avatar + nom -->
            <div class="flex items-center gap-4 mb-5">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#1a1a2e] to-[#2d2d50] flex items-center justify-center text-[#c9a96e] text-xl font-bold shrink-0">
                    <?= mb_strtoupper(mb_substr($annonce['contact_nom'], 0, 1)) ?>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-[#1a1a2e] text-base leading-snug"><?= htmlspecialchars($annonce['contact_nom']) ?></p>
                    <p class="text-xs text-[#1a1a2e]/40 mt-0.5">Agent immobilier</p>
                </div>
            </div>

            <!-- Infos contact -->
            <div class="space-y-3 mb-5">
                <a href="mailto:<?= htmlspecialchars($annonce['contact_email']) ?>"
                   class="flex items-center gap-3 p-3 rounded-xl bg-[#f8f6f3] hover:bg-[#ede9e3] transition-colors group">
                    <span class="w-8 h-8 rounded-lg bg-[#1a1a2e] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-[#c9a96e]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <span class="text-sm text-[#1a1a2e]/70 truncate group-hover:text-[#1a1a2e] transition-colors">
                        <?= htmlspecialchars($annonce['contact_email']) ?>
                    </span>
                </a>

                <?php if (!empty($annonce['contact_telephone'])): ?>
                <a href="tel:<?= htmlspecialchars($annonce['contact_telephone']) ?>"
                   class="flex items-center gap-3 p-3 rounded-xl bg-[#f8f6f3] hover:bg-[#ede9e3] transition-colors group">
                    <span class="w-8 h-8 rounded-lg bg-[#1a1a2e] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-[#c9a96e]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold text-[#1a1a2e] group-hover:text-[#c9a96e] transition-colors">
                        <?= htmlspecialchars($annonce['contact_telephone']) ?>
                    </span>
                </a>
                <?php endif; ?>
            </div>

            <!-- CTA email -->
            <a href="mailto:<?= htmlspecialchars($annonce['contact_email']) ?>?subject=<?= urlencode('Demande d\'information — '.$annonce['titre']) ?>"
               class="w-full flex items-center justify-center gap-2 bg-[#c9a96e] hover:bg-[#b8935a] text-white font-bold text-sm px-5 py-3.5 rounded-xl transition-all hover:shadow-lg hover:shadow-[#c9a96e]/20 mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Contacter l'agent
            </a>
            <a href="?page=biens&action=edit&id=<?= $annonce['bien_id'] ?>"
               class="w-full flex items-center justify-center gap-2 bg-[#f8f6f3] hover:bg-[#ede9e3] text-[#1a1a2e] font-semibold text-sm px-5 py-3.5 rounded-xl transition-colors border border-[#e8e4df]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
                </svg>
                Voir le bien
            </a>

            <!-- Date publication -->
            <p class="text-center text-xs text-[#1a1a2e]/25 mt-4">
                Publiée le <?= date('d/m/Y', strtotime($annonce['created_at'])) ?>
            </p>
        </div>

        <!-- Propriétaire du bien -->
        <?php if ($proprietaire): ?>
        <div class="bg-white rounded-2xl border border-[#e8e4df] p-5 shadow-sm fade-up delay-1">
            <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-3">Propriétaire du bien</p>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl <?= $proprietaire['type']==='professionnel' ? 'bg-[#c9a96e]/15' : 'bg-[#1a1a2e]/8' ?> flex items-center justify-center text-xs font-bold text-[#1a1a2e]">
                    <?= mb_strtoupper(mb_substr($proprietaire['nom'], 0, 1)) ?>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-[#1a1a2e] truncate"><?= htmlspecialchars($proprietaire['nom']) ?></p>
                    <?php if ($proprietaire['type'] === 'professionnel' && !empty($proprietaire['raison_sociale'])): ?>
                    <p class="text-xs text-[#c9a96e] truncate"><?= htmlspecialchars($proprietaire['raison_sociale']) ?></p>
                    <?php elseif (!empty($proprietaire['telephone'])): ?>
                    <p class="text-xs text-[#1a1a2e]/40"><?= htmlspecialchars($proprietaire['telephone']) ?></p>
                    <?php endif; ?>
                </div>
                <span class="ml-auto text-[10px] font-bold px-2.5 py-1 rounded-full <?= $proprietaire['type']==='professionnel' ? 'bg-[#c9a96e]/15 text-[#1a1a2e]' : 'bg-[#1a1a2e]/8 text-[#1a1a2e]' ?> shrink-0">
                    <?= $proprietaire['type']==='professionnel' ? 'Pro' : 'Particulier' ?>
                </span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions gestion -->
        <div class="bg-white rounded-2xl border border-[#e8e4df] p-5 shadow-sm fade-up delay-2">
            <p class="text-[10px] font-bold text-[#1a1a2e]/35 uppercase tracking-widest mb-3">Gestion</p>
            <div class="space-y-2">
                <a href="?page=annonces&action=edit&id=<?= $annonce['id'] ?>"
                   class="flex items-center gap-3 text-sm font-semibold text-[#1a1a2e] bg-[#f8f6f3] border border-[#e8e4df] px-4 py-2.5 rounded-xl hover:bg-[#ede9e3] transition-colors">
                    <svg class="w-4 h-4 text-[#c9a96e]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier l'annonce
                </a>
                <a href="?page=annonces&action=create&bien_id=<?= $annonce['bien_id'] ?>"
                   class="flex items-center gap-3 text-sm font-semibold text-[#1a1a2e] bg-[#f8f6f3] border border-[#e8e4df] px-4 py-2.5 rounded-xl hover:bg-[#ede9e3] transition-colors">
                    <svg class="w-4 h-4 text-[#c9a96e]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Nouvelle annonce pour ce bien
                </a>
                <form method="POST" action="?page=annonces&action=delete&id=<?= $annonce['id'] ?>"
                      onsubmit="return confirm('Supprimer cette annonce définitivement ?')">
                    <button type="submit"
                            class="w-full flex items-center gap-3 text-sm font-semibold text-red-500 bg-red-50 border border-red-100 px-4 py-2.5 rounded-xl hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer l'annonce
                    </button>
                </form>
            </div>
        </div>

    </div><!-- /sidebar -->
</div>

<!-- ── ANNONCES SIMILAIRES ────────────────────────────────────────────────────── -->
<?php if (!empty($similaires)): ?>
<div class="mb-10 fade-up">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-1 h-6 rounded-full bg-[#c9a96e]"></div>
        <h2 class="font-playfair text-2xl font-bold text-[#1a1a2e]">Annonces similaires</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($similaires as $sim):
            $simPhotos = $allPhotos[$sim['bien_type']] ?? $allPhotos['appartement'];
            $simPhotoId = $simPhotos[$sim['bien_id'] % count($simPhotos)];
            $simUrl     = 'https://images.unsplash.com/' . $simPhotoId . '?w=600&h=400&fit=crop&auto=format&q=80';
            $simPrix    = $sim['prix_demande'];
            $simIsVente = $sim['type_transaction'] === 'vente';
        ?>
        <a href="?page=annonces&action=show&id=<?= $sim['id'] ?>"
           class="property-card block bg-white rounded-2xl overflow-hidden border border-[#e8e4df] shadow-sm hover:border-[#c9a96e]/30 group">
            <div class="relative overflow-hidden" style="height:180px">
                <img src="<?= $simUrl ?>" alt="" class="card-img w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <span class="absolute top-3 left-3 text-[11px] font-bold px-2.5 py-1 rounded-full <?= $simIsVente ? 'badge-vente' : 'badge-location' ?>">
                    <?= $simIsVente ? 'Vente' : 'Location' ?>
                </span>
                <div class="absolute bottom-3 left-3">
                    <p class="text-lg font-bold text-white">
                        <?= $simPrix ? number_format($simPrix, 0, ',', ' ').' €' : '—' ?>
                    </p>
                </div>
            </div>
            <div class="p-4">
                <p class="font-playfair font-bold text-[#1a1a2e] mb-1 group-hover:text-[#c9a96e] transition-colors line-clamp-2 text-sm leading-snug">
                    <?= htmlspecialchars($sim['titre']) ?>
                </p>
                <p class="text-xs text-[#1a1a2e]/40"><?= htmlspecialchars($sim['ville']) ?> · <?= number_format($sim['surface'],0) ?> m²</p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ── RETOUR ─────────────────────────────────────────────────────────────────── -->
<div class="flex items-center justify-between pt-6 border-t border-[#e8e4df] fade-up">
    <a href="?page=annonces"
       class="inline-flex items-center gap-2 text-sm font-semibold text-[#1a1a2e]/50 hover:text-[#1a1a2e] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour aux annonces
    </a>
    <span class="text-xs text-[#1a1a2e]/25 font-mono">#<?= $annonce['id'] ?></span>
</div>

<!-- ── SCRIPTS ─────────────────────────────────────────────────────────────────── -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Scroll animations
const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (!e.isIntersecting) return;
        e.target.classList.add('visible');
        obs.unobserve(e.target);
    });
}, { threshold: 0.06, rootMargin: '0px 0px -30px 0px' });
document.querySelectorAll('.fade-up').forEach(el => obs.observe(el));

// Map de localisation
(async function initDetailMap() {
    const ville = <?= json_encode($annonce['ville']) ?>;
    const key   = 'nina_v2_' + ville.toLowerCase().trim();
    let coords  = null;

    const cached = localStorage.getItem(key);
    if (cached) { try { coords = JSON.parse(cached); } catch(e){} }

    if (!coords) {
        try {
            const r = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(ville+', France')}&format=json&limit=1`,
                {headers:{'Accept-Language':'fr'}});
            const d = await r.json();
            if (d.length) {
                coords = [parseFloat(d[0].lat), parseFloat(d[0].lon)];
                localStorage.setItem(key, JSON.stringify(coords));
            }
        } catch(e) {}
    }

    const center = coords ?? [46.6, 2.3];
    const zoom   = coords ? 13 : 6;

    const map = L.map('detail-map', { zoomControl: true, scrollWheelZoom: false })
                 .setView(center, zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 18
    }).addTo(map);

    if (coords) {
        const icon = L.divIcon({
            html: `<div style="width:26px;height:26px;border-radius:50%;background:#c9a96e;border:4px solid white;box-shadow:0 4px 16px rgba(201,169,110,.5)"></div>`,
            iconSize: [26,26], iconAnchor: [13,13], className:''
        });
        L.marker(coords, {icon})
         .addTo(map)
         .bindPopup(`<b style="font-family:'DM Sans',sans-serif">${ville}</b><br><span style="color:#c9a96e;font-weight:700"><?= number_format($prix,0,',',' ') ?> €</span>`)
         .openPopup();
    }
})();
</script>

<?php require __DIR__ . '/../layout_footer.php'; ?>
