<?php
/** @var array $proprietaire */
/** @var array $biens */
/** @var array $biensSelectionnes */
/** @var array $errors */
require __DIR__ . '/../layout.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-8">
        <a href="?page=proprietaires" class="text-[#1a1a2e]/40 hover:text-[#1a1a2e] transition-colors text-sm">← Retour</a>
        <div>
            <p class="text-[#c9a96e] text-xs font-semibold uppercase tracking-widest">Gestion</p>
            <h2 class="font-playfair text-2xl font-bold text-[#1a1a2e]">Modifier le propriétaire</h2>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-4 py-3 space-y-1">
        <?php foreach ($errors as $err): ?>
        <p class="text-sm text-red-700">— <?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="?page=proprietaires&action=update&id=<?= $proprietaire['id'] ?>"
          class="bg-white rounded-2xl border border-[#e8e4df] p-8 space-y-6">

        <div>
            <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Type</label>
            <select name="type" id="type-select" onchange="toggleFields()"
                    class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] bg-white focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                <option value="particulier"   <?= $proprietaire['type'] === 'particulier'   ? 'selected' : '' ?>>Particulier</option>
                <option value="professionnel" <?= $proprietaire['type'] === 'professionnel' ? 'selected' : '' ?>>Professionnel</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Nom complet <span class="text-red-500">*</span></label>
                <input type="text" name="nom" required value="<?= htmlspecialchars($proprietaire['nom']) ?>"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" required value="<?= htmlspecialchars($proprietaire['email']) ?>"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
        </div>

        <div id="fields-particulier"
             class="<?= $proprietaire['type'] !== 'particulier' ? 'hidden' : '' ?> space-y-4 border-t border-[#e8e4df] pt-4">
            <p class="text-xs font-bold text-[#c9a96e] uppercase tracking-widest">Particulier</p>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Téléphone <span class="text-red-500">*</span></label>
                <input type="text" name="telephone" value="<?= htmlspecialchars($proprietaire['telephone'] ?? '') ?>"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
        </div>

        <div id="fields-professionnel"
             class="<?= $proprietaire['type'] !== 'professionnel' ? 'hidden' : '' ?> space-y-4 border-t border-[#e8e4df] pt-4">
            <p class="text-xs font-bold text-[#c9a96e] uppercase tracking-widest">Professionnel</p>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Raison sociale <span class="text-red-500">*</span></label>
                <input type="text" name="raisonSociale" value="<?= htmlspecialchars($proprietaire['raison_sociale'] ?? '') ?>"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">SIRET <span class="text-red-500">*</span></label>
                <input type="text" name="siret" value="<?= htmlspecialchars($proprietaire['siret'] ?? '') ?>"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
        </div>

        <?php if (!empty($biens)): ?>
        <div class="border-t border-[#e8e4df] pt-4">
            <p class="text-xs font-bold text-[#c9a96e] uppercase tracking-widest mb-3">Biens associés</p>
            <div class="grid grid-cols-2 gap-2">
                <?php foreach ($biens as $bien): ?>
                <label class="flex items-center gap-2 bg-[#f8f6f3] rounded-xl px-3 py-2 cursor-pointer hover:bg-[#e8e4df] transition-colors border border-[#e8e4df]">
                    <input type="checkbox" name="biens[]" value="<?= $bien['id'] ?>"
                           class="w-4 h-4 accent-[#c9a96e] rounded"
                           <?= in_array($bien['id'], $biensSelectionnes) ? 'checked' : '' ?>>
                    <span class="text-xs">
                        <span class="font-mono text-[#1a1a2e]/40"><?= htmlspecialchars($bien['reference']) ?></span>
                        <span class="text-[#1a1a2e]/70 ml-1"><?= htmlspecialchars($bien['ville']) ?></span>
                    </span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <p class="text-xs text-[#1a1a2e]/40">Les champs marqués <span class="text-red-500">*</span> sont obligatoires.</p>

        <div class="flex gap-3 pt-2 border-t border-[#e8e4df]">
            <button type="submit"
                    class="bg-[#1a1a2e] text-white px-6 py-2.5 rounded-xl hover:bg-[#2a2a4e] transition-colors font-semibold text-sm">
                Enregistrer les modifications
            </button>
            <a href="?page=proprietaires"
               class="bg-[#f8f6f3] text-[#1a1a2e] border border-[#e8e4df] px-6 py-2.5 rounded-xl hover:bg-[#e8e4df] transition-colors font-semibold text-sm">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
function toggleFields() {
    const type = document.getElementById('type-select').value;
    document.getElementById('fields-particulier').classList.toggle('hidden',   type !== 'particulier');
    document.getElementById('fields-professionnel').classList.toggle('hidden', type !== 'professionnel');
}
</script>

<?php require __DIR__ . '/../layout_footer.php'; ?>
