<?php
$categories = [
    'relojes-caballero' => 'Relojes de caballero',
    'relojes-dama' => 'Relojes de dama',
    'correas' => 'Correas',
];

$products = [
    [
        'slug' => 'correa-fossil',
        'category_slug' => 'correas',
        'img' => './media/S241079_main-300x400.jpg',
        'alt' => 'Correa Fossil',
        'discount' => '-6%',
        'status' => '',
        'wish' => false,
        'meta' => 'Correas · Oferta',
        'name' => 'Correa Fossil',
        'old_price' => 'S/ 160.00',
        'price' => 'S/ 150.00',
        'action' => 'Ver detalle',
    ],
    [
        'slug' => 'reloj-fossil-fs4682',
        'category_slug' => 'relojes-caballero',
        'img' => './media/FS4682_main-300x400.jpg',
        'alt' => 'Reloj Fossil FS4682',
        'discount' => '-17%',
        'status' => 'Agotado',
        'wish' => false,
        'meta' => 'Caballero · Oferta · Relojes',
        'name' => 'Reloj Fossil FS4682',
        'old_price' => 'S/ 600.00',
        'price' => 'S/ 500.00',
        'action' => 'Ver detalle',
    ],
    [
        'slug' => 'reloj-fossil-fs4735',
        'category_slug' => 'relojes-caballero',
        'img' => './media/FS4735_main-300x400.jpg',
        'alt' => 'Reloj Fossil FS4735',
        'discount' => '-17%',
        'status' => '',
        'wish' => true,
        'meta' => 'Caballero · Oferta · Relojes',
        'name' => 'Reloj Fossil FS4735',
        'old_price' => 'S/ 600.00',
        'price' => 'S/ 500.00',
        'action' => 'Ver detalle',
    ],
    [
        'slug' => 'reloj-fossil-fs4812',
        'category_slug' => 'relojes-caballero',
        'img' => './media/FS4812_main-300x400.jpg',
        'alt' => 'Reloj Fossil FS4812',
        'discount' => '-17%',
        'status' => '',
        'wish' => false,
        'meta' => 'Caballero · Oferta · Relojes',
        'name' => 'Reloj Fossil FS4812',
        'old_price' => 'S/ 600.00',
        'price' => 'S/ 500.00',
        'action' => 'Ver detalle',
    ],
    [
        'slug' => 'reloj-fossil-fs4813',
        'category_slug' => 'relojes-caballero',
        'img' => './media/FS4813_main-300x400.jpg',
        'alt' => 'Reloj Fossil FS4813',
        'discount' => '-17%',
        'status' => '',
        'wish' => false,
        'meta' => 'Caballero · Oferta · Relojes',
        'name' => 'Reloj Fossil FS4813',
        'old_price' => 'S/ 600.00',
        'price' => 'S/ 500.00',
        'action' => 'Ver detalle',
    ],
];

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$cat = $_GET['cat'] ?? '';
$categoryName = $categories[$cat] ?? 'Categoría no encontrada';

$filteredProducts = array_filter($products, function ($product) use ($cat) {
    return ($product['category_slug'] ?? '') === $cat;
});

if (!isset($categories[$cat])) {
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

        <?php if (!isset($categories[$cat])): ?>

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
                    <article class="group rounded-[1.7rem] border border-black/5 bg-white p-3 shadow-[0_24px_80px_rgba(0,0,0,.08)] transition duration-300 hover:-translate-y-1 hover:border-[#5FD8AD]/50">
                        <a href="./producto.php?p=<?= e($product['slug']); ?>" class="relative grid aspect-[4/5] place-items-center overflow-hidden rounded-[1.35rem] bg-[#f6f5f1]">
                            <?php if (!empty($product['status'])): ?>
                                <span class="absolute left-3 top-3 z-10 rounded-full bg-black px-3 py-1.5 text-[11px] font-black uppercase tracking-[.14em] text-white">
                                    <?= e($product['status']); ?>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($product['discount'])): ?>
                                <span class="absolute right-3 top-3 z-10 rounded-full bg-[#5FD8AD] px-3 py-1.5 text-[11px] font-black text-black">
                                    <?= e($product['discount']); ?>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($product['wish'])): ?>
                                <span class="absolute bottom-3 right-3 z-10 inline-flex items-center gap-1 rounded-full bg-white/90 px-3 py-2 text-xs font-bold text-neutral-700 shadow-sm backdrop-blur-xl">
                                    ♡ Lo deseo
                                </span>
                            <?php endif; ?>

                            <img src="<?= e($product['img']); ?>" alt="<?= e($product['alt']); ?>" class="h-full w-full object-contain p-5 transition duration-700 group-hover:scale-105">
                        </a>

                        <div class="px-2 pb-3 pt-5 text-center">
                            <p class="text-xs font-bold uppercase tracking-[.18em] text-[#2D9B6B]">
                                <?= e($product['meta']); ?>
                            </p>

                            <h3 class="mt-3 min-h-[3rem] text-base font-black uppercase leading-6 text-neutral-950">
                                <?= e($product['name']); ?>
                            </h3>

                            <div class="mt-3 flex items-center justify-center gap-2 text-sm">
                                <del class="text-neutral-400"><?= e($product['old_price']); ?></del>
                                <span class="text-base font-black text-neutral-950"><?= e($product['price']); ?></span>
                            </div>

                            <a href="./producto.php?p=<?= e($product['slug']); ?>" class="mt-5 inline-flex rounded-full bg-[#5FD8AD] px-5 py-3 text-xs font-black uppercase tracking-[.13em] text-black transition hover:bg-black hover:text-white">
                                <?= e($product['action']); ?>
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