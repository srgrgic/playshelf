<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/RawgClient.php';

header_remove('X-Powered-By');

$putanja = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metoda  = $_SERVER['REQUEST_METHOD'];

if (str_starts_with($putanja, '/api/')) {
    header('Content-Type: application/json; charset=utf-8');

    if ($putanja === '/api/v1/igre' && $metoda === 'GET') {
        $upit     = $_GET['q'] ?? '';
        $stranica = max(1, (int)($_GET['stranica'] ?? 1));
        try {
            $rezultat = rawgSearchGames($upit, $stranica);
            echo json_encode([
                'podaci' => [
                    'ukupno'   => $rezultat['count'],
                    'stranica' => $stranica,
                    'igre'     => array_map(fn($g) => [
                        'id'      => $g['id'],
                        'naslov'  => $g['name'],
                        'slika'   => $g['background_image'],
                        'ocjena'  => $g['rating'],
                        'izlazak' => $g['released'],
                    ], $rezultat['results']),
                ],
                'greska' => null,
            ], JSON_UNESCAPED_UNICODE);
        } catch (RuntimeException $e) {
            http_response_code(502);
            echo json_encode([
                'podaci' => null,
                'greska' => ['poruka' => 'Vanjski servis nije dostupan'],
            ]);
        }
        exit;
    }

    http_response_code(404);
    echo json_encode(['podaci' => null, 'greska' => ['poruka' => 'Ruta ne postoji']]);
    exit;
}


require __DIR__ . '/../templates/pocetna.php';