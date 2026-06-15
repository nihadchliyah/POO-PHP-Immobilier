<?php
/** @var array  $annonce */
/** @var array  $biens   */
/** @var array  $errors  */
require __DIR__ . '/../layout.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-8">
        <a href="?page=annonces" class="text-[#1a1a2e]/40 hover:text-[#1a1a2e] transition-colors text-sm">← Retour</a>
        <div>
            <p class="text-[#c9a96e] text-xs font-semibold uppercase tracking-widest">Annonces</p>
            <h2 class="font-playfair text-2xl font-bold text-[#1a1a2e]">Modifier l'annonce</h2>
        </div>
        <span class="text-xs text-[#1a1a2e]/40 bg-[#f8f6f3] border border-[#e8e4df] px-2 py-1 rounded-lg">
            #<?= $annonce['id'] ?>
        </span>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-4 py-3 space-y-1">
        <?php foreach ($errors as $err): ?>
        <p class="text-sm text-red-700">— <?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="?page=annonces&action=update&id=<?= $annonce['id'] ?>"
          class="bg-white rounded-2xl border border-[#e8e4df] p-8 space-y-6">

        <!-- Bien concerné -->
        <div>
            <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">
                Bien immobilier <span class="text-red-500">*</span>
            </label>
            <select name="bien_id" required
                    class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] bg-white focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                <option value="">— Choisir un bien —</option>
                <?php foreach ($biens as $b): ?>
                <option value="<?= $b['id'] ?>" <?= (int)$annonce['bien_id'] === $b['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($b['reference']) ?> — <?= htmlspecialchars($b['ville']) ?>
                    (<?= ucfirst($b['type']) ?>, <?= number_format($b['prix'], 0, ',', ' ') ?> €)
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Titre -->
        <div>
            <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">
                Titre de l'annonce <span class="text-red-500">*</span>
            </label>
            <input type="text" name="titre" required
                   value="<?= htmlspecialchars($annonce['titre']) ?>"
                   class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
        </div>

        <!-- Description -->
        <div>
            <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Description</label>
            <textarea name="description" rows="4"
                      class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e] resize-none"><?= htmlspecialchars($annonce['description']) ?></textarea>
        </div>

        <!-- Type transaction + Prix + Statut -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Transaction</label>
                <select name="type_transaction"
                        class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] bg-white focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                    <option value="vente"    <?= $annonce['type_transaction'] === 'vente'    ? 'selected' : '' ?>>Vente</option>
                    <option value="location" <?= $annonce['type_transaction'] === 'location' ? 'selected' : '' ?>>Location</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Prix demandé (€)</label>
                <input type="number" name="prix_demande" min="0" step="500"
                       value="<?= htmlspecialchars($annonce['prix_demande'] ?? '') ?>"
                       placeholder="Laisser vide = prix du bien"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Statut</label>
                <select name="statut"
                        class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] bg-white focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                    <option value="active"   <?= $annonce['statut'] === 'active'   ? 'selected' : '' ?>>Active</option>
                    <option value="archivee" <?= $annonce['statut'] === 'archivee' ? 'selected' : '' ?>>Archivée</option>
                </select>
            </div>
        </div>

        <!-- Contact -->
        <div class="border-t border-[#e8e4df] pt-6">
            <p class="text-xs font-bold text-[#c9a96e] uppercase tracking-widest mb-4">Contact</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="contact_nom" required
                           value="<?= htmlspecialchars($annonce['contact_nom']) ?>"
                           class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="contact_email" required
                           value="<?= htmlspecialchars($annonce['contact_email']) ?>"
                           class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Téléphone</label>
                    <input type="tel" name="contact_telephone"
                           value="<?= htmlspecialchars($annonce['contact_telephone'] ?? '') ?>"
                           class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                </div>
            </div>
        </div>

        <p class="text-xs text-[#1a1a2e]/40">Les champs marqués <span class="text-red-500">*</span> sont obligatoires.</p>

        <div class="flex gap-3 pt-2 border-t border-[#e8e4df]">
            <button type="submit"
                    class="bg-[#1a1a2e] text-white px-6 py-2.5 rounded-xl hover:bg-[#2a2a4e] transition-colors font-semibold text-sm">
                Enregistrer les modifications
            </button>
            <a href="?page=annonces"
               class="bg-[#f8f6f3] text-[#1a1a2e] border border-[#e8e4df] px-6 py-2.5 rounded-xl hover:bg-[#e8e4df] transition-colors font-semibold text-sm">
                Annuler
            </a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>
