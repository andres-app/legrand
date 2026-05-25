<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$data = loadStoreData();

$categories = $data['categories'] ?? [];
$products = $data['products'] ?? [];

$totalCategories = count($categories);
$totalProducts = count($products);

$availableProducts = count(array_filter($products, function ($p) {
    return empty($p['status']);
}));

$soldOutProducts = count(array_filter($products, function ($p) {
    return strtolower($p['status'] ?? '') === 'agotado';
}));

$productsWithDiscount = count(array_filter($products, function ($p) {
    return !empty($p['discount']);
}));

$productsWithImage = count(array_filter($products, function ($p) {
    return !empty($p['img']);
}));

$coverageImage = $totalProducts > 0 ? round(($productsWithImage / $totalProducts) * 100) : 0;
$soldOutRate = $totalProducts > 0 ? round(($soldOutProducts / $totalProducts) * 100) : 0;
$availableRate = $totalProducts > 0 ? round(($availableProducts / $totalProducts) * 100) : 0;

$categoryStats = [];

foreach ($categories as $cat) {
    $slug = $cat['slug'] ?? '';

    $count = count(array_filter($products, function ($p) use ($slug) {
        return ($p['category_slug'] ?? '') === $slug;
    }));

    $categoryStats[] = [
        'title' => $cat['title'] ?? $slug,
        'slug' => $slug,
        'count' => $count
    ];
}

usort($categoryStats, function ($a, $b) {
    return $b['count'] <=> $a['count'];
});

$topCategory = $categoryStats[0] ?? [
    'title' => 'Sin categoría dominante',
    'count' => 0
];

