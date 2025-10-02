<?php
require_once '../models/Demande.php';

class AdminController {
    private $demande;

    public function __construct($pdo) {
        $this->demande = new Demande($pdo);
    }

    public function listerDemandes() {
        $demandes = $this->demande->listerEnAttente();
        require '../views/admin/demandes.php';
    }

    public function validerDemande() {
        $id_demande = $_GET['id_demande'];
        $statut = $_GET['statut'];
        $this->demande->valider($id_demande, $statut);
        header('Location: ../public/index.php?action=admin_demandes');
    }
}
?>
