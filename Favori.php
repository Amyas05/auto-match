<?php
require_once 'Db.php';

class Favori
{
    private $connection;

    public function __construct()
    {
        $db = new Db();
        $this->connection = $db->getConnection();
    }

    public function basculer($utilisateurId, $voitureId)
    {
        $query = $this->connection->prepare(
            'SELECT 1 FROM favoris WHERE utilisateur_id = :utilisateur_id AND voiture_id = :voiture_id'
        );
        $query->execute(['utilisateur_id' => $utilisateurId, 'voiture_id' => $voitureId]);
        if ($query->fetch()) {
            $query = $this->connection->prepare(
                'DELETE FROM favoris WHERE utilisateur_id = :utilisateur_id AND voiture_id = :voiture_id'
            );
            return $query->execute(['utilisateur_id' => $utilisateurId, 'voiture_id' => $voitureId]);
        }

        $query = $this->connection->prepare(
            'INSERT INTO favoris (utilisateur_id, voiture_id) VALUES (:utilisateur_id, :voiture_id)'
        );
        return $query->execute(['utilisateur_id' => $utilisateurId, 'voiture_id' => $voitureId]);
    }

    public function contient($utilisateurId, $voitureId)
    {
        $query = $this->connection->prepare(
            'SELECT 1 FROM favoris WHERE utilisateur_id = :utilisateur_id AND voiture_id = :voiture_id'
        );
        $query->execute(['utilisateur_id' => $utilisateurId, 'voiture_id' => $voitureId]);
        return (bool) $query->fetch();
    }

    public function getForUser($utilisateurId)
    {
        $query = $this->connection->prepare(
            "SELECT v.id AS voiture_id, a.id AS annonce_id, a.titre, v.marque, v.modele,
                    v.prix, v.image_url
             FROM favoris f
             JOIN voitures v ON v.id = f.voiture_id
             JOIN annonces a ON a.voiture_id = v.id AND a.statut = 'publiee'
             WHERE f.utilisateur_id = :utilisateur_id ORDER BY f.date_ajout DESC"
        );
        $query->execute(['utilisateur_id' => $utilisateurId]);
        return $query->fetchAll();
    }
}