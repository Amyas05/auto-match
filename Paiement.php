<?php
require_once 'Db.php';

class Paiement
{
    private $connection;

    public function __construct()
    {
        $db = new Db();
        $this->connection = $db->getConnection();
    }

    public function creerSuivi($commandeId, $montant, $mode)
    {
        $query = $this->connection->prepare(
            'INSERT INTO paiements (commande_id, montant, mode) VALUES (:commande_id, :montant, :mode)'
        );
        return $query->execute([
            'commande_id' => $commandeId,
            'montant' => $montant,
            'mode' => $mode
        ]);
    }

    public function valider($paiementId)
    {
        $query = $this->connection->prepare("UPDATE paiements SET statut = 'valide', date_paiement = NOW() WHERE id = :id");
        return $query->execute(['id' => $paiementId]);
    }

    public function rembourser($paiementId)
    {
        $query = $this->connection->prepare("UPDATE paiements SET statut = 'rembourse' WHERE id = :id AND statut = 'valide'");
        return $query->execute(['id' => $paiementId]);
    }
}