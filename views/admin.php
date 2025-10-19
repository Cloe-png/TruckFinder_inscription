<?php
// Initialisation des variables si elles n'existent pas
$action = $action ?? 'admin_demandes';
$demandes = $demandes ?? [];
$alert = $_GET['alert'] ?? null;

$alert_html = '';
if ($alert) {
    $alerts = [
        'success' => ['message' => 'Demande traitée avec succès !', 'type' => 'success'],
        'error' => ['message' => 'Erreur lors du traitement de la demande.', 'type' => 'danger'],
    ];

    if (isset($alerts[$alert])) {
        $alert_html = '<div class="alert alert-' . $alerts[$alert]['type'] . ' alert-dismissible fade show" role="alert">
                          ' . $alerts[$alert]['message'] . '
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                       </div>';
    }
    if (isset($_GET['detail'])) {
        $alert_html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                          ' . htmlspecialchars($_GET['detail']) . '
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                       </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TruckFinder - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #2e59d9;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --sidebar-width: 250px;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
        }
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            width: var(--sidebar-width);
            background-color: white;
        }
        .sidebar .nav-link {
            font-weight: 500;
            color: var(--dark-color);
            padding: 10px 20px;
            margin: 2px 0;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(78, 115, 223, 0.1);
            color: var(--primary-color);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
        }
        .admin-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border: none;
        }
        .admin-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }
        .demande-card {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .demande-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .demande-header {
            background-color: rgba(78, 115, 223, 0.05);
            border-bottom: 1px solid rgba(78, 115, 223, 0.1);
            padding: 12px 15px;
            border-radius: 10px 10px 0 0;
        }
        .statut-badge {
            font-size: 0.8rem;
            padding: 0.3em 0.6em;
            border-radius: 15px;
            font-weight: 500;
        }
        .action-btn {
            margin: 0 5px;
            min-width: 90px;
            font-weight: 500;
        }
        .no-demandes {
            background-color: rgba(248, 249, 250, 0.7);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            border: 1px dashed rgba(0, 0, 0, 0.1);
        }
        .search-container {
            margin-bottom: 20px;
        }
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 0 20px;
        }
        .footer {
            background-color: white;
            padding: 20px;
            text-align: center;
            margin-left: var(--sidebar-width);
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        .card-title {
            font-weight: 600;
            color: var(--dark-color);
        }
        .foodtruck-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .footer {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="d-flex align-items-center justify-content-center py-3">
            <h4 class="mb-0 text-primary">
                <i class="bi bi-truck me-2"></i>TruckFinder
            </h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $action === 'admin_demandes' ? 'active' : '' ?>" href="index.php?action=admin_demandes">
                    <i class="bi bi-clipboard-check"></i> Demandes
                </a>
            </li>
            <li class="nav-item mt-auto">
                <a class="nav-link text-danger" href="index.php?action=deconnexion">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </li>
        </ul>
    </div>
    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card admin-card">
                        <div class="card-header admin-header d-flex justify-content-between align-items-center">
                            <h2 class="mb-0">
                                <i class="bi bi-<?= $action === 'admin_demandes' ? 'clipboard-check' : 'truck' ?> me-2"></i>
                                <?= $action === 'admin_demandes' ? 'Demandes en attente' : 'Tous les Foodtrucks' ?>
                            </h2>
                        </div>
                        <div class="card-body">
                            <?= $alert_html ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0 text-muted">
                                    <?= empty($demandes) ? 'Aucun' : count($demandes) ?> <?= $action === 'admin_demandes' ? 'demande en attente' : 'foodtruck' . (count($demandes) > 1 ? 's' : '') ?>
                                </h5>
                                <?php if ($action === 'admin_demandes'): ?>
                                    <a href="index.php?action=admin_foodtrucks" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-eye me-1"></i>Voir tous
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?action=admin_demandes" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-clipboard-check me-1"></i>En attente
                                    </a>
                                <?php endif; ?>
                            </div>

                            <?php if (empty($demandes)): ?>
                                <div class="no-demandes">
                                    <i class="bi bi-<?= $action === 'admin_demandes' ? 'clipboard-x' : 'truck' ?> display-4 text-muted mb-3"></i>
                                    <h4 class="text-muted">
                                        <?= $action === 'admin_demandes' ? 'Aucune demande en attente' : 'Aucun foodtruck enregistré' ?>
                                    </h4>
                                    <p class="text-muted mb-0">
                                        <?= $action === 'admin_demandes' ? 'Toutes les demandes ont été traitées.' : 'Aucun foodtruck n\'a encore été enregistré.' ?>
                                    </p>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($demandes as $demande): ?>
                                        <div class="col-md-6 col-lg-4 mb-3" data-foodtruck="<?= strtolower($demande['nom_foodtruck']) ?>" data-gerant="<?= strtolower($demande['nom']) ?>">
                                            <div class="card demande-card h-100">
                                                <div class="demande-header">
                                                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                                                        <?= htmlspecialchars($demande['nom_foodtruck']) ?>
                                                        <span class="badge <?= $demande['statut'] === 'en_attente' ? 'bg-warning' : ($demande['statut'] === 'approuvé' ? 'bg-success' : 'bg-danger') ?> statut-badge">
                                                            <?= ucfirst(str_replace('_', ' ', $demande['statut'])) ?>
                                                        </span>
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="text-center mb-3">
                                                        <img src="<?= !empty($demande['logo']) ? htmlspecialchars($demande['logo']) : 'https://via.placeholder.com/300x150?text=' . urlencode($demande['nom_foodtruck']) ?>"
                                                             class="foodtruck-image" alt="<?= htmlspecialchars($demande['nom_foodtruck']) ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="d-flex mb-2">
                                                            <i class="bi bi-person-fill text-primary me-2"></i>
                                                            <div>
                                                                <small class="text-muted">Gérant</small>
                                                                <p class="mb-0"><?= htmlspecialchars($demande['nom']) ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex mb-2">
                                                            <i class="bi bi-envelope-fill text-primary me-2"></i>
                                                            <div>
                                                                <small class="text-muted">Email</small>
                                                                <p class="mb-0"><?= htmlspecialchars($demande['email']) ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex mb-2">
                                                            <i class="bi bi-telephone-fill text-primary me-2"></i>
                                                            <div>
                                                                <small class="text-muted">Téléphone</small>
                                                                <p class="mb-0"><?= htmlspecialchars($demande['telephone']) ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex mb-2">
                                                            <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                                            <div>
                                                                <small class="text-muted">Adresse</small>
                                                                <p class="mb-0"><?= htmlspecialchars($demande['adresse']) ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex">
                                                            <i class="bi bi-tag-fill text-primary me-2"></i>
                                                            <div>
                                                                <small class="text-muted">Type de cuisine</small>
                                                                <p class="mb-0"><?= htmlspecialchars($demande['type_cuisine']) ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                        <span class="text-muted small">
                                                            <i class="bi bi-calendar-event-fill me-1"></i>
                                                            <?= (new DateTime($demande['date_demande']))->format('d/m/Y H:i') ?>
                                                        </span>
                                                        <?php if ($demande['statut'] === 'en_attente'): ?>
                                                            <div>
                                                                <a href="index.php?action=valider_demande&id_foodtruck=<?= $demande['id_foodtruck'] ?>&statut=approuvé"
                                                                   class="btn btn-success btn-sm action-btn">
                                                                    <i class="bi bi-check-circle-fill me-1"></i>Approuver
                                                                </a>
                                                                <a href="index.php?action=valider_demande&id_foodtruck=<?= $demande['id_foodtruck'] ?>&statut=rejeté"
                                                                   class="btn btn-outline-danger btn-sm action-btn">
                                                                    <i class="bi bi-x-circle-fill me-1"></i>Rejeter
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
</body>
</html>
