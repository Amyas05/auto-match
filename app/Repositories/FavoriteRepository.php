<?php
namespace App\Repositories;
use PDO;

class FavoriteRepository
{
    public function __construct(private PDO $database) {}

    public function toggle(int $userId, int $carId): bool
    {
        $statement = $this->database->prepare('SELECT 1 FROM favoris WHERE utilisateur_id = :user AND voiture_id = :car');
        $statement->execute(['user' => $userId, 'car' => $carId]);
        if ($statement->fetch()) {
            $statement = $this->database->prepare('DELETE FROM favoris WHERE utilisateur_id = :user AND voiture_id = :car');
        } else {
            $statement = $this->database->prepare('INSERT INTO favoris (utilisateur_id, voiture_id) VALUES (:user, :car)');
        }
        return $statement->execute(['user' => $userId, 'car' => $carId]);
    }

    public function contains(int $userId, int $carId): bool
    {
        $statement = $this->database->prepare('SELECT 1 FROM favoris WHERE utilisateur_id = :user AND voiture_id = :car');
        $statement->execute(['user' => $userId, 'car' => $carId]);
        return (bool) $statement->fetchColumn();
    }

    public function forUser(int $userId): array
    {
        $statement = $this->database->prepare("SELECT v.id AS voiture_id, a.id AS annonce_id, a.titre, v.marque, v.modele, v.prix, v.image_url
            FROM favoris f JOIN voitures v ON v.id = f.voiture_id
            JOIN annonces a ON a.voiture_id = v.id AND a.statut = 'publiee'
            WHERE f.utilisateur_id = :user ORDER BY f.date_ajout DESC");
        $statement->execute(['user' => $userId]);
        return $statement->fetchAll();
    }
}
