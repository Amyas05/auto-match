<?php
require_once 'Db.php';

class Reservation
{
    private $connection;

    public function __construct()
    {
        $db = new Db();
        $this->connection = $db->getConnection();
    }

    public function creer($clientId, $voitureId, $dateEssai)
    {
        $query = $this->connection->prepare(
            "INSERT INTO reservations (client_id, voiture_id, date_essai)
             SELECT :client_id, v.id, :date_essai FROM voitures v
             WHERE v.id = :voiture_id AND v.statut = 'disponible'"
        );
        $query->execute([
            'client_id' => $clientId,
            'voiture_id' => $voitureId,
            'date_essai' => $dateEssai
        ]);
        return $query->rowCount() > 0;
    }

    public function getForClient($clientId)
    {
        $query = $this->connection->prepare(
            'SELECT r.id, r.date_essai, r.statut, v.marque, v.modele
             FROM reservations r JOIN voitures v ON v.id = r.voiture_id
             WHERE r.client_id = :client_id ORDER BY r.date_essai DESC'
        );
        $query->execute(['client_id' => $clientId]);
        return $query->fetchAll();
    }
}