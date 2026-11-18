<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Repositories\AdRepository;
use PDOException;

require_once dirname(__DIR__, 2) . '/DemoData.php';

class CatalogueController
{
    public function index(array $config): void
    {
        $filters = [
            'recherche' => trim($_GET['recherche'] ?? ''),
            'carburant' => trim($_GET['carburant'] ?? ''),
            'prix_min' => max(0, min(80000, (int) ($_GET['prix_min'] ?? 0))),
            'prix_max' => max(0, min(80000, (int) ($_GET['prix_max'] ?? 80000))),
        ];

        if ($filters['prix_min'] > $filters['prix_max']) {
            $filters['prix_max'] = $filters['prix_min'];
        }

        $databaseError = '';
        $perPage = 18;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        try {
            $repository = new AdRepository(Database::connection($config));
            $totalAds = $repository->count($filters);
            $totalPages = max(1, (int) ceil($totalAds / $perPage));
            $page = min($page, $totalPages);
            $ads = $repository->search($filters, $perPage, ($page - 1) * $perPage);
        } catch (PDOException $exception) {
            $allAds = \DemoData::annonces($filters);
            $totalAds = count($allAds);
            $totalPages = max(1, (int) ceil($totalAds / $perPage));
            $page = min($page, $totalPages);
            $ads = array_slice($allAds, ($page - 1) * $perPage, $perPage);
            $databaseError = 'Mode aperçu : annonces de démonstration. Démarrez MySQL et importez database.sql pour utiliser les comptes et les demandes.';
        }

        View::render('catalogue/index', [
            'titlePage' => 'Voitures d’occasion',
            'filters' => $filters,
            'ads' => $ads,
            'databaseError' => $databaseError,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalAds' => $totalAds,
        ]);
    }
}
