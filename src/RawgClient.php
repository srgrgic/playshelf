<?php
require_once __DIR__ . '/config.php';

function rawgGet(string $path, array $query = []): array {
    $query['key'] = env('RAWG_API_KEY');
    $url = 'https://api.rawg.io/api/' . $path . '?' . http_build_query($query);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false || $code !== 200) {
        throw new RuntimeException("RAWG greška, HTTP $code");
    }
    return json_decode($body, true);
}

function rawgSearchGames(string $upit, int $stranica = 1): array {
    return rawgGet('games', [
        'search'    => $upit,
        'page'      => $stranica,
        'page_size' => 12,
    ]);
}