<?php require __DIR__ . '/../layout.php'; ?>

<h2 class="text-3xl font-bold text-blue-900 mb-8">Patterns de conception</h2>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- SINGLETON -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full uppercase">Singleton</span>
            <h3 class="text-lg font-bold text-gray-800">DatabaseConnection</h3>
        </div>
        <p class="text-sm text-gray-500 mb-4">Une seule instance de connexion à la base de données dans toute l'application.</p>
        <div class="bg-gray-50 rounded-lg p-4 text-sm font-mono">
            <p class="text-gray-600">$db1 = DatabaseConnection::getInstance()</p>
            <p class="text-gray-600">$db2 = DatabaseConnection::getInstance()</p>
            <p class="mt-2 font-semibold <?= $memeInstance ? 'text-green-600' : 'text-red-600' ?>">
                <?= $memeInstance ? 'Même instance — Singleton OK' : 'Instances différentes' ?>
            </p>
        </div>
    </div>

    <!-- OBSERVER -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full uppercase">Observer</span>
            <h3 class="text-lg font-bold text-gray-800">Notification propriétaire</h3>
        </div>
        <p class="text-sm text-gray-500 mb-4">Le propriétaire est notifié automatiquement à chaque changement de statut de son bien.</p>
        <div class="space-y-2 text-sm">
            <div class="bg-blue-50 rounded px-3 py-2">
                <span><?= htmlspecialchars($msgLouer) ?></span>
            </div>
            <div class="bg-gray-50 rounded px-3 py-2">
                <span><?= htmlspecialchars($msgResilier) ?></span>
            </div>
            <div class="bg-green-50 rounded px-3 py-2">
                <span><?= htmlspecialchars($msgVendre) ?></span>
            </div>
        </div>
        <?php if (!empty($historique)): ?>
        <div class="mt-4 border-t pt-4">
            <p class="text-xs font-semibold text-gray-500 mb-2">Historique des événements</p>
            <?php foreach ($historique as $entree): ?>
            <div class="text-xs text-gray-400 font-mono"><?= $entree['date'] ?> — <?= strtoupper($entree['evenement']) ?> — <?= $entree['reference'] ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- STRATEGY -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full uppercase">Strategy</span>
            <h3 class="text-lg font-bold text-gray-800">Simulateur de prêt</h3>
        </div>
        <p class="text-sm text-gray-500 mb-4">
            Montant : <strong><?= number_format($montant, 0, ',', ' ') ?> €</strong> sur <strong><?= $dureeAns ?> ans</strong>
        </p>
        <div class="space-y-2">
            <?php foreach ($banques as $banque): ?>
            <?php
                $mensualite = $banque->calculerMensualite($montant, $dureeAns);
                $coutTotal  = $banque->calculerCoutTotal($montant, $dureeAns);
            ?>
            <div class="flex justify-between items-center bg-gray-50 rounded-lg px-4 py-3 text-sm">
                <span class="font-semibold text-gray-700"><?= htmlspecialchars($banque->getNomPrestataire()) ?></span>
                <span class="text-blue-700 font-bold"><?= number_format($mensualite, 2, ',', ' ') ?> €/mois</span>
                <span class="text-gray-400 text-xs"><?= $banque->getTauxAnnuel() ?>%</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- FACTORY -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full uppercase">Factory</span>
            <h3 class="text-lg font-bold text-gray-800">ProprietaireFactory</h3>
        </div>
        <p class="text-sm text-gray-500 mb-4">Création dynamique selon le type — particulier ou professionnel.</p>
        <div class="space-y-3">
            <div class="bg-blue-50 rounded-lg px-4 py-3 text-sm">
                <p class="font-semibold text-blue-800">Particulier — <?= htmlspecialchars($alice->getNom()) ?></p>
                <p class="text-gray-500"><?= htmlspecialchars($alice->getEmail()) ?></p>
                <?php if ($alice instanceof ProprietaireParticulier): ?>
                <p class="text-gray-500">Tél : <?= htmlspecialchars($alice->getTelephone()) ?></p>
                <?php endif; ?>
            </div>
            <div class="bg-purple-50 rounded-lg px-4 py-3 text-sm">
                <p class="font-semibold text-purple-800">Professionnel — <?= htmlspecialchars($bob->getNom()) ?></p>
                <?php if ($bob instanceof ProprietaireProfessionnel): ?>
                <p class="text-gray-500"><?= htmlspecialchars($bob->getRaisonSociale()) ?></p>
                <p class="text-gray-500 font-mono text-xs">SIRET : <?= htmlspecialchars($bob->getSiret()) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>
