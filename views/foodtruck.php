<?php
if (!isset($_SESSION['id_utilisateur']) || ($_SESSION['role'] ?? '') !== 'foodtruck') {
    header('Location: index.php?action=login');
    exit;
}

if (!isset($foodtruckData) || empty($foodtruckData)) {
    die('Erreur : données foodtruck introuvables.');
}

$menu = [];
if (!empty($foodtruckData['menu'])) {
    $decodedMenu = json_decode($foodtruckData['menu'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedMenu)) {
        $menu = $decodedMenu;
    }
}

$adresses = $adresses ?? [];
$adressesPresentes = array_values(array_filter($adresses, static fn($a) => !empty($a['est_present'])));
$adresseActive = !empty($adressesPresentes) ? $adressesPresentes[0] : (!empty($adresses) ? $adresses[0] : null);
$latCarte = isset($adresseActive['latitude']) && $adresseActive['latitude'] !== null ? (float)$adresseActive['latitude'] : 48.8566;
$lngCarte = isset($adresseActive['longitude']) && $adresseActive['longitude'] !== null ? (float)$adresseActive['longitude'] : 2.3522;

$joursIndex = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
$jourActuel = $joursIndex[(int)date('w')];
$heureActuelle = date('H:i');
$etatOuverture = ['ouvert' => false, 'message' => 'Fermé'];
if (isset($horaires[$jourActuel])) {
    $h = $horaires[$jourActuel];
    $debut = $h['debut'] ?? '00:00';
    $fin = $h['fin'] ?? '00:00';
    $ouvertJour = !empty($h['ouvert']);
    $ouvertMaintenant = $ouvertJour && $heureActuelle >= $debut && $heureActuelle <= $fin;
    $etatOuverture = [
        'ouvert' => $ouvertMaintenant,
        'message' => $ouvertMaintenant
            ? 'Ouvert maintenant (' . $debut . ' - ' . $fin . ')'
            : 'Fermé actuellement'
    ];
}
?>

