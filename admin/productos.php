<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: index.php');
    exit;
}

$jsonPath = __DIR__ . '/../data/tienda.json';

if (!file_exists($jsonPath)) {
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

$categories = $data['categories'] ?? [];
$products = $data['products'] ?? [];

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function imgAdmin($path)
{
    if (!$path) return '';
    return '../' . str_replace('./', '', $path);
}

function categoryTitle($slug, $categories)
{
    foreach ($categories as $cat) {
        if (($cat['slug'] ?? '') === $slug) {
            return $cat['title'] ?? $slug;
        }
    }

    return $slug ?: 'Sin categoría';
}

function isSoldOut($product)
{
    $status = strtolower(trim((string)($product['status'] ?? '')));
    return in_array($status, ['agotado', 'sin stock', 'no disponible'], true);
}

function productPriceNumber($value)
{
    $clean = preg_replace('/[^0-9.,]/', '', (string)$value);
    $clean = str_replace(',', '.', $clean);
    return (float)$clean;
}

function shortText($value, $limit = 90)
{
    $value = trim((string)$value);
    if (mb_strlen($value, 'UTF-8') <= $limit) {
        return $value;
    }

    return mb_substr($value, 0, $limit, 'UTF-8') . '...';
}

$editSlug = $_GET['edit'] ?? '';
$current = null;
$isEdit = false;

foreach ($products as $product) {
    if (($product['slug'] ?? '') === $editSlug) {
        $current = $product;
        $isEdit = true;
        break;
    }
}

$totalProducts = count($products);
$totalCategories = count($categories);
$availableProducts = count(array_filter($products, fn($product) => !isSoldOut($product)));
$soldOutProducts = $totalProducts - $availableProducts;
$productsWithImage = count(array_filter($products, fn($product) => !empty($product['img'])));
$productsWithDiscount = count(array_filter($products, fn($product) => !empty($product['discount'])));
$imageCoverage = $totalProducts > 0 ? round(($productsWithImage / $totalProducts) * 100) : 0;

$categoryStats = [];
foreach ($categories as $cat) {
    $slug = $cat['slug'] ?? '';
    $categoryStats[$slug] = [
        'title' => $cat['title'] ?? $slug,
        'count' => 0
    ];
}

foreach ($products as $product) {
    $slug = $product['category_slug'] ?? '';
    if (!isset($categoryStats[$slug])) {
        $categoryStats[$slug] = [
            'title' => categoryTitle($slug, $categories),
            'count' => 0
        ];
    }
    $categoryStats[$slug]['count']++;
}

usort($categoryStats, fn($a, $b) => ($b['count'] ?? 0) <=> ($a['count'] ?? 0));

$topCategory = $categoryStats[0] ?? [
    'title' => 'Sin categoría líder',
    'count' => 0
];

$avgPrice = 0;
if ($totalProducts > 0) {
    $prices = array_map(fn($product) => productPriceNumber($product['price'] ?? 0), $products);
    $avgPrice = round(array_sum($prices) / max(count($prices), 1), 2);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos | Admin Le Grand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --brand: #2D9B6B;
            --mint: #5FD8AD;
            --cream: #FBFAF7;
            --soft: #F4F1EA;
            --gold: #D9B873;
            --coral: #E87961;
            --ink: #1F2933;
        }

        body {
            background:
                radial-gradient(circle at 8% -12%, rgba(95, 216, 173, .18), transparent 34rem),
                radial-gradient(circle at 94% 2%, rgba(217, 184, 115, .20), transparent 32rem),
                linear-gradient(180deg, #fbfaf7 0%, #f7f5ef 48%, #f3f0e8 100%);
        }

        .soft-card {
            box-shadow: 0 24px 80px rgba(31, 41, 51, .08);
        }

        .premium-ring {
            box-shadow:
                inset 0 0 0 1px rgba(255, 255, 255, .66),
                0 24px 80px rgba(45, 155, 107, .12);
        }

        .modal-overlay {
            opacity: 0;
            pointer-events: none;
            transition: opacity .22s ease;
        }

        .modal-overlay.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-panel {
            transform: translateY(18px) scale(.98);
            opacity: 0;
            transition: transform .22s ease, opacity .22s ease;
        }

        .modal-overlay.is-open .modal-panel {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .field-focus:focus {
            box-shadow: 0 0 0 4px rgba(95, 216, 173, .18);
        }

        .select-caret {
            background-image:
                linear-gradient(45deg, transparent 50%, #2D9B6B 50%),
                linear-gradient(135deg, #2D9B6B 50%, transparent 50%);
            background-position:
                calc(100% - 22px) calc(50% - 3px),
                calc(100% - 16px) calc(50% - 3px);
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
        }

        @media (max-width: 640px) {
            .modal-panel {
                border-radius: 1.5rem 1.5rem 0 0;
            }
        }
    </style>
</head>

<body class="min-h-screen text-neutral-900 antialiased">

<div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">

<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div>
        <header class="sticky top-0 z-40 border-b border-white/70 bg-white/80 backdrop-blur-xl">
            <div class="flex min-h-[76px] items-center justify-between px-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.22em] text-[#2D9B6B]">Le Grand</p>
                    <h1 class="text-xl font-black tracking-[-.03em] sm:text-2xl">Gestión de productos</h1>
                </div>

                <div class="flex items-center gap-2">
                    <a href="../index.php" target="_blank" class="hidden rounded-full border border-emerald-100 bg-white px-5 py-3 text-sm font-black text-neutral-700 shadow-sm transition hover:-translate-y-.5 hover:border-[#5FD8AD] hover:text-[#2D9B6B] sm:inline-flex">
                        Ver tienda
                    </a>

                    <a href="logout.php" class="rounded-full bg-[#F4F1EA] px-5 py-3 text-sm font-black text-neutral-700 transition hover:-translate-y-.5 hover:bg-red-50 hover:text-red-700">
                        Salir
                    </a>
                </div>
            </div>
        </header>

        <main class="px-4 py-8 sm:px-6 lg:px-8">

            <section class="relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 p-6 premium-ring sm:p-8">
                <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-[#5FD8AD]/20 blur-3xl"></div>
                <div class="absolute -bottom-24 left-1/3 h-72 w-72 rounded-full bg-[#D9B873]/20 blur-3xl"></div>

                <div class="relative flex flex-col gap-7 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <span class="inline-flex rounded-full bg-emerald-50 px-4 py-2 text-xs font-black uppercase tracking-[.18em] text-[#2D9B6B]">
                            Catálogo comercial
                        </span>

                        <h2 class="mt-4 text-4xl font-black tracking-[-.06em] text-neutral-950 sm:text-5xl">
                            Productos
                        </h2>

                        <p class="mt-4 max-w-2xl text-sm font-semibold leading-6 text-neutral-500 sm:text-base">
                            Administra productos visibles, precios, descuentos, categorías, estados e imágenes desde una pantalla más clara y rápida.
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3 lg:min-w-[520px]">
                        <div class="rounded-3xl border border-emerald-100 bg-white/80 p-5 shadow-sm">
                            <p class="text-xs font-black uppercase tracking-[.16em] text-neutral-400">Total</p>
                            <p class="mt-2 text-3xl font-black text-neutral-950"><?= e($totalProducts); ?></p>
                            <p class="mt-1 text-xs font-bold text-neutral-400">productos</p>
                        </div>

                        <div class="rounded-3xl border border-emerald-100 bg-emerald-50/70 p-5 shadow-sm">
                            <p class="text-xs font-black uppercase tracking-[.16em] text-[#2D9B6B]">Disponibles</p>
                            <p class="mt-2 text-3xl font-black text-[#2D9B6B]"><?= e($availableProducts); ?></p>
                            <p class="mt-1 text-xs font-bold text-emerald-700/60">activos</p>
                        </div>

                        <div class="rounded-3xl border border-orange-100 bg-orange-50/70 p-5 shadow-sm">
                            <p class="text-xs font-black uppercase tracking-[.16em] text-orange-700">Agotados</p>
                            <p class="mt-2 text-3xl font-black text-orange-700"><?= e($soldOutProducts); ?></p>
                            <p class="mt-1 text-xs font-bold text-orange-700/60">sin venta</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-[1.6rem] border border-white/70 bg-white/80 p-5 soft-card">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-neutral-500">Categoría líder</p>
                            <p class="mt-2 truncate text-2xl font-black text-neutral-950"><?= e($topCategory['title']); ?></p>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-xl">🏷️</div>
                    </div>
                    <p class="mt-3 text-xs font-bold text-neutral-400"><?= e($topCategory['count']); ?> productos asociados</p>
                </div>

                <div class="rounded-[1.6rem] border border-white/70 bg-white/80 p-5 soft-card">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-neutral-500">Con imagen</p>
                            <p class="mt-2 text-2xl font-black text-[#2D9B6B]"><?= e($imageCoverage); ?>%</p>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-xl">🖼️</div>
                    </div>
                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-neutral-100">
                        <div class="h-full rounded-full bg-[#5FD8AD]" style="width: <?= e($imageCoverage); ?>%"></div>
                    </div>
                </div>

                <div class="rounded-[1.6rem] border border-white/70 bg-white/80 p-5 soft-card">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-neutral-500">En oferta</p>
                            <p class="mt-2 text-2xl font-black text-[#C7962E]"><?= e($productsWithDiscount); ?></p>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-amber-50 text-xl">%</div>
                    </div>
                    <p class="mt-3 text-xs font-bold text-neutral-400">productos con descuento</p>
                </div>

                <div class="rounded-[1.6rem] border border-white/70 bg-white/80 p-5 soft-card">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-neutral-500">Precio promedio</p>
                            <p class="mt-2 text-2xl font-black text-neutral-950">S/ <?= e(number_format($avgPrice, 2)); ?></p>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-neutral-100 text-xl">💳</div>
                    </div>
                    <p class="mt-3 text-xs font-bold text-neutral-400">referencial del catálogo</p>
                </div>
            </section>

            <section class="mt-6 rounded-[2rem] border border-white/70 bg-white/85 p-5 soft-card sm:p-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <h3 class="text-2xl font-black tracking-[-.03em]">Inventario visual</h3>
                        <p class="mt-1 text-sm font-semibold text-neutral-500">Busca, filtra y edita productos sin cargar una pantalla de formulario pesada.</p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="relative">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400">⌕</span>
                            <input
                                id="searchInput"
                                type="search"
                                placeholder="Buscar producto, etiqueta o categoría..."
                                class="field-focus w-full rounded-2xl border border-emerald-100 bg-[#FBFAF7] py-3 pl-10 pr-4 text-sm font-bold outline-none sm:w-[320px]">
                        </div>

                        <select id="categoryFilter" class="select-caret field-focus appearance-none rounded-2xl border border-emerald-100 bg-[#FBFAF7] px-4 py-3 pr-10 text-sm font-black outline-none">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= e($cat['slug'] ?? ''); ?>"><?= e($cat['title'] ?? ''); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <select id="statusFilter" class="select-caret field-focus appearance-none rounded-2xl border border-emerald-100 bg-[#FBFAF7] px-4 py-3 pr-10 text-sm font-black outline-none">
                            <option value="">Todos los estados</option>
                            <option value="disponible">Disponibles</option>
                            <option value="agotado">Agotados</option>
                            <option value="oferta">Con descuento</option>
                            <option value="sin-imagen">Sin imagen</option>
                        </select>

                        <button
                            type="button"
                            onclick="openProductModal(false)"
                            class="rounded-2xl bg-[#2D9B6B] px-5 py-3 text-sm font-black text-white shadow-[0_18px_40px_rgba(45,155,107,.25)] transition hover:-translate-y-.5 hover:bg-[#247d57]">
                            + Nuevo producto
                        </button>
                    </div>
                </div>

                <div class="mt-6 grid gap-5 sm:grid-cols-2 2xl:grid-cols-4 xl:grid-cols-3" id="productsGrid">
                    <?php foreach ($products as $product): ?>
                        <?php
                        $productCategory = $product['category_slug'] ?? '';
                        $productStatus = isSoldOut($product) ? 'agotado' : 'disponible';
                        $hasImage = !empty($product['img']);
                        $hasDiscount = !empty($product['discount']);
                        $searchText = strtolower(trim(
                            ($product['name'] ?? '') . ' ' .
                            ($product['slug'] ?? '') . ' ' .
                            ($product['meta'] ?? '') . ' ' .
                            categoryTitle($productCategory, $categories)
                        ));
                        ?>
                        <article
                            class="product-card group rounded-[1.8rem] border border-white/70 bg-[#FBFAF7] p-4 shadow-sm transition hover:-translate-y-1 hover:bg-white hover:shadow-[0_28px_70px_rgba(31,41,51,.10)]"
                            data-search="<?= e($searchText); ?>"
                            data-category="<?= e($productCategory); ?>"
                            data-status="<?= e($productStatus); ?>"
                            data-discount="<?= $hasDiscount ? '1' : '0'; ?>"
                            data-image="<?= $hasImage ? '1' : '0'; ?>">

                            <div class="relative overflow-hidden rounded-[1.4rem] bg-white">
                                <?php if ($hasImage): ?>
                                    <img src="<?= e(imgAdmin($product['img'])); ?>" class="h-56 w-full object-contain p-5 transition duration-300 group-hover:scale-[1.03]" alt="<?= e($product['alt'] ?? $product['name'] ?? 'Producto'); ?>">
                                <?php else: ?>
                                    <div class="grid h-56 place-items-center bg-gradient-to-br from-neutral-50 to-emerald-50/60 text-sm font-black text-neutral-400">
                                        Sin imagen
                                    </div>
                                <?php endif; ?>

                                <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                                    <?php if ($hasDiscount): ?>
                                        <span class="rounded-full bg-[#D9B873] px-3 py-1 text-xs font-black text-white shadow-sm">
                                            <?= e($product['discount']); ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if (isSoldOut($product)): ?>
                                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-black text-red-700 shadow-sm">
                                            Agotado
                                        </span>
                                    <?php else: ?>
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-[#2D9B6B] shadow-sm">
                                            Disponible
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mt-4">
                                <p class="text-xs font-black uppercase tracking-[.18em] text-[#2D9B6B]">
                                    <?= e(categoryTitle($productCategory, $categories)); ?>
                                </p>

                                <h4 class="mt-2 min-h-[3.5rem] text-xl font-black leading-tight text-neutral-950">
                                    <?= e($product['name'] ?? 'Producto sin nombre'); ?>
                                </h4>

                                <p class="mt-1 text-xs font-bold text-neutral-400">
                                    <?= e($product['slug'] ?? ''); ?>
                                </p>

                                <?php if (!empty($product['description'])): ?>
                                    <p class="mt-3 min-h-[2.7rem] text-sm font-semibold leading-5 text-neutral-500">
                                        <?= e(shortText($product['description'], 82)); ?>
                                    </p>
                                <?php else: ?>
                                    <p class="mt-3 min-h-[2.7rem] text-sm font-semibold leading-5 text-neutral-400">
                                        Agrega una descripción comercial para mejorar la lectura del detalle.
                                    </p>
                                <?php endif; ?>

                                <div class="mt-4 flex flex-wrap items-end gap-2">
                                    <?php if (!empty($product['old_price'])): ?>
                                        <del class="text-sm font-bold text-neutral-400"><?= e($product['old_price']); ?></del>
                                    <?php endif; ?>

                                    <span class="text-2xl font-black text-neutral-950"><?= e($product['price'] ?? 'S/ 0.00'); ?></span>
                                </div>

                                <?php if (!empty($product['meta'])): ?>
                                    <div class="mt-3 rounded-2xl bg-white px-3 py-2 text-xs font-bold leading-5 text-neutral-500">
                                        <?= e($product['meta']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-5 grid grid-cols-[1fr_auto] gap-2">
                                <a href="productos.php?edit=<?= e($product['slug'] ?? ''); ?>" class="rounded-2xl bg-emerald-50 px-4 py-3 text-center text-sm font-black text-[#2D9B6B] transition hover:bg-[#2D9B6B] hover:text-white">
                                    Editar
                                </a>

                                <form action="save.php" method="POST" onsubmit="return confirm('¿Eliminar este producto?')">
                                    <input type="hidden" name="type" value="product">
                                    <input type="hidden" name="mode" value="delete">
                                    <input type="hidden" name="old_slug" value="<?= e($product['slug'] ?? ''); ?>">

                                    <button class="rounded-2xl bg-red-50 px-4 py-3 text-sm font-black text-red-700 transition hover:bg-red-600 hover:text-white">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>

                    <div id="emptyFilterState" class="hidden col-span-full rounded-[2rem] border border-dashed border-emerald-200 bg-emerald-50/40 p-10 text-center">
                        <p class="text-4xl">🔎</p>
                        <h3 class="mt-4 text-2xl font-black">No hay productos con ese filtro</h3>
                        <p class="mt-2 text-sm font-semibold text-neutral-500">Limpia la búsqueda o cambia la categoría para ver más resultados.</p>
                    </div>

                    <?php if (empty($products)): ?>
                        <div class="col-span-full rounded-[2rem] border border-dashed border-emerald-200 bg-emerald-50/40 p-10 text-center">
                            <p class="text-4xl">⌁</p>
                            <h3 class="mt-4 text-2xl font-black">Todavía no hay productos</h3>
                            <p class="mt-2 text-sm font-semibold text-neutral-500">Crea el primer producto desde el botón superior para empezar a construir tu catálogo.</p>
                            <button
                                type="button"
                                onclick="openProductModal(false)"
                                class="mt-5 rounded-2xl bg-[#2D9B6B] px-5 py-3 text-sm font-black text-white shadow-[0_18px_40px_rgba(45,155,107,.25)]">
                                Crear primer producto
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[1fr_380px]">
                <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 soft-card">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-black tracking-[-.03em]">Distribución por categoría</h3>
                            <p class="mt-1 text-sm font-semibold text-neutral-500">Ayuda visual para revisar si el catálogo está equilibrado.</p>
                        </div>

                        <a href="categorias.php" class="rounded-full bg-[#F4F1EA] px-4 py-2 text-xs font-black text-neutral-600 transition hover:bg-emerald-50 hover:text-[#2D9B6B]">
                            Gestionar categorías
                        </a>
                    </div>

                    <div class="mt-6 space-y-4">
                        <?php foreach ($categoryStats as $stat): ?>
                            <?php $percent = $totalProducts > 0 ? round(($stat['count'] / $totalProducts) * 100) : 0; ?>
                            <div class="rounded-2xl bg-[#FBFAF7] p-4">
                                <div class="flex items-center justify-between gap-4 text-sm font-black">
                                    <span class="truncate"><?= e($stat['title']); ?></span>
                                    <span class="text-neutral-500"><?= e($stat['count']); ?> prod.</span>
                                </div>
                                <div class="mt-3 h-3 overflow-hidden rounded-full bg-white">
                                    <div class="h-full rounded-full bg-[#5FD8AD]" style="width: <?= e($percent); ?>%"></div>
                                </div>
                                <p class="mt-2 text-xs font-bold text-neutral-400"><?= e($percent); ?>% del catálogo</p>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($categoryStats)): ?>
                            <div class="rounded-2xl bg-[#FBFAF7] p-8 text-center text-sm font-bold text-neutral-400">
                                No hay categorías para graficar.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <aside class="rounded-[2rem] border border-white/70 bg-white/85 p-6 soft-card">
                    <h3 class="text-2xl font-black tracking-[-.03em]">Guía rápida</h3>
                    <p class="mt-1 text-sm font-semibold text-neutral-500">Jerarquía recomendada para que la tienda se vea más sólida.</p>

                    <div class="mt-6 space-y-3">
                        <div class="rounded-2xl bg-emerald-50 p-4">
                            <p class="font-black text-[#2D9B6B]">1. Imagen limpia</p>
                            <p class="mt-1 text-sm font-semibold text-emerald-800/70">Usa fondo claro y producto centrado para mantener consistencia visual.</p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-4">
                            <p class="font-black text-amber-700">2. Precio visible</p>
                            <p class="mt-1 text-sm font-semibold text-amber-800/70">El precio actual debe ser el foco; el anterior solo si hay descuento real.</p>
                        </div>

                        <div class="rounded-2xl bg-orange-50 p-4">
                            <p class="font-black text-orange-700">3. Estado claro</p>
                            <p class="mt-1 text-sm font-semibold text-orange-800/70">Marca “Agotado” solo cuando no debe comprarse o consultarse.</p>
                        </div>

                        <div class="rounded-2xl bg-[#F4F1EA] p-4">
                            <p class="font-black text-neutral-700">4. Etiquetas útiles</p>
                            <p class="mt-1 text-sm font-semibold text-neutral-500">Ejemplo: Caballero · Oferta · Acero · Casual.</p>
                        </div>
                    </div>
                </aside>
            </section>

        </main>
    </div>
</div>

<div id="productModal" class="modal-overlay fixed inset-0 z-50 flex items-end justify-center bg-neutral-950/35 p-0 backdrop-blur-sm sm:items-center sm:p-6">
    <div class="modal-panel max-h-[94vh] w-full max-w-5xl overflow-hidden rounded-t-[2rem] bg-white shadow-[0_40px_120px_rgba(31,41,51,.28)] sm:rounded-[2rem]">
        <div class="flex items-start justify-between gap-4 border-b border-neutral-100 bg-[#FBFAF7] px-5 py-5 sm:px-7">
            <div>
                <p class="text-xs font-black uppercase tracking-[.2em] text-[#2D9B6B]">
                    <?= $isEdit ? 'Edición de producto' : 'Nuevo producto'; ?>
                </p>
                <h3 class="mt-1 text-2xl font-black tracking-[-.04em] text-neutral-950">
                    <?= $isEdit ? 'Editar producto' : 'Crear producto'; ?>
                </h3>
                <p class="mt-1 text-sm font-semibold text-neutral-500">
                    Completa la información comercial más importante sin saturar la pantalla principal.
                </p>
            </div>

            <a href="productos.php" class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-xl font-black text-neutral-500 shadow-sm transition hover:bg-red-50 hover:text-red-700" aria-label="Cerrar modal">
                ×
            </a>
        </div>

        <div class="max-h-[calc(94vh-102px)] overflow-y-auto px-5 py-6 sm:px-7">
            <form action="save.php" method="POST" enctype="multipart/form-data" class="grid gap-6">
                <input type="hidden" name="type" value="product">
                <input type="hidden" name="mode" value="<?= $isEdit ? 'update' : 'create'; ?>">
                <input type="hidden" name="old_slug" value="<?= e($current['slug'] ?? ''); ?>">

                <div class="grid gap-6 lg:grid-cols-[320px_1fr]">
                    <div class="rounded-[1.7rem] border border-emerald-100 bg-[#FBFAF7] p-4">
                        <p class="text-sm font-black text-neutral-600">Vista visual</p>
                        <p class="mt-1 text-xs font-semibold leading-5 text-neutral-400">La imagen debe comunicar calidad antes que cantidad de texto.</p>

                        <div class="mt-4 overflow-hidden rounded-[1.4rem] bg-white">
                            <?php if ($isEdit && !empty($current['img'])): ?>
                                <img id="previewImage" src="<?= e(imgAdmin($current['img'])); ?>" class="h-72 w-full object-contain p-5" alt="Imagen actual">
                            <?php else: ?>
                                <div id="previewPlaceholder" class="grid h-72 place-items-center bg-gradient-to-br from-emerald-50 to-amber-50 text-center">
                                    <div>
                                        <p class="text-4xl">🖼️</p>
                                        <p class="mt-3 text-sm font-black text-neutral-500">Vista previa</p>
                                        <p class="mt-1 text-xs font-semibold text-neutral-400">Sube una imagen principal</p>
                                    </div>
                                </div>
                                <img id="previewImage" src="" class="hidden h-72 w-full object-contain p-5" alt="Vista previa">
                            <?php endif; ?>
                        </div>

                        <label class="mt-4 block">
                            <span class="text-sm font-black text-neutral-700">Imagen principal</span>
                            <input id="imageInput" name="img" type="file" accept="image/*" class="mt-2 w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm font-bold text-neutral-600">
                        </label>

                        <p class="mt-2 text-xs font-semibold text-neutral-400">
                            Al editar, si no subes una nueva imagen, se conserva la actual.
                        </p>
                    </div>

                    <div class="grid gap-5">
                        <?php if (empty($categories)): ?>
                            <div class="rounded-2xl border border-orange-100 bg-orange-50 px-4 py-3 text-sm font-bold text-orange-700">
                                Primero registra una categoría para poder crear productos correctamente.
                            </div>
                        <?php endif; ?>

                        <div class="grid gap-5 lg:grid-cols-2">
                            <div>
                                <label class="text-sm font-black text-neutral-700">Categoría</label>
                                <select name="category_slug" required class="select-caret field-focus mt-2 w-full appearance-none rounded-2xl border border-emerald-100 bg-white px-4 py-3 pr-10 text-sm font-bold outline-none">
                                    <option value="">Seleccionar categoría</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= e($cat['slug'] ?? ''); ?>" <?= (($current['category_slug'] ?? '') === ($cat['slug'] ?? '')) ? 'selected' : ''; ?>>
                                            <?= e($cat['title'] ?? ''); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-black text-neutral-700">Nombre del producto</label>
                                <input name="name" required value="<?= e($current['name'] ?? ''); ?>" placeholder="Ej: Reloj Fossil FS4813" class="field-focus mt-2 w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm font-bold outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-black text-neutral-700">Meta / etiquetas</label>
                            <input name="meta" value="<?= e($current['meta'] ?? ''); ?>" placeholder="Caballero · Oferta · Relojes" class="field-focus mt-2 w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm font-bold outline-none">
                            <p class="mt-2 text-xs font-semibold text-neutral-400">Sirve para ordenar visualmente y mejorar la lectura comercial.</p>
                        </div>

                        <div class="grid gap-5 lg:grid-cols-4">
                            <div>
                                <label class="text-sm font-black text-neutral-700">Precio anterior</label>
                                <input name="old_price" value="<?= e($current['old_price'] ?? ''); ?>" placeholder="S/ 600.00" class="field-focus mt-2 w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm font-bold outline-none">
                            </div>

                            <div>
                                <label class="text-sm font-black text-neutral-700">Precio actual</label>
                                <input name="price" value="<?= e($current['price'] ?? ''); ?>" placeholder="S/ 500.00" class="field-focus mt-2 w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm font-bold outline-none">
                            </div>

                            <div>
                                <label class="text-sm font-black text-neutral-700">Descuento</label>
                                <input name="discount" value="<?= e($current['discount'] ?? ''); ?>" placeholder="-17%" class="field-focus mt-2 w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm font-bold outline-none">
                            </div>

                            <div>
                                <label class="text-sm font-black text-neutral-700">Estado</label>
                                <select name="status" class="select-caret field-focus mt-2 w-full appearance-none rounded-2xl border border-emerald-100 bg-white px-4 py-3 pr-10 text-sm font-bold outline-none">
                                    <option value="" <?= empty($current['status']) ? 'selected' : ''; ?>>Disponible</option>
                                    <option value="Agotado" <?= (($current['status'] ?? '') === 'Agotado') ? 'selected' : ''; ?>>Agotado</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-black text-neutral-700">Descripción comercial</label>
                            <textarea name="description" rows="4" placeholder="Describe materiales, estilo, ocasión de uso o beneficio principal del producto." class="field-focus mt-2 w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm font-bold leading-6 outline-none"><?= e($current['description'] ?? ''); ?></textarea>
                        </div>

                        <label class="flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/50 px-4 py-3 font-bold text-neutral-700">
                            <input type="checkbox" name="wish" value="1" class="h-4 w-4 accent-[#2D9B6B]" <?= !empty($current['wish']) ? 'checked' : ''; ?>>
                            Mostrar “Lo deseo”
                        </label>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-neutral-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="productos.php" class="rounded-2xl bg-[#F4F1EA] px-6 py-4 text-center text-sm font-black text-neutral-600 transition hover:bg-neutral-200">
                        Cancelar
                    </a>

                    <button class="rounded-2xl bg-[#2D9B6B] px-7 py-4 text-sm font-black text-white shadow-[0_18px_40px_rgba(45,155,107,.25)] transition hover:-translate-y-.5 hover:bg-[#247d57]">
                        <?= $isEdit ? 'Guardar cambios' : 'Crear producto'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('productModal');
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const productCards = Array.from(document.querySelectorAll('.product-card'));
    const emptyFilterState = document.getElementById('emptyFilterState');
    const imageInput = document.getElementById('imageInput');
    const previewImage = document.getElementById('previewImage');
    const previewPlaceholder = document.getElementById('previewPlaceholder');

    function openProductModal() {
        modal.classList.add('is-open');
        document.body.classList.add('overflow-hidden');
    }

    function closeProductModal() {
        window.location.href = 'productos.php';
    }

    function normalize(value) {
        return (value || '').toString().trim().toLowerCase();
    }

    function applyFilters() {
        const q = normalize(searchInput?.value);
        const category = normalize(categoryFilter?.value);
        const status = normalize(statusFilter?.value);
        let visible = 0;

        productCards.forEach(card => {
            const matchesSearch = !q || normalize(card.dataset.search).includes(q);
            const matchesCategory = !category || normalize(card.dataset.category) === category;

            let matchesStatus = true;

            if (status === 'disponible' || status === 'agotado') {
                matchesStatus = normalize(card.dataset.status) === status;
            }

            if (status === 'oferta') {
                matchesStatus = card.dataset.discount === '1';
            }

            if (status === 'sin-imagen') {
                matchesStatus = card.dataset.image === '0';
            }

            const show = matchesSearch && matchesCategory && matchesStatus;
            card.classList.toggle('hidden', !show);

            if (show) visible++;
        });

        if (emptyFilterState) {
            emptyFilterState.classList.toggle('hidden', visible !== 0 || productCards.length === 0);
        }
    }

    searchInput?.addEventListener('input', applyFilters);
    categoryFilter?.addEventListener('change', applyFilters);
    statusFilter?.addEventListener('change', applyFilters);

    imageInput?.addEventListener('change', event => {
        const file = event.target.files?.[0];

        if (!file || !previewImage) return;

        const reader = new FileReader();

        reader.onload = e => {
            previewImage.src = e.target.result;
            previewImage.classList.remove('hidden');
            previewPlaceholder?.classList.add('hidden');
        };

        reader.readAsDataURL(file);
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeProductModal();
        }
    });

    modal.addEventListener('click', event => {
        if (event.target === modal) {
            closeProductModal();
        }
    });

    <?php if ($isEdit): ?>
    openProductModal();
    <?php endif; ?>
</script>

</body>
</html>
