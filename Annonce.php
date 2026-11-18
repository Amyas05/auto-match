<?php
require_once 'Voiture.php';
require_once 'Favori.php';
require_once 'Reservation.php';
require_once 'Commande.php';
session_start();
$annonceId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$modeDemo = false;
$annonce = false;
try {
    $voiture = new Voiture();
    $annonce = $annonceId ? $voiture->afficherDetails($annonceId) : false;
} catch (PDOException $exception) {
    require_once 'DemoData.php';
    $modeDemo = true;
    $annonce = $annonceId ? DemoData::trouver($annonceId) : false;
}
if (!$annonce) {
    http_response_code(404);
    $titrePage = 'Annonce introuvable';
    require 'header.php';
    echo '<section class="empty-state page-empty"><h1>Cette annonce n’est plus disponible.</h1><a class="button button-dark" href="index.php">Retour aux annonces</a></section>';
    require 'footer.php';
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($modeDemo) {
        $message = 'Les annonces sont en mode aperçu. Démarrez MySQL et importez database.sql pour enregistrer vos demandes.';
    } elseif (!isset($_SESSION['utilisateur'])) {
        header('Location: connexion.php');
        exit();
    }
    if ($_SESSION['utilisateur']['role'] !== 'client') {
        $message = 'Cette action est réservée aux comptes client.';
    } elseif (isset($_POST['favori'])) {
        $favori = new Favori();
        $favori->basculer($_SESSION['utilisateur']['id'], $annonce['id']);
        $message = 'Votre sélection a été mise à jour.';
    } elseif (isset($_POST['reservation'])) {
        $dateEssai = $_POST['date_essai'] ?? '';
        if ($dateEssai < date('Y-m-d\TH:i')) {
            $message = 'Choisissez une date d’essai dans le futur.';
        } else {
            $reservation = new Reservation();
            $message = $reservation->creer($_SESSION['utilisateur']['id'], $annonce['id'], date('Y-m-d H:i:s', strtotime($dateEssai)))
                ? 'Votre demande d’essai a bien été envoyée.'
                : 'Cette voiture n’est plus disponible.';
        }
    } elseif (isset($_POST['commande'])) {
        $commande = new Commande();
        $resultat = $commande->passer($_SESSION['utilisateur']['id'], $annonce['id']);
        $message = $resultat ? 'Votre demande d’achat est enregistrée. Le vendeur pourra vous recontacter.' : 'Cette voiture n’est plus disponible.';
    }
}

$favori = false;
if (!$modeDemo && isset($_SESSION['utilisateur']) && $_SESSION['utilisateur']['role'] === 'client') {
    $favori = (new Favori())->contient($_SESSION['utilisateur']['id'], $annonce['id']);
}
$titrePage = $annonce['marque'] . ' ' . $annonce['modele'];
require 'header.php';
?>
<section class="detail-page container">
    <a class="back-link" href="index.php#annonces">← Retour aux annonces</a>
    <?php if ($message): ?><div class="alert alert-info detail-alert"><?= e($message) ?></div><?php endif; ?>
    <div class="detail-layout">
        <div class="detail-main">
            <div class="detail-photo" style="background-image:url('<?= e($annonce['image_url'] ?: 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1600&q=90') ?>')"></div>
            <p class="eyebrow eyebrow-dark detail-eyebrow"><?= e($annonce['marque']) ?> · <?= (int) $annonce['annee'] ?> · <?= e($annonce['carburant']) ?></p>
            <h1><?= e($annonce['titre']) ?></h1>
            <div class="spec-grid"><div><span>Modèle</span><strong><?= e($annonce['modele']) ?></strong></div><div><span>Kilométrage</span><strong><?= number_format((int) $annonce['kilometrage'], 0, ',', ' ') ?> km</strong></div><div><span>Année</span><strong><?= (int) $annonce['annee'] ?></strong></div><div><span>Énergie</span><strong><?= e($annonce['carburant']) ?></strong></div></div>
            <h2>À propos de cette voiture</h2><p class="detail-description"><?= nl2br(e($annonce['description'])) ?></p>
        </div>
        <aside class="detail-aside"><p class="eyebrow eyebrow-dark">PRIX DE VENTE</p><p class="detail-price"><?= number_format((float) $annonce['prix'], 0, ',', ' ') ?> <span>€</span></p><p class="seller-name"><?= e($annonce['nom_concession'] ?: 'Vendeur particulier') ?></p>
            <?php if ($modeDemo): ?>
                <p class="demo-detail-notice">Annonce de démonstration. Les demandes seront disponibles après activation de la base.</p>
                <a class="button button-dark detail-login" href="inscription.php">Créer un compte <span aria-hidden="true">↗</span></a>
            <?php elseif ($utilisateurConnecte && $utilisateurConnecte['role'] === 'client'): ?>
                <form method="POST" class="detail-actions"><button class="button button-outline" name="favori" type="submit"><?= $favori ? 'Retirer des favoris' : 'Ajouter aux favoris' ?></button><label>Demander un essai<input type="datetime-local" name="date_essai" min="<?= date('Y-m-d\TH:i') ?>" required></label><button class="button button-dark" name="reservation" type="submit">Réserver un essai</button><button class="button button-lime" name="commande" type="submit" formnovalidate>Demander l’achat <span aria-hidden="true">↗</span></button></form>
            <?php else: ?><a class="button button-dark detail-login" href="connexion.php">Connectez-vous pour contacter le vendeur</a><?php endif; ?>
            <p class="secure-note">Aucun paiement n’est effectué sur ce site. Votre demande est transmise au vendeur.</p>
        </aside>
    </div>
</section>
<?php require 'footer.php'; ?><?php
require_once 'Db.php';
require_once 'Voiture.php';

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