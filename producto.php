<?php
// producto.php

$jsonPath = __DIR__ . '/data/tienda.json';

$data = [
    'categories' => [],
    'products' => []
];

if (file_exists($jsonPath)) {
    $jsonContent = file_get_contents($jsonPath);
    $decoded = json_decode($jsonContent, true);

    if (is_array($decoded)) {
        $data['categories'] = $decoded['categories'] ?? [];
        $data['products'] = $decoded['products'] ?? [];
    }
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function getCategoryTitle($categorySlug, $categories)
{
    foreach ($categories as $category) {
        if (($category['slug'] ?? '') === $categorySlug) {
            return $category['title'] ?? 'Sin categoría';
        }
    }

    return 'Sin categoría';
}

$slug = trim($_GET['p'] ?? '');
$product = null;

foreach ($data['products'] as $item) {
    if (($item['slug'] ?? '') === $slug) {
        $product = $item;
        break;
    }
}

if (!$product) {
    http_response_code(404);
}

$categoryTitle = $product
    ? getCategoryTitle($product['category_slug'] ?? '', $data['categories'])
    : '';

$status = trim($product['status'] ?? '');

if ($status === '') {
    $status = 'Disponible';
}

$isSoldOut = mb_strtolower($status, 'UTF-8') === 'agotado';

$mainImage = $product['img'] ?? '';
$gallery = $product['gallery'] ?? [];

if (empty($gallery) && $mainImage !== '') {
    $gallery = [$mainImage];
}

if ($mainImage === '' && !empty($gallery[0])) {
    $mainImage = $gallery[0];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $product ? e($product['name']) : 'Producto no encontrado'; ?> | Le Grand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#f7f6f2] text-neutral-950">

<header class="sticky top-0 z-50 border-b border-black/5 bg-white/90 backdrop-blur-xl">
    <div class="mx-auto flex min-h-[76px] w-[min(1180px,92%)] items-center justify-between">
        <a href="./index.php">
            <img src="./media/cropped-logo-1.png" class="w-[180px]" alt="Le Grand">
        </a>

        <a href="./index.php#tienda" class="rounded-full bg-black px-5 py-3 text-sm font-black text-white transition hover:bg-[#5FD8AD] hover:text-black">
            Volver a tienda
        </a>
    </div>
</header>

<?php if (!$product): ?>

<main class="grid min-h-[70vh] place-items-center px-4 text-center">
    <div class="max-w-xl rounded-[2rem] bg-white p-8 shadow-[0_30px_100px_rgba(0,0,0,.08)]">
        <h1 class="text-4xl font-black">Producto no encontrado</h1>

        <p class="mt-3 text-neutral-500">
            El producto seleccionado no existe o fue removido.
        </p>

        <?php if ($slug !== ''): ?>
            <div class="mt-5 rounded-2xl bg-neutral-50 px-4 py-3 text-sm font-bold text-neutral-500">
                Slug consultado:
                <span class="text-black"><?= e($slug); ?></span>
            </div>
        <?php endif; ?>

        <a href="./index.php#tienda" class="mt-6 inline-flex rounded-full bg-black px-6 py-3 font-bold text-white">
            Ver productos
        </a>
    </div>
</main>

<?php else: ?>

<main class="px-4 py-12">
    <section class="mx-auto grid w-[min(1180px,100%)] gap-10 lg:grid-cols-[1.05fr_.95fr]">

        <div class="grid gap-5">
            <div class="relative overflow-hidden rounded-[2rem] bg-white p-6 shadow-[0_30px_100px_rgba(0,0,0,.10)]">
                <?php if (!empty($product['discount'])): ?>
                    <span class="absolute right-5 top-5 rounded-full bg-[#5FD8AD] px-4 py-2 text-xs font-black text-black">
                        <?= e($product['discount']); ?>
                    </span>
                <?php endif; ?>

                <?php if ($mainImage !== ''): ?>
                    <img 
                        id="mainImage" 
                        src="<?= e($mainImage); ?>" 
                        alt="<?= e($product['alt'] ?? $product['name']); ?>" 
                        class="mx-auto h-[480px] w-full object-contain"
                    >
                <?php else: ?>
                    <div class="grid h-[480px] place-items-center text-center">
                        <div>
                            <p class="text-5xl font-black text-neutral-300">LG</p>
                            <p class="mt-3 text-sm font-bold text-neutral-400">Sin imagen disponible</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($gallery)): ?>
                <div class="grid grid-cols-4 gap-3 sm:grid-cols-6">
                    <?php foreach ($gallery as $img): ?>
                        <button 
                            type="button" 
                            onclick="changeImage('<?= e($img); ?>')" 
                            class="rounded-2xl border border-black/10 bg-white p-2 shadow-sm transition hover:border-[#5FD8AD]"
                        >
                            <img 
                                src="<?= e($img); ?>" 
                                alt="<?= e($product['name']); ?>" 
                                class="h-24 w-full object-contain"
                            >
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="rounded-[2rem] bg-white p-6 shadow-[0_30px_100px_rgba(0,0,0,.08)] sm:p-8">
            <p class="text-xs font-black uppercase tracking-[.22em] text-[#2D9B6B]">
                <?= e($product['meta'] ?? $categoryTitle); ?>
            </p>

            <h1 class="mt-4 text-4xl font-black leading-none tracking-[-.05em] sm:text-5xl">
                <?= e($product['name']); ?>
            </h1>

            <div class="mt-5 flex items-center gap-3">
                <?php if (!empty($product['old_price'])): ?>
                    <del class="text-xl font-bold text-neutral-400">
                        <?= e($product['old_price']); ?>
                    </del>
                <?php endif; ?>

                <span class="text-3xl font-black">
                    <?= e($product['price'] ?? ''); ?>
                </span>
            </div>

            <div class="mt-5 inline-flex rounded-full <?= $isSoldOut ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700'; ?> px-4 py-2 text-sm font-black">
                <?= e($status); ?>
            </div>

            <p class="mt-7 text-base font-medium leading-8 text-neutral-600">
                <?= e($product['description'] ?? 'Producto seleccionado por Le Grand Montres & Bijoux.'); ?>
            </p>

            <div class="mt-8 grid gap-3 sm:grid-cols-2">
                <?php if (!$isSoldOut): ?>
                    <a 
                        href="https://wa.me/51999999999?text=Hola,%20quiero%20consultar%20por%20<?= rawurlencode($product['name']); ?>"
                        target="_blank"
                        class="inline-flex items-center justify-center rounded-2xl bg-[#5FD8AD] px-6 py-4 text-sm font-black uppercase tracking-[.12em] text-black transition hover:bg-black hover:text-white"
                    >
                        WhatsApp
                    </a>

                    <button 
                        type="button" 
                        class="rounded-2xl bg-black px-6 py-4 text-sm font-black uppercase tracking-[.12em] text-white"
                    >
                        Añadir al carrito
                    </button>
                <?php else: ?>
                    <button 
                        type="button" 
                        disabled 
                        class="col-span-full rounded-2xl bg-neutral-200 px-6 py-4 text-sm font-black uppercase tracking-[.12em] text-neutral-500"
                    >
                        Producto agotado
                    </button>
                <?php endif; ?>
            </div>

            <div class="mt-8 border-t border-black/10 pt-6 text-sm font-semibold text-neutral-600">
                <p>
                    <strong class="text-black">Categoría:</strong> 
                    <?= e($categoryTitle); ?>
                </p>

                <p class="mt-2">
                    <strong class="text-black">Código:</strong> 
                    <?= e($product['slug']); ?>
                </p>

                <p class="mt-2">
                    <strong class="text-black">Tienda:</strong> 
                    Le Grand Montres & Bijoux
                </p>
            </div>
        </div>

    </section>
</main>

<script>
function changeImage(src) {
    const mainImage = document.getElementById('mainImage');

    if (mainImage) {
        mainImage.src = src;
    }
}
</script>

<?php endif; ?>

</body>
</html>