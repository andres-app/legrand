<?php
//admin/categorias.php
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

$categories = $data['categories'] ?? [];
$products = $data['products'] ?? [];

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function imgAdmin($path)
{
    if (!$path) return '';
    return '../' . str_replace('./', '', $path);
}

function countProductsByCategory($slug, $products)
{
    return count(array_filter($products, function ($product) use ($slug) {
        return ($product['category_slug'] ?? '') === $slug;
    }));
}

$totalCategories = count($categories);
$totalProducts = count($products);
$emptyCategories = 0;
$mostLoadedCategory = null;
$mostLoadedCount = 0;

foreach ($categories as $cat) {
    $slug = $cat['slug'] ?? '';
    $qty = countProductsByCategory($slug, $products);

    if ($qty === 0) {
        $emptyCategories++;
    }

    if ($qty > $mostLoadedCount) {
        $mostLoadedCount = $qty;
        $mostLoadedCategory = $cat;
    }
}

$editSlug = $_GET['edit'] ?? '';
$current = null;
$isEdit = false;

foreach ($categories as $cat) {
    if (($cat['slug'] ?? '') === $editSlug) {
        $current = $cat;
        $isEdit = true;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías | Admin Le Grand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --cream: #fbfaf6;
            --cream-2: #f4efe4;
            --green: #2D9B6B;
            --green-dark: #173f35;
            --mint: #5FD8AD;
            --gold: #C8A35F;
            --ink: #1f2a24;
        }

        body {
            background:
                radial-gradient(circle at 8% -10%, rgba(95, 216, 173, .22), transparent 34rem),
                radial-gradient(circle at 92% 0%, rgba(200, 163, 95, .24), transparent 30rem),
                radial-gradient(circle at 70% 95%, rgba(45, 155, 107, .10), transparent 28rem),
                linear-gradient(180deg, #fffdf8 0%, #f6f1e7 100%);
        }

        .premium-grid {
            background-image:
                linear-gradient(rgba(45, 155, 107, .055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(45, 155, 107, .055) 1px, transparent 1px);
            background-size: 34px 34px;
        }

        .soft-card {
            box-shadow: 0 24px 70px rgba(45, 63, 52, .09);
        }

        .category-card:hover .category-img {
            transform: scale(1.035);
        }
    </style>
</head>

<body class="min-h-screen text-[#1f2a24] antialiased premium-grid">

<div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">

<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div>
        <header class="sticky top-0 z-40 border-b border-emerald-900/10 bg-[#fffdf8]/90 backdrop-blur-xl">
            <div class="flex min-h-[76px] items-center justify-between px-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.22em] text-[#2D9B6B]">Le Grand</p>
                    <h1 class="text-xl font-black tracking-[-.03em] text-[#173f35] sm:text-2xl">Gestión de categorías</h1>
                </div>

                <div class="flex items-center gap-2">
                    <a href="../index.php" target="_blank" class="hidden rounded-full border border-emerald-900/10 bg-white/80 px-5 py-3 text-sm font-black text-[#173f35] shadow-sm transition hover:-translate-y-.5 hover:border-[#2D9B6B]/30 hover:bg-[#ecfff7] sm:inline-flex">
                        Ver tienda
                    </a>

                    <a href="logout.php" class="rounded-full bg-[#173f35] px-5 py-3 text-sm font-black text-white shadow-[0_14px_35px_rgba(23,63,53,.22)] transition hover:-translate-y-.5 hover:bg-[#2D9B6B]">
                        Salir
                    </a>
                </div>
            </div>
        </header>

        <main class="px-4 py-8 sm:px-6 lg:px-8">

            <section class="relative overflow-hidden rounded-[2.2rem] border border-emerald-900/10 bg-white/75 p-6 soft-card backdrop-blur-xl sm:p-8">
                <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-[#5FD8AD]/25 blur-3xl"></div>
                <div class="absolute -bottom-28 left-20 h-72 w-72 rounded-full bg-[#C8A35F]/20 blur-3xl"></div>

                <div class="relative flex flex-col gap-7 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <span class="inline-flex rounded-full border border-[#2D9B6B]/15 bg-[#ecfff7] px-4 py-2 text-xs font-black uppercase tracking-[.20em] text-[#2D9B6B]">
                            Catálogo premium
                        </span>

                        <h2 class="mt-5 max-w-3xl text-4xl font-black tracking-[-.06em] text-[#173f35] sm:text-5xl">
                            Categorías ordenadas, visuales y listas para vender
                        </h2>

                        <p class="mt-4 max-w-2xl text-sm font-medium leading-7 text-[#55645d] sm:text-base">
                            Administra las líneas comerciales de la tienda con una vista más limpia, cálida y elegante, sin bloques oscuros pesados.
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-3 rounded-[1.6rem] border border-emerald-900/10 bg-[#fffdf8]/85 p-3 shadow-sm backdrop-blur-xl sm:min-w-[430px]">
                        <div class="rounded-[1.2rem] bg-[#ecfff7] p-4">
                            <p class="text-[10px] font-black uppercase tracking-[.16em] text-[#2D9B6B]">Categorías</p>
                            <p class="mt-2 text-3xl font-black text-[#173f35]"><?= e($totalCategories); ?></p>
                        </div>

                        <div class="rounded-[1.2rem] bg-[#fff7e6] p-4">
                            <p class="text-[10px] font-black uppercase tracking-[.16em] text-[#a67c2e]">Productos</p>
                            <p class="mt-2 text-3xl font-black text-[#173f35]"><?= e($totalProducts); ?></p>
                        </div>

                        <div class="rounded-[1.2rem] bg-white p-4 ring-1 ring-emerald-900/10">
                            <p class="text-[10px] font-black uppercase tracking-[.16em] text-[#879188]">Vacías</p>
                            <p class="mt-2 text-3xl font-black text-[#173f35]"><?= e($emptyCategories); ?></p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-6 grid gap-4 md:grid-cols-3">
                <article class="rounded-[1.7rem] border border-emerald-900/10 bg-white/80 p-5 soft-card backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[.18em] text-[#2D9B6B]">Total categorías</p>
                            <h3 class="mt-2 text-3xl font-black text-[#173f35]"><?= e($totalCategories); ?></h3>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#ecfff7] text-[#2D9B6B]">
                            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M7 12h10"/><path d="M10 18h4"/></svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm font-medium text-[#6c776f]">Líneas visibles para ordenar la vitrina digital.</p>
                </article>

                <article class="rounded-[1.7rem] border border-emerald-900/10 bg-white/80 p-5 soft-card backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[.18em] text-[#a67c2e]">Mayor movimiento</p>
                            <h3 class="mt-2 truncate text-2xl font-black text-[#173f35]">
                                <?= e($mostLoadedCategory['title'] ?? 'Sin datos'); ?>
                            </h3>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#fff7e6] text-[#a67c2e]">
                            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="m17 8-5-5-5 5"/><path d="M19 21H5"/></svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm font-medium text-[#6c776f]"><?= e($mostLoadedCount); ?> productos asociados.</p>
                </article>

                <article class="rounded-[1.7rem] border border-emerald-900/10 bg-white/80 p-5 soft-card backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[.18em] text-[#c26a4b]">Por completar</p>
                            <h3 class="mt-2 text-3xl font-black text-[#173f35]"><?= e($emptyCategories); ?></h3>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#fff1eb] text-[#c26a4b]">
                            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm font-medium text-[#6c776f]">Categorías sin productos asociados.</p>
                </article>
            </section>

            <section class="mt-6 rounded-[2rem] border border-emerald-900/10 bg-white/85 p-6 soft-card backdrop-blur-xl">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.20em] text-[#2D9B6B]">
                            <?= $isEdit ? 'Modo edición' : 'Nuevo registro'; ?>
                        </p>
                        <h3 class="mt-2 text-2xl font-black tracking-[-.03em] text-[#173f35]">
                            <?= $isEdit ? 'Editar categoría' : 'Nueva categoría'; ?>
                        </h3>
                        <p class="mt-1 text-sm font-medium text-[#6c776f]">
                            <?= $isEdit ? 'Modifica la categoría seleccionada sin perder la imagen actual.' : 'Registra una categoría para agrupar y mostrar mejor tus productos.'; ?>
                        </p>
                    </div>

                    <?php if ($isEdit): ?>
                        <a href="categorias.php" class="rounded-full border border-emerald-900/10 bg-[#fffdf8] px-5 py-3 text-sm font-black text-[#173f35] transition hover:bg-[#ecfff7]">
                            Cancelar edición
                        </a>
                    <?php endif; ?>
                </div>

                <form action="save.php" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-5">
                    <input type="hidden" name="type" value="category">
                    <input type="hidden" name="mode" value="<?= $isEdit ? 'update' : 'create'; ?>">
                    <input type="hidden" name="old_slug" value="<?= e($current['slug'] ?? ''); ?>">

                    <?php if ($isEdit && !empty($current['img'])): ?>
                        <div class="rounded-[1.5rem] border border-emerald-900/10 bg-[#fbfaf6] p-4">
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <p class="text-sm font-black text-[#55645d]">Imagen actual</p>
                                <span class="rounded-full bg-[#ecfff7] px-3 py-1 text-xs font-black text-[#2D9B6B]">Se conserva si no subes otra</span>
                            </div>
                            <img src="<?= e(imgAdmin($current['img'])); ?>" class="h-64 w-full rounded-[1.2rem] bg-white object-cover shadow-sm">
                        </div>
                    <?php endif; ?>

                    <div class="grid gap-5 lg:grid-cols-3">
                        <div>
                            <label class="text-sm font-black text-[#173f35]">Nombre de categoría</label>
                            <input name="title" required value="<?= e($current['title'] ?? ''); ?>" placeholder="Ej: Relojes de dama" class="mt-2 w-full rounded-2xl border border-emerald-900/10 bg-[#fffdf8] px-4 py-3 font-semibold text-[#173f35] outline-none transition placeholder:text-[#9ca59e] focus:border-[#5FD8AD] focus:bg-white focus:ring-4 focus:ring-[#5FD8AD]/20">
                        </div>

                        <div>
                            <label class="text-sm font-black text-[#173f35]">Subtítulo</label>
                            <input name="subtitle" value="<?= e($current['subtitle'] ?? 'Ver colección'); ?>" placeholder="Ver colección" class="mt-2 w-full rounded-2xl border border-emerald-900/10 bg-[#fffdf8] px-4 py-3 font-semibold text-[#173f35] outline-none transition placeholder:text-[#9ca59e] focus:border-[#5FD8AD] focus:bg-white focus:ring-4 focus:ring-[#5FD8AD]/20">
                        </div>

                        <div>
                            <label class="text-sm font-black text-[#173f35]">Imagen</label>
                            <input name="img" type="file" accept="image/*" class="mt-2 w-full rounded-2xl border border-emerald-900/10 bg-[#fffdf8] px-4 py-3 text-sm font-semibold text-[#55645d] file:mr-4 file:rounded-full file:border-0 file:bg-[#ecfff7] file:px-4 file:py-2 file:font-black file:text-[#2D9B6B] hover:file:bg-[#d9faeb]">
                            <p class="mt-2 text-xs font-semibold text-[#879188]">
                                Recomendado: imagen horizontal, nítida y ligera.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button class="rounded-2xl bg-[#2D9B6B] px-6 py-4 font-black text-white shadow-[0_18px_45px_rgba(45,155,107,.25)] transition hover:-translate-y-.5 hover:bg-[#23835a]">
                            <?= $isEdit ? 'Guardar cambios' : 'Crear categoría'; ?>
                        </button>

                        <?php if ($isEdit): ?>
                            <a href="categorias.php" class="rounded-2xl border border-emerald-900/10 bg-[#fffdf8] px-6 py-4 text-center font-black text-[#173f35] transition hover:bg-[#ecfff7]">
                                Cancelar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="mt-6 rounded-[2rem] border border-emerald-900/10 bg-white/85 p-6 soft-card backdrop-blur-xl">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.20em] text-[#2D9B6B]">Catálogo</p>
                        <h3 class="mt-2 text-2xl font-black tracking-[-.03em] text-[#173f35]">Listado de categorías</h3>
                        <p class="mt-1 text-sm font-medium text-[#6c776f]">Control visual de las categorías registradas en tu JSON.</p>
                    </div>

                    <div class="flex rounded-full border border-emerald-900/10 bg-[#fffdf8] p-1">
                        <span class="rounded-full bg-[#ecfff7] px-4 py-2 text-xs font-black uppercase tracking-[.14em] text-[#2D9B6B]">
                            <?= e($totalCategories); ?> activas
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <?php foreach ($categories as $cat): ?>
                        <?php
                        $qtyProducts = countProductsByCategory($cat['slug'] ?? '', $products);
                        ?>
                        <article class="category-card overflow-hidden rounded-[2rem] border border-emerald-900/10 bg-[#fffdf8] p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_80px_rgba(45,63,52,.12)]">
                            <div class="overflow-hidden rounded-[1.5rem] bg-[#f3efe5]">
                                <?php if (!empty($cat['img'])): ?>
                                    <img src="<?= e(imgAdmin($cat['img'])); ?>" class="category-img h-56 w-full object-cover transition duration-500">
                                <?php else: ?>
                                    <div class="grid h-56 place-items-center text-sm font-black text-[#9ca59e]">
                                        Sin imagen
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-[.18em] text-[#2D9B6B]">
                                            Categoría
                                        </p>

                                        <h4 class="mt-2 text-xl font-black leading-tight text-[#173f35]">
                                            <?= e($cat['title'] ?? ''); ?>
                                        </h4>
                                    </div>

                                    <span class="shrink-0 rounded-full bg-[#fff7e6] px-3 py-1 text-xs font-black text-[#a67c2e]">
                                        <?= e($qtyProducts); ?> prod.
                                    </span>
                                </div>

                                <p class="mt-2 truncate rounded-full bg-white px-3 py-2 text-xs font-bold text-[#879188] ring-1 ring-emerald-900/10">
                                    <?= e($cat['slug'] ?? ''); ?>
                                </p>

                                <div class="mt-4 h-2 overflow-hidden rounded-full bg-[#edf1ec]">
                                    <div class="h-full rounded-full bg-gradient-to-r from-[#5FD8AD] to-[#C8A35F]" style="width: <?= e(min(100, max(8, $qtyProducts * 12))); ?>%"></div>
                                </div>
                            </div>

                            <div class="mt-5 flex gap-2">
                                <a href="categorias.php?edit=<?= e($cat['slug'] ?? ''); ?>" class="flex-1 rounded-2xl bg-[#ecfff7] px-4 py-3 text-center text-sm font-black text-[#2D9B6B] ring-1 ring-[#2D9B6B]/15 transition hover:bg-[#2D9B6B] hover:text-white">
                                    Editar
                                </a>

                                <form action="save.php" method="POST" onsubmit="return confirm('¿Eliminar esta categoría? También se eliminarán sus productos asociados.')">
                                    <input type="hidden" name="type" value="category">
                                    <input type="hidden" name="mode" value="delete">
                                    <input type="hidden" name="old_slug" value="<?= e($cat['slug'] ?? ''); ?>">

                                    <button class="rounded-2xl bg-[#fff1eb] px-4 py-3 text-sm font-black text-[#c26a4b] ring-1 ring-[#c26a4b]/10 transition hover:bg-[#c26a4b] hover:text-white">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>

                    <?php if (empty($categories)): ?>
                        <div class="col-span-full rounded-[2rem] border border-dashed border-emerald-900/15 bg-[#fffdf8] p-10 text-center">
                            <div class="mx-auto grid h-16 w-16 place-items-center rounded-3xl bg-[#ecfff7] text-[#2D9B6B]">
                                <sv<?php
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

$categories = $data['categories'] ?? [];
$products = $data['products'] ?? [];

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function imgAdmin($path)
{
    if (!$path) return '';
    return '../' . str_replace('./', '', $path);
}

function countProductsByCategory($slug, $products)
{
    return count(array_filter($products, function ($product) use ($slug) {
        return ($product['category_slug'] ?? '') === $slug;
    }));
}

$totalCategories = count($categories);
$totalProducts = count($products);
$emptyCategories = 0;
$categoriesWithImage = 0;
$mostLoadedCategory = null;
$mostLoadedCount = 0;
$categoryStats = [];

foreach ($categories as $cat) {
    $slug = $cat['slug'] ?? '';
    $qty = countProductsByCategory($slug, $products);

    if ($qty === 0) {
        $emptyCategories++;
    }

    if (!empty($cat['img'])) {
        $categoriesWithImage++;
    }

    if ($qty > $mostLoadedCount) {
        $mostLoadedCount = $qty;
        $mostLoadedCategory = $cat;
    }

    $categoryStats[] = [
        'title' => $cat['title'] ?? 'Sin nombre',
        'slug' => $slug,
        'qty' => $qty,
        'img' => $cat['img'] ?? '',
        'subtitle' => $cat['subtitle'] ?? '',
    ];
}

usort($categoryStats, function ($a, $b) {
    return $b['qty'] <=> $a['qty'];
});

$editSlug = $_GET['edit'] ?? '';
$current = null;
$isEdit = false;

foreach ($categories as $cat) {
    if (($cat['slug'] ?? '') === $editSlug) {
        $current = $cat;
        $isEdit = true;
        break;
    }
}

$completion = $totalCategories > 0 ? round(($categoriesWithImage / $totalCategories) * 100) : 0;
$emptyPercent = $totalCategories > 0 ? round(($emptyCategories / $totalCategories) * 100) : 0;
$filledPercent = max(0, 100 - $emptyPercent);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías | Admin Le Grand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --cream: #fffdf8;
            --cream-2: #f7f1e6;
            --green: #2D9B6B;
            --green-dark: #173f35;
            --mint: #5FD8AD;
            --gold: #C8A35F;
            --coral: #c26a4b;
            --slate: #52645b;
        }

        body {
            background:
                radial-gradient(circle at 8% -10%, rgba(95, 216, 173, .24), transparent 34rem),
                radial-gradient(circle at 92% 0%, rgba(200, 163, 95, .22), transparent 30rem),
                radial-gradient(circle at 70% 95%, rgba(45, 155, 107, .10), transparent 28rem),
                linear-gradient(180deg, #fffdf8 0%, #f6f0e5 100%);
        }

        .premium-grid {
            background-image:
                linear-gradient(rgba(45, 155, 107, .05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(45, 155, 107, .05) 1px, transparent 1px);
            background-size: 34px 34px;
        }

        .soft-card {
            box-shadow: 0 24px 70px rgba(45, 63, 52, .09);
        }

        .category-card:hover .category-img {
            transform: scale(1.035);
        }

        .modal-open {
            overflow: hidden;
        }

        .modal-backdrop {
            animation: modalFade .18s ease-out;
        }

        .modal-panel {
            animation: modalUp .22s ease-out;
        }

        @keyframes modalFade {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes modalUp {
            from { opacity: 0; transform: translateY(18px) scale(.985); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
</head>

<body class="min-h-screen text-[#173f35] antialiased premium-grid<?= $isEdit ? ' modal-open' : ''; ?>">

<div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">

<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div>
        <header class="sticky top-0 z-40 border-b border-emerald-900/10 bg-[#fffdf8]/90 backdrop-blur-xl">
            <div class="flex min-h-[76px] items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.22em] text-[#2D9B6B]">Le Grand</p>
                    <h1 class="text-xl font-black tracking-[-.03em] text-[#173f35] sm:text-2xl">Gestión de categorías</h1>
                </div>

                <div class="flex items-center gap-2">
                    <a href="../index.php" target="_blank" class="hidden rounded-full border border-emerald-900/10 bg-white/80 px-5 py-3 text-sm font-black text-[#173f35] shadow-sm transition hover:-translate-y-.5 hover:border-[#2D9B6B]/30 hover:bg-[#ecfff7] sm:inline-flex">
                        Ver tienda
                    </a>

                    <button type="button" onclick="openCreateModal()" class="rounded-full bg-[#2D9B6B] px-5 py-3 text-sm font-black text-white shadow-[0_14px_35px_rgba(45,155,107,.23)] transition hover:-translate-y-.5 hover:bg-[#23835a]">
                        + Nueva categoría
                    </button>
                </div>
            </div>
        </header>

        <main class="px-4 py-8 sm:px-6 lg:px-8">

            <section class="relative overflow-hidden rounded-[2.2rem] border border-emerald-900/10 bg-white/75 p-6 soft-card backdrop-blur-xl sm:p-8">
                <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-[#5FD8AD]/25 blur-3xl"></div>
                <div class="absolute -bottom-28 left-20 h-72 w-72 rounded-full bg-[#C8A35F]/20 blur-3xl"></div>

                <div class="relative grid gap-8 xl:grid-cols-[1fr_420px] xl:items-end">
                    <div>
                        <span class="inline-flex rounded-full border border-[#2D9B6B]/15 bg-[#ecfff7] px-4 py-2 text-xs font-black uppercase tracking-[.20em] text-[#2D9B6B]">
                            Catálogo premium
                        </span>

                        <h2 class="mt-5 max-w-3xl text-4xl font-black tracking-[-.06em] text-[#173f35] sm:text-5xl">
                            Categorías claras para vender mejor
                        </h2>

                        <p class="mt-4 max-w-2xl text-sm font-medium leading-7 text-[#52645b] sm:text-base">
                            Usa imágenes consistentes, nombres cortos y agrupaciones ordenadas para que el cliente encuentre rápido cada línea de productos.
                        </p>

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                            <button type="button" onclick="openCreateModal()" class="inline-flex items-center justify-center rounded-2xl bg-[#2D9B6B] px-6 py-4 font-black text-white shadow-[0_18px_45px_rgba(45,155,107,.25)] transition hover:-translate-y-.5 hover:bg-[#23835a]">
                                Crear categoría
                            </button>

                            <a href="productos.php" class="inline-flex items-center justify-center rounded-2xl border border-emerald-900/10 bg-[#fffdf8] px-6 py-4 font-black text-[#173f35] transition hover:bg-[#ecfff7]">
                                Ver productos
                            </a>
                        </div>
                    </div>

                    <div class="rounded-[1.8rem] border border-emerald-900/10 bg-[#fffdf8]/85 p-4 shadow-sm backdrop-blur-xl">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="rounded-[1.2rem] bg-[#ecfff7] p-4">
                                <p class="text-[10px] font-black uppercase tracking-[.16em] text-[#2D9B6B]">Categorías</p>
                                <p class="mt-2 text-3xl font-black text-[#173f35]"><?= e($totalCategories); ?></p>
                            </div>

                            <div class="rounded-[1.2rem] bg-[#fff7e6] p-4">
                                <p class="text-[10px] font-black uppercase tracking-[.16em] text-[#a67c2e]">Productos</p>
                                <p class="mt-2 text-3xl font-black text-[#173f35]"><?= e($totalProducts); ?></p>
                            </div>

                            <div class="rounded-[1.2rem] bg-white p-4 ring-1 ring-emerald-900/10">
                                <p class="text-[10px] font-black uppercase tracking-[.16em] text-[#c26a4b]">Vacías</p>
                                <p class="mt-2 text-3xl font-black text-[#173f35]"><?= e($emptyCategories); ?></p>
                            </div>
                        </div>

                        <div class="mt-4 rounded-[1.3rem] bg-white p-4 ring-1 ring-emerald-900/10">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[.16em] text-[#879188]">Calidad visual</p>
                                    <p class="mt-1 text-sm font-bold text-[#52645b]">Categorías con imagen</p>
                                </div>
                                <p class="text-2xl font-black text-[#2D9B6B]"><?= e($completion); ?>%</p>
                            </div>
                            <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-[#edf1ec]">
                                <div class="h-full rounded-full bg-gradient-to-r from-[#5FD8AD] to-[#C8A35F]" style="width: <?= e($completion); ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-6 grid gap-4 md:grid-cols-3">
                <article class="rounded-[1.7rem] border border-emerald-900/10 bg-white/80 p-5 soft-card backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[.18em] text-[#2D9B6B]">Cobertura visual</p>
                            <h3 class="mt-2 text-3xl font-black text-[#173f35]"><?= e($categoriesWithImage); ?>/<?= e($totalCategories); ?></h3>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#ecfff7] text-[#2D9B6B]">
                            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="4"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.5-3.5a2 2 0 0 0-2.8 0L6 20"/></svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm font-medium text-[#6c776f]">Prioriza categorías con buena foto para elevar la percepción premium.</p>
                </article>

                <article class="rounded-[1.7rem] border border-emerald-900/10 bg-white/80 p-5 soft-card backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs font-black uppercase tracking-[.18em] text-[#a67c2e]">Mayor movimiento</p>
                            <h3 class="mt-2 truncate text-2xl font-black text-[#173f35]">
                                <?= e($mostLoadedCategory['title'] ?? 'Sin datos'); ?>
                            </h3>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#fff7e6] text-[#a67c2e]">
                            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="m17 8-5-5-5 5"/><path d="M19 21H5"/></svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm font-medium text-[#6c776f]"><?= e($mostLoadedCount); ?> productos asociados.</p>
                </article>

                <article class="rounded-[1.7rem] border border-emerald-900/10 bg-white/80 p-5 soft-card backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[.18em] text-[#c26a4b]">Por completar</p>
                            <h3 class="mt-2 text-3xl font-black text-[#173f35]"><?= e($emptyCategories); ?></h3>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#fff1eb] text-[#c26a4b]">
                            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm font-medium text-[#6c776f]">Categorías sin productos asociados. Conviene completarlas o retirarlas.</p>
                </article>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[1fr_360px]">
                <div class="rounded-[2rem] border border-emerald-900/10 bg-white/85 p-6 soft-card backdrop-blur-xl">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[.20em] text-[#2D9B6B]">Catálogo</p>
                            <h3 class="mt-2 text-2xl font-black tracking-[-.03em] text-[#173f35]">Listado de categorías</h3>
                            <p class="mt-1 text-sm font-medium text-[#6c776f]">Vista visual para editar, ordenar y detectar categorías incompletas.</p>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row">
                            <div class="relative">
                                <input id="categorySearch" type="search" placeholder="Buscar categoría..." class="w-full rounded-2xl border border-emerald-900/10 bg-[#fffdf8] px-4 py-3 pl-11 text-sm font-bold text-[#173f35] outline-none transition placeholder:text-[#9ca59e] focus:border-[#5FD8AD] focus:bg-white focus:ring-4 focus:ring-[#5FD8AD]/20 sm:w-72">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-[#879188]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                            <button type="button" onclick="openCreateModal()" class="rounded-2xl bg-[#ecfff7] px-5 py-3 text-sm font-black text-[#2D9B6B] ring-1 ring-[#2D9B6B]/15 transition hover:bg-[#2D9B6B] hover:text-white">
                                + Agregar
                            </button>
                        </div>
                    </div>

                    <div id="categoryGrid" class="mt-6 grid gap-5 sm:grid-cols-2 2xl:grid-cols-3">
                        <?php foreach ($categories as $cat): ?>
                            <?php
                            $qtyProducts = countProductsByCategory($cat['slug'] ?? '', $products);
                            $hasImage = !empty($cat['img']);
                            $statusLabel = $qtyProducts > 0 ? 'Activa' : 'Sin productos';
                            $statusClass = $qtyProducts > 0
                                ? 'bg-[#ecfff7] text-[#2D9B6B] ring-[#2D9B6B]/15'
                                : 'bg-[#fff1eb] text-[#c26a4b] ring-[#c26a4b]/10';
                            ?>
                            <article class="category-card category-item overflow-hidden rounded-[2rem] border border-emerald-900/10 bg-[#fffdf8] p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_80px_rgba(45,63,52,.12)]" data-search="<?= e(strtolower(($cat['title'] ?? '') . ' ' . ($cat['subtitle'] ?? '') . ' ' . ($cat['slug'] ?? ''))); ?>">
                                <div class="relative overflow-hidden rounded-[1.5rem] bg-[#f3efe5]">
                                    <?php if ($hasImage): ?>
                                        <img src="<?= e(imgAdmin($cat['img'])); ?>" class="category-img h-56 w-full object-cover transition duration-500" alt="<?= e($cat['title'] ?? 'Categoría'); ?>">
                                    <?php else: ?>
                                        <div class="grid h-56 place-items-center bg-[#f8f2e8] text-sm font-black text-[#9ca59e]">
                                            Sin imagen
                                        </div>
                                    <?php endif; ?>

                                    <div class="absolute left-3 top-3 flex gap-2">
                                        <span class="rounded-full px-3 py-1 text-[11px] font-black uppercase tracking-[.10em] ring-1 <?= e($statusClass); ?>">
                                            <?= e($statusLabel); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-xs font-black uppercase tracking-[.18em] text-[#2D9B6B]">Categoría</p>

                                            <h4 class="mt-2 truncate text-xl font-black leading-tight text-[#173f35]">
                                                <?= e($cat['title'] ?? ''); ?>
                                            </h4>

                                            <p class="mt-1 line-clamp-1 text-sm font-semibold text-[#6c776f]">
                                                <?= e($cat['subtitle'] ?? 'Ver colección'); ?>
                                            </p>
                                        </div>

                                        <span class="shrink-0 rounded-full bg-[#fff7e6] px-3 py-1 text-xs font-black text-[#a67c2e]">
                                            <?= e($qtyProducts); ?> prod.
                                        </span>
                                    </div>

                                    <p class="mt-3 truncate rounded-full bg-white px-3 py-2 text-xs font-bold text-[#879188] ring-1 ring-emerald-900/10">
                                        <?= e($cat['slug'] ?? ''); ?>
                                    </p>

                                    <div class="mt-4">
                                        <div class="flex justify-between text-[11px] font-black uppercase tracking-[.13em] text-[#879188]">
                                            <span>Movimiento</span>
                                            <span><?= e($qtyProducts); ?> items</span>
                                        </div>
                                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-[#edf1ec]">
                                            <div class="h-full rounded-full bg-gradient-to-r from-[#5FD8AD] to-[#C8A35F]" style="width: <?= e(min(100, max(8, $qtyProducts * 12))); ?>%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 flex gap-2">
                                    <a href="categorias.php?edit=<?= e($cat['slug'] ?? ''); ?>" class="flex-1 rounded-2xl bg-[#ecfff7] px-4 py-3 text-center text-sm font-black text-[#2D9B6B] ring-1 ring-[#2D9B6B]/15 transition hover:bg-[#2D9B6B] hover:text-white">
                                        Editar
                                    </a>

                                    <form action="save.php" method="POST" onsubmit="return confirm('¿Eliminar esta categoría? También se eliminarán sus productos asociados.')">
                                        <input type="hidden" name="type" value="category">
                                        <input type="hidden" name="mode" value="delete">
                                        <input type="hidden" name="old_slug" value="<?= e($cat['slug'] ?? ''); ?>">

                                        <button class="rounded-2xl bg-[#fff1eb] px-4 py-3 text-sm font-black text-[#c26a4b] ring-1 ring-[#c26a4b]/10 transition hover:bg-[#c26a4b] hover:text-white">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>

                        <?php if (empty($categories)): ?>
                            <div class="col-span-full rounded-[2rem] border border-dashed border-emerald-900/15 bg-[#fffdf8] p-10 text-center">
                                <div class="mx-auto grid h-16 w-16 place-items-center rounded-3xl bg-[#ecfff7] text-[#2D9B6B]">
                                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h10"/></svg>
                                </div>
                                <h3 class="mt-4 text-2xl font-black text-[#173f35]">No hay categorías registradas</h3>
                                <p class="mt-2 font-medium text-[#6c776f]">Crea la primera categoría desde el botón superior.</p>
                                <button type="button" onclick="openCreateModal()" class="mt-5 rounded-2xl bg-[#2D9B6B] px-6 py-4 font-black text-white shadow-[0_18px_45px_rgba(45,155,107,.25)] transition hover:-translate-y-.5 hover:bg-[#23835a]">
                                    Crear primera categoría
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="emptySearch" class="mt-6 hidden rounded-[2rem] border border-dashed border-emerald-900/15 bg-[#fffdf8] p-8 text-center">
                        <h3 class="text-xl font-black text-[#173f35]">No se encontraron categorías</h3>
                        <p class="mt-2 text-sm font-semibold text-[#6c776f]">Prueba con otro nombre, subtítulo o slug.</p>
                    </div>
                </div>

                <aside class="space-y-6">
                    <section class="rounded-[2rem] border border-emerald-900/10 bg-white/85 p-6 soft-card backdrop-blur-xl">
                        <p class="text-xs font-black uppercase tracking-[.20em] text-[#2D9B6B]">Salud del catálogo</p>
                        <h3 class="mt-2 text-2xl font-black tracking-[-.03em] text-[#173f35]">Jerarquía visual</h3>
                        <p class="mt-2 text-sm font-medium leading-6 text-[#6c776f]">Un catálogo premium necesita categorías con imagen, nombres claros y productos asociados.</p>

                        <div class="mt-6 space-y-4">
                            <div>
                                <div class="flex justify-between text-xs font-black uppercase tracking-[.13em] text-[#879188]">
                                    <span>Con contenido</span>
                                    <span><?= e($filledPercent); ?>%</span>
                                </div>
                                <div class="mt-2 h-3 overflow-hidden rounded-full bg-[#edf1ec]">
                                    <div class="h-full rounded-full bg-[#2D9B6B]" style="width: <?= e($filledPercent); ?>%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs font-black uppercase tracking-[.13em] text-[#879188]">
                                    <span>Con imagen</span>
                                    <span><?= e($completion); ?>%</span>
                                </div>
                                <div class="mt-2 h-3 overflow-hidden rounded-full bg-[#edf1ec]">
                                    <div class="h-full rounded-full bg-[#C8A35F]" style="width: <?= e($completion); ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[2rem] border border-emerald-900/10 bg-white/85 p-6 soft-card backdrop-blur-xl">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[.20em] text-[#a67c2e]">Top categorías</p>
                                <h3 class="mt-2 text-xl font-black text-[#173f35]">Más cargadas</h3>
                            </div>
                            <div class="grid h-11 w-11 place-items-center rounded-2xl bg-[#fff7e6] text-[#a67c2e]">
                                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m7 14 3-3 4 4 5-7"/></svg>
                            </div>
                        </div>

                        <div class="mt-5 space-y-3">
                            <?php foreach (array_slice($categoryStats, 0, 5) as $item): ?>
                                <div class="rounded-2xl bg-[#fffdf8] p-3 ring-1 ring-emerald-900/10">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="truncate text-sm font-black text-[#173f35]"><?= e($item['title']); ?></p>
                                        <span class="shrink-0 rounded-full bg-[#ecfff7] px-2.5 py-1 text-xs font-black text-[#2D9B6B]"><?= e($item['qty']); ?></span>
                                    </div>
                                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#edf1ec]">
                                        <div class="h-full rounded-full bg-gradient-to-r from-[#5FD8AD] to-[#C8A35F]" style="width: <?= e(min(100, max(8, $item['qty'] * 12))); ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (empty($categoryStats)): ?>
                                <div class="rounded-2xl border border-dashed border-emerald-900/15 bg-[#fffdf8] p-5 text-sm font-bold text-[#6c776f]">
                                    Aún no hay datos para mostrar.
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <section class="rounded-[2rem] border border-emerald-900/10 bg-[#173f35] p-6 text-white shadow-[0_24px_70px_rgba(23,63,53,.18)]">
                        <p class="text-xs font-black uppercase tracking-[.20em] text-[#5FD8AD]">Guía rápida</p>
                        <h3 class="mt-2 text-xl font-black">Recomendación visual</h3>
                        <ul class="mt-4 space-y-3 text-sm font-medium leading-6 text-white/78">
                            <li>• Usa fotos horizontales y con fondo limpio.</li>
                            <li>• Evita nombres largos; máximo 2 a 4 palabras.</li>
                            <li>• No dejes categorías vacías en la tienda final.</li>
                        </ul>
                    </section>
                </aside>
            </section>

        </main>
    </div>
</div>

<div id="categoryModal" class="<?= $isEdit ? 'fixed' : 'hidden'; ?> inset-0 z-50 overflow-y-auto">
    <div class="modal-backdrop min-h-full bg-[#173f35]/45 px-4 py-6 backdrop-blur-sm sm:px-6">
        <div class="mx-auto flex min-h-full max-w-5xl items-center justify-center">
            <section class="modal-panel w-full overflow-hidden rounded-[2rem] bg-[#fffdf8] shadow-[0_35px_120px_rgba(23,63,53,.30)] ring-1 ring-white/50">
                <div class="grid lg:grid-cols-[.85fr_1.15fr]">
                    <aside class="relative overflow-hidden bg-[#ecfff7] p-6 sm:p-8">
                        <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-[#5FD8AD]/40 blur-3xl"></div>
                        <div class="absolute -bottom-24 left-8 h-56 w-56 rounded-full bg-[#C8A35F]/25 blur-3xl"></div>

                        <div class="relative">
                            <span id="modalBadge" class="inline-flex rounded-full bg-white/80 px-4 py-2 text-xs font-black uppercase tracking-[.18em] text-[#2D9B6B] ring-1 ring-[#2D9B6B]/15">
                                <?= $isEdit ? 'Edición rápida' : 'Nuevo registro'; ?>
                            </span>

                            <h2 id="modalTitle" class="mt-5 text-3xl font-black tracking-[-.05em] text-[#173f35] sm:text-4xl">
                                <?= $isEdit ? 'Editar categoría' : 'Nueva categoría'; ?>
                            </h2>

                            <p id="modalDescription" class="mt-4 text-sm font-semibold leading-7 text-[#52645b]">
                                <?= $isEdit ? 'Actualiza el nombre, subtítulo o imagen sin perder la estructura del catálogo.' : 'Crea una categoría clara y visual para mejorar la navegación del cliente.'; ?>
                            </p>

                            <div class="mt-6 rounded-[1.5rem] bg-white/75 p-4 ring-1 ring-emerald-900/10">
                                <p class="text-xs font-black uppercase tracking-[.16em] text-[#879188]">Vista previa</p>

                                <div class="mt-3 overflow-hidden rounded-[1.2rem] bg-[#f4efe4]">
                                    <img id="previewImage" src="<?= $isEdit && !empty($current['img']) ? e(imgAdmin($current['img'])) : ''; ?>" class="<?= $isEdit && !empty($current['img']) ? 'block' : 'hidden'; ?> h-56 w-full object-cover" alt="Vista previa">
                                    <div id="previewEmpty" class="<?= $isEdit && !empty($current['img']) ? 'hidden' : 'grid'; ?> h-56 place-items-center px-6 text-center">
                                        <div>
                                            <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-white text-[#2D9B6B] shadow-sm">
                                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="4"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.5-3.5a2 2 0 0 0-2.8 0L6 20"/></svg>
                                            </div>
                                            <p class="mt-3 text-sm font-black text-[#879188]">Selecciona una imagen para verla aquí</p>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($isEdit && !empty($current['img'])): ?>
                                    <p id="keepImageHint" class="mt-3 rounded-2xl bg-[#fff7e6] px-4 py-3 text-xs font-black text-[#a67c2e]">
                                        Si no subes otra imagen, se conserva la actual.
                                    </p>
                                <?php else: ?>
                                    <p id="keepImageHint" class="mt-3 rounded-2xl bg-[#fff7e6] px-4 py-3 text-xs font-black text-[#a67c2e]">
                                        Recomendado: imagen horizontal, clara y ligera.
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </aside>

                    <div class="p-6 sm:p-8">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[.18em] text-[#2D9B6B]">Formulario</p>
                                <h3 class="mt-2 text-2xl font-black text-[#173f35]">Datos de la categoría</h3>
                            </div>

                            <button type="button" onclick="closeModal()" class="grid h-11 w-11 place-items-center rounded-2xl bg-[#f6f0e5] text-[#52645b] transition hover:bg-[#fff1eb] hover:text-[#c26a4b]" aria-label="Cerrar modal">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>

                        <form id="categoryForm" action="save.php" method="POST" enctype="multipart/form-data" class="mt-7 grid gap-5">
                            <input type="hidden" name="type" value="category">
                            <input id="formMode" type="hidden" name="mode" value="<?= $isEdit ? 'update' : 'create'; ?>">
                            <input id="oldSlug" type="hidden" name="old_slug" value="<?= e($current['slug'] ?? ''); ?>">

                            <div>
                                <label class="flex items-center justify-between gap-3 text-sm font-black text-[#173f35]">
                                    <span>Nombre de categoría</span>
                                    <span class="text-xs font-black text-[#c26a4b]">Obligatorio</span>
                                </label>
                                <input id="titleInput" name="title" required value="<?= e($current['title'] ?? ''); ?>" placeholder="Ej: Relojes de dama" class="mt-2 w-full rounded-2xl border border-emerald-900/10 bg-white px-4 py-3.5 font-semibold text-[#173f35] outline-none transition placeholder:text-[#9ca59e] focus:border-[#5FD8AD] focus:bg-white focus:ring-4 focus:ring-[#5FD8AD]/20">
                                <p class="mt-2 text-xs font-semibold text-[#879188]">Debe ser corto, comercial y fácil de leer.</p>
                            </div>

                            <div>
                                <label class="text-sm font-black text-[#173f35]">Subtítulo o llamada</label>
                                <input id="subtitleInput" name="subtitle" value="<?= e($current['subtitle'] ?? 'Ver colección'); ?>" placeholder="Ver colección" class="mt-2 w-full rounded-2xl border border-emerald-900/10 bg-white px-4 py-3.5 font-semibold text-[#173f35] outline-none transition placeholder:text-[#9ca59e] focus:border-[#5FD8AD] focus:bg-white focus:ring-4 focus:ring-[#5FD8AD]/20">
                                <p class="mt-2 text-xs font-semibold text-[#879188]">Ejemplos: Ver colección, Nueva temporada, Descubrir modelos.</p>
                            </div>

                            <div class="rounded-[1.5rem] border border-dashed border-[#2D9B6B]/25 bg-[#ecfff7]/50 p-4">
                                <label class="text-sm font-black text-[#173f35]">Imagen de categoría</label>
                                <input id="imageInput" name="img" type="file" accept="image/*" class="mt-3 w-full rounded-2xl border border-emerald-900/10 bg-white px-4 py-3 text-sm font-semibold text-[#52645b] file:mr-4 file:rounded-full file:border-0 file:bg-[#ecfff7] file:px-4 file:py-2 file:font-black file:text-[#2D9B6B] hover:file:bg-[#d9faeb]">
                                <p class="mt-3 text-xs font-semibold leading-5 text-[#6c776f]">Para mejor resultado visual: 1200x800 px, fondo limpio, sin texto encima.</p>
                            </div>

                            <div class="grid gap-3 rounded-[1.5rem] bg-[#f7f1e6]/70 p-4 sm:grid-cols-3">
                                <div class="rounded-2xl bg-white p-3 ring-1 ring-emerald-900/10">
                                    <p class="text-[10px] font-black uppercase tracking-[.14em] text-[#2D9B6B]">Color</p>
                                    <p class="mt-1 text-sm font-black text-[#173f35]">Verde premium</p>
                                </div>
                                <div class="rounded-2xl bg-white p-3 ring-1 ring-emerald-900/10">
                                    <p class="text-[10px] font-black uppercase tracking-[.14em] text-[#a67c2e]">Imagen</p>
                                    <p class="mt-1 text-sm font-black text-[#173f35]">Alta calidad</p>
                                </div>
                                <div class="rounded-2xl bg-white p-3 ring-1 ring-emerald-900/10">
                                    <p class="text-[10px] font-black uppercase tracking-[.14em] text-[#c26a4b]">Slug</p>
                                    <p class="mt-1 text-sm font-black text-[#173f35]">Automático</p>
                                </div>
                            </div>

                            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                                <button type="button" onclick="closeModal()" class="rounded-2xl border border-emerald-900/10 bg-[#fffdf8] px-6 py-4 text-center font-black text-[#173f35] transition hover:bg-[#f6f0e5]">
                                    Cancelar
                                </button>

                                <button id="submitButton" class="rounded-2xl bg-[#2D9B6B] px-6 py-4 font-black text-white shadow-[0_18px_45px_rgba(45,155,107,.25)] transition hover:-translate-y-.5 hover:bg-[#23835a]">
                                    <?= $isEdit ? 'Guardar cambios' : 'Crear categoría'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const formMode = document.getElementById('formMode');
    const oldSlug = document.getElementById('oldSlug');
    const modalBadge = document.getElementById('modalBadge');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const submitButton = document.getElementById('submitButton');
    const titleInput = document.getElementById('titleInput');
    const subtitleInput = document.getElementById('subtitleInput');
    const imageInput = document.getElementById('imageInput');
    const previewImage = document.getElementById('previewImage');
    const previewEmpty = document.getElementById('previewEmpty');
    const keepImageHint = document.getElementById('keepImageHint');
    const searchInput = document.getElementById('categorySearch');
    const emptySearch = document.getElementById('emptySearch');

    function showModal() {
        modal.classList.remove('hidden');
        modal.classList.add('fixed');
        document.body.classList.add('modal-open');
        setTimeout(() => titleInput.focus(), 60);
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('fixed');
        document.body.classList.remove('modal-open');

        <?php if ($isEdit): ?>
        window.history.replaceState({}, document.title, 'categorias.php');
        <?php endif; ?>
    }

    function openCreateModal() {
        form.reset();
        formMode.value = 'create';
        oldSlug.value = '';
        titleInput.value = '';
        subtitleInput.value = 'Ver colección';
        modalBadge.textContent = 'Nuevo registro';
        modalTitle.textContent = 'Nueva categoría';
        modalDescription.textContent = 'Crea una categoría clara y visual para mejorar la navegación del cliente.';
        submitButton.textContent = 'Crear categoría';
        keepImageHint.textContent = 'Recomendado: imagen horizontal, clara y ligera.';
        previewImage.removeAttribute('src');
        previewImage.classList.add('hidden');
        previewImage.classList.remove('block');
        previewEmpty.classList.remove('hidden');
        previewEmpty.classList.add('grid');
        showModal();
    }

    imageInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            previewImage.src = event.target.result;
            previewImage.classList.remove('hidden');
            previewImage.classList.add('block');
            previewEmpty.classList.add('hidden');
            previewEmpty.classList.remove('grid');
        };
        reader.readAsDataURL(file);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal || event.target.classList.contains('modal-backdrop')) {
            closeModal();
        }
    });

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const items = document.querySelectorAll('.category-item');
            let visible = 0;

            items.forEach(item => {
                const text = item.dataset.search || '';
                const matches = text.includes(query);
                item.classList.toggle('hidden', !matches);
                if (matches) visible++;
            });

            if (emptySearch) {
                emptySearch.classList.toggle('hidden', visible !== 0 || items.length === 0);
            }
        });
    }
</script>

</body>
</html>
g width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h10"/></svg>
                            </div>
                            <h3 class="mt-4 text-2xl font-black text-[#173f35]">No hay categorías registradas</h3>
                            <p class="mt-2 font-medium text-[#6c776f]">Agrega tu primera categoría desde el formulario superior.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </main>
    </div>
</div>

</body>
</html>
