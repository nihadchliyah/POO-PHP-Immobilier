<?php require __DIR__ . '/../layout.php'; ?>

<div class="flex justify-between items-center mb-10">
    <div>
        <p class="text-[#c9a96e] font-semibold text-xs uppercase tracking-widest mb-1">Gestion</p>
        <h2 class="font-playfair text-4xl font-bold text-[#1a1a2e]">Propriétaires</h2>
        <?php if (!empty($_GET['q'])): ?>
        <p class="text-sm text-[#1a1a2e]/50 mt-1">
            <?= count($proprietaires) ?> résultat(s) pour
            <strong class="text-[#1a1a2e]">"<?= htmlspecialchars($_GET['q']) ?>"</strong>
        </p>
        <?php endif; ?>
    </div>
    <a href="?page=proprietaires&action=create"
       class="bg-[#1a1a2e] text-white text-sm font-semibold px-5 py-2.5 rounded-full hover:bg-[#2a2a4e] transition-colors">
        + Nouveau propriétaire
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<?php foreach ($proprietaires as $proprio): ?>

<?php
    $estPro     = $proprio['type'] === 'professionnel';
    $badgeStyle = $estPro
        ? 'bg-[#c9a96e]/20 text-[#1a1a2e]'
        : 'bg-[#1a1a2e]/10 text-[#1a1a2e]';
    $badgeLabel = $estPro ? 'Professionnel' : 'Particulier';
?>

<div class="property-card bg-white rounded-2xl border border-[#e8e4df] overflow-hidden flex flex-col">
    <div class="h-1 <?= $estPro ? 'bg-[#c9a96e]' : 'bg-[#1a1a2e]' ?>"></div>
    <div class="p-6 flex flex-col flex-1">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="font-playfair text-xl font-bold text-[#1a1a2e]"><?= htmlspecialchars($proprio['nom']) ?></h3>
                <p class="text-sm text-[#1a1a2e]/50 mt-0.5"><?= htmlspecialchars($proprio['email']) ?></p>
                <?php if ($proprio['telephone']): ?>
                    <p class="text-sm text-[#1a1a2e]/50">Tél : <?= htmlspecialchars($proprio['telephone']) ?></p>
                <?php endif; ?>
                <?php if ($estPro && $proprio['raison_sociale']): ?>
                    <p class="text-sm text-[#1a1a2e]/70 font-medium"><?= htmlspecialchars($proprio['raison_sociale']) ?></p>
                    <p class="text-xs text-[#1a1a2e]/40 font-mono">SIRET : <?= htmlspecialchars($proprio['siret'] ?? '') ?></p>
                <?php endif; ?>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full <?= $badgeStyle ?>"><?= $badgeLabel ?></span>
        </div>

        <div class="border-t border-[#e8e4df] pt-4 mb-4">
            <p class="text-xs font-semibold text-[#1a1a2e]/40 uppercase tracking-wide mb-3">
                Portefeuille — <?= count($proprio['biens']) ?> bien(s)
            </p>
            <?php if (empty($proprio['biens'])): ?>
                <p class="text-sm text-[#1a1a2e]/30 italic">Aucun bien enregistré.</p>
            <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($proprio['biens'] as $bien): ?>
                <div class="flex justify-between items-center bg-[#f8f6f3] rounded-xl px-3 py-2 text-sm">
                    <span class="font-mono text-[#1a1a2e]/40 text-xs"><?= htmlspecialchars($bien['reference']) ?></span>
                    <span class="text-[#1a1a2e]/70"><?= htmlspecialchars($bien['ville']) ?></span>
                    <span class="font-semibold text-[#c9a96e]"><?= number_format($bien['prix'], 0, ',', ' ') ?> €</span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="flex gap-2 pt-3 border-t border-[#e8e4df] mt-auto">
            <a href="?page=proprietaires&action=edit&id=<?= $proprio['id'] ?>"
               class="flex-1 text-center text-sm font-medium text-[#1a1a2e] bg-[#f8f6f3] border border-[#e8e4df] px-3 py-2 rounded-xl hover:bg-[#e8e4df] transition-colors">
                Modifier
            </a>
            <form method="POST" action="?page=proprietaires&action=delete&id=<?= $proprio['id'] ?>"
                  onsubmit="return confirm('Supprimer ce propriétaire ?')">
                <button type="submit"
                        class="text-sm font-medium text-red-600 bg-red-50 border border-red-100 px-3 py-2 rounded-xl hover:bg-red-100 transition-colors">
                    Supprimer
                </button>
            </form>
        </div>
    </div>
</div>

<?php endforeach; ?>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>
