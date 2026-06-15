<?php
/** @var array $bien */
/** @var array $errors */
require __DIR__ . '/../layout.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-8">
        <a href="?page=biens" class="text-[#1a1a2e]/40 hover:text-[#1a1a2e] transition-colors text-sm">← Retour</a>
        <div>
            <p class="text-[#c9a96e] text-xs font-semibold uppercase tracking-widest">Catalogue</p>
            <h2 class="font-playfair text-2xl font-bold text-[#1a1a2e]">Modifier le bien</h2>
        </div>
        <span class="text-xs font-mono text-[#1a1a2e]/40 bg-[#f8f6f3] border border-[#e8e4df] px-2 py-1 rounded-lg">
            <?= htmlspecialchars($bien['reference']) ?>
        </span>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-4 py-3 space-y-1">
        <?php foreach ($errors as $err): ?>
        <p class="text-sm text-red-700">— <?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php $d = $bien['details']; ?>

    <form method="POST" action="?page=biens&action=update&id=<?= $bien['id'] ?>"
          class="bg-white rounded-2xl border border-[#e8e4df] p-8 space-y-6">

        <div>
            <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Type de bien</label>
            <select name="type" id="type-select" onchange="toggleFields()"
                    class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] bg-white focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                <option value="appartement" <?= $bien['type'] === 'appartement' ? 'selected' : '' ?>>Appartement</option>
                <option value="maison"      <?= $bien['type'] === 'maison'      ? 'selected' : '' ?>>Maison</option>
                <option value="local"       <?= $bien['type'] === 'local'       ? 'selected' : '' ?>>Local commercial</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Ville <span class="text-red-500">*</span></label>
                <input type="text" name="ville" required value="<?= htmlspecialchars($bien['ville']) ?>"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Statut</label>
                <select name="statut"
                        class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] bg-white focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                    <option value="disponible" <?= $bien['statut'] === 'disponible' ? 'selected' : '' ?>>Disponible</option>
                    <option value="loue"       <?= $bien['statut'] === 'loue'       ? 'selected' : '' ?>>Loué</option>
                    <option value="vendu"      <?= $bien['statut'] === 'vendu'      ? 'selected' : '' ?>>Vendu</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Prix (€) <span class="text-red-500">*</span></label>
                <input type="number" name="prix" required min="1" value="<?= $bien['prix'] ?>"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Surface (m²) <span class="text-red-500">*</span></label>
                <input type="number" name="surface" required min="1" step="0.5" value="<?= $bien['surface'] ?>"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
        </div>

        <!-- Appartement -->
        <div id="fields-appartement" class="<?= $bien['type'] !== 'appartement' ? 'hidden' : '' ?> space-y-4 border-t border-[#e8e4df] pt-4">
            <p class="text-xs font-bold text-[#c9a96e] uppercase tracking-widest">Appartement</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Type</label>
                    <select name="typeAppartement"
                            class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] bg-white focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                        <?php foreach (['Studio','T1','T2','T3','T4','T5+'] as $t): ?>
                        <option value="<?= $t ?>" <?= ($d['typeAppartement'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Étage</label>
                    <input type="number" name="etage" min="0" value="<?= $d['etage'] ?? 0 ?>"
                           class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                </div>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="ascenseur" class="w-4 h-4 accent-[#c9a96e] rounded"
                       <?= ($d['ascenseur'] ?? false) ? 'checked' : '' ?>>
                <span class="text-sm text-[#1a1a2e]/70">Ascenseur</span>
            </label>
        </div>

        <!-- Maison -->
        <div id="fields-maison" class="<?= $bien['type'] !== 'maison' ? 'hidden' : '' ?> space-y-4 border-t border-[#e8e4df] pt-4">
            <p class="text-xs font-bold text-[#c9a96e] uppercase tracking-widest">Maison</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Chambres</label>
                    <input type="number" name="nbChambres" min="1" value="<?= $d['nbChambres'] ?? 1 ?>"
                           class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Surface jardin (m²)</label>
                    <input type="number" name="surfaceJardin" min="0" step="0.5" value="<?= $d['surfaceJardin'] ?? 0 ?>"
                           class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Garage</label>
                <input type="text" name="garage" value="<?= htmlspecialchars($d['garage'] ?? '') ?>"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="jardin" class="w-4 h-4 accent-[#c9a96e] rounded"
                       <?= ($d['jardin'] ?? false) ? 'checked' : '' ?>>
                <span class="text-sm text-[#1a1a2e]/70">Jardin</span>
            </label>
        </div>

        <!-- Local -->
        <div id="fields-local" class="<?= $bien['type'] !== 'local' ? 'hidden' : '' ?> space-y-4 border-t border-[#e8e4df] pt-4">
            <p class="text-xs font-bold text-[#c9a96e] uppercase tracking-widest">Local commercial</p>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Activité <span class="text-red-500">*</span></label>
                <input type="text" name="activite" value="<?= htmlspecialchars($d['activite'] ?? '') ?>"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
        </div>

        <p class="text-xs text-[#1a1a2e]/40">Les champs marqués <span class="text-red-500">*</span> sont obligatoires.</p>

        <div class="flex gap-3 pt-2 border-t border-[#e8e4df]">
            <button type="submit"
                    class="bg-[#1a1a2e] text-white px-6 py-2.5 rounded-xl hover:bg-[#2a2a4e] transition-colors font-semibold text-sm">
                Enregistrer les modifications
            </button>
            <a href="?page=biens"
               class="bg-[#f8f6f3] text-[#1a1a2e] border border-[#e8e4df] px-6 py-2.5 rounded-xl hover:bg-[#e8e4df] transition-colors font-semibold text-sm">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
function toggleFields() {
    const type = document.getElementById('type-select').value;
    document.getElementById('fields-appartement').classList.toggle('hidden', type !== 'appartement');
    document.getElementById('fields-maison').classList.toggle('hidden',     type !== 'maison');
    document.getElementById('fields-local').classList.toggle('hidden',      type !== 'local');
}
</script>

<?php require __DIR__ . '/../layout_footer.php'; ?>
