<?php
class FoodTruck {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function creer($id_utilisateur, $nom_foodtruck, $description, $type_cuisine, $adresse, $telephone, $logo) {
        $stmt = $this->pdo->prepare("
            INSERT INTO foodtrucks (id_utilisateur, nom_foodtruck, description, type_cuisine, adresse, telephone, logo, statut)
            VALUES (:id_utilisateur, :nom_foodtruck, :description, :type_cuisine, :adresse, :telephone, :logo, 'en_attente')
        ");
        $stmt->execute([
            ':id_utilisateur' => $id_utilisateur,
            ':nom_foodtruck' => $nom_foodtruck,
            ':description' => $description,
            ':type_cuisine' => $type_cuisine,
            ':adresse' => $adresse,
            ':telephone' => $telephone,
            ':logo' => $logo
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function getFoodtruckData($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM foodtrucks WHERE id_utilisateur = :id_utilisateur");
        $stmt->bindParam(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateMenu($foodtruckId, $menu) {
        $stmt = $this->pdo->prepare("UPDATE foodtrucks SET menu = :menu WHERE id_foodtruck = :id_foodtruck");
        $stmt->bindParam(':menu', $menu, PDO::PARAM_STR);
        $stmt->bindParam(':id_foodtruck', $foodtruckId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateHoraires($foodtruckId, $horaires) {
        $stmt = $this->pdo->prepare("UPDATE foodtrucks SET horaires = :horaires WHERE id_foodtruck = :id_foodtruck");
        $stmt->bindParam(':horaires', $horaires, PDO::PARAM_STR);
        $stmt->bindParam(':id_foodtruck', $foodtruckId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getFoodtruckByUserId($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM foodtrucks WHERE id_utilisateur = :id_utilisateur LIMIT 1");
        $stmt->bindParam(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAdresses($foodtruckId) {
        $stmt = $this->pdo->prepare("
            SELECT id_adresse, adresse, latitude, longitude, est_present
            FROM foodtruck_adresses
            WHERE id_foodtruck = :id_foodtruck
            ORDER BY id_adresse ASC
        ");
        $stmt->bindParam(':id_foodtruck', $foodtruckId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function upsertAdresses($foodtruckId, array $adresses) {
        $this->pdo->beginTransaction();
        try {
            $delete = $this->pdo->prepare("DELETE FROM foodtruck_adresses WHERE id_foodtruck = :id_foodtruck");
            $delete->bindParam(':id_foodtruck', $foodtruckId, PDO::PARAM_INT);
            $delete->execute();

            $insert = $this->pdo->prepare("
                INSERT INTO foodtruck_adresses (id_foodtruck, adresse, latitude, longitude, est_present)
                VALUES (:id_foodtruck, :adresse, :latitude, :longitude, :est_present)
            ");

            $firstAdresse = null;
            foreach ($adresses as $adresse) {
                $adresseTexte = trim((string)($adresse['adresse'] ?? ''));
                if ($adresseTexte === '') {
                    continue;
                }

                if ($firstAdresse === null) {
                    $firstAdresse = $adresseTexte;
                }

                $latitude = ($adresse['latitude'] ?? '') === '' ? null : (float)$adresse['latitude'];
                $longitude = ($adresse['longitude'] ?? '') === '' ? null : (float)$adresse['longitude'];
                $estPresent = !empty($adresse['est_present']) ? 1 : 0;

                $insert->execute([
                    ':id_foodtruck' => $foodtruckId,
                    ':adresse' => $adresseTexte,
                    ':latitude' => $latitude,
                    ':longitude' => $longitude,
                    ':est_present' => $estPresent
                ]);
            }

            if ($firstAdresse !== null) {
                $syncAdresse = $this->pdo->prepare("UPDATE foodtrucks SET adresse = :adresse WHERE id_foodtruck = :id_foodtruck");
                $syncAdresse->execute([
                    ':adresse' => $firstAdresse,
                    ':id_foodtruck' => $foodtruckId
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function definirAdressePresence($foodtruckId, $idAdresse) {
        $this->pdo->beginTransaction();
        try {
            $reset = $this->pdo->prepare("UPDATE foodtruck_adresses SET est_present = 0 WHERE id_foodtruck = :id_foodtruck");
            $reset->execute([':id_foodtruck' => $foodtruckId]);

            $set = $this->pdo->prepare("
                UPDATE foodtruck_adresses
                SET est_present = 1
                WHERE id_foodtruck = :id_foodtruck AND id_adresse = :id_adresse
            ");
            $set->execute([
                ':id_foodtruck' => $foodtruckId,
                ':id_adresse' => $idAdresse
            ]);

            $this->pdo->commit();
            return $set->rowCount() > 0;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
?>
