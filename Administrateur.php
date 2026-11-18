<?php
require_once 'Utilisateur.php';

class Administrateur extends Utilisateur
{
    public function __construct($nom = null, $prenom = null, $email = null)
    {
        parent::__construct($nom, $prenom, $email, null, 'admin');
    }

    public function gererUtilisateurs()
    {
        $db = new Db();
        $query = $db->getConnection()->query(
            'SELECT id, nom, prenom, email, role, date_creation FROM utilisateurs ORDER BY date_creation DESC'
        );
        return $query->fetchAll();
    }

    public function modererAnnonce($annonceId, $statut)
    {
        if (!in_array($statut, ['publiee', 'refusee', 'archivee'], true)) {
            return false;
        }

        $db = new Db();
        $query = $db->getConnection()->prepare('UPDATE annonces SET statut = :statut WHERE id = :id');
        return $query->execute(['statut' => $statut, 'id' => $annonceId]);
    }
}