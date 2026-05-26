<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/uploads.php';

$jsonPath = __DIR__ . '/../data/tienda.json';

if (!file_exists($jsonPath)) {
    if (!is_dir(__DIR__ . '/../data')) {
        mkdir(__DIR__ . '/../data', 0775, true);
    }

    file_put_contents($jsonPath, json_encode([
        'categories' => [],
        'products' => []
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$data = json_decode(file_get_contents($jsonPath), true);

if (!is_array($data)) {
    $data = [
        'categories' => [],
        'products' => []
    ];
}

$data['categories'] = $data['categories'] ?? [];
$data['products'] = $data['products'] ?? [];

function slugify($text)
{
    $text = strtolower(trim($text));
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function saveJson($path, $data)
{
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function redirectDashboard($params = '')
{
    $url = 'dashboard.php';

    if ($params !== '') {
        $url .= '?' . ltrim($params, '?');
    }

    header('Location: ' . $url);
    exit;
}

$type = $_POST['type'] ?? '';
$mode = $_POST['mode'] ?? 'create';

/* =========================================================
   CATEGORÍAS
========================================================= */
if ($type === 'category') {
    $oldSlug = trim($_POST['old_slug'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? 'Ver colección');

    if ($mode === 'delete' && $oldSlug !== '') {
        $data['categories'] = array_values(array_filter($data['categories'], function ($cat) use ($oldSlug) {
            return ($cat['slug'] ?? '') !== $oldSlug;
        }));

        $data['products'] = array_values(array_filter($data['products'], function ($product) use ($oldSlug) {
            return ($product['category_slug'] ?? '') !== $oldSlug;
        }));

        saveJson($jsonPath, $data);
        redirectDashboard('ok=category_deleted');
    }

    if ($title !== '') {
        $slug = $oldSlug ?: slugify($title);
        $newImg = uploadImage($_FILES['img'] ?? [], 'categoria');

        $categoryData = [
            'slug' => $slug,
            'title' => $title,
            'subtitle' => $subtitle,
            'img' => $newImg,
            'alt' => $title
        ];

        if ($mode === 'update' && $oldSlug !== '') {
            foreach ($data['categories'] as &$cat) {
                if (($cat['slug'] ?? '') === $oldSlug) {
                    $categoryData['img'] = $newImg ?: ($cat['img'] ?? '');
                    $cat = $categoryData;
                    break;
                }
            }
            unset($cat);
        } else {
            $categoryData['img'] = $newImg;
            $data['categories'][] = $categoryData;
        }

        saveJson($jsonPath, $data);
        redirectDashboard('ok=category_saved');
    }

    redirectDashboard('error=category_empty');
}

/* =========================================================
   PRODUCTOS
========================================================= */
if ($type === 'product') {
    $oldSlug = trim($_POST['old_slug'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $categorySlug = trim($_POST['category_slug'] ?? '');

    if ($mode === 'delete' && $oldSlug !== '') {
        $data['products'] = array_values(array_filter($data['products'], function ($product) use ($oldSlug) {
            return ($product['slug'] ?? '') !== $oldSlug;
        }));

        saveJson($jsonPath, $data);
        redirectDashboard('ok=product_deleted');
    }

    if ($name === '' || $categorySlug === '') {
        redirectDashboard('error=product_empty');
    }

    $saleFeatured = !empty($_POST['sale_featured']);
    $saleOrder = max(1, min(5, (int)($_POST['sale_order'] ?? 5)));

    /*
        Validación:
        Solo se permiten máximo 5 productos marcados para “Rebajas disponibles”.
        Cuando se edita un producto existente, ese mismo producto no se cuenta.
    */
    if ($saleFeatured) {
        $currentSaleCount = 0;

        foreach ($data['products'] as $existingProduct) {
            $existingSlug = $existingProduct['slug'] ?? '';

            if ($oldSlug !== '' && $existingSlug === $oldSlug) {
                continue;
            }

            if (!empty($existingProduct['sale_featured'])) {
                $currentSaleCount++;
            }
        }

        if ($currentSaleCount >= 5) {
            redirectDashboard('error=max_rebajas');
        }
    }

    $slug = $oldSlug ?: slugify($name);
    $newImg = uploadImage($_FILES['img'] ?? [], 'producto');

    $productData = [
        'slug' => $slug,
        'category_slug' => $categorySlug,
        'img' => $newImg,
        'alt' => $name,
        'discount' => trim($_POST['discount'] ?? ''),
        'status' => trim($_POST['status'] ?? ''),
        'wish' => !empty($_POST['wish']),

        /*
            NUEVO:
            Control manual para la sección “Rebajas disponibles”.
        */
        'sale_featured' => $saleFeatured,
        'sale_order' => $saleOrder,

        'meta' => trim($_POST['meta'] ?? ''),
        'name' => $name,
        'old_price' => trim($_POST['old_price'] ?? ''),
        'price' => trim($_POST['price'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'action' => 'Ver detalle',
        'gallery' => []
    ];

    if ($mode === 'update' && $oldSlug !== '') {
        $updated = false;

        foreach ($data['products'] as &$product) {
            if (($product['slug'] ?? '') === $oldSlug) {
                $finalImg = $newImg ?: ($product['img'] ?? '');

                $productData['img'] = $finalImg;
                $productData['gallery'] = $product['gallery'] ?? [];

                if ($newImg) {
                    $productData['gallery'][] = $newImg;
                    $productData['gallery'] = array_values(array_unique($productData['gallery']));
                }

                if (empty($productData['gallery']) && $finalImg) {
                    $productData['gallery'] = [$finalImg];
                }

                $product = $productData;
                $updated = true;
                break;
            }
        }

        unset($product);

        if (!$updated) {
            $productData['img'] = $newImg;
            $productData['gallery'] = $newImg ? [$newImg] : [];
            $data['products'][] = $productData;
        }
    } else {
        $productData['img'] = $newImg;
        $productData['gallery'] = $newImg ? [$newImg] : [];
        $data['products'][] = $productData;
    }

    saveJson($jsonPath, $data);
    redirectDashboard('ok=product_saved');
}

redirectDashboard();