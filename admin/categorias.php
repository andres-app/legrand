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

    <aside class="hidden border-r border-black/5 bg-white/90 backdrop-blur-xl lg:block">
        <div class="sticky top-0 flex h-screen flex-col">
            <div class="border-b border-black/5 px-7 py-6">
                <img src="../media/cropped-logo-1.png" class="w-48" alt="Le Grand">
                <p class="mt-3 text-xs font-black uppercase tracking-[.2em] text-[#2D9B6B]">
                    Panel administrativo
                </p>
            </div>

            <nav class="flex-1 space-y-2 px-4 py-6 text-sm font-black">
                <a href="dashboard.php" class="block rounded-2xl px-4 py-3 text-neutral-600 hover:bg-neutral-100">Dashboard</a>
                <a href="categorias.php" class="block rounded-2xl bg-black px-4 py-3 text-white">Categorías</a>
                <a href="productos.php" class="block rounded-2xl px-4 py-3 text-neutral-600 hover:bg-neutral-100">Productos</a>
                <a href="../index.php" target="_blank" class="block rounded-2xl px-4 py-3 text-neutral-600 hover:bg-neutral-100">Ver tienda</a>
            </nav>

            <div class="border-t border-black/5 p-4">
                <a href="logout.php" class="block rounded-2xl bg-red-50 px-5 py-3 text-center text-sm font-black text-red-700 hover:bg-red-600 hover:text-white">
                    Cerrar sesión
                </a>
            </div>
        </div>
    </aside>

    <div>
        <header class="sticky top-0 z-40 border-b border-black/5 bg-white/85 backdrop-blur-xl">
            <div class="flex min-h-[76px] items-center justify-between px-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.22em] text-[#2D9B6B]">Le Grand</p>
                    <h1 class="text-xl font-black tracking-[-.03em] sm:text-2xl">Gestión de categorías</h1>
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
                            Catálogo
                        </p>
                        <h2 class="mt-3 text-4xl font-black tracking-[-.06em] sm:text-5xl">
                            Categorías
                        </h2>
                        <p class="mt-4 max-w-2xl text-white/55">
                            Organiza los productos por líneas comerciales para mejorar la navegación de la tienda.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur-xl">
                        <p class="text-xs font-black uppercase tracking-[.18em] text-white/45">
                            Total categorías
                        </p>
                        <p class="mt-1 text-3xl font-black"><?= e(count($categories)); ?></p>
                    </div>
                </div>
            </section>

            <section class="mt-6 rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="text-2xl font-black">
                            <?= $isEdit ? 'Editar categoría' : 'Nueva categoría'; ?>
                        </h3>
                        <p class="mt-1 text-sm text-neutral-500">
                            <?= $isEdit ? 'Modifica los datos de la categoría seleccionada.' : 'Registra una nueva categoría para agrupar productos.'; ?>
                        </p>
                    </div>

                    <?php if ($isEdit): ?>
                        <a href="categorias.php" class="rounded-full bg-neutral-100 px-5 py-3 text-sm font-black hover:bg-black hover:text-white">
                            Cancelar edición
                        </a>
                    <?php endif; ?>
                </div>

                <form action="save.php" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-5">
                    <input type="hidden" name="type" value="category">
                    <input type="hidden" name="mode" value="<?= $isEdit ? 'update' : 'create'; ?>">
                    <input type="hidden" name="old_slug" value="<?= e($current['slug'] ?? ''); ?>">

                    <?php if ($isEdit && !empty($current['img'])): ?>
                        <div class="rounded-[1.5rem] border border-black/5 bg-[#f7f6f2] p-4">
                            <p class="mb-3 text-sm font-black text-neutral-500">Imagen actual</p>
                            <img src="<?= e(imgAdmin($current['img'])); ?>" class="h-64 w-full rounded-[1.2rem] bg-white object-cover">
                        </div>
                    <?php endif; ?>

                    <div class="grid gap-5 lg:grid-cols-3">
                        <div>
                            <label class="text-sm font-bold">Nombre de categoría</label>
                            <input name="title" required value="<?= e($current['title'] ?? ''); ?>" placeholder="Ej: Relojes de dama" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:ring-4 focus:ring-emerald-100">
                        </div>

                        <div>
                            <label class="text-sm font-bold">Subtítulo</label>
                            <input name="subtitle" value="<?= e($current['subtitle'] ?? 'Ver colección'); ?>" placeholder="Ver colección" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:ring-4 focus:ring-emerald-100">
                        </div>

                        <div>
                            <label class="text-sm font-bold">Imagen</label>
                            <input name="img" type="file" accept="image/*" class="mt-2 w-full rounded-2xl border border-black/10 bg-white px-4 py-3">
                            <p class="mt-2 text-xs font-semibold text-neutral-400">
                                Al editar, si no subes una imagen nueva, se conserva la actual.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button class="rounded-2xl bg-black px-6 py-4 font-black text-white hover:bg-[#5FD8AD] hover:text-black">
                            <?= $isEdit ? 'Guardar cambios' : 'Crear categoría'; ?>
                        </button>

                        <?php if ($isEdit): ?>
                            <a href="categorias.php" class="rounded-2xl bg-neutral-100 px-6 py-4 text-center font-black hover:bg-neutral-200">
                                Cancelar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="mt-6 rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="text-2xl font-black">Listado de categorías</h3>
                        <p class="mt-1 text-sm text-neutral-500">Categorías registradas en el archivo JSON.</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <?php foreach ($categories as $cat): ?>
                        <?php
                        $qtyProducts = countProductsByCategory($cat['slug'] ?? '', $products);
                        ?>
                        <article class="rounded-[2rem] border border-black/5 bg-[#fbfaf7] p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                            <?php if (!empty($cat['img'])): ?>
                                <img src="<?= e(imgAdmin($cat['img'])); ?>" class="h-56 w-full rounded-[1.5rem] bg-white object-cover">
                            <?php else: ?>
                                <div class="grid h-56 place-items-center rounded-[1.5rem] bg-neutral-100 text-sm font-bold text-neutral-400">
                                    Sin imagen
                                </div>
                            <?php endif; ?>

                            <div class="mt-4">
                                <p class="text-xs font-black uppercase tracking-[.18em] text-[#2D9B6B]">
                                    Categoría
                                </p>

                                <h4 class="mt-2 text-xl font-black leading-tight">
                                    <?= e($cat['title'] ?? ''); ?>
                                </h4>

                                <p class="mt-1 text-xs font-bold text-neutral-400">
                                    <?= e($cat['slug'] ?? ''); ?>
                                </p>

                                <div class="mt-3 inline-flex rounded-full bg-white px-3 py-1 text-xs font-black text-neutral-600">
                                    <?= e($qtyProducts); ?> productos asociados
                                </div>
                            </div>

                            <div class="mt-5 flex gap-2">
                                <a href="categorias.php?edit=<?= e($cat['slug'] ?? ''); ?>" class="flex-1 rounded-2xl bg-[#5FD8AD] px-4 py-3 text-center text-sm font-black text-black hover:bg-black hover:text-white">
                                    Editar
                                </a>

                                <form action="save.php" method="POST" onsubmit="return confirm('¿Eliminar esta categoría? También se eliminarán sus productos asociados.')">
                                    <input type="hidden" name="type" value="category">
                                    <input type="hidden" name="mode" value="delete">
                                    <input type="hidden" name="old_slug" value="<?= e($cat['slug'] ?? ''); ?>">

                                    <button class="rounded-2xl bg-red-50 px-4 py-3 text-sm font-black text-red-700 hover:bg-red-600 hover:text-white">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>

                    <?php if (empty($categories)): ?>
                        <div class="col-span-full rounded-[2rem] bg-[#fbfaf7] p-10 text-center">
                            <h3 class="text-2xl font-black">No hay categorías registradas</h3>
                            <p class="mt-2 text-neutral-500">Agrega tu primera categoría desde el formulario superior.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </main>
    </div>
</div>

</body>
</html>