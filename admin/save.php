<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/uploads.php';

$jsonPath = __DIR__ . '/../data/tienda.json';

$data = json_decode(file_get_contents($jsonPath), true);

if (!$data) {
    $data = [
        'categories' => [],
        'products' => []
    ];
}

function slugify($text)
{
    $text = strtolower(trim($text));
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

$type = $_POST['type'] ?? '';

if ($type === 'category') {
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? 'Ver colección');

    if ($title !== '') {
        $slug = slugify($title);
        $img = uploadImage($_FILES['img'] ?? [], 'categoria');

        $data['categories'][] = [
            'slug' => $slug,
            'title' => $title,
            'subtitle' => $subtitle,
            'img' => $img,
            'alt' => $title
        ];
    }
}

if ($type === 'product') {
    $name = trim($_POST['name'] ?? '');
    $categorySlug = trim($_POST['category_slug'] ?? '');

    if ($name !== '' && $categorySlug !== '') {
        $slug = slugify($name);
        $img = uploadImage($_FILES['img'] ?? [], 'producto');

        $data['products'][] = [
            'slug' => $slug,
            'category_slug' => $categorySlug,
            'img' => $img,
            'alt' => $name,
            'discount' => trim($_POST['discount'] ?? ''),
            'status' => trim($_POST['status'] ?? ''),
            'wish' => false,
            'meta' => trim($_POST['meta'] ?? ''),
            'name' => $name,
            'old_price' => trim($_POST['old_price'] ?? ''),
            'price' => trim($_POST['price'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'action' => 'Ver detalle',
            'gallery' => [$img]
        ];
    }
}

file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header('Location: dashboard.php');
exit;