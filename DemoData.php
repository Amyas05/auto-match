<?php
class DemoData
{
    public static function annonces($filtres = [])
    {
        $annonces = [
            [
                'annonce_id' => 1,
                'id' => 1,
                'marque' => 'BMW',
                'modele' => 'Série 2',
                'annee' => 2018,
                'kilometrage' => 114043,
                'carburant' => 'Diesel',
                'prix' => 18490,
                'statut' => 'disponible',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=1200&q=85',
                'titre' => 'BMW Série 2 Luxury - 2018',
                'description' => 'BMW Série 2 Luxury de 2018, soigneusement entretenue. Motorisation diesel, 114 043 km. Contactez-nous pour organiser une visite ou un essai.',
                'nom_concession' => 'Auto-Match Sélection',
                'telephone' => '0600000001'
            ],
            [
                'annonce_id' => 2,
                'id' => 2,
                'marque' => 'Peugeot',
                'modele' => '3008',
                'annee' => 2020,
                'kilometrage' => 72870,
                'carburant' => 'Diesel',
                'prix' => 19490,
                'statut' => 'disponible',
                'image_url' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=85',
                'titre' => 'Peugeot 3008 GT Line - 2020',
                'description' => 'Peugeot 3008 GT Line de 2020, confortable et polyvalente. Motorisation diesel, 72 870 km. Contactez-nous pour organiser une visite ou un essai.',
                'nom_concession' => 'Auto-Match Sélection',
                'telephone' => '0600000001'
            ],
            [
                'annonce_id' => 3,
                'id' => 3,
                'marque' => 'Honda',
                'modele' => 'Civic',
                'annee' => 2019,
                'kilometrage' => 93350,
                'carburant' => 'Essence',
                'prix' => 14890,
                'statut' => 'disponible',
                'image_url' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=85',
                'titre' => 'Honda Civic Executive - 2019',
                'description' => 'Honda Civic Executive de 2019, agréable au quotidien et bien entretenue. Motorisation essence, 93 350 km. Contactez-nous pour organiser une visite ou un essai.',
                'nom_concession' => 'Auto-Match Sélection',
                'telephone' => '0600000001'
            ]
        ];

        $voituresSupplementaires = [
            ['Mercedes-Benz', 'Classe A', 2021, 66000, 'Essence', 23900, 'photo-1618843479313-40f8afb4b4d8'],
            ['Audi', 'A3', 2020, 78500, 'Diesel', 21900, 'photo-1606664515524-ed2f786a0bd6'],
            ['Renault', 'Clio', 2022, 32400, 'Essence', 15900, 'photo-1549317661-bd32c8ce0db2'],
            ['Toyota', 'Yaris', 2021, 41700, 'Hybride', 17900, 'photo-1552519507-da3b142c6e3d'],
            ['Volkswagen', 'Golf', 2019, 68900, 'Diesel', 18900, 'photo-1502877338535-766e1452684a'],
            ['Citroen', 'C3', 2020, 52400, 'Essence', 12900, 'photo-1492144534655-ae79c964c9d7'],
            ['Dacia', 'Duster', 2021, 58200, 'Diesel', 16900, 'photo-1533473359331-0135ef1b58bf'],
            ['Tesla', 'Model 3', 2020, 60100, 'Electrique', 28900, 'photo-1560958089-b8a1929cea89'],
            ['Ford', 'Puma', 2022, 29300, 'Essence', 20900, 'photo-1503376780353-7e6692767b70'],
            ['Kia', 'Sportage', 2020, 74200, 'Diesel', 19900, 'photo-1519641471654-76ce0107ad1b'],
            ['Hyundai', 'Kona', 2021, 45900, 'Hybride', 22700, 'photo-1542362567-b07e54358753'],
            ['Mini', 'Cooper', 2019, 51800, 'Essence', 17500, 'photo-1503736334956-4c8f8e92946d'],
            ['Nissan', 'Qashqai', 2020, 66700, 'Diesel', 18500, 'photo-1511919884226-fd3cad34687c'],
            ['Opel', 'Corsa', 2021, 38200, 'Essence', 13900, 'photo-1494976388531-d1058494cdd8'],
            ['Mazda', 'CX-30', 2021, 43700, 'Essence', 21800, 'photo-1552519507-da3b142c6e3d'],
            ['Skoda', 'Octavia', 2019, 81300, 'Diesel', 17400, 'photo-1502877338535-766e1452684a'],
            ['Fiat', '500', 2022, 24600, 'Essence', 14500, 'photo-1549317661-bd32c8ce0db2'],
            ['Volvo', 'XC40', 2020, 63100, 'Hybride', 29900, 'photo-1519641471654-76ce0107ad1b'],
            ['Alfa Romeo', 'Giulietta', 2018, 85600, 'Diesel', 15900, 'photo-1492144534655-ae79c964c9d7'],
            ['Lexus', 'UX', 2021, 39900, 'Hybride', 27900, 'photo-1560958089-b8a1929cea89']
        ];

        $voituresParBudget = [
            ['Dacia', 'Sandero', 2016, 102000, 'Essence', 7900, 'photo-1549317661-bd32c8ce0db2'],
            ['Renault', 'Twingo', 2018, 76500, 'Essence', 8500, 'photo-1492144534655-ae79c964c9d7'],
            ['Peugeot', '208', 2016, 88400, 'Diesel', 8900, 'photo-1552519507-da3b142c6e3d'],
            ['Citroen', 'C1', 2019, 57300, 'Essence', 9200, 'photo-1502877338535-766e1452684a'],
            ['Toyota', 'Aygo', 2017, 69200, 'Essence', 9500, 'photo-1494976388531-d1058494cdd8'],
            ['Ford', 'Fiesta', 2017, 81200, 'Diesel', 9800, 'photo-1503736334956-4c8f8e92946d'],
            ['Hyundai', 'i10', 2018, 64100, 'Essence', 9900, 'photo-1542362567-b07e54358753'],
            ['Seat', 'Leon', 2021, 35700, 'Essence', 23900, 'photo-1502877338535-766e1452684a'],
            ['Honda', 'HR-V', 2021, 42600, 'Hybride', 26900, 'photo-1519641471654-76ce0107ad1b'],
            ['BMW', '118i', 2019, 48900, 'Essence', 24900, 'photo-1555215695-3004980ad54e'],
            ['Audi', 'Q2', 2020, 38100, 'Essence', 28900, 'photo-1606664515524-ed2f786a0bd6'],
            ['Renault', 'Captur', 2022, 27500, 'Hybride', 22900, 'photo-1533473359331-0135ef1b58bf'],
            ['Peugeot', '508', 2019, 61700, 'Diesel', 25900, 'photo-1549317661-bd32c8ce0db2'],
            ['Toyota', 'C-HR', 2020, 44200, 'Hybride', 27900, 'photo-1552519507-da3b142c6e3d'],
            ['Skoda', 'Karoq', 2022, 29800, 'Essence', 32900, 'photo-1533473359331-0135ef1b58bf'],
            ['Volkswagen', 'Tiguan', 2021, 39600, 'Diesel', 35900, 'photo-1519641471654-76ce0107ad1b'],
            ['Mercedes-Benz', 'Classe C', 2020, 51200, 'Diesel', 38900, 'photo-1618843479313-40f8afb4b4d8'],
            ['BMW', 'X1', 2021, 33800, 'Essence', 37900, 'photo-1555215695-3004980ad54e'],
            ['Audi', 'Q3', 2021, 42100, 'Diesel', 34900, 'photo-1606664515524-ed2f786a0bd6'],
            ['Kia', 'EV6', 2022, 36500, 'Electrique', 39900, 'photo-1560958089-b8a1929cea89'],
            ['Peugeot', '5008', 2022, 31700, 'Diesel', 33900, 'photo-1549317661-bd32c8ce0db2'],
            ['Tesla', 'Model Y', 2022, 35400, 'Electrique', 45900, 'photo-1560958089-b8a1929cea89'],
            ['Volvo', 'XC60', 2021, 48200, 'Hybride', 48900, 'photo-1519641471654-76ce0107ad1b'],
            ['BMW', '320d', 2022, 29100, 'Diesel', 42900, 'photo-1555215695-3004980ad54e'],
            ['Audi', 'A4', 2022, 32700, 'Diesel', 43900, 'photo-1606664515524-ed2f786a0bd6'],
            ['Mercedes-Benz', 'GLA', 2022, 26300, 'Essence', 46900, 'photo-1618843479313-40f8afb4b4d8'],
            ['Toyota', 'RAV4', 2023, 18400, 'Hybride', 41900, 'photo-1533473359331-0135ef1b58bf'],
            ['Porsche', 'Macan', 2018, 71300, 'Essence', 49900, 'photo-1503376780353-7e6692767b70'],
            ['BMW', 'X5', 2021, 45800, 'Diesel', 68900, 'photo-1555215695-3004980ad54e'],
            ['Mercedes-Benz', 'GLC', 2022, 39200, 'Hybride', 62900, 'photo-1618843479313-40f8afb4b4d8'],
            ['Audi', 'Q5', 2022, 41700, 'Diesel', 57900, 'photo-1606664515524-ed2f786a0bd6'],
            ['Porsche', 'Taycan', 2021, 28100, 'Electrique', 77900, 'photo-1503376780353-7e6692767b70'],
            ['Tesla', 'Model S', 2021, 33600, 'Electrique', 72900, 'photo-1560958089-b8a1929cea89'],
            ['Lexus', 'RX', 2022, 30900, 'Hybride', 64900, 'photo-1560958089-b8a1929cea89'],
            ['Land Rover', 'Defender', 2020, 52700, 'Diesel', 75900, 'photo-1533473359331-0135ef1b58bf']
        ];

        foreach (array_merge($voituresSupplementaires, $voituresParBudget) as $index => $voiture) {
            list($marque, $modele, $annee, $kilometrage, $carburant, $prix, $photo) = $voiture;
            $titre = $marque . ' ' . $modele . ' - ' . $annee;
            $annonces[] = [
                'annonce_id' => $index + 4,
                'id' => $index + 4,
                'marque' => $marque,
                'modele' => $modele,
                'annee' => $annee,
                'kilometrage' => $kilometrage,
                'carburant' => $carburant,
                'prix' => $prix,
                'statut' => 'disponible',
                'image_url' => 'https://images.unsplash.com/' . $photo . '?auto=format&fit=crop&w=1200&q=85',
                'titre' => $titre,
                'description' => $titre . ', bien entretenue, motorisation ' . strtolower($carburant) . ', ' . number_format($kilometrage, 0, ',', ' ') . ' km. Contactez-nous pour organiser une visite ou un essai.',
                'nom_concession' => 'Auto-Match Sélection',
                'telephone' => '0600000001'
            ];
        }

        return array_values(array_filter($annonces, function ($annonce) use ($filtres) {
            $recherche = mb_strtolower(trim($filtres['recherche'] ?? ''), 'UTF-8');
            $texte = mb_strtolower($annonce['marque'] . ' ' . $annonce['modele'] . ' ' . $annonce['titre'], 'UTF-8');
            if ($recherche !== '' && mb_strpos($texte, $recherche, 0, 'UTF-8') === false) {
                return false;
            }
            if (!empty($filtres['carburant']) && $annonce['carburant'] !== $filtres['carburant']) {
                return false;
            }
            if (isset($filtres['prix_min']) && $annonce['prix'] < (float) $filtres['prix_min']) {
                return false;
            }
            if (isset($filtres['prix_max']) && $annonce['prix'] > (float) $filtres['prix_max']) {
                return false;
            }
            return true;
        }));
    }

    public static function trouver($annonceId)
    {
        foreach (self::annonces() as $annonce) {
            if ($annonce['annonce_id'] === (int) $annonceId) {
                return $annonce;
            }
        }
        return false;
    }
}