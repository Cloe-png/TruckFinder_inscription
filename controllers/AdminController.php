<?php
require_once '../models/Demande.php';

class AdminController {
    private $pdo;
    private $demande;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->demande = new Demande($pdo);
    }

    public function listerDemandes() {
        if (!isset($_SESSION['id_utilisateur']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?alert=access_denied');
            exit();
        }

        $action = 'admin_demandes';
        try {
            $demandes = $this->demande->listerEnAttente();
            if (!is_array($demandes)) {
                $demandes = [];
            }
        } catch (Exception $e) {
            error_log('Erreur lors de la recuperation des demandes: ' . $e->getMessage());
            $demandes = [];
        }

        include '../views/admin.php';
    }

    public function listerFoodtrucks() {
        if (!isset($_SESSION['id_utilisateur']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?alert=access_denied');
            exit();
        }

        $action = 'admin_foodtrucks';
        try {
            $stmt = $this->pdo->prepare(
                'SELECT ft.id_foodtruck, ft.nom_foodtruck, ft.description, ft.type_cuisine,
                        ft.adresse, ft.telephone, ft.logo, u.nom, u.email,
                        ft.statut, u.date_creation AS date_demande
                 FROM foodtrucks ft
                 JOIN utilisateurs u ON ft.id_utilisateur = u.id_utilisateur
                 ORDER BY u.date_creation DESC'
            );
            $stmt->execute();
            $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!is_array($demandes)) {
                $demandes = [];
            }
        } catch (Exception $e) {
            error_log('Erreur lors de la recuperation des foodtrucks: ' . $e->getMessage());
            $demandes = [];
        }

        include '../views/admin.php';
    }

    public function validerDemande() {
        if (!isset($_SESSION['id_utilisateur']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?alert=access_denied');
            exit();
        }

        if (!isset($_GET['id_foodtruck']) || !isset($_GET['statut'])) {
            header('Location: index.php?action=admin_demandes&alert=error&detail=' . urlencode('Parametres manquants'));
            exit();
        }

        $id_foodtruck = (int)$_GET['id_foodtruck'];
        $statut = strtolower(trim((string)$_GET['statut']));

        if (!in_array($statut, ['en_attente', 'approuve', 'rejete'], true)) {
            header('Location: index.php?action=admin_demandes&alert=error&detail=' . urlencode('Statut invalide'));
            exit();
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare('UPDATE foodtrucks SET statut = ? WHERE id_foodtruck = ?');
            $stmt->execute([$statut, $id_foodtruck]);

            $stmt = $this->pdo->prepare('UPDATE demandesinscription SET statut = ? WHERE id_foodtruck = ?');
            $stmt->execute([$statut, $id_foodtruck]);

            $this->pdo->commit();
            header('Location: index.php?action=admin_demandes&alert=success');
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Erreur lors de la validation de la demande: ' . $e->getMessage());
            header('Location: index.php?action=admin_demandes&alert=error&detail=' . urlencode($e->getMessage()));
        }
    }

    public function supprimerFoodtruck() {
        if (!isset($_SESSION['id_utilisateur']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?alert=access_denied');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_foodtrucks&alert=error&detail=' . urlencode('Methode non autorisee'));
            exit();
        }

        $idFoodtruck = isset($_POST['id_foodtruck']) ? (int)$_POST['id_foodtruck'] : 0;
        if ($idFoodtruck <= 0) {
            header('Location: index.php?action=admin_foodtrucks&alert=error&detail=' . urlencode('Foodtruck invalide'));
            exit();
        }

        try {
            $this->pdo->beginTransaction();

            $stmtUser = $this->pdo->prepare('SELECT id_utilisateur FROM foodtrucks WHERE id_foodtruck = ? LIMIT 1');
            $stmtUser->execute([$idFoodtruck]);
            $idUtilisateur = (int)$stmtUser->fetchColumn();

            if ($idUtilisateur <= 0) {
                throw new Exception('Foodtruck introuvable.');
            }

            $delAdresses = $this->pdo->prepare('DELETE FROM foodtruck_adresses WHERE id_foodtruck = ?');
            $delAdresses->execute([$idFoodtruck]);

            $delDemandes = $this->pdo->prepare('DELETE FROM demandesinscription WHERE id_foodtruck = ?');
            $delDemandes->execute([$idFoodtruck]);

            $delFt = $this->pdo->prepare('DELETE FROM foodtrucks WHERE id_foodtruck = ?');
            $delFt->execute([$idFoodtruck]);

            $delUser = $this->pdo->prepare('DELETE FROM utilisateurs WHERE id_utilisateur = ? AND role = "foodtruck"');
            $delUser->execute([$idUtilisateur]);

            $this->pdo->commit();
            header('Location: index.php?action=admin_foodtrucks&alert=foodtruck_deleted');
            exit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Erreur suppression foodtruck: ' . $e->getMessage());
            header('Location: index.php?action=admin_foodtrucks&alert=error&detail=' . urlencode($e->getMessage()));
            exit();
        }
    }
}
?>
