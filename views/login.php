<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card-custom p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-box-arrow-in-right display-6 text-warning mb-3"></i>
                <h2 class="fw-bold text-custom-turquoise">Connexion</h2>
                <p class="text-muted">Connectez-vous pour gérer votre food truck ou découvrir nos services.</p>
            </div>

            <form action="index.php?action=do_login" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control-custom form-control" id="email" name="email" required>
                    <div class="invalid-feedback">
                        Veuillez entrer un email valide.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="mot_de_passe" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control-custom form-control" id="mot_de_passe" name="mot_de_passe" required>
                    <div class="invalid-feedback">
                        Veuillez entrer votre mot de passe.
                    </div>
                </div>
                <div class="d-grid">
                    <button class="btn btn-primary-custom btn-lg" type="submit">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p class="mb-0">Pas encore de compte ? <a href="index.php?action=register" class="text-custom-turquoise text-decoration-none fw-bold">S'inscrire</a></p>
            </div>
        </div>
    </div>
</div>
