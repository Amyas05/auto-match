<?php
$titrePage = $titlePage;
require dirname(__DIR__, 3) . '/header.php';
?>
<div class="catalog-spacer" aria-hidden="true"></div>
<section class="search-section" id="annonces">
    <div class="container">
        <div class="section-heading">
            <div><p class="eyebrow eyebrow-dark">LE STOCK DU MOMENT</p><h2>Nos dernières annonces</h2></div>
            <p><?= count($ads) ?> annonce<?= count($ads) > 1 ? 's' : '' ?> disponible<?= count($ads) > 1 ? 's' : '' ?></p>
        </div>
        <form class="search-bar" method="GET" action="index.php">
            <label class="search-field"><span>Marque ou modèle</span><input type="search" name="recherche" value="<?= \App\Core\View::escape($filters['recherche']) ?>" placeholder="Ex. Peugeot 308"></label>
            <label class="search-field"><span>Énergie</span><select name="carburant"><option value="">Toutes</option>
                <?php foreach (['Essence', 'Diesel', 'Hybride', 'Électrique'] as $carburant): ?>
                    <option value="<?= \App\Core\View::escape($carburant) ?>" <?= $filters['carburant'] === $carburant ? 'selected' : '' ?>><?= \App\Core\View::escape($carburant) ?></option>
                <?php endforeach; ?>
            </select></label>
            <div class="budget-field">
                <span>Budget (€)</span>
                <label class="budget-control-row" for="prix_min"><span>Minimum <output id="prix_min_valeur"><?= number_format($filters['prix_min'], 0, ',', ' ') ?> €</output></span><input id="prix_min" type="range" name="prix_min" min="0" max="80000" step="500" value="<?= $filters['prix_min'] ?>"></label>
                <label class="budget-control-row" for="prix_max"><span>Maximum <output id="prix_max_valeur"><?= number_format($filters['prix_max'], 0, ',', ' ') ?> €</output></span><input id="prix_max" type="range" name="prix_max" min="0" max="80000" step="500" value="<?= $filters['prix_max'] ?>"></label>
            </div>
            <button class="button button-dark search-submit" type="submit">Rechercher <span aria-hidden="true">↗</span></button>
        </form>
        <script>
        const prixMinimum=document.getElementById('prix_min');const prixMaximum=document.getElementById('prix_max');
        const formatPrix=v=>Number(v).toLocaleString('fr-FR')+' €';
        prixMinimum.addEventListener('input',()=>{if(+prixMinimum.value>+prixMaximum.value)prixMaximum.value=prixMinimum.value;document.getElementById('prix_min_valeur').textContent=formatPrix(prixMinimum.value);document.getElementById('prix_max_valeur').textContent=formatPrix(prixMaximum.value)});
        prixMaximum.addEventListener('input',()=>{if(+prixMaximum.value<+prixMinimum.value)prixMinimum.value=prixMaximum.value;document.getElementById('prix_min_valeur').textContent=formatPrix(prixMinimum.value);document.getElementById('prix_max_valeur').textContent=formatPrix(prixMaximum.value)});
        </script>
        <?php if ($databaseError): ?><div class="alert alert-warning database-notice" role="status"><?= \App\Core\View::escape($databaseError) ?></div><?php endif; ?>
        <?php if ($ads): ?>
            <div class="car-grid">
                <?php foreach ($ads as $ad): ?>
                    <article class="car-card">
                        <a class="car-image" href="annonce.php?id=<?= (int) $ad['annonce_id'] ?>" style="background-image:url('<?= \App\Core\View::escape($ad['image_url'] ?: 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1200&q=85') ?>')"><span class="car-price-overlay"><?= number_format((float) $ad['prix'], 0, ',', ' ') ?> €</span></a>
                        <div class="car-card-body">
                            <div class="car-card-title"><div><p class="eyebrow eyebrow-dark"><?= \App\Core\View::escape($ad['marque']) ?> · <?= (int) $ad['annee'] ?></p><h3><?= \App\Core\View::escape($ad['modele']) ?></h3></div><span class="fuel-pill"><?= \App\Core\View::escape($ad['carburant']) ?></span></div>
                            <p class="car-meta"><?= number_format((int) $ad['kilometrage'], 0, ',', ' ') ?> km <span>·</span> <?= \App\Core\View::escape($ad['nom_concession'] ?: 'Vendeur particulier') ?></p>
                            <div class="car-card-bottom"><span><?= \App\Core\View::escape($ad['nom_concession'] ?: 'Vendeur particulier') ?></span><a href="annonce.php?id=<?= (int) $ad['annonce_id'] ?>">Voir l’annonce <span aria-hidden="true">↗</span></a></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?><div class="empty-state"><span class="empty-icon">⌕</span><h3>Aucune annonce pour cette recherche</h3><p>Modifiez les filtres ou revenez bientôt.</p><a class="button button-outline" href="index.php">Réinitialiser la recherche</a></div><?php endif; ?>
        <?php if ($totalPages > 1):
            $paginationParams = array_filter([
                'recherche' => $filters['recherche'],
                'carburant' => $filters['carburant'],
                'prix_min' => $filters['prix_min'],
                'prix_max' => $filters['prix_max'],
            ], fn($value) => $value !== '' && $value !== 0 && $value !== 80000);
        ?>
            <nav class="pagination-nav" aria-label="Pagination des annonces">
                <div class="pagination-pages">
                    <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++):
                        $pageParams = $paginationParams + ['page' => $pageNumber];
                    ?><a class="pagination-page <?= $pageNumber === $page ? 'is-current' : '' ?>" <?= $pageNumber === $page ? 'aria-current="page"' : '' ?> href="index.php?<?= http_build_query($pageParams) ?>"><?= $pageNumber ?></a><?php endfor; ?>
                </div>
            </nav>
        <?php endif; ?>
    </div>
</section>
<section class="info-section" id="apropos"><div class="container info-section-inner"><p class="eyebrow eyebrow-dark">À PROPOS</p><h2>Une rencontre simple entre votre prochain trajet et la bonne voiture.</h2><p>Auto-Match rassemble des annonces de véhicules d’occasion pour vous aider à comparer les modèles, consulter leurs caractéristiques et prendre contact avec les vendeurs.</p></div></section>
<section class="help-section" id="aide"><div class="container help-section-inner"><div><p class="eyebrow">BESOIN D’AIDE ?</p><h2>On vous accompagne.</h2></div><p>Consultez les annonces et ouvrez une fiche pour découvrir les détails d’un véhicule. Pour enregistrer vos favoris ou demander un essai, connectez-vous depuis <a href="connexion.php">Mon compte</a>.</p></div></section>
<?php require dirname(__DIR__, 3) . '/footer.php'; ?>
