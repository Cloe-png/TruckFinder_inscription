<?php
session_start();
require_once '../config/database.php';

// Gestion des actions
$action = $_GET['action'] ?? 'home';
$alert = $_GET['alert'] ?? null;
$content = '';
$navigation = '';
$title = 'FoodTruck App';
$demandes_html = '';

// Traitement des formulaires (inchangé)
if ($action === 'do_login') {
    // ... (logique de connexion)
}
else if ($action === 'do_register') {
    // ... (logique d'inscription)
}
else if ($action === 'deconnexion') {
    session_destroy();
    header('Location: index.php?alert=logout_success');
    exit;
}

// Préparation de la navigation
if (isset($_SESSION['role'])) {
    $navigation = '<a href="index.php?action=deconnexion" class="btn btn-danger">Déconnexion</a>';
    if ($_SESSION['role'] === 'admin') {
        $navigation .= ' <a href="index.php?action=admin" class="btn btn-primary">Admin</a>';
    }
} else {
    $navigation = '<a href="index.php" class="btn btn-primary">Accueil</a>';
}

// Préparation des alertes
$alert_html = '';
if ($alert) {
    $alerts = [
        'login_success' => ['message' => 'Connexion réussie !', 'type' => 'success'],
        'login_error' => ['message' => 'Email ou mot de passe incorrect.', 'type' => 'error'],
        'register_success' => ['message' => 'Inscription réussie ! En attente de validation.', 'type' => 'success'],
        'logout_success' => ['message' => 'Déconnexion réussie.', 'type' => 'success']
    ];
    $alert_html = '<div class="alert alert-' . ($alerts[$alert]['type'] === 'error' ? 'error' : 'success') . '">' . $alerts[$alert]['message'] . '</div>';
}

// Chargement de la vue appropriée
if ($action === 'login') {
    $title = 'Connexion';
    $content = file_get_contents('../views/login.php');
} else if ($action === 'register') {
    $title = 'Inscription';
    $content = file_get_contents('../views/register.php');
} else if ($action === 'admin') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: index.php?alert=access_denied');
        exit;
    }
    $title = 'Admin - Demandes';
    // Exemple de données (à remplacer par une requête SQL)
    $demandes = [
        ['id' => 1, 'nom_foodtruck' => 'Burger King', 'nom' => 'Jean', 'email' => 'jean@example.com'],
        ['id' => 2, 'nom_foodtruck' => 'Pizza Truck', 'nom' => 'Marie', 'email' => 'marie@example.com']
    ];
    foreach ($demandes as $demande) {
        $demandes_html .= '
            <tr>
                <td>' . $demande['nom_foodtruck'] . '</td>
                <td>' . $demande['nom'] . '</td>
                <td>' . $demande['email'] . '</td>
                <td>
                    <a href="index.php?action=valider&id=' . $demande['id'] . '&statut=approuvé" class="btn btn-success">Approuver</a>
                    <a href="index.php?action=valider&id=' . $demande['id'] . '&statut=rejeté" class="btn btn-danger">Rejeter</a>
                </td>
            </tr>
        ';
    }
    $content = str_replace('<!--{DEMANDES}-->', $demandes_html, file_get_contents('../views/admin.html'));
} else {
    $content = file_get_contents('../views/home.php');
}

// Affichage final
$layout = file_get_contents('../views/layout.php');
$layout = str_replace('{TITLE}', $title, $layout);
$layout = str_replace('<!--{NAVIGATION}-->', $navigation, $layout);
$layout = str_replace('<!--{ALERT}-->', $alert_html, $layout);
$layout = str_replace('<!--{CONTENT}-->', $content, $layout);
echo $layout;
?>
