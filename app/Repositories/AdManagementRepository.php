<?php
namespace App\Repositories;
use PDO;

class AdManagementRepository
{
    public function __construct(private PDO $database) {}

    public function publish(int $sellerId, array $car, string $title, string $description): int
    {
        $this->database->beginTransaction();
        try {
            $statement = $this->database->prepare('INSERT INTO voitures (marque, modele, annee, kilometrage, carburant, prix, image_url)
                VALUES (:marque, :modele, :annee, :kilometrage, :carburant, :prix, :image_url)');
            $statement->execute([
                'marque' => $car['marque'], 'modele' => $car['modele'], 'annee' => $car['annee'],
                'kilometrage' => $car['kilometrage'], 'carburant' => $car['carburant'],
                'prix' => $car['prix'], 'image_url' => $car['image_url'] ?: null,
            ]);
            $carId = (int) $this->database->lastInsertId();
            $statement = $this->database->prepare('INSERT INTO annonces (voiture_id, vendeur_id, titre, description)
                VALUES (:car, :seller, :title, :description)');
            $statement->execute(['car' => $carId, 'seller' => $sellerId, 'title' => $title, 'description' => $description]);
            $adId = (int) $this->database->lastInsertId();
            $this->database->commit();
            return $adId;
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            throw $exception;
        }
    }

    public function forSeller(int $sellerId): array
    {
        $statement = $this->database->prepare("SELECT a.id AS annonce_id, a.titre, a.statut AS statut_annonce, a.date_publication,
            v.marque, v.modele, v.prix, v.image_url, v.statut AS statut_voiture
            FROM annonces a JOIN voitures v ON v.id = a.voiture_id
            WHERE a.vendeur_id = :seller ORDER BY a.date_publication DESC");
        $statement->execute(['seller' => $sellerId]);
        return $statement->fetchAll();
    }

    public function archive(int $adId, int $sellerId): bool
    {
        $statement = $this->database->prepare("UPDATE annonces SET statut = 'archivee' WHERE id = :ad AND vendeur_id = :seller");
        return $statement->execute(['ad' => $adId, 'seller' => $sellerId]);
    }
}
