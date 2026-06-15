<?php require __DIR__ . '/../layout.php'; ?>

<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-8">
        <a href="?page=biens" class="text-gray-400 hover:text-gray-600 transition text-sm">← Retour</a>
        <h2 class="text-3xl font-bold text-blue-900">Estimation</h2>
        <span class="text-xs font-mono text-gray-400 bg-gray-100 px-2 py-1 rounded">
            <?= htmlspecialchars($row['reference']) ?>
        </span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-1"><?= htmlspecialchars($row['ville']) ?></h3>
        <p class="text-sm text-gray-500 mb-4">Prix actuel</p>
        <p class="text-3xl font-bold text-blue-900"><?= number_format($row['prix'], 0, ',', ' ') ?> €</p>
        <p class="text-sm text-gray-400 mt-1"><?= number_format($row['surface'], 0) ?> m²</p>
    </div>

    <?php if (!empty($estimations)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <p class="text-sm font-semibold text-gray-600 mb-4">
            Valeur estimée — taux de revalorisation <strong>2 % / an</strong>
        </p>
        <div class="space-y-3">
            <?php foreach ($estimations as $annees => $valeur): ?>
            <div class="flex justify-between items-center bg-gray-50 rounded-lg px-4 py-3">
                <span class="text-sm text-gray-600">Dans <strong><?= $annees ?> ans</strong></span>
                <span class="text-blue-900 font-bold"><?= number_format($valeur, 0, ',', ' ') ?> €</span>
                <span class="text-xs text-green-600 font-semibold">
                    +<?= number_format($valeur - $row['prix'], 0, ',', ' ') ?> €
                </span>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="text-xs text-gray-400 mt-4">
            Formule : prix × (1 + taux)^années — méthode <code>estimer()</code> sur l'objet PHP
        </p>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>
