<section class="detail-page container">
    <a class="back-link" href="index.php#annonces">← Retour aux annonces</a>
    <?php if ($message): ?><div class="alert alert-info detail-alert"><?= e($message) ?></div><?php endif; ?>
    <div class="detail-layout">
        <div class="detail-main">
            <div class="detail-photo" style="background-image:url('<?= e($ad['image_url'] ?: 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1600&q=90') ?>')"></div>
            <p class="eyebrow eyebrow-dark detail-eyebrow"><?= e($ad['marque']) ?> · <?= (int) $ad['annee'] ?> · <?= e($ad['carburant']) ?></p>
            <h1><?= e($ad['titre']) ?></h1>
            <div class="spec-grid"><div><span>Modèle</span><strong><?= e($ad['modele']) ?></strong></div><div><span>Kilométrage</span><strong><?= number_format((int) $ad['kilometrage'], 0, ',', ' ') ?> km</strong></div><div><span>Année</span><strong><?= (int) $ad['annee'] ?></strong></div><div><span>Énergie</span><strong><?= e($ad['carburant']) ?></strong></div></div>
            <h2>À propos de cette voiture</h2><p class="detail-description"><?= nl2br(e($ad['description'])) ?></p>
        </div>
        <aside class="detail-aside">
            <p class="eyebrow eyebrow-dark">PRIX DE VENTE</p><p class="detail-price"><?= number_format((float) $ad['prix'], 0, ',', ' ') ?> <span>€</span></p><p class="seller-name"><?= e($ad['nom_concession'] ?: 'Vendeur particulier') ?></p>
            <?php if ($modeDemo): ?><p class="demo-detail-notice">Annonce de démonstration. Les demandes seront disponibles après activation de la base.</p><a class="button button-dark detail-login" href="inscription.php">Créer un compte <span aria-hidden="true">↗</span></a>
            <?php elseif ($user && ($user['role'] ?? '') === 'client'): ?><form method="POST" class="detail-actions"><input type="hidden" name="_csrf" value="<?= e($csrf) ?>"><button class="button button-outline" name="favori" type="submit"><?= $favorite ? 'Retirer des favoris' : 'Ajouter aux favoris' ?></button><label>Demander un essai<input type="datetime-local" name="date_essai" min="<?= date('Y-m-d\TH:i') ?>" required></label><button class="button button-dark" name="reservation" type="submit">Réserver un essai</button><button class="button button-lime" name="commande" type="submit" formnovalidate>Demander l’achat <span aria-hidden="true">↗</span></button></form>
            <?php else: ?><a class="button button-dark detail-login" href="connexion.php">Connectez-vous pour contacter le vendeur</a><?php endif; ?>
            <p class="secure-note">Aucun paiement n’est effectué sur ce site. Votre demande est transmise au vendeur.</p>
        </aside>
    </div>
</section>
