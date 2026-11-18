<?php
require_once 'Db.php';

class Voiture
{
    private $connection;

    public function __construct()
    {
        $db = new Db();
        $this->connection = $db->getConnection();
    }

    public function rechercher($filtres = [])
    {
        $sql = "SELECT a.id AS annonce_id, a.titre, a.description, a.date_publication,
                       v.*, u.nom_concession
                FROM annonces a
                JOIN voitures v ON v.id = a.voiture_id
                JOIN utilisateurs u ON u.id = a.vendeur_id
                WHERE a.statut = 'publiee' AND v.statut = 'disponible'";
        $parametres = [];

        if (!empty($filtres['recherche'])) {
            $sql .= ' AND (v.marque LIKE :recherche OR v.modele LIKE :recherche OR a.titre LIKE :recherche)';
            $parametres['recherche'] = '%' . $filtres['recherche'] . '%';
        }
        if (!empty($filtres['carburant'])) {
            $sql .= ' AND v.carburant = :carburant';
            $parametres['carburant'] = $filtres['carburant'];
        }
        if (isset($filtres['prix_min']) && (float) $filtres['prix_min'] > 0) {
            $sql .= ' AND v.prix >= :prix_min';
            $parametres['prix_min'] = (float) $filtres['prix_min'];
        }
        if (isset($filtres['prix_max'])) {
            $sql .= ' AND v.prix <= :prix_max';
            $parametres['prix_max'] = (float) $filtres['prix_max'];
        }

        $sql .= ' ORDER BY a.date_publication DESC';
        $query = $this->connection->prepare($sql);
        $query->execute($parametres);
        return $query->fetchAll();
    }

    public function afficherDetails($annonceId)
    {
        $query = $this->connection->prepare(
            "SELECT a.id AS annonce_id, a.titre, a.description, a.date_publication,
                    v.*, u.id AS vendeur_id, u.nom_concession, u.telephone
             FROM annonces a
             JOIN voitures v ON v.id = a.voiture_id
             JOIN utilisateurs u ON u.id = a.vendeur_id
             WHERE a.id = :id AND a.statut = 'publiee'"
        );
        $query->execute(['id' => $annonceId]);
        return $query->fetch();
    }

    public function creer($donnees)
    {
        $query = $this->connection->prepare(
            'INSERT INTO voitures (marque, modele, annee, kilometrage, carburant, prix, image_url)
             VALUES (:marque, :modele, :annee, :kilometrage, :carburant, :prix, :image_url)'
        );
        $query->execute([
            'marque' => $donnees['marque'],
            'modele' => $donnees['modele'],
            'annee' => $donnees['annee'],
            'kilometrage' => $donnees['kilometrage'],
            'carburant' => $donnees['carburant'],
            'prix' => $donnees['prix'],
            'image_url' => $donnees['image_url'] ?: null
        ]);
        return $this->connection->lastInsertId();
    }
}