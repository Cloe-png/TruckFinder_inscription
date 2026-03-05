<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';
require_once '../controllers/UtilisateurController.php';
require_once '../controllers/AdminController.php';

$controller = new UtilisateurController($pdo);
$adminController = new AdminController($pdo);

$action = $_GET['action'] ?? 'home';
$alert = $_GET['alert'] ?? null;
$content = '';
$navigation = '';
$title = 'TruckFinder';
$alert_html = '';

if ($alert) {
    $alerts = [
        'login_success' => ['message' => 'Connexion réussie !', 'type' => 'success'],
        'login_error' => ['message' => 'Email ou mot de passe incorrect.', 'type' => 'danger'],
        'register_success' => ['message' => 'Inscription réussie ! Votre compte est en attente de validation.', 'type' => 'success'],
        'register_error' => ['message' => 'Erreur lors de l\'action.', 'type' => 'danger'],
        'menu_saved' => ['message' => 'Menu enregistré avec succès.', 'type' => 'success'],
        'horaires_saved' => ['message' => 'Horaires enregistrés avec succès.', 'type' => 'success'],
        'account_deleted' => ['message' => 'Compte supprimé avec succès.', 'type' => 'success'],
        'foodtruck_deleted' => ['message' => 'Compte foodtruck supprimé avec succès.', 'type' => 'success'],
        'deconnexion' => ['message' => 'Déconnexion réussie.', 'type' => 'success'],
        'access_denied' => ['message' => 'Accès refusé.', 'type' => 'danger'],
        'compte_non_valide' => ['message' => 'Votre compte est en attente de validation par un administrateur.', 'type' => 'warning'],
        'empty_fields' => ['message' => 'Veuillez remplir tous les champs.', 'type' => 'danger'],
        'invalid_role' => ['message' => 'Rôle utilisateur invalide.', 'type' => 'danger']
    ];

    if (isset($alerts[$alert])) {
        $alert_html = '<div class="alert alert-' . $alerts[$alert]['type'] . '">' . $alerts[$alert]['message'] . '</div>';
    }

    if (isset($_GET['detail'])) {
        $alert_html .= '<div class="alert alert-danger">' . htmlspecialchars($_GET['detail']) . '</div>';
    }
}

if (isset($_SESSION['role'])) {
    $navigation = '<li class="nav-item"><a class="nav-link" href="index.php?action=deconnexion">Déconnexion</a></li>';
    if ($_SESSION['role'] === 'admin') {
        $navigation .= '<li class="nav-item"><a class="nav-link" href="index.php?action=admin_demandes">Admin</a></li>';
    } else {
        $navigation .= '<li class="nav-item"><a class="nav-link" href="index.php?action=foodtruck_dashboard">Mon FoodTruck</a></li>';
    }
} else {
    $navigation = '<li class="nav-item"><a class="nav-link" href="index.php?action=login">Connexion</a></li>
                   <li class="nav-item"><a class="nav-link" href="index.php?action=register">S\'inscrire</a></li>';
}

switch ($action) {
    case 'login':
        $title = 'Connexion';
        ob_start();
        include '../views/login.php';
        $content = ob_get_clean();
        break;

    case 'register':
        $title = 'Inscription';
        ob_start();
        include '../views/register.php';
        $content = ob_get_clean();
        break;

    case 'do_register':
        $controller->traiterInscription();
        break;

    case 'do_login':
        $controller->traiterConnexion();
        break;

    case 'foodtruck_dashboard':
        if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'foodtruck') {
            header('Location: index.php?alert=access_denied');
            exit;
        }
        $title = 'Mon FoodTruck';
        ob_start();
        $controller->dashboard();
        $content = ob_get_clean();
        break;

    case 'save_adresses':
        if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'foodtruck') {
            header('Location: index.php?alert=access_denied');
            exit;
        }
        $controller->sauvegarderAdresses();
        break;

    case 'set_presence_adresse':
        if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'foodtruck') {
            header('Location: index.php?alert=access_denied');
            exit;
        }
        $controller->definirPresenceAdresse();
        break;

    case 'edit_menu':
        if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'foodtruck') {
            header('Location: index.php?alert=access_denied');
            exit;
        }
        $title = 'Édition du menu';
        ob_start();
        $controller->afficherEditionMenu();
        $content = ob_get_clean();
        break;

    case 'save_menu':
        if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'foodtruck') {
            header('Location: index.php?alert=access_denied');
            exit;
        }
        $controller->sauvegarderMenu();
        break;

    case 'save_horaires':
        if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'foodtruck') {
            header('Location: index.php?alert=access_denied');
            exit;
        }
        $controller->sauvegarderHoraires();
        break;

    case 'delete_account':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?alert=access_denied');
            exit;
        }
        if (!isset($_SESSION['id_utilisateur']) || !isset($_SESSION['role'])) {
            header('Location: index.php?alert=access_denied');
            exit;
        }
        $controller->supprimerCompteConnecte();
        break;

    case 'admin_demandes':
        if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?alert=access_denied');
            exit;
        }
        $title = 'Administration - Demandes en attente';
        ob_start();
        $adminController->listerDemandes();
        $content = ob_get_clean();
        break;

    case 'admin_foodtrucks':
        if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?alert=access_denied');
            exit;
        }
        $title = 'Administration - Tous les FoodTrucks';
        ob_start();
        $adminController->listerFoodtrucks();
        $content = ob_get_clean();
        break;

    case 'valider_demande':
        if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?alert=access_denied');
            exit;
        }
        $adminController->validerDemande();
        break;

    case 'delete_foodtruck':
        if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?alert=access_denied');
            exit;
        }
        $adminController->supprimerFoodtruck();
        break;

    case 'deconnexion':
        session_unset();
        session_destroy();
        header('Location: index.php?alert=deconnexion');
        exit;

    default:
        $title = 'Accueil';
        ob_start();
        include '../views/home.php';
        $content = ob_get_clean();
        break;
}

$layout = file_get_contents('../views/layout.php');
$layout = str_replace('{TITLE}', $title, $layout);
$layout = str_replace('<!--{NAVIGATION}-->', $navigation, $layout);
$layout = str_replace('<!--{ALERT}-->', $alert_html, $layout);
$layout = str_replace('<!--{CONTENT}-->', $content, $layout);
echo $layout;
?>

