<?php
require_once __DIR__ . '/../Db.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}

function normalizeText($value)
{
    $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    $value = strtolower($value);
    return trim(preg_replace('/[^a-z0-9]+/', ' ', $value));
}

function requestJson($url)
{
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_USERAGENT => 'AutoMatchLocalDemo/1.0 (vehicle photo catalog)'
    ]);
    $body = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);

    if ($body === false || $status < 200 || $status >= 300) {
        return false;
    }
    return json_decode($body, true);
}

function downloadImage($url)
{
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => 'AutoMatchLocalDemo/1.0 (vehicle photo catalog)'
    ]);
    $body = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);

    if ($body === false || $status < 200 || $status >= 300 || !getimagesizefromstring($body)) {
        return false;
    }
    return $body;
}

function searchTerms($make, $model)
{
    $aliases = [
        'BMW|Série 2' => 'BMW 2 Series F22',
        'BMW|118i' => 'BMW 1 Series F40',
        'BMW|320d' => 'BMW 3 Series G20',
        'Mercedes-Benz|Classe A' => 'Mercedes-Benz A-Class W177',
        'Mercedes-Benz|Classe C' => 'Mercedes-Benz C-Class',
        'Mercedes-Benz|GLA' => 'Mercedes-Benz GLA-Class',
        'Mercedes-Benz|GLC' => 'Mercedes-Benz GLC-Class',
        'Seat|Leon' => 'SEAT Leon car',
        'Skoda|Octavia' => 'Skoda Octavia car',
        'Skoda|Karoq' => 'Skoda Karoq car',
        'Mini|Cooper' => 'Mini Hatch Cooper car',
        'Fiat|500' => 'Fiat 500 2007 car',
        'Ford|Puma' => 'Ford Puma crossover car',
        'Porsche|Taycan' => 'Porsche Taycan car',
        'Land Rover|Defender' => 'Land Rover Defender car'
    ];
    $key = $make . '|' . $model;
    return $aliases[$key] ?? ($make . ' ' . $model . ' car');
}

function articleTitle($make, $model, $year)
{
    $models = [
        'BMW|Série 2' => 'BMW_2_Series_(F22)',
        'Peugeot|3008' => 'Peugeot_3008',
        'Honda|Civic' => 'Honda_Civic_(tenth_generation)',
        'Mercedes-Benz|Classe A' => 'Mercedes-Benz_A-Class_(W177)',
        'Audi|A3' => 'Audi_A3',
        'Renault|Clio' => 'Renault_Clio_(fifth_generation)',
        'Toyota|Yaris' => 'Toyota_Yaris_(XP210)',
        'Volkswagen|Golf' => 'Volkswagen_Golf_Mk7',
        'Citroen|C3' => 'Citroen_C3_(third_generation)',
        'Dacia|Duster' => 'Dacia_Duster',
        'Tesla|Model 3' => 'Tesla_Model_3',
        'Ford|Puma' => 'Ford_Puma_(crossover)',
        'Kia|Sportage' => 'Kia_Sportage',
        'Hyundai|Kona' => 'Hyundai_Kona',
        'Mini|Cooper' => 'Mini_Hatch',
        'Nissan|Qashqai' => 'Nissan_Qashqai',
        'Opel|Corsa' => 'Opel_Corsa',
        'Mazda|CX-30' => 'Mazda_CX-30',
        'Skoda|Octavia' => 'Skoda_Octavia',
        'Fiat|500' => 'Fiat_500_(2007)',
        'Volvo|XC40' => 'Volvo_XC40',
        'Alfa Romeo|Giulietta' => 'Alfa_Romeo_Giulietta_(940)',
        'Lexus|UX' => 'Lexus_UX',
        'Toyota|Corolla' => 'Toyota_Corolla_(E210)',
        'Dacia|Sandero' => 'Dacia_Sandero',
        'Renault|Twingo' => 'Renault_Twingo_III',
        'Peugeot|208' => 'Peugeot_208_(second_generation)',
        'Citroen|C1' => 'Citroen_C1',
        'Toyota|Aygo' => 'Toyota_Aygo',
        'Ford|Fiesta' => 'Ford_Fiesta_(seventh_generation)',
        'Hyundai|i10' => 'Hyundai_i10',
        'Seat|Leon' => 'SEAT_Leon_(third_generation)',
        'Honda|HR-V' => 'Honda_HR-V_(third_generation)',
        'BMW|118i' => 'BMW_1_Series_(F40)',
        'Audi|Q2' => 'Audi_Q2',
        'Renault|Captur' => 'Renault_Captur_(second_generation)',
        'Peugeot|508' => 'Peugeot_508_(second_generation)',
        'Toyota|C-HR' => 'Toyota_C-HR',
        'Skoda|Karoq' => 'Skoda_Karoq',
        'Volkswagen|Tiguan' => 'Volkswagen_Tiguan_(second_generation)',
        'Mercedes-Benz|Classe C' => 'Mercedes-Benz_C-Class_(W205)',
        'BMW|X1' => 'BMW_X1_(F48)',
        'Audi|Q3' => 'Audi_Q3_(second_generation)',
        'Kia|EV6' => 'Kia_EV6',
        'Peugeot|5008' => 'Peugeot_5008_(second_generation)',
        'Tesla|Model Y' => 'Tesla_Model_Y',
        'Volvo|XC60' => 'Volvo_XC60_(second_generation)',
        'BMW|320d' => 'BMW_3_Series_(G20)',
        'Audi|A4' => 'Audi_A4_(B9)',
        'Mercedes-Benz|GLA' => 'Mercedes-Benz_GLA_(H247)',
        'Toyota|RAV4' => 'Toyota_RAV4_(fifth_generation)',
        'Porsche|Macan' => 'Porsche_Macan',
        'BMW|X5' => 'BMW_X5_(G05)',
        'Mercedes-Benz|GLC' => 'Mercedes-Benz_GLC_(X253)',
        'Audi|Q5' => 'Audi_Q5_(second_generation)',
        'Porsche|Taycan' => 'Porsche_Taycan',
        'Tesla|Model S' => 'Tesla_Model_S',
        'Lexus|RX' => 'Lexus_RX_(fifth_generation)',
        'Land Rover|Defender' => 'Land_Rover_Defender_(L663)'
    ];
    return $models[$make . '|' . $model] ?? null;
}

