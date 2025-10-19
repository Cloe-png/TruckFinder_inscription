<?php
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'foodtruck') {
    header("Location: index.php?action=login");
    exit;
}
if (!isset($foodtruckData) || empty($foodtruckData)) {
    die("Erreur : données du foodtruck introuvables.");
}
$menu = [];
if (!empty($foodtruckData['menu'])) {
    $decodedMenu = json_decode($foodtruckData['menu'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedMenu)) {
        $menu = $decodedMenu;
    }
}
$horaires = [
    'lundi' => ['ouvert' => true, 'debut' => '09:00', 'fin' => '17:30'],
    'mardi' => ['ouvert' => true, 'debut' => '08:00', 'fin' => '17:30'],
    'mercredi' => ['ouvert' => true, 'debut' => '08:00', 'fin' => '15:30'],
    'jeudi' => ['ouvert' => true, 'debut' => '08:00', 'fin' => '17:30'],
    'vendredi' => ['ouvert' => true, 'debut' => '08:00', 'fin' => '12:00'],
    'samedi' => ['ouvert' => true, 'debut' => '09:00', 'fin' => '18:00'],
    'dimanche' => ['ouvert' => false, 'debut' => '09:00', 'fin' => '18:00']
];
if (!empty($foodtruckData['horaires'])) {
    $decodedHoraires = json_decode($foodtruckData['horaires'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedHoraires)) {
        $horaires = $decodedHoraires;
    }
}
$jours = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
$jourActuel = $jours[date('w')];
$heureActuelle = date('H:i');
$horaireAujourdhui = $horaires[$jourActuel];
$estOuvert = $horaireAujourdhui['ouvert'] && ($heureActuelle >= $horaireAujourdhui['debut'] && $heureActuelle <= $horaireAujourdhui['fin']);
$adresses = [
    ['id' => 1, 'adresse' => $foodtruckData['adresse'] ?? '', 'est_actuelle' => true, 'latitude' => 48.8566, 'longitude' => 2.3522],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Foodtruck - <?= htmlspecialchars($foodtruckData['nom_foodtruck'] ?? 'Mon Foodtruck') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #6b90ffff;
            --secondary-color: #073032ff;
            --success-color: #4CAF50;
            --danger-color: #F44336;
            --warning-color: #FFC107;
            --info-color: #2196F3;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --foodtruck-gradient: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        body {
            background-color: #f5f5f5;
        }

        #map { height: 400px; border-radius: 10px; }

        .foodtruck-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
            transition: transform 0.3s ease;
        }

        .foodtruck-card:hover {
            transform: translateY(-5px);
        }

        .foodtruck-header {
            background: var(--foodtruck-gradient);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px;
        }

        .address-card {
            background-color: var(--light-color);
            border-left: 4px solid var(--secondary-color);
        }

        .menu-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            overflow: hidden;
        }

        .menu-card:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .menu-img {
            height: 150px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
            filter: brightness(0.9);
            transition: filter 0.3s ease;
        }

        .menu-card:hover .menu-img {
            filter: brightness(1);
        }

        .horaire-card {
            background-color: var(--light-color);
            border-left: 4px solid var(--info-color);
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .horaire-card:hover {
            transform: translateY(-3px);
        }

        .horaire-actif {
            background-color: rgba(33, 150, 243, 0.1);
            border-left: 4px solid var(--info-color);
            box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.2);
        }

        .presence-btn {
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .presence-oui {
            background-color: var(--success-color);
            color: white;
        }

        .presence-non {
            background-color: var(--danger-color);
            color: white;
        }

        .statut-badge {
            font-size: 1rem;
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: bold;
        }

        .badge.bg-success {
            background-color: var(--success-color);
        }

        .badge.bg-danger {
            background-color: var(--danger-color);
        }

        .card-title {
            color: var(--dark-color);
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card-body {
            padding: 20px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-secondary {
            background-color: var(--light-color);
            border-color: #dee2e6;
            color: var(--dark-color);
        }

        .btn {
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .fa-icon {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card foodtruck-card">
                    <div class="card-header foodtruck-header d-flex justify-content-between align-items-center">
                        <h3><i class="fas fa-truck fa-lg fa-fw me-2"></i><?= htmlspecialchars($foodtruckData['nom_foodtruck'] ?? 'Mon Foodtruck') ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="card-text"><strong><i class="fas fa-info-circle fa-fw me-2"></i>Description :</strong> <?= htmlspecialchars($foodtruckData['description'] ?? '') ?></p>
                                <p class="card-text"><strong><i class="fas fa-utensils fa-fw me-2"></i>Type de cuisine :</strong> <?= htmlspecialchars($foodtruckData['type_cuisine'] ?? '') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="card-text"><strong><i class="fas fa-phone fa-fw me-2"></i>Téléphone :</strong> <a href="tel:<?= htmlspecialchars($foodtruckData['telephone'] ?? '') ?>"><?= htmlspecialchars($foodtruckData['telephone'] ?? '') ?></a></p>
                               
                                </span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Carte OpenStreetMap -->
            <div class="col-md-6 mb-4">
                <div class="card foodtruck-card h-100">
                    <div class="card-header foodtruck-header">
                        <h5><i class="fas fa-map-marker-alt fa-fw me-2"></i>Où nous trouver ?</h5>
                    </div>
                    <div class="card-body">
                        <div id="map"></div>
                        <div class="mt-3">
                            <h6><i class="fas fa-map-pin fa-fw me-2"></i>Adresse actuelle :</h6>
                            <p><?= htmlspecialchars($adresses[0]['adresse'] ?? '') ?></p>
                            <div class="mt-2">
                                <span><i class="fas fa-check-circle fa-fw me-2"></i>Présent à cette adresse : </span>
                                <span id="presence-indicator" class="presence-btn <?= $adresses[0]['est_actuelle'] ? 'presence-oui' : 'presence-non' ?>" onclick="togglePresence()">
                                    <?= $adresses[0]['est_actuelle'] ? 'Oui' : 'Non' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Menu -->
            <div class="col-md-6 mb-4">
                <div class="card foodtruck-card h-100">
                    <div class="card-header foodtruck-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-utensils fa-fw me-2"></i>Notre Menu</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if (empty($menu)): ?>
                                <div class="col-12 text-center">
                                    <p class="text-muted">Le menu n'est pas encore disponible.</p>
                                    <a href="edit_menu.php?action=edit_menu&id=<?= $foodtruckData['id_foodtruck'] ?>" class="btn btn-primary mt-2"><i class="fas fa-plus fa-fw me-2"></i>Ajouter des plats</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($menu as $plat): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card menu-card h-100">
                                            <img src="<?= htmlspecialchars($plat['image'] ?? 'https://via.placeholder.com/150?text=Plat') ?>" class="card-img-top menu-img" alt="<?= htmlspecialchars($plat['nom'] ?? 'Plat') ?>">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($plat['nom'] ?? 'Plat') ?></h5>
                                                <p class="card-text"><?= htmlspecialchars($plat['description'] ?? '') ?></p>
                                                <p class="card-text"><strong><?= number_format($plat['prix'] ?? 0, 2) ?> €</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card foodtruck-card">
                    <div class="card-header foodtruck-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-clock fa-fw me-2"></i>Nos Horaires</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($horaires)): ?>
                            <div class="text-center">
                                <p class="text-muted">Les horaires d'ouverture ne sont pas encore définis.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($horaires as $jour => $horaire): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card horaire-card h-100 <?= $jour === $jourActuel ? 'horaire-actif' : '' ?>">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <i class="fas <?= $jour === $jourActuel ? 'fa-calendar-day text-primary' : 'fa-calendar-alt' ?> fa-fw me-2"></i>
                                                    <?= htmlspecialchars(ucfirst($jour)) ?>
                                                </h5>
                                                <p class="card-text">
                                                    <?= $horaire['ouvert'] ?
                                                        '<span class="badge bg-success"><i class="fas fa-check-circle fa-fw me-1"></i>Ouvert</span>' :
                                                        '<span class="badge bg-danger"><i class="fas fa-times-circle fa-fw me-1"></i>Fermé</span>' ?><br>
                                                    <?= $horaire['ouvert'] ?
                                                        '<i class="fas fa-clock fa-fw me-1"></i>' . $horaire['debut'] . ' - ' . $horaire['fin'] :
                                                        '<i class="fas fa-clock fa-fw me-1"></i>Fermé' ?>
                                                </p>
                                                <?php if ($jour === $jourActuel): ?>
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar-check fa-fw me-1"></i>Aujourd'hui -
                                                            <?= $estOuvert ?
                                                                '<span class="text-success"><i class="fas fa-check-circle fa-fw me-1"></i>Ouvert</span>' :
                                                                '<span class="text-danger"><i class="fas fa-times-circle fa-fw me-1"></i>Fermé</span>' ?>
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('ajouter-adresse').addEventListener('click', function() {
            const container = document.getElementById('adresses-container');
            const newAddressDiv = document.createElement('div');
            newAddressDiv.className = 'card address-card mb-3';
            newAddressDiv.innerHTML = `
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="adresses[]" required>
                        </div>
                        <div class="col-md-3 d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="adresse_actuelle" id="new_adresse" value="new" required>
                                <label class="form-check-label" for="new_adresse">
                                    <i class="fas fa-map-marker-alt fa-fw me-1"></i>Actuellement ici
                                </label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="adresses_ids[]" value="new">
                    <input type="hidden" name="latitudes[]" value="">
                    <input type="hidden" name="longitudes[]" value="">
                </div>
            `;
            container.appendChild(newAddressDiv);
        });

        function togglePresence() {
            const presenceIndicator = document.getElementById('presence-indicator');
            if (presenceIndicator.classList.contains('presence-oui')) {
                presenceIndicator.textContent = 'Non';
                presenceIndicator.classList.remove('presence-oui');
                presenceIndicator.classList.add('presence-non');
                presenceIndicator.innerHTML = '<i class="fas fa-times-circle fa-fw me-1"></i>Non';
            } else {
                presenceIndicator.textContent = 'Oui';
                presenceIndicator.classList.remove('presence-non');
                presenceIndicator.classList.add('presence-oui');
                presenceIndicator.innerHTML = '<i class="fas fa-check-circle fa-fw me-1"></i>Oui';
            }
        }

        function updateStatut() {
            const horaires = <?php echo json_encode($horaires); ?>;
            const jours = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
            const jourActuel = jours[new Date().getDay()];
            const heureActuelle = new Date().toTimeString().substring(0, 5);

            const horaireAujourdhui = horaires[jourActuel];
            const estOuvert = horaireAujourdhui.ouvert && (heureActuelle >= horaireAujourdhui.debut && heureActuelle <= horaireAujourdhui.fin);

            // Mettre à jour le statut global
            const statutGlobal = document.getElementById('statut-global');
            statutGlobal.classList.remove('bg-success', 'bg-danger');
            statutGlobal.classList.add(estOuvert ? 'bg-success' : 'bg-danger');
            statutGlobal.innerHTML = estOuvert ?
                '<i class="fas fa-check-circle fa-fw me-1"></i>Ouvert' :
                '<i class="fas fa-times-circle fa-fw me-1"></i>Fermé';

            // Mettre à jour l'indicateur dans les horaires
            document.querySelectorAll('.horaire-actif small.text-muted').forEach(el => {
                el.innerHTML = 'Aujourd\'hui - ' + (estOuvert ?
                    '<span class="text-success"><i class="fas fa-check-circle fa-fw me-1"></i>Ouvert</span>' :
                    '<span class="text-danger"><i class="fas fa-times-circle fa-fw me-1"></i>Fermé</span>');
            });
        }

        // Mettre à jour le statut immédiatement
        updateStatut();

        // Mettre à jour le statut toutes les minutes
        setInterval(updateStatut, 60000);
    </script>
    <!-- Intégration de Leaflet pour les cartes -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([<?= $adresses[0]['latitude'] ?? 48.8566 ?>, <?= $adresses[0]['longitude'] ?? 2.3522 ?>], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        const marker = L.marker([<?= $adresses[0]['latitude'] ?? 48.8566 ?>, <?= $adresses[0]['longitude'] ?? 2.3522 ?>]).addTo(map);
        marker.bindPopup("<b><?= htmlspecialchars($foodtruckData['nom_foodtruck'] ?? 'Mon Foodtruck') ?></b><br><?= htmlspecialchars($adresses[0]['adresse'] ?? '') ?>").openPopup();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
