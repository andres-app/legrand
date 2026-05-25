<?php
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function imgAdmin($path)
{
    if (!$path) {
        return '';
    }

    return '../' . str_replace('./', '', $path);
}

function loadStoreData()
{
    $jsonPath = __DIR__ . '/../../data/tienda.json';

    if (!file_exists($jsonPath)) {
        if (!is_dir(__DIR__ . '/../../data')) {
            mkdir(__DIR__ . '/../../data', 0775, true);
        }

        file_put_contents($jsonPath, json_encode([
            'categories' => [],
            'products' => []
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    $data = json_decode(file_get_contents($jsonPath), true);

    return $data ?: [
        'categories' => [],
        'products' => []
    ];
}

function categoryTitle($slug, $categories)
{
    foreach ($categories as $cat) {
        if (($cat['slug'] ?? '') === $slug) {
            return $cat['title'] ?? $slug;
        }
    }

    return $slug;
}