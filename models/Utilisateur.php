<?php
class Utilisateur {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function trouverParEmail($email) {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function inscrire($nom, $email, $mot_de_passe) {
        try {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM utilisateurs WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Cet email est deja utilise.');
            }

            $stmt = $this->pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, 'foodtruck')");
            if (!$stmt->execute([$nom, $email, $mot_de_passe])) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Erreur lors de l'insertion de l'utilisateur: " . $errorInfo[2]);
            }

            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Erreur de base de donnees: ' . $e->getMessage());
        }
    }

    public function supprimerCompteConnecte($idUtilisateur, $role) {
        $this->pdo->beginTransaction();
        try {
            if ($role === 'foodtruck') {
                $stmtFt = $this->pdo->prepare('SELECT id_foodtruck FROM foodtrucks WHERE id_utilisateur = ? LIMIT 1');
                $stmtFt->execute([$idUtilisateur]);
                $idFoodtruck = (int)$stmtFt->fetchColumn();

                if ($idFoodtruck > 0) {
                    $delAdresses = $this->pdo->prepare('DELETE FROM foodtruck_adresses WHERE id_foodtruck = ?');
                    $delAdresses->execute([$idFoodtruck]);

                    $delDemandes = $this->pdo->prepare('DELETE FROM demandesinscription WHERE id_foodtruck = ?');
                    $delDemandes->execute([$idFoodtruck]);

                    $delFt = $this->pdo->prepare('DELETE FROM foodtrucks WHERE id_foodtruck = ?');
                    $delFt->execute([$idFoodtruck]);
                }
            }

            if ($role === 'admin') {
                $countAdmins = (int)$this->pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'admin'")->fetchColumn();
                if ($countAdmins <= 1) {
                    throw new Exception('Impossible de supprimer le dernier compte administrateur.');
                }
            }

            $delUser = $this->pdo->prepare('DELETE FROM utilisateurs WHERE id_utilisateur = ?');
            $delUser->execute([$idUtilisateur]);

            if ($delUser->rowCount() === 0) {
                throw new Exception('Compte introuvable ou deja supprime.');
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
?>
