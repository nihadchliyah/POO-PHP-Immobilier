<?php require __DIR__ . '/../layout.php'; ?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-8">
        <a href="?page=biens" class="text-[#1a1a2e]/40 hover:text-[#1a1a2e] transition-colors text-sm">← Retour</a>
        <div>
            <p class="text-[#c9a96e] text-xs font-semibold uppercase tracking-widest">Catalogue</p>
            <h2 class="font-playfair text-2xl font-bold text-[#1a1a2e]">Nouveau bien</h2>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-4 py-3 space-y-1">
        <?php foreach ($errors as $err): ?>
        <p class="text-sm text-red-700">— <?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="?page=biens&action=store"
          class="bg-white rounded-2xl border border-[#e8e4df] p-8 space-y-6">

        <div>
            <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Type de bien</label>
            <select name="type" id="type-select" onchange="toggleFields()"
                    class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] bg-white focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                <option value="appartement">Appartement</option>
                <option value="maison">Maison</option>
                <option value="local">Local commercial</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Ville <span class="text-red-500">*</span></label>
                <input type="text" name="ville" required placeholder="Paris"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Statut</label>
                <select name="statut"
                        class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] bg-white focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                    <option value="disponible">Disponible</option>
                    <option value="loue">Loué</option>
                    <option value="vendu">Vendu</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Prix (€) <span class="text-red-500">*</span></label>
                <input type="number" name="prix" required min="1" placeholder="200000"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Surface (m²) <span class="text-red-500">*</span></label>
                <input type="number" name="surface" required min="1" step="0.5" placeholder="50"
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
        </div>

        <!-- Appartement -->
        <div id="fields-appartement" class="space-y-4 border-t border-[#e8e4df] pt-4">
            <p class="text-xs font-bold text-[#c9a96e] uppercase tracking-widest">Appartement</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Type</label>
                    <select name="typeAppartement"
                            class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] bg-white focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                        <option value="Studio">Studio</option>
                        <option value="T1">T1</option>
                        <option value="T2">T2</option>
                        <option value="T3">T3</option>
                        <option value="T4">T4</option>
                        <option value="T5+">T5+</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Étage</label>
                    <input type="number" name="etage" min="0" value="0"
                           class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                </div>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="ascenseur" class="w-4 h-4 accent-[#c9a96e] rounded">
                <span class="text-sm text-[#1a1a2e]/70">Ascenseur</span>
            </label>
        </div>

        <!-- Maison -->
        <div id="fields-maison" class="hidden space-y-4 border-t border-[#e8e4df] pt-4">
            <p class="text-xs font-bold text-[#c9a96e] uppercase tracking-widest">Maison</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Chambres</label>
                    <input type="number" name="nbChambres" min="1" value="3"
                           class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Surface jardin (m²)</label>
                    <input type="number" name="surfaceJardin" min="0" value="0" step="0.5"
                           class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Garage</label>
                <input type="text" name="garage" placeholder="Garage simple, Double garage..."
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="jardin" class="w-4 h-4 accent-[#c9a96e] rounded">
                <span class="text-sm text-[#1a1a2e]/70">Jardin</span>
            </label>
        </div>

        <!-- Local -->
        <div id="fields-local" class="hidden space-y-4 border-t border-[#e8e4df] pt-4">
            <p class="text-xs font-bold text-[#c9a96e] uppercase tracking-widest">Local commercial</p>
            <div>
                <label class="block text-xs font-semibold text-[#1a1a2e]/50 uppercase tracking-wide mb-2">Activité <span class="text-red-500">*</span></label>
                <input type="text" name="activite" placeholder="Restaurant, Pharmacie..."
                       class="w-full border border-[#e8e4df] rounded-xl px-4 py-2.5 text-sm text-[#1a1a2e] focus:outline-none focus:ring-2 focus:ring-[#c9a96e]">
            </div>
        </div>

        <p class="text-xs text-[#1a1a2e]/40">Les champs marqués <span class="text-red-500">*</span> sont obligatoires.</p>

        <div class="flex gap-3 pt-2 border-t border-[#e8e4df]">
            <button type="submit"
                    class="bg-[#1a1a2e] text-white px-6 py-2.5 rounded-xl hover:bg-[#2a2a4e] transition-colors font-semibold text-sm">
                Enregistrer
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
