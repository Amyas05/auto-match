<section class="dashboard-page container">
    <?php if ($estVendeur): ?>
        <div class="dashboard-heading"><div><p class="eyebrow eyebrow-dark">ESPACE VENDEUR</p><h1>Vos annonces.</h1></div><a class="button button-dark" href="publier.php">+ Déposer une annonce</a></div>
        <?php if (isset($_GET['archive'])): ?><div class="alert alert-info">L’annonce a été archivée.</div><?php endif; ?>
        <?php if ($annonces): ?><div class="seller-list"><?php foreach ($annonces as $item): ?><article class="seller-row">
            <div class="seller-thumb" style="background-image:url('<?= e($item['image_url'] ?: 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=500&q=80') ?>')"></div>
            <div class="seller-row-info"><p class="eyebrow eyebrow-dark"><?= e($item['marque']) ?> · <?= e($item['modele']) ?></p><h2><?= e($item['titre']) ?></h2><span><?= number_format((float) $item['prix'], 0, ',', ' ') ?> € · <?= e($item['statut_annonce']) ?></span></div>
            <?php if ($item['statut_annonce'] === 'publiee'): ?><a class="row-action" href="annonce.php?id=<?= (int) $item['annonce_id'] ?>">Voir <span aria-hidden="true">↗</span></a><form method="POST" action="espace.php" onsubmit="return confirm('Archiver cette annonce ?')"><input type="hidden" name="_csrf" value="<?= e($csrf) ?>"><button class="row-action row-action-muted" name="archiver" value="<?= (int) $item['annonce_id'] ?>" type="submit">Archiver</button></form><?php endif; ?>
        </article><?php endforeach; ?></div>
        <?php else: ?><div class="empty-state"><span class="empty-icon">＋</span><h2>Votre première annonce vous attend.</h2><p>Ajoutez les informations de la voiture pour la présenter aux acheteurs.</p><a class="button button-dark" href="publier.php">Déposer une annonce</a></div><?php endif; ?>
    <?php else: ?>
        <div class="dashboard-heading"><div><p class="eyebrow eyebrow-dark">ESPACE ACHETEUR</p><h1>Votre sélection.</h1></div></div>
        <?php if (isset($_GET['favori'])): ?><div class="alert alert-info">Le favori a été retiré.</div><?php endif; ?>
        <h2 class="dashboard-section-title" id="favoris">Mes favoris</h2>
        <?php if ($favoris): ?><div class="seller-list"><?php foreach ($favoris as $item): ?><article class="seller-row">
            <div class="seller-thumb" style="background-image:url('<?= e($item['image_url'] ?: 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=500&q=80') ?>')"></div>
            <div class="seller-row-info"><p class="eyebrow eyebrow-dark"><?= e($item['marque']) ?> · <?= e($item['modele']) ?></p><h2><?= e($item['titre']) ?></h2><span><?= number_format((float) $item['prix'], 0, ',', ' ') ?> €</span></div>
            <a class="row-action" href="annonce.php?id=<?= (int) $item['annonce_id'] ?>">Voir <span aria-hidden="true">↗</span></a>
            <form method="POST" action="espace.php#favoris"><input type="hidden" name="_csrf" value="<?= e($csrf) ?>"><button class="row-action row-action-muted" name="retirer_favori" value="<?= (int) $item['voiture_id'] ?>" type="submit">Retirer</button></form>
        </article><?php endforeach; ?></div><?php else: ?><p class="dashboard-empty">Aucun favori pour le moment. Enregistrez une annonce pour la retrouver ici.</p><?php endif; ?>
        <h2 class="dashboard-section-title" id="demandes">Mes demandes</h2>
        <?php if ($reservations || $commandes): ?><div class="request-list"><?php foreach ($reservations as $item): ?><article class="request-row"><div><span class="request-type">ESSAI</span><strong><?= e($item['marque'] . ' ' . $item['modele']) ?></strong></div><span><?= date('d/m/Y à H:i', strtotime($item['date_essai'])) ?></span><span class="request-status"><?= e($item['statut']) ?></span></article><?php endforeach; ?><?php foreach ($commandes as $item): ?><article class="request-row"><div><span class="request-type">ACHAT</span><strong><?= e($item['marque'] . ' ' . $item['modele']) ?></strong></div><span><?= number_format((float) $item['montant_total'], 0, ',', ' ') ?> €</span><span class="request-status"><?= e($item['statut']) ?></span></article><?php endforeach; ?></div>
        <?php else: ?><p class="dashboard-empty">Vos demandes d’essai et d’achat apparaîtront ici.</p><?php endif; ?>
    <?php endif; ?>
</section>
