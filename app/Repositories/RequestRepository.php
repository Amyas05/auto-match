<?php
namespace App\Repositories;
use PDO;

class RequestRepository
{
    public function __construct(private PDO $database) {}

    public function reserve(int $clientId, int $carId, string $date): bool
    {
        $statement = $this->database->prepare("INSERT INTO reservations (client_id, voiture_id, date_essai)
            SELECT :client, v.id, :date FROM voitures v WHERE v.id = :car AND v.statut = 'disponible'");
        $statement->execute(['client' => $clientId, 'date' => $date, 'car' => $carId]);
        return $statement->rowCount() > 0;
    }

    public function reservationsFor(int $clientId): array
    {
        $statement = $this->database->prepare('SELECT r.id, r.date_essai, r.statut, v.marque, v.modele
            FROM reservations r JOIN voitures v ON v.id = r.voiture_id
            WHERE r.client_id = :client ORDER BY r.date_essai DESC');
        $statement->execute(['client' => $clientId]);
        return $statement->fetchAll();
    }

    public function order(int $clientId, int $carId): bool|int
    {
        $this->database->beginTransaction();
        try {
            $statement = $this->database->prepare("SELECT prix FROM voitures WHERE id = :car AND statut = 'disponible' FOR UPDATE");
            $statement->execute(['car' => $carId]);
            $car = $statement->fetch();
            if (!$car) { $this->database->rollBack(); return false; }
            $statement = $this->database->prepare('INSERT INTO commandes (client_id, voiture_id, montant_total) VALUES (:client, :car, :amount)');
            $statement->execute(['client' => $clientId, 'car' => $carId, 'amount' => $car['prix']]);
            $orderId = (int) $this->database->lastInsertId();
            $statement = $this->database->prepare("UPDATE voitures SET statut = 'reservee' WHERE id = :car AND statut = 'disponible'");
            $statement->execute(['car' => $carId]);
            $this->database->commit();
            return $orderId;
        } catch (\Throwable $exception) { $this->database->rollBack(); throw $exception; }
    }

    public function ordersFor(int $clientId): array
    {
        $statement = $this->database->prepare('SELECT c.id, c.date_commande, c.montant_total, c.statut, v.marque, v.modele
            FROM commandes c JOIN voitures v ON v.id = c.voiture_id
            WHERE c.client_id = :client ORDER BY c.date_commande DESC');
        $statement->execute(['client' => $clientId]);
        return $statement->fetchAll();
    }
}
