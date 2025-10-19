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
        $action = 'admin_demandes';
        try {
            $demandes = $this->demande->listerEnAttente();
            if (!is_array($demandes)) {
                $demandes = [];
            }
            error_log("Nombre de demandes trouvées: " . count($demandes));
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des demandes: " . $e->getMessage());
            $demandes = [];
        }

        include '../views/admin.php';
    }

    public function listerFoodtrucks() {
        $action = 'admin_foodtrucks';
        try {
            $stmt = $this->pdo->prepare("
                SELECT ft.id_foodtruck, ft.nom_foodtruck, ft.description, ft.type_cuisine,
                       ft.adresse, ft.telephone, ft.logo, u.nom, u.email,
                       ft.statut, ft.date_creation as date_demande
                FROM foodtrucks ft
                JOIN utilisateurs u ON ft.id_utilisateur = u.id_utilisateur
                ORDER BY ft.date_creation DESC
            ");
            $stmt->execute();
            $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!is_array($demandes)) {
                $demandes = [];
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des foodtrucks: " . $e->getMessage());
            $demandes = [];
        }

        include '../views/admin.php';
    }

    public function validerDemande() {
        if (!isset($_GET['id_foodtruck']) || !isset($_GET['statut'])) {
            header('Location: index.php?action=admin_demandes&alert=error&detail=' . urlencode('Paramètres manquants'));
            exit();
        }

        $id_foodtruck = $_GET['id_foodtruck'];
        $statut = $_GET['statut'];

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("UPDATE foodtrucks SET statut = ? WHERE id_foodtruck = ?");
            $stmt->execute([$statut, $id_foodtruck]);

            $stmt = $this->pdo->prepare("UPDATE demandesinscription SET statut = ? WHERE id_foodtruck = ?");
            $stmt->execute([$statut, $id_foodtruck]);

            $this->pdo->commit();
            header('Location: index.php?action=admin_demandes&alert=success');
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Erreur lors de la validation de la demande: " . $e->getMessage());
            header('Location: index.php?action=admin_demandes&alert=error&detail=' . urlencode($e->getMessage()));
        }
    }
}
?>
