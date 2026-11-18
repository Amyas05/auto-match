<?php

namespace App\Repositories;

use PDO;

class AdRepository
{
    public function __construct(private PDO $database) {}

    private function addSearchFilters(array $filters, string &$sql, array &$parameters): void
    {
        if (!empty($filters['recherche'])) {
            $sql .= ' AND (v.marque LIKE :recherche OR v.modele LIKE :recherche OR a.titre LIKE :recherche)';
            $parameters['recherche'] = '%' . $filters['recherche'] . '%';
        }
        if (!empty($filters['carburant'])) {
            $sql .= ' AND v.carburant = :carburant';
            $parameters['carburant'] = $filters['carburant'];
        }
        if (($filters['prix_min'] ?? 0) > 0) {
            $sql .= ' AND v.prix >= :prix_min';
            $parameters['prix_min'] = (float) $filters['prix_min'];
        }
        if (isset($filters['prix_max'])) {
            $sql .= ' AND v.prix <= :prix_max';
            $parameters['prix_max'] = (float) $filters['prix_max'];
        }
    }

    public function count(array $filters = []): int
    {
        $sql = "SELECT COUNT(*)
                FROM annonces a
                JOIN voitures v ON v.id = a.voiture_id
                JOIN utilisateurs u ON u.id = a.vendeur_id
                WHERE a.statut = 'publiee' AND v.statut = 'disponible'";
        $parameters = [];
        $this->addSearchFilters($filters, $sql, $parameters);
        $statement = $this->database->prepare($sql);
        $statement->execute($parameters);
        return (int) $statement->fetchColumn();
    }

    public function search(array $filters = [], int $limit = 18, int $offset = 0): array
    {
        $sql = "SELECT a.id AS annonce_id, a.titre, a.description, a.date_publication,
                       v.*, u.nom_concession
                FROM annonces a
                JOIN voitures v ON v.id = a.voiture_id
                JOIN utilisateurs u ON u.id = a.vendeur_id
                WHERE a.statut = 'publiee' AND v.statut = 'disponible'";
        $parameters = [];
        $this->addSearchFilters($filters, $sql, $parameters);

        $sql .= ' ORDER BY a.date_publication DESC LIMIT :limit OFFSET :offset';
        $statement = $this->database->prepare($sql);

        foreach ($parameters as $name => $value) {
            $statement->bindValue(':' . $name, $value);
        }
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function findPublished(int $adId): array|false
    {
        $statement = $this->database->prepare(
            "SELECT a.id AS annonce_id, a.titre, a.description, a.date_publication,
                    v.*, u.id AS vendeur_id, u.nom_concession, u.telephone
             FROM annonces a
             JOIN voitures v ON v.id = a.voiture_id
             JOIN utilisateurs u ON u.id = a.vendeur_id
             WHERE a.id = :id AND a.statut = 'publiee'"
        );
        $statement->execute(['id' => $adId]);
        return $statement->fetch();
    }
}
