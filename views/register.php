<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-person-plus-fill display-6 text-warning mb-3"></i>
                <h2 class="fw-bold text-custom-turquoise">Créer votre compte FoodTruck</h2>
                <p class="text-muted">Rejoignez notre communauté et boostez votre visibilité !</p>
            </div>
            <form action="index.php?action=do_register" method="post" class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom du gérant</label>
                        <input type="text" class="form-control-custom form-control" id="nom" name="nom" required>
                        <div class="invalid-feedback">Veuillez entrer votre nom.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control-custom form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Veuillez entrer un email valide.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control-custom form-control" id="mot_de_passe" name="mot_de_passe" minlength="6" required>
                        <div class="invalid-feedback">Le mot de passe doit faire au moins 6 caractères.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="nom_foodtruck" class="form-label">Nom du Food Truck</label>
                        <input type="text" class="form-control-custom form-control" id="nom_foodtruck" name="nom_foodtruck" required>
                        <div class="invalid-feedback">Veuillez entrer le nom de votre food truck.</div>
                    </div>
                    <div class="col-12">
                        <label for="type_cuisine" class="form-label">Type de cuisine</label>
                        <select class="form-select form-control-custom" id="type_cuisine" name="type_cuisine" required>
                            <option value="" selected disabled>Sélectionnez...</option>
                            <option value="Burger">Burger</option>
                            <option value="Pizza">Pizza</option>
                            <option value="Asiatique">Asiatique</option>
                            <option value="Mexicain">Mexicain</option>
                            <option value="Végétarien">Végétarien</option>
                            <option value="Kebab">Kebab / Gyros</option>
                            <option value="Sandwich">Sandwich / Panini</option>
                            <option value="PouletFrit">Poulet frit</option>
                            <option value="Tacos">Tacos</option>
                            <option value="Crepes">Crêpes / Gaufres</option>
                            <option value="Glace">Glaces / Frozen Yogurt</option>
                            <option value="Barbecue">Barbecue / Grillades</option>
                            <option value="PokeBowl">Poké Bowl / Healthy</option>
                            <option value="Indien">Indien (Curry, Naan...)</option>
                            <option value="Oriental">Oriental (Tajine, Couscous...)</option>
                            <option value="Autre">Autre</option>
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner un type de cuisine.</div>
                    </div>

                    <div class="col-12 mt-3">
                        <div class="card border-2a3547">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Menu de base</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Nous allons créer un menu de base pour votre foodtruck avec ces 3 éléments. Vous pourrez les modifier plus tard.</p>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">Menu du jour</h5>
                                                <p class="card-text">Notre spécialité du moment</p>
                                                <p>Je n'en ai aucune idée.</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">12,50 €</small>
                                                    <i class="bi bi-hamburger text-warning"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">Boisson</h5>
                                                <p class="card-text">Boisson au choix</p>
                                                <p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">2,50 €</small>
                                                    <i class="bi bi-cup-straw text-primary"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">Dessert</h5>
                                                <p class="card-text">Dessert maison</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">4,00 €</small>
                                                    <i class="bi bi-cake text-danger"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control-custom form-control" id="description" name="description" rows="3" required></textarea>
                        <div class="invalid-feedback">Veuillez entrer une description.</div>
                    </div>
                      
                    <div class="col-md-6">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control-custom form-control" id="adresse" name="adresse" required>
                        <div class="invalid-feedback">Veuillez entrer votre adresse.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control-custom form-control" id="telephone" name="telephone" required>
                        <div class="invalid-feedback">Veuillez entrer votre numéro de téléphone.</div>
                    </div>

                  
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button class="btn btn-primary-custom btn-lg" type="submit">
                        <i class="bi bi-send-fill me-2"></i> S'inscrire
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
})
</script>
