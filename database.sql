SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS site_vente_voitures CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE site_vente_voitures;

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(30) DEFAULT NULL,
    role ENUM('client', 'vendeur', 'admin') NOT NULL DEFAULT 'client',
    adresse VARCHAR(255) DEFAULT NULL,
    nom_concession VARCHAR(150) DEFAULT NULL,
    niveau_acces VARCHAR(50) DEFAULT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS voitures (
    id INT NOT NULL AUTO_INCREMENT,
    marque VARCHAR(80) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    annee SMALLINT NOT NULL,
    kilometrage INT NOT NULL DEFAULT 0,
    carburant VARCHAR(40) NOT NULL,
    prix DECIMAL(12, 2) NOT NULL,
    statut ENUM('disponible', 'reservee', 'vendue') NOT NULL DEFAULT 'disponible',
    image_url VARCHAR(500) DEFAULT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS annonces (
    id INT NOT NULL AUTO_INCREMENT,
    voiture_id INT NOT NULL,
    vendeur_id INT NOT NULL,
    titre VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    date_publication DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'publiee', 'archivee', 'refusee') NOT NULL DEFAULT 'publiee',
    PRIMARY KEY (id),
    FOREIGN KEY (voiture_id) REFERENCES voitures(id) ON DELETE CASCADE,
    FOREIGN KEY (vendeur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS favoris (
    utilisateur_id INT NOT NULL,
    voiture_id INT NOT NULL,
    date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (utilisateur_id, voiture_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (voiture_id) REFERENCES voitures(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reservations (
    id INT NOT NULL AUTO_INCREMENT,
    client_id INT NOT NULL,
    voiture_id INT NOT NULL,
    date_essai DATETIME NOT NULL,
    statut ENUM('en_attente', 'confirmee', 'annulee') NOT NULL DEFAULT 'en_attente',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (client_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (voiture_id) REFERENCES voitures(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS commandes (
    id INT NOT NULL AUTO_INCREMENT,
    client_id INT NOT NULL,
    voiture_id INT NOT NULL,
    date_commande DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    montant_total DECIMAL(12, 2) NOT NULL,
    statut ENUM('en_attente', 'confirmee', 'annulee') NOT NULL DEFAULT 'en_attente',
    PRIMARY KEY (id),
    FOREIGN KEY (client_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (voiture_id) REFERENCES voitures(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS paiements (
    id INT NOT NULL AUTO_INCREMENT,
    commande_id INT NOT NULL,
    montant DECIMAL(12, 2) NOT NULL,
    mode VARCHAR(50) NOT NULL,
    date_paiement DATETIME DEFAULT NULL,
    statut ENUM('en_attente', 'valide', 'rembourse') NOT NULL DEFAULT 'en_attente',
    PRIMARY KEY (id),
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
);

INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, role, adresse, nom_concession)
VALUES
    ('Demo', 'Vendeur', 'vendeur@auto-match.test', '$2y$10$UkTKK.7z/g1EYnQQZZ28nuPFKwkZw2kfumasKcDwz9ihlul5n5cdW', '0600000001', 'vendeur', NULL, 'Auto-Match Sélection'),
    ('Demo', 'Acheteur', 'acheteur@auto-match.test', '$2y$10$zSsAkwOv8YeM53xutWiKje0VSlMl5f85hKaIjwWtk05d85Wli/rWu', '0600000002', 'client', 'Paris', NULL)
ON DUPLICATE KEY UPDATE email = VALUES(email);

INSERT INTO voitures (marque, modele, annee, kilometrage, carburant, prix, statut, image_url)
SELECT donnees.marque, donnees.modele, donnees.annee, donnees.kilometrage, donnees.carburant,
       donnees.prix, 'disponible', donnees.image_url
FROM (
    SELECT 'BMW' AS marque, 'Série 2' AS modele, 2018 AS annee, 114043 AS kilometrage, 'Diesel' AS carburant, 18490.00 AS prix,
           'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=1200&q=85' AS image_url
    UNION ALL SELECT 'Peugeot', '3008', 2020, 72870, 'Diesel', 19490.00,
           'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=85'
    UNION ALL SELECT 'Honda', 'Civic', 2019, 93350, 'Essence', 14890.00,
           'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Mercedes-Benz', 'Classe A', 2021, 66000, 'Essence', 23900.00,
            'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Audi', 'A3', 2020, 78500, 'Diesel', 21900.00,
            'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Renault', 'Clio', 2022, 32400, 'Essence', 15900.00,
            'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Toyota', 'Yaris', 2021, 41700, 'Hybride', 17900.00,
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Volkswagen', 'Golf', 2019, 68900, 'Diesel', 18900.00,
            'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Citroen', 'C3', 2020, 52400, 'Essence', 12900.00,
            'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Dacia', 'Duster', 2021, 58200, 'Diesel', 16900.00,
            'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Tesla', 'Model 3', 2020, 60100, 'Electrique', 28900.00,
            'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Ford', 'Puma', 2022, 29300, 'Essence', 20900.00,
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Kia', 'Sportage', 2020, 74200, 'Diesel', 19900.00,
            'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Hyundai', 'Kona', 2021, 45900, 'Hybride', 22700.00,
            'https://images.unsplash.com/photo-1542362567-b07e54358753?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Mini', 'Cooper', 2019, 51800, 'Essence', 17500.00,
            'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Nissan', 'Qashqai', 2020, 66700, 'Diesel', 18500.00,
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Opel', 'Corsa', 2021, 38200, 'Essence', 13900.00,
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Mazda', 'CX-30', 2021, 43700, 'Essence', 21800.00,
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Skoda', 'Octavia', 2019, 81300, 'Diesel', 17400.00,
            'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Fiat', '500', 2022, 24600, 'Essence', 14500.00,
            'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Volvo', 'XC40', 2020, 63100, 'Hybride', 29900.00,
            'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Alfa Romeo', 'Giulietta', 2018, 85600, 'Diesel', 15900.00,
            'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1200&q=85'
        UNION ALL SELECT 'Lexus', 'UX', 2021, 39900, 'Hybride', 27900.00,
            'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Dacia', 'Sandero', 2016, 102000, 'Essence', 7900.00,
                'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Renault', 'Twingo', 2018, 76500, 'Essence', 8500.00,
                'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Peugeot', '208', 2016, 88400, 'Diesel', 8900.00,
                'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Citroen', 'C1', 2019, 57300, 'Essence', 9200.00,
                'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Toyota', 'Aygo', 2017, 69200, 'Essence', 9500.00,
                'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Ford', 'Fiesta', 2017, 81200, 'Diesel', 9800.00,
                'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Hyundai', 'i10', 2018, 64100, 'Essence', 9900.00,
                'https://images.unsplash.com/photo-1542362567-b07e54358753?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Seat', 'Leon', 2021, 35700, 'Essence', 23900.00,
                'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Honda', 'HR-V', 2021, 42600, 'Hybride', 26900.00,
                'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'BMW', '118i', 2019, 48900, 'Essence', 24900.00,
                'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Audi', 'Q2', 2020, 38100, 'Essence', 28900.00,
                'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Renault', 'Captur', 2022, 27500, 'Hybride', 22900.00,
                'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Peugeot', '508', 2019, 61700, 'Diesel', 25900.00,
                'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Toyota', 'C-HR', 2020, 44200, 'Hybride', 27900.00,
                'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Skoda', 'Karoq', 2022, 29800, 'Essence', 32900.00,
                'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Volkswagen', 'Tiguan', 2021, 39600, 'Diesel', 35900.00,
                'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Mercedes-Benz', 'Classe C', 2020, 51200, 'Diesel', 38900.00,
                'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'BMW', 'X1', 2021, 33800, 'Essence', 37900.00,
                'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Audi', 'Q3', 2021, 42100, 'Diesel', 34900.00,
                'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Kia', 'EV6', 2022, 36500, 'Electrique', 39900.00,
                'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Peugeot', '5008', 2022, 31700, 'Diesel', 33900.00,
                'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Tesla', 'Model Y', 2022, 35400, 'Electrique', 45900.00,
                'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Volvo', 'XC60', 2021, 48200, 'Hybride', 48900.00,
                'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'BMW', '320d', 2022, 29100, 'Diesel', 42900.00,
                'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Audi', 'A4', 2022, 32700, 'Diesel', 43900.00,
                'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Mercedes-Benz', 'GLA', 2022, 26300, 'Essence', 46900.00,
                'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Toyota', 'RAV4', 2023, 18400, 'Hybride', 41900.00,
                'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Porsche', 'Macan', 2018, 71300, 'Essence', 49900.00,
                'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'BMW', 'X5', 2021, 45800, 'Diesel', 68900.00,
                'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Mercedes-Benz', 'GLC', 2022, 39200, 'Hybride', 62900.00,
                'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Audi', 'Q5', 2022, 41700, 'Diesel', 57900.00,
                'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Porsche', 'Taycan', 2021, 28100, 'Electrique', 77900.00,
                'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Tesla', 'Model S', 2021, 33600, 'Electrique', 72900.00,
                'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Lexus', 'RX', 2022, 30900, 'Hybride', 64900.00,
                'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=1200&q=85'
            UNION ALL SELECT 'Land Rover', 'Defender', 2020, 52700, 'Diesel', 75900.00,
                'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=85'
) AS donnees
WHERE NOT EXISTS (
    SELECT 1 FROM voitures existante
    WHERE existante.marque = donnees.marque AND existante.modele = donnees.modele AND existante.annee = donnees.annee
);

INSERT INTO annonces (voiture_id, vendeur_id, titre, description, statut)
SELECT voiture.id, vendeur.id, CONCAT(voiture.marque, ' ', voiture.modele, ' - ', voiture.annee),
       CONCAT('Découvrez cette ', voiture.marque, ' ', voiture.modele, ' de ', voiture.annee,
              ', soigneusement entretenue. Motorisation ', voiture.carburant, ', ',
        CAST(voiture.kilometrage AS CHAR), ' km. Contactez-nous pour organiser une visite ou un essai.'),
       'publiee'
FROM voitures voiture
JOIN utilisateurs vendeur ON vendeur.email = 'vendeur@auto-match.test'
WHERE (voiture.marque, voiture.modele, voiture.annee) IN (
    ('BMW', 'Série 2', 2018),
    ('Peugeot', '3008', 2020),
    ('Honda', 'Civic', 2019),
    ('Mercedes-Benz', 'Classe A', 2021),
    ('Audi', 'A3', 2020),
    ('Renault', 'Clio', 2022),
    ('Toyota', 'Yaris', 2021),
    ('Volkswagen', 'Golf', 2019),
    ('Citroen', 'C3', 2020),
    ('Dacia', 'Duster', 2021),
    ('Tesla', 'Model 3', 2020),
    ('Ford', 'Puma', 2022),
    ('Kia', 'Sportage', 2020),
    ('Hyundai', 'Kona', 2021),
    ('Mini', 'Cooper', 2019),
    ('Nissan', 'Qashqai', 2020),
    ('Opel', 'Corsa', 2021),
    ('Mazda', 'CX-30', 2021),
    ('Skoda', 'Octavia', 2019),
    ('Fiat', '500', 2022),
    ('Volvo', 'XC40', 2020),
    ('Alfa Romeo', 'Giulietta', 2018),
    ('Lexus', 'UX', 2021),
    ('Dacia', 'Sandero', 2016),
    ('Renault', 'Twingo', 2018),
    ('Peugeot', '208', 2016),
    ('Citroen', 'C1', 2019),
    ('Toyota', 'Aygo', 2017),
    ('Ford', 'Fiesta', 2017),
    ('Hyundai', 'i10', 2018),
    ('Seat', 'Leon', 2021),
    ('Honda', 'HR-V', 2021),
    ('BMW', '118i', 2019),
    ('Audi', 'Q2', 2020),
    ('Renault', 'Captur', 2022),
    ('Peugeot', '508', 2019),
    ('Toyota', 'C-HR', 2020),
    ('Skoda', 'Karoq', 2022),
    ('Volkswagen', 'Tiguan', 2021),
    ('Mercedes-Benz', 'Classe C', 2020),
    ('BMW', 'X1', 2021),
    ('Audi', 'Q3', 2021),
    ('Kia', 'EV6', 2022),
    ('Peugeot', '5008', 2022),
    ('Tesla', 'Model Y', 2022),
    ('Volvo', 'XC60', 2021),
    ('BMW', '320d', 2022),
    ('Audi', 'A4', 2022),
    ('Mercedes-Benz', 'GLA', 2022),
    ('Toyota', 'RAV4', 2023),
    ('Porsche', 'Macan', 2018),
    ('BMW', 'X5', 2021),
    ('Mercedes-Benz', 'GLC', 2022),
    ('Audi', 'Q5', 2022),
    ('Porsche', 'Taycan', 2021),
    ('Tesla', 'Model S', 2021),
    ('Lexus', 'RX', 2022),
    ('Land Rover', 'Defender', 2020)
)
AND NOT EXISTS (
    SELECT 1 FROM annonces existante
    WHERE existante.voiture_id = voiture.id AND existante.vendeur_id = vendeur.id
);