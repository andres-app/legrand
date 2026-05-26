<?php
// categoria.php

$jsonPath = __DIR__ . '/data/tienda.json';

$data = [
    'categories' => [],
    'products' => []
];

if (file_exists($jsonPath)) {
    $jsonContent = file_get_contents($jsonPath);
    $decodedData = json_decode($jsonContent, true);

    if (is_array($decodedData)) {
        $data['categories'] = $decodedData['categories'] ?? [];
        $data['products'] = $decodedData['products'] ?? [];
    }
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function normalizeImagePath($path)
{
    $path = trim((string) $path);

    if ($path === '') {
        return './media/no-image.png';
    }

    return $path;
}

function normalizeDiscount($discount)
{
    $discount = trim((string) $discount);

    if ($discount === '') {
        return '';
    }

    if (str_contains($discount, '%')) {
        return $discount;
    }

    if (is_numeric($discount)) {
        return '-' . $discount . '%';
    }

    return $discount;
}

function normalizePrice($price)
{
    $price = trim((string) $price);

    if ($price === '') {
        return '';
    }

    if (str_starts_with($price, 'S/')) {
        return $price;
    }

    if (is_numeric($price)) {
        return 'S/ ' . number_format((float) $price, 2);
    }

    return $price;
}

$cat = $_GET['cat'] ?? '';

$categoryMap = [];

foreach ($data['categories'] as $category) {
    $slug = $category['slug'] ?? '';

    if ($slug !== '') {
        $categoryMap[$slug] = [
            'title' => $category['title'] ?? 'Categoría',
            'subtitle' => $category['subtitle'] ?? 'Ver colección',
            'img' => $category['img'] ?? '',
            'alt' => $category['alt'] ?? ($category['title'] ?? 'Categoría')
        ];
    }
}

$categoryExists = isset($categoryMap[$cat]);
$categoryName = $categoryExists ? $categoryMap[$cat]['title'] : 'Categoría no encontrada';

$products = $data['products'] ?? [];

// Nuevos productos primero
$products = array_reverse($products);

$filteredProducts = array_values(array_filter($products, function ($product) use ($cat) {
    return trim((string)($product['category_slug'] ?? '')) === trim((string)$cat);
}));

if (!$categoryExists) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= e($categoryName); ?> | Le Grand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background:
                radial-gradient(circle at 5% -10%, rgba(95, 216, 173, .14), transparent 34rem),
                radial-gradient(circle at 95% 0%, rgba(217, 184, 115, .16), transparent 30rem),
                linear-gradient(180deg, #fbfaf7 0%, #ffffff 45%, #f7f6f2 100%);
        }
    </style>
</head>

<body class="min-h-screen text-neutral-950 antialiased">

<header class="sticky top-0 z-50 border-b border-black/5 bg-white/85 backdrop-blur-xl">
    <div class="mx-auto flex min-h-[76px] w-[min(1180px,92%)] items-center justify-between">
        <a href="./index.php">
            <img src="./media/cropped-logo-1.png" class="w-[180px]" alt="Le Grand">
        </a>

        <a href="./index.php#relojes" class="rounded-full bg-black px-5 py-3 text-sm font-black text-white transition hover:bg-[#5FD8AD] hover:text-black">
            Volver
        </a>
    </div>
</header>

<main class="px-4 py-14">
    <section class="mx-auto w-[min(1180px,100%)]">
        <div class="mb-10 text-center">
            <p class="text-xs font-black uppercase tracking-[.22em] text-[#2D9B6B]">
                Colección
            </p>

            <h1 class="mt-4 text-4xl font-black tracking-[-.05em] sm:text-5xl">
                <?= e($categoryName); ?>
            </h1>

            <p class="mx-auto mt-4 max-w-xl text-neutral-500">
                Productos disponibles en esta categoría.
            </p>
        </div>

        <?php if (!$categoryExists): ?>

            <div class="rounded-[2rem] border border-black/5 bg-white p-10 text-center shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                <h2 class="text-2xl font-black">Categoría no encontrada</h2>
                <p class="mt-3 text-neutral-500">La categoría seleccionada no existe.</p>

                <a href="./index.php#relojes" class="mt-6 inline-flex rounded-full bg-black px-6 py-3 text-sm font-black text-white transition hover:bg-[#5FD8AD] hover:text-black">
                    Ver categorías
                </a>
            </div>

        <?php elseif (empty($filteredProducts)): ?>

            <div class="rounded-[2rem] border border-black/5 bg-white p-10 text-center shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                <h2 class="text-2xl font-black">No hay productos disponibles</h2>
                <p class="mt-3 text-neutral-500">Aún no se registraron productos para esta categoría.</p>
            </div>

        <?php else: ?>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <?php foreach ($filteredProducts as $product): ?>
                    <?php
                    $slug = $product['slug'] ?? '';
                    $name = $product['name'] ?? 'Producto sin nombre';
                    $img = normalizeImagePath($product['img'] ?? '');
                    $alt = $product['alt'] ?? $name;
                    $discount = normalizeDiscount($product['discount'] ?? '');
                    $status = trim((string)($product['status'] ?? ''));
                    $wish = !empty($product['wish']);
                    $meta = $product['meta'] ?? 'Producto';
                    $oldPrice = normalizePrice($product['old_price'] ?? '');
                    $price = normalizePrice($product['price'] ?? '');
                    $action = $product['action'] ?? 'Ver detalle';
                    ?>

                    <article class="group rounded-[1.7rem] border border-black/5 bg-white p-3 shadow-[0_24px_80px_rgba(0,0,0,.08)] transition duration-300 hover:-translate-y-1 hover:border-[#5FD8AD]/50">
                        <a href="./producto.php?p=<?= e($slug); ?>" class="relative grid aspect-[4/5] place-items-center overflow-hidden rounded-[1.35rem] bg-[#f6f5f1]">
                            <?php if ($status !== ''): ?>
                                <span class="absolute left-3 top-3 z-10 rounded-full bg-black px-3 py-1.5 text-[11px] font-black uppercase tracking-[.14em] text-white">
                                    <?= e($status); ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($discount !== ''): ?>
                                <span class="absolute right-3 top-3 z-10 rounded-full bg-[#5FD8AD] px-3 py-1.5 text-[11px] font-black text-black">
                                    <?= e($discount); ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($wish): ?>
                                <span class="absolute bottom-3 right-3 z-10 inline-flex items-center gap-1 rounded-full bg-white/90 px-3 py-2 text-xs font-bold text-neutral-700 shadow-sm backdrop-blur-xl">
                                    ♡ Lo deseo
                                </span>
                            <?php endif; ?>

                            <img src="<?= e($img); ?>" alt="<?= e($alt); ?>" class="h-full w-full object-contain p-5 transition duration-700 group-hover:scale-105">
                        </a>

                        <div class="px-2 pb-3 pt-5 text-center">
                            <p class="text-xs font-bold uppercase tracking-[.18em] text-[#2D9B6B]">
                                <?= e($meta); ?>
                            </p>

                            <h3 class="mt-3 min-h-[3rem] text-base font-black uppercase leading-6 text-neutral-950">
                                <?= e($name); ?>
                            </h3>

                            <div class="mt-3 flex items-center justify-center gap-2 text-sm">
                                <?php if ($oldPrice !== ''): ?>
                                    <del class="text-neutral-400"><?= e($oldPrice); ?></del>
                                <?php endif; ?>

                                <?php if ($price !== ''): ?>
                                    <span class="text-base font-black text-neutral-950"><?= e($price); ?></span>
                                <?php endif; ?>
                            </div>

                            <a href="./producto.php?p=<?= e($slug); ?>" class="mt-5 inline-flex rounded-full bg-[#5FD8AD] px-5 py-3 text-xs font-black uppercase tracking-[.13em] text-black transition hover:bg-black hover:text-white">
                                <?= e($action); ?>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </section>
</main>

</body>
</html>