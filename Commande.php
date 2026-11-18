<?php
require_once 'Db.php';

class Commande
{
    private $connection;

    public function __construct()
    {
        $db = new Db();
        $this->connection = $db->getConnection();
    }

    public function passer($clientId, $voitureId)
    {
        $this->connection->beginTransaction();
        try {
            $query = $this->connection->prepare(
                "SELECT prix FROM voitures WHERE id = :id AND statut = 'disponible' FOR UPDATE"
            );
            $query->execute(['id' => $voitureId]);
            $voiture = $query->fetch();
            if (!$voiture) {
                $this->connection->rollBack();
                return false;
            }

            $query = $this->connection->prepare(
                'INSERT INTO commandes (client_id, voiture_id, montant_total) VALUES (:client_id, :voiture_id, :montant)'
            );
            $query->execute([
                'client_id' => $clientId,
                'voiture_id' => $voitureId,
                'montant' => $voiture['prix']
            ]);
            $commandeId = $this->connection->lastInsertId();

            $query = $this->connection->prepare(
                "UPDATE voitures SET statut = 'reservee' WHERE id = :id AND statut = 'disponible'"
            );
            $query->execute(['id' => $voitureId]);
            $this->connection->commit();
            return $commandeId;
        } catch (Exception $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    public function annuler($commandeId, $clientId)
    {
        $this->connection->beginTransaction();
        try {
            $query = $this->connection->prepare(
                "SELECT voiture_id FROM commandes WHERE id = :id AND client_id = :client_id AND statut = 'en_attente' FOR UPDATE"
            );
            $query->execute(['id' => $commandeId, 'client_id' => $clientId]);
            $commande = $query->fetch();
            if (!$commande) {
                $this->connection->rollBack();
                return false;
            }

            $query = $this->connection->prepare("UPDATE commandes SET statut = 'annulee' WHERE id = :id");
            $query->execute(['id' => $commandeId]);
            $query = $this->connection->prepare("UPDATE voitures SET statut = 'disponible' WHERE id = :id AND statut = 'reservee'");
            $query->execute(['id' => $commande['voiture_id']]);
            $this->connection->commit();
            return true;
        } catch (Exception $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    public function getForClient($clientId)
    {
        $query = $this->connection->prepare(
            'SELECT c.id, c.date_commande, c.montant_total, c.statut, v.marque, v.modele
             FROM commandes c JOIN voitures v ON v.id = c.voiture_id
             WHERE c.client_id = :client_id ORDER BY c.date_commande DESC'
        );
        $query->execute(['client_id' => $clientId]);
        return $query->fetchAll();
    }
}