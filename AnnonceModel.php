<?php
require_once 'Db.php';

class Annonce
{
    private $connection;

    public function __construct()
    {
        $db = new Db();
        $this->connection = $db->getConnection();
    }

    public function publier($vendeurId, $donnees, $titre, $description)
    {
        $this->connection->beginTransaction();
        try {
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
            $voitureId = $this->connection->lastInsertId();
            $query = $this->connection->prepare(
                'INSERT INTO annonces (voiture_id, vendeur_id, titre, description)
                 VALUES (:voiture_id, :vendeur_id, :titre, :description)'
            );
            $query->execute([
                'voiture_id' => $voitureId,
                'vendeur_id' => $vendeurId,
                'titre' => $titre,
                'description' => $description
            ]);
            $this->connection->commit();
            return $this->connection->lastInsertId();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    public function modifier($annonceId, $vendeurId, $titre, $description)
    {
        $query = $this->connection->prepare(
            'UPDATE annonces SET titre = :titre, description = :description
             WHERE id = :id AND vendeur_id = :vendeur_id'
        );
        return $query->execute([
            'titre' => $titre,
            'description' => $description,
            'id' => $annonceId,
            'vendeur_id' => $vendeurId
        ]);
    }

    public function archiver($annonceId, $vendeurId)
    {
        $query = $this->connection->prepare(
            "UPDATE annonces SET statut = 'archivee' WHERE id = :id AND vendeur_id = :vendeur_id"
        );
        return $query->execute(['id' => $annonceId, 'vendeur_id' => $vendeurId]);
    }

    public function getForSeller($vendeurId)
    {
        $query = $this->connection->prepare(
            'SELECT a.id AS annonce_id, a.titre, a.statut AS statut_annonce, a.date_publication,
                    v.marque, v.modele, v.prix, v.image_url, v.statut AS statut_voiture
             FROM annonces a JOIN voitures v ON v.id = a.voiture_id
             WHERE a.vendeur_id = :vendeur_id ORDER BY a.date_publication DESC'
        );
        $query->execute(['vendeur_id' => $vendeurId]);
        return $query->fetchAll();
    }
}