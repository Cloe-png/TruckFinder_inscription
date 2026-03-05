<?php
$action = $action ?? 'admin_demandes';
$demandes = $demandes ?? [];
$alert = $_GET['alert'] ?? null;

$alert_html = '';
if ($alert) {
    $alerts = [
        'success' => ['message' => 'Demande traitée avec succès.', 'type' => 'success'],
        'error' => ['message' => 'Erreur lors du traitement de la demande.', 'type' => 'danger'],
    ];

    if (isset($alerts[$alert])) {
        $alert_html = '<div class="alert alert-' . $alerts[$alert]['type'] . ' alert-dismissible fade show" role="alert">' .
            $alerts[$alert]['message'] .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }

    if (isset($_GET['detail'])) {
        $alert_html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">' .
            htmlspecialchars($_GET['detail']) .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}

$total = count($demandes);
$pending = count(array_filter($demandes, static fn($d) => ($d['statut'] ?? '') === 'en_attente'));
$approved = count(array_filter($demandes, static fn($d) => ($d['statut'] ?? '') === 'approuve'));
$rejected = count(array_filter($demandes, static fn($d) => ($d['statut'] ?? '') === 'rejete'));
?>

<style>
    .ad-card { border: 0; border-radius: 14px; box-shadow: 0 10px 22px rgba(0,0,0,.07); }
    .ad-kpi { border-radius: 12px; padding: 14px; color: #fff; }
    .ad-kpi h3 { margin: 0; }
    .ad-head { background: linear-gradient(135deg,#1f4c5c,#2b6777); color:#fff; border-radius:14px 14px 0 0; }
</style>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="ad-kpi" style="background:#1f4c5c"><small>Total</small><h3><?= $total ?></h3></div></div>
    <div class="col-md-3"><div class="ad-kpi" style="background:#f39c12"><small>En attente</small><h3><?= $pending ?></h3></div></div>
    <div class="col-md-3"><div class="ad-kpi" style="background:#27ae60"><small>Approuvés</small><h3><?= $approved ?></h3></div></div>
    <div class="col-md-3"><div class="ad-kpi" style="background:#c0392b"><small>Rejetés</small><h3><?= $rejected ?></h3></div></div>
</div>

<div class="card ad-card">
    <div class="card-header ad-head d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><?= $action === 'admin_demandes' ? 'Demandes en attente' : 'Tous les foodtrucks' ?></h4>
        <div class="d-flex gap-2">
            <a href="index.php?action=admin_demandes" class="btn btn-light btn-sm">Demandes</a>
            <a href="index.php?action=admin_foodtrucks" class="btn btn-outline-light btn-sm">Tous</a>
            <form method="post" action="index.php?action=delete_account" class="m-0" onsubmit="return confirm('Confirmer la suppression de votre compte admin ?');">
                <button type="submit" class="btn btn-danger btn-sm">Supprimer mon compte</button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <?= $alert_html ?>

        <?php if (empty($demandes)): ?>
            <div class="alert alert-info mb-0">Aucune donnée à afficher.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Foodtruck</th>
                            <th>Gérant</th>
                            <th>Contact</th>
                            <th>Cuisine</th>
                            <th>Adresse</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($demandes as $demande): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($demande['nom_foodtruck']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($demande['description'] ?? '') ?></small>
                                </td>
                                <td><?= htmlspecialchars($demande['nom']) ?></td>
                                <td>
                                    <div><?= htmlspecialchars($demande['email']) ?></div>
                                    <small><?= htmlspecialchars($demande['telephone'] ?? '') ?></small>
                                </td>
                                <td><?= htmlspecialchars($demande['type_cuisine'] ?? '') ?></td>
                                <td><?= htmlspecialchars($demande['adresse'] ?? '') ?></td>
                                <td>
                                    <?php $st = $demande['statut'] ?? 'en_attente'; ?>
                                    <span class="badge <?= $st === 'approuve' ? 'bg-success' : ($st === 'rejete' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                        <?= htmlspecialchars($st) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($demande['date_demande'])): ?>
                                        <?= (new DateTime($demande['date_demande']))->format('d/m/Y H:i') ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (($demande['statut'] ?? '') === 'en_attente'): ?>
                                        <div class="d-flex gap-1">
                                            <a href="index.php?action=valider_demande&id_foodtruck=<?= (int)$demande['id_foodtruck'] ?>&statut=approuve" class="btn btn-success btn-sm">Approuver</a>
                                            <a href="index.php?action=valider_demande&id_foodtruck=<?= (int)$demande['id_foodtruck'] ?>&statut=rejete" class="btn btn-outline-danger btn-sm">Rejeter</a>
                                        </div>
                                    <?php endif; ?>
                                    <form method="post" action="index.php?action=delete_foodtruck" class="mt-1" onsubmit="return confirm('Supprimer définitivement ce compte foodtruck ?');">
                                        <input type="hidden" name="id_foodtruck" value="<?= (int)$demande['id_foodtruck'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