<style>
    @import url('https://unpkg.com/leaflet@1.7.1/dist/leaflet.css');

    .tf-card { border: 0; border-radius: 16px; box-shadow: 0 10px 26px rgba(0, 0, 0, .08); }
    .tf-head { background: linear-gradient(120deg, #184e77, #1f7a8c); color: #fff; border-radius: 16px 16px 0 0; }
    .tf-pill { border-radius: 999px; }
    .tf-subtle { color: #6c757d; font-size: .92rem; }
    .tf-section-title { font-weight: 700; margin-bottom: .8rem; }

    #map {
        height: 420px;
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }

    .leaflet-container {
        height: 100%;
        width: 100%;
        overflow: hidden;
        background: #f6f8fa;
    }
</style>

<div class="row g-4">
    <div class="col-12">
        <div class="card tf-card">
            <div class="card-header tf-head d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?= htmlspecialchars($foodtruckData['nom_foodtruck'] ?? 'Mon FoodTruck') ?></h4>
                <div class="d-flex gap-2">
                    <form method="post" action="index.php?action=delete_account" class="m-0" onsubmit="return confirm('Confirmer la suppression de votre compte ? Cette action est définitive.');">
                        <button type="submit" class="btn btn-outline-light btn-sm tf-pill">Supprimer mon compte</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <p class="mb-1"><strong>Description :</strong> <?= htmlspecialchars($foodtruckData['description'] ?? '') ?></p>
                        <p class="mb-1"><strong>Type de cuisine :</strong> <?= htmlspecialchars($foodtruckData['type_cuisine'] ?? '') ?></p>
                        <p class="mb-0"><strong>Téléphone :</strong> <?= htmlspecialchars($foodtruckData['telephone'] ?? '') ?></p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge tf-pill <?= ($foodtruckData['statut'] ?? '') === 'approuve' ? 'bg-success' : 'bg-warning text-dark' ?>">
                            Statut : <?= htmlspecialchars($foodtruckData['statut'] ?? 'en_attente') ?>
                        </span>
                        <div class="mt-2">
                            <span class="badge tf-pill <?= $etatOuverture['ouvert'] ? 'bg-success' : 'bg-danger' ?>">
                                <?= htmlspecialchars($etatOuverture['message']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card tf-card h-100">
            <div class="card-body">
                <div class="tf-section-title">Mes adresses</div>
                <form method="post" action="index.php?action=save_adresses">
                    <div id="adresses-container">
                        <?php foreach ($adresses as $index => $adresse): ?>
                            <div class="row g-2 mb-2 adresse-row align-items-center">
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="adresses[<?= $index ?>]" value="<?= htmlspecialchars($adresse['adresse'] ?? '') ?>" required>
                                    <input type="hidden" name="latitudes[<?= $index ?>]" value="<?= htmlspecialchars((string)($adresse['latitude'] ?? '')) ?>">
                                    <input type="hidden" name="longitudes[<?= $index ?>]" value="<?= htmlspecialchars((string)($adresse['longitude'] ?? '')) ?>">
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-adresse">X</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="button" id="add-adresse" class="btn btn-outline-primary">+ Ajouter</button>
                        <button type="submit" class="btn btn-primary">Enregistrer les adresses</button>
                    </div>
                </form>

                <hr>

                <div class="tf-section-title">Présence du jour</div>
                <p class="tf-subtle mb-2">Clique sur une adresse pour indiquer où tu seras présent.</p>

                <div class="list-group shadow-sm">
                    <?php if (empty($adresses)): ?>
                        <div class="list-group-item">Aucune adresse enregistrée.</div>
                    <?php else: ?>
                        <?php foreach ($adresses as $adresse): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center <?= !empty($adresse['est_present']) ? 'list-group-item-success' : '' ?>">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge rounded-pill text-bg-secondary">Adresse</span>
                                    <span><?= htmlspecialchars($adresse['adresse'] ?? '') ?></span>
                                    <?php if (!empty($adresse['est_present'])): ?>
                                        <span class="badge bg-success">Actuelle</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($adresse['id_adresse'])): ?>
                                    <form method="post" action="index.php?action=set_presence_adresse" class="m-0">
                                        <input type="hidden" name="id_adresse" value="<?= (int)$adresse['id_adresse'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success">Je serai ici</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card tf-card h-100">
            <div class="card-body">
                <div class="tf-section-title">Carte</div>
                <div id="map"></div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card tf-card mb-4">
            <div class="card-body">
                <div class="tf-section-title">Horaires d'ouverture</div>
                <p class="tf-subtle">Tu peux modifier tes heures d'ouverture pour chaque jour.</p>
                <form method="post" action="index.php?action=save_horaires">
                    <div class="row g-2">
                        <?php foreach (($horaires ?? []) as $jour => $h): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong><?= htmlspecialchars(ucfirst($jour)) ?></strong>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="horaires[<?= $jour ?>][ouvert]" value="1" <?= !empty($h['ouvert']) ? 'checked' : '' ?>>
                                        </div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small">Début</label>
                                            <input type="time" class="form-control form-control-sm" name="horaires[<?= $jour ?>][debut]" value="<?= htmlspecialchars($h['debut'] ?? '11:00') ?>">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">Fin</label>
                                            <input type="time" class="form-control form-control-sm" name="horaires[<?= $jour ?>][fin]" value="<?= htmlspecialchars($h['fin'] ?? '14:30') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Enregistrer les horaires</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card tf-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="tf-section-title mb-0">Menu</div>
                    <a href="index.php?action=edit_menu" class="btn btn-outline-primary btn-sm">Modifier</a>
                </div>

                <?php if (empty($menu)): ?>
                    <p class="text-muted mb-0">Aucun plat pour le moment.</p>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($menu as $plat): ?>
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <h6 class="mb-1"><?= htmlspecialchars($plat['nom'] ?? '') ?></h6>
                                    <p class="small text-muted mb-2"><?= htmlspecialchars($plat['description'] ?? '') ?></p>
                                    <strong><?= number_format((float)($plat['prix'] ?? 0), 2) ?> €</strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    const map = L.map('map', { zoomControl: true }).setView([<?= $latCarte ?>, <?= $lngCarte ?>], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const addresses = <?= json_encode($adresses, JSON_UNESCAPED_UNICODE) ?>;
    let activeAddress = null;
    const markers = [];

    addresses.forEach((item) => {
        const lat = item.latitude !== null ? parseFloat(item.latitude) : null;
        const lng = item.longitude !== null ? parseFloat(item.longitude) : null;
        if (lat !== null && lng !== null && !Number.isNaN(lat) && !Number.isNaN(lng)) {
            const marker = L.marker([lat, lng]).addTo(map).bindPopup(item.adresse || 'Adresse');
            markers.push(marker);
            if (item.est_present) {
                activeAddress = { ...item, lat, lng, marker };
            }
        } else if (item.est_present) {
            activeAddress = { ...item, lat: null, lng: null, marker: null };
        }
    });

    if (activeAddress && activeAddress.marker) {
        map.setView([activeAddress.lat, activeAddress.lng], 15);
        activeAddress.marker.openPopup();
    } else if (markers.length > 0) {
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.2));
    }

    if (activeAddress && !activeAddress.marker && activeAddress.adresse) {
        const query = encodeURIComponent(activeAddress.adresse);
        fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${query}`)
            .then((res) => res.json())
            .then((data) => {
                if (!Array.isArray(data) || data.length === 0) {
                    return;
                }
                const lat = parseFloat(data[0].lat);
                const lon = parseFloat(data[0].lon);
                if (Number.isNaN(lat) || Number.isNaN(lon)) {
                    return;
                }
                const marker = L.marker([lat, lon]).addTo(map).bindPopup(activeAddress.adresse);
                map.setView([lat, lon], 15);
                marker.openPopup();
            })
            .catch(() => {});
    }

    setTimeout(() => map.invalidateSize(), 100);

    const container = document.getElementById('adresses-container');
    const addBtn = document.getElementById('add-adresse');

    function bindRemove() {
        document.querySelectorAll('.remove-adresse').forEach((btn) => {
            btn.onclick = function () {
                const rows = container.querySelectorAll('.adresse-row');
                if (rows.length > 1) {
                    this.closest('.adresse-row').remove();
                }
            };
        });
    }

    addBtn.addEventListener('click', () => {
        const index = container.querySelectorAll('.adresse-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 adresse-row align-items-center';
        row.innerHTML = `
            <div class="col-md-10">
                <input type="text" class="form-control" name="adresses[${index}]" required>
                <input type="hidden" name="latitudes[${index}]" value="">
                <input type="hidden" name="longitudes[${index}]" value="">
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-adresse">X</button>
            </div>
        `;
        container.appendChild(row);
        bindRemove();
    });

    bindRemove();
</script>