$latestProducts = array_slice(array_reverse($products), 0, 5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Ejecutivo | Le Grand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background:
                radial-gradient(circle at 5% -10%, rgba(95, 216, 173, .13), transparent 34rem),
                radial-gradient(circle at 95% 0%, rgba(217, 184, 115, .14), transparent 30rem),
                linear-gradient(180deg, #fbfaf7 0%, #f7f6f2 100%);
        }
    </style>
</head>

<body class="min-h-screen text-neutral-950 antialiased">

<div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">

<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div>
        <header class="sticky top-0 z-40 border-b border-black/5 bg-white/85 backdrop-blur-xl">
            <div class="flex min-h-[76px] items-center justify-between px-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.22em] text-[#2D9B6B]">Le Grand</p>
                    <h1 class="text-xl font-black tracking-[-.03em] sm:text-2xl">Dashboard ejecutivo</h1>
                </div>

                <div class="flex items-center gap-2">
                    <a href="../index.php" target="_blank" class="hidden rounded-full bg-neutral-100 px-5 py-3 text-sm font-black hover:bg-black hover:text-white sm:inline-flex">
                        Ver tienda
                    </a>

                    <a href="logout.php" class="rounded-full bg-black px-5 py-3 text-sm font-black text-white hover:bg-[#5FD8AD] hover:text-black">
                        Salir
                    </a>
                </div>
            </div>
        </header>

        <main class="px-4 py-8 sm:px-6 lg:px-8">

            <section class="rounded-[2rem] bg-black p-6 text-white shadow-[0_30px_100px_rgba(0,0,0,.18)] sm:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.24em] text-[#5FD8AD]">
                            Gestión comercial
                        </p>
                        <h2 class="mt-3 text-4xl font-black tracking-[-.06em] sm:text-5xl">
                            Estado del catálogo
                        </h2>
                        <p class="mt-4 max-w-2xl text-white/55">
                            Vista ejecutiva para revisar cobertura, disponibilidad, productos agotados y distribución por categoría.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur-xl">
                        <p class="text-xs font-black uppercase tracking-[.18em] text-white/45">Categoría líder</p>
                        <p class="mt-1 text-xl font-black"><?= e($topCategory['title']); ?></p>
                        <p class="text-sm text-white/50"><?= e($topCategory['count']); ?> productos</p>
                    </div>
                </div>
            </section>

            <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-[1.7rem] bg-white p-6 shadow-[0_20px_70px_rgba(0,0,0,.07)]">
                    <p class="text-sm font-bold text-neutral-500">Total productos</p>
                    <p class="mt-3 text-4xl font-black"><?= e($totalProducts); ?></p>
                    <p class="mt-2 text-xs font-bold text-neutral-400">Catálogo publicado</p>
                </div>

                <div class="rounded-[1.7rem] bg-white p-6 shadow-[0_20px_70px_rgba(0,0,0,.07)]">
                    <p class="text-sm font-bold text-neutral-500">Disponibles</p>
                    <p class="mt-3 text-4xl font-black text-[#2D9B6B]"><?= e($availableProducts); ?></p>
                    <p class="mt-2 text-xs font-bold text-neutral-400"><?= e($availableRate); ?>% del catálogo</p>
                </div>

                <div class="rounded-[1.7rem] bg-white p-6 shadow-[0_20px_70px_rgba(0,0,0,.07)]">
                    <p class="text-sm font-bold text-neutral-500">Agotados</p>
                    <p class="mt-3 text-4xl font-black text-red-600"><?= e($soldOutProducts); ?></p>
                    <p class="mt-2 text-xs font-bold text-neutral-400"><?= e($soldOutRate); ?>% sin stock</p>
                </div>

                <div class="rounded-[1.7rem] bg-white p-6 shadow-[0_20px_70px_rgba(0,0,0,.07)]">
                    <p class="text-sm font-bold text-neutral-500">Categorías</p>
                    <p class="mt-3 text-4xl font-black"><?= e($totalCategories); ?></p>
                    <p class="mt-2 text-xs font-bold text-neutral-400">Líneas comerciales</p>
                </div>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_.9fr]">

                <div class="rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-black">Distribución por categoría</h3>
                            <p class="mt-1 text-sm text-neutral-500">Cantidad de productos por línea.</p>
                        </div>
                        <a href="categorias.php" class="rounded-full bg-neutral-100 px-4 py-2 text-xs font-black hover:bg-black hover:text-white">
                            Gestionar
                        </a>
                    </div>

                    <div class="mt-6 space-y-4">
                        <?php foreach ($categoryStats as $stat): ?>
                            <?php
                            $percent = $totalProducts > 0 ? round(($stat['count'] / $totalProducts) * 100) : 0;
                            ?>
                            <div>
                                <div class="mb-2 flex justify-between text-sm font-bold">
                                    <span><?= e($stat['title']); ?></span>
                                    <span><?= e($stat['count']); ?> productos</span>
                                </div>
                                <div class="h-3 overflow-hidden rounded-full bg-neutral-100">
                                    <div class="h-full rounded-full bg-[#5FD8AD]" style="width: <?= e($percent); ?>%"></div>
                                </div>
                                <p class="mt-1 text-xs font-bold text-neutral-400"><?= e($percent); ?>% del catálogo</p>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($categoryStats)): ?>
                            <div class="rounded-2xl bg-neutral-50 p-6 text-center text-sm font-bold text-neutral-400">
                                No hay categorías registradas.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                    <h3 class="text-2xl font-black">Indicadores de calidad</h3>
                    <p class="mt-1 text-sm text-neutral-500">Revisión rápida del catálogo.</p>

                    <div class="mt-6 grid gap-4">
                        <div class="rounded-2xl bg-[#f7f6f2] p-5">
                            <div class="flex items-center justify-between">
                                <p class="font-black">Productos con imagen</p>
                                <p class="text-2xl font-black"><?= e($coverageImage); ?>%</p>
                            </div>
                            <div class="mt-3 h-3 overflow-hidden rounded-full bg-white">
                                <div class="h-full rounded-full bg-[#5FD8AD]" style="width: <?= e($coverageImage); ?>%"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-[#f7f6f2] p-5">
                            <p class="text-sm font-bold text-neutral-500">Productos en oferta</p>
                            <p class="mt-2 text-3xl font-black"><?= e($productsWithDiscount); ?></p>
                        </div>

                        <div class="rounded-2xl bg-[#f7f6f2] p-5">
                            <p class="text-sm font-bold text-neutral-500">Recomendación gerencial</p>
                            <p class="mt-2 text-sm font-semibold leading-6 text-neutral-600">
                                <?php if ($soldOutRate >= 30): ?>
                                    Revisar reposición: el porcentaje de productos agotados es alto.
                                <?php elseif ($coverageImage < 80): ?>
                                    Mejorar carga visual: varios productos no tienen imagen.
                                <?php elseif ($totalProducts < 10): ?>
                                    Ampliar catálogo para mejorar variedad comercial.
                                <?php else: ?>
                                    El catálogo mantiene una estructura comercial saludable.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

            </section>

            <section class="mt-6 rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-black">Últimos productos registrados</h3>
                        <p class="mt-1 text-sm text-neutral-500">Vista rápida de los productos más recientes.</p>
                    </div>

                    <a href="productos.php" class="rounded-full bg-black px-4 py-2 text-xs font-black text-white hover:bg-[#5FD8AD] hover:text-black">
                        Ver productos
                    </a>
                </div>

                <div class="mt-5 overflow-hidden rounded-2xl border border-black/5">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#f7f6f2] text-xs uppercase tracking-[.16em] text-neutral-500">
                            <tr>
                                <th class="px-4 py-4">Producto</th>
                                <th class="px-4 py-4">Categoría</th>
                                <th class="px-4 py-4">Precio</th>
                                <th class="px-4 py-4">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5 bg-white">
                            <?php foreach ($latestProducts as $product): ?>
                                <tr>
                                    <td class="px-4 py-4 font-black"><?= e($product['name'] ?? ''); ?></td>
                                    <td class="px-4 py-4 text-neutral-500"><?= e($product['category_slug'] ?? ''); ?></td>
                                    <td class="px-4 py-4 font-black"><?= e($product['price'] ?? ''); ?></td>
                                    <td class="px-4 py-4">
                                        <?php if (!empty($product['status'])): ?>
                                            <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-black text-red-700">
                                                <?= e($product['status']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">
                                                Disponible
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($latestProducts)): ?>
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center font-bold text-neutral-400">
                                        No hay productos registrados.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>
</div>

</body>
</html>