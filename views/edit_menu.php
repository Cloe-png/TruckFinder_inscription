<?php
if (!isset($_SESSION['id_utilisateur']) || ($_SESSION['role'] ?? '') !== 'foodtruck') {
    header('Location: index.php?action=login');
    exit;
}

if (!isset($foodtruckData) || empty($foodtruckData)) {
    die('Erreur: données du foodtruck introuvables.');
}

$menu = [];
if (!empty($foodtruckData['menu'])) {
    $decodedMenu = json_decode($foodtruckData['menu'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedMenu)) {
        $menu = $decodedMenu;
    }
}
?>

<style>
    .menu-card { border: 0; border-radius: 14px; box-shadow: 0 8px 24px rgba(0,0,0,.08); }
    .menu-head { background: linear-gradient(120deg,#ff6b6b,#ff8e53); color: #fff; border-radius: 14px 14px 0 0; }
</style>

<div class="card menu-card">
    <div class="card-header menu-head d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Édition du menu</h4>
        <a href="index.php?action=foodtruck_dashboard" class="btn btn-light btn-sm">Retour dashboard</a>
    </div>
    <div class="card-body">
        <form action="index.php?action=save_menu" method="post">
            <div id="plats-container">
                <?php if (empty($menu)): ?>
                    <p class="text-muted">Aucun plat. Ajoute ton premier plat ci-dessous.</p>
                <?php else: ?>
                    <?php foreach ($menu as $index => $plat): ?>
                        <div class="card mb-3 plat-card">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3"><input type="text" class="form-control" name="plats[<?= $index ?>][nom]" value="<?= htmlspecialchars($plat['nom'] ?? '') ?>" required></div>
                                    <div class="col-md-4"><input type="text" class="form-control" name="plats[<?= $index ?>][description]" value="<?= htmlspecialchars($plat['description'] ?? '') ?>" required></div>
                                    <div class="col-md-2"><input type="number" step="0.01" class="form-control" name="plats[<?= $index ?>][prix]" value="<?= htmlspecialchars((string)($plat['prix'] ?? '0')) ?>" required></div>
                                    <div class="col-md-2"><input type="url" class="form-control" name="plats[<?= $index ?>][image]" value="<?= htmlspecialchars($plat['image'] ?? '') ?>"></div>
                                    <div class="col-md-1 text-end"><button type="button" class="btn btn-outline-danger remove-plat">X</button></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2">
                <button type="button" id="add-plat" class="btn btn-outline-primary">+ Ajouter un plat</button>
                <button type="submit" class="btn btn-primary">Enregistrer le menu</button>
            </div>
        </form>
    </div>
</div>

<script>
    const platsContainer = document.getElementById('plats-container');
    const addPlatBtn = document.getElementById('add-plat');

    addPlatBtn.addEventListener('click', function() {
        const index = platsContainer.querySelectorAll('.plat-card').length;
        const card = document.createElement('div');
        card.className = 'card mb-3 plat-card';
        card.innerHTML = `
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3"><input type="text" class="form-control" name="plats[${index}][nom]" placeholder="Nom" required></div>
                    <div class="col-md-4"><input type="text" class="form-control" name="plats[${index}][description]" placeholder="Description" required></div>
                    <div class="col-md-2"><input type="number" step="0.01" class="form-control" name="plats[${index}][prix]" placeholder="Prix" required></div>
                    <div class="col-md-2"><input type="url" class="form-control" name="plats[${index}][image]" placeholder="URL image"></div>
                    <div class="col-md-1 text-end"><button type="button" class="btn btn-outline-danger remove-plat">X</button></div>
                </div>
            </div>
        `;
        platsContainer.appendChild(card);
    });

    // Delegation: works for existing and future remove buttons.
    platsContainer.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-plat');
        if (!button) {
            return;
        }
        const card = button.closest('.plat-card');
        if (card) {
            card.remove();
        }
    });
</script>

