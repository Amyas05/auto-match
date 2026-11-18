# Auto-Match

Petit site PHP/MySQL de vente de voitures d’occasion, construit avec des pages PHP, des classes métier simples et une feuille CSS séparée.

## Installation avec MAMP

1. Placez le dossier dans `C:\MAMP\htdocs\site_vente_voitures`.
2. Démarrez Apache et MySQL depuis MAMP.
3. Importez `database.sql` dans phpMyAdmin.
4. Vérifiez l’utilisateur et le mot de passe MySQL dans `Db.php` (valeurs initiales : `root` / `root`).
5. Si PHP affiche `could not find driver`, activez `extension=pdo_mysql` dans le `php.ini` de la version PHP sélectionnée dans MAMP, puis redémarrez Apache.
6. Ouvrez `http://localhost/site_vente_voitures/`.
7. Créez un compte vendeur pour publier les premières annonces.

Les comptes acheteur et vendeur peuvent être créés depuis le formulaire. Le compte administrateur doit être créé manuellement dans la base. Les demandes d’achat et les paiements sont uniquement enregistrés à titre de suivi : aucune transaction bancaire n’est intégrée.

Pour un import depuis la ligne de commande, utilisez le client MySQL avec `--default-character-set=utf8mb4` afin de préserver les accents.

Si MySQL ne démarre pas, l’accueil et les fiches des cinquante-huit voitures restent consultables en mode aperçu via `DemoData.php`. Les formulaires de compte, favoris, réservations, commandes et espaces privés nécessitent MySQL.

## Données de démonstration

L’import SQL ajoute cinquante-huit voitures réparties sur des budgets d’environ 8 000 à 78 000 €, ainsi que deux comptes fictifs : `vendeur@auto-match.test` et `acheteur@auto-match.test`. Le mot de passe des deux comptes est `AutoMatchDemo2026`. Ces comptes sont réservés au test local.