function commonsImageInfo($imageUrl)
{
    $path = parse_url($imageUrl, PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    $fileName = rawurldecode(end($segments));
    $fileName = preg_replace('/^\d+px-/', '', $fileName);
    $url = 'https://commons.wikimedia.org/w/api.php?' . http_build_query([
        'action' => 'query',
        'titles' => 'File:' . $fileName,
        'prop' => 'imageinfo',
        'iiprop' => 'url|extmetadata',
        'iiurlwidth' => 1200,
        'format' => 'json'
    ]);
    $response = requestJson($url);
    $page = current($response['query']['pages'] ?? []);
    $imageInfo = $page['imageinfo'][0] ?? null;
    if (!$imageInfo) {
        return false;
    }

    $metadata = $imageInfo['extmetadata'] ?? [];
    return [
        'download_url' => $imageInfo['thumburl'] ?? $imageInfo['url'],
        'file_title' => $page['title'],
        'file_page' => $imageInfo['descriptionurl'] ?? 'https://commons.wikimedia.org/wiki/' . rawurlencode($page['title']),
        'artist' => strip_tags($metadata['Artist']['value'] ?? 'Author listed on Commons'),
        'license' => strip_tags($metadata['LicenseShortName']['value'] ?? ''),
        'license_url' => strip_tags($metadata['LicenseUrl']['value'] ?? '')
    ];
}

function licenseAllowsReuse($license)
{
    return (bool) preg_match('/^(CC0|CC BY(?:-SA)?(?:\s|$)|Public domain|PD)/i', trim($license));
}

function candidateScore($title, $make, $model)
{
    $modelKey = $make . '|' . $model;
    $title = normalizeText($title);
    $make = normalizeText($make);
    $modelAliases = [
        'BMW|Série 2' => 'BMW M235i',
        'BMW|118i' => '1 series',
        'BMW|320d' => '3 series',
        'Mercedes-Benz|Classe A' => 'a class',
        'Mercedes-Benz|Classe C' => 'c class',
        'Mercedes-Benz|GLA' => 'gla class',
        'Mercedes-Benz|GLC' => 'glc class',
        'Seat|Leon' => 'leon',
        'Skoda|Octavia' => 'octavia',
        'Skoda|Karoq' => 'karoq',
        'Mini|Cooper' => 'mini hatch',
        'Fiat|500' => 'fiat 500',
        'Ford|Puma' => 'puma',
        'Porsche|Taycan' => 'taycan',
        'Land Rover|Defender' => 'defender'
    ];
    $model = normalizeText($modelAliases[$modelKey] ?? $model);
    $makeTokens = array_values(array_filter(explode(' ', $make), function ($token) {
        return strlen($token) > 1 && $token !== 'benz';
    }));
    $modelTokens = array_values(array_filter(explode(' ', $model), function ($token) {
        return (strlen($token) > 1 || ctype_digit($token))
            && !in_array($token, ['serie', 'class', 'classe', 'model'], true);
    }));

    $score = 0;
    foreach ($makeTokens as $token) {
        if (strpos($title, $token) !== false) {
            $score += 2;
        }
    }
    foreach ($modelTokens as $token) {
        if (strpos($title, $token) !== false) {
            $score += 5;
        }
    }
    if (preg_match('/\b(car|automobile|vehicle|disambiguation|film|song)\b/', $title)) {
        $score -= 2;
    }
    return $score;
}

function commonsSearchCandidate($make, $model)
{
    $url = 'https://commons.wikimedia.org/w/api.php?' . http_build_query([
        'action' => 'query',
        'generator' => 'search',
        'gsrsearch' => searchTerms($make, $model),
        'gsrnamespace' => 6,
        'gsrlimit' => 12,
        'prop' => 'imageinfo',
        'iiprop' => 'url',
        'iiurlwidth' => 1200,
        'format' => 'json'
    ]);
    $response = requestJson($url);
    $pages = array_values($response['query']['pages'] ?? []);
    usort($pages, function ($left, $right) use ($make, $model) {
        return candidateScore($right['title'], $make, $model)
            <=> candidateScore($left['title'], $make, $model);
    });

    foreach ($pages as $page) {
        $imageUrl = $page['imageinfo'][0]['thumburl'] ?? null;
        if ($imageUrl && candidateScore($page['title'], $make, $model) >= 7) {
            return $imageUrl;
        }
    }
    return false;
}

$db = new Db();
$connection = $db->getConnection();
$vehicles = $connection->query('SELECT id, marque, modele, annee FROM voitures ORDER BY id')->fetchAll();
$imageDirectory = dirname(__DIR__) . '/images/cars';
if (!is_dir($imageDirectory)) {
    mkdir($imageDirectory, 0777, true);
}

$manifest = [];
$credits = [];
$imageFileTitles = [];
foreach ($vehicles as $vehicle) {
    $pageTitle = articleTitle($vehicle['marque'], $vehicle['modele'], (int) $vehicle['annee']);
    if (!$pageTitle) {
        echo 'No confident match: ', $vehicle['marque'], ' ', $vehicle['modele'], PHP_EOL;
        continue;
    }

    $summary = requestJson('https://en.wikipedia.org/api/rest_v1/page/summary/' . rawurlencode($pageTitle));
    $imageUrl = $summary['thumbnail']['source'] ?? null;
    $imageInfo = $imageUrl ? commonsImageInfo($imageUrl) : false;
    if (!$imageInfo || !licenseAllowsReuse($imageInfo['license'])) {
        $imageUrl = commonsSearchCandidate($vehicle['marque'], $vehicle['modele']);
        $imageInfo = $imageUrl ? commonsImageInfo($imageUrl) : false;
    }
    if (!$imageInfo || !licenseAllowsReuse($imageInfo['license'])) {
        echo 'No verified model image: ', $vehicle['marque'], ' ', $vehicle['modele'], PHP_EOL;
        continue;
    }

    $image = downloadImage($imageInfo['download_url']);
    if (!$image) {
        echo 'Image download failed: ', $vehicle['marque'], ' ', $vehicle['modele'], PHP_EOL;
        continue;
    }

    $fileName = sprintf('vehicle-%d.jpg', $vehicle['id']);
    file_put_contents($imageDirectory . '/' . $fileName, $image);
    $relativePath = 'images/cars/' . $fileName;
    $manifest[$vehicle['id']] = [
        'path' => $relativePath,
        'make' => $vehicle['marque'],
        'model' => $vehicle['modele'],
        'year' => (int) $vehicle['annee'],
        'article' => $summary['title'] ?? $pageTitle,
        'article_url' => $summary['content_urls']['desktop']['page'] ?? ('https://en.wikipedia.org/wiki/' . $pageTitle),
        'file_title' => $imageInfo['file_title'],
        'credit' => [
            'artist' => $imageInfo['artist'],
            'license' => $imageInfo['license'],
            'license_url' => $imageInfo['license_url'],
            'file_page' => $imageInfo['file_page']
        ]
    ];
    $query = $connection->prepare('UPDATE voitures SET image_url = :image_url WHERE id = :id');
    $query->execute(['image_url' => $relativePath, 'id' => $vehicle['id']]);
    echo 'Downloaded: ', $vehicle['marque'], ' ', $vehicle['modele'], ' => ', $summary['title'] ?? $pageTitle,
        ' [', $imageInfo['license'], ']', PHP_EOL;
}

file_put_contents(
    $imageDirectory . '/manifest.json',
    json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
);

$creditLines = [
    '# Photos des véhicules',
    '',
    'Photos téléchargées depuis Wikipédia/ Wikimedia Commons. Chaque fichier conserve son titre de source, auteur et licence ci-dessous.',
    ''
];
foreach ($manifest as $entry) {
    $credit = $entry['credit'] ?? [];
    $creditLines[] = '- ' . $entry['make'] . ' ' . $entry['model'] . ' (' . $entry['year'] . '): '
        . '[' . $entry['file_title'] . '](' . ($credit['file_page'] ?? $entry['article_url']) . ')'
        . ' — ' . ($credit['artist'] ?? 'Auteur sur Commons')
        . ', [' . ($credit['license'] ?? 'licence à vérifier') . '](' . ($credit['license_url'] ?? '') . ')';
}
file_put_contents(dirname(__DIR__) . '/IMAGE_CREDITS.md', implode(PHP_EOL, $creditLines) . PHP_EOL);
echo 'Matched images: ', count($manifest), ' / ', count($vehicles), PHP_EOL;