<!-- views/success.php -->
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card-custom p-4 p-md-5 text-center">
            <i class="bi bi-check-circle-fill display-1 text-success mb-3"></i>
            <h2 class="fw-bold text-custom-turquoise mb-4">Connexion réussie !</h2>
            <p class="text-muted mb-4">Vous êtes maintenant connecté à votre espace.</p>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="index.php?action=admin_demandes" class="btn btn-primary-custom btn-lg">
                    <i class="bi bi-speedometer2 me-2"></i> Accéder au tableau de bord admin
                </a>
            <?php else: ?>
                <a href="index.php?action=foodtruck_dashboard" class="btn btn-primary-custom btn-lg">
                    <i class="bi bi-truck me-2"></i> Accéder à mon espace FoodTruck
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

