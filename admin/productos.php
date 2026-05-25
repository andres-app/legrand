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

function categoryTitle($slug, $categories)
{
    foreach ($categories as $cat) {
        if (($cat['slug'] ?? '') === $slug) {
            return $cat['title'] ?? $slug;
        }
    }

    return $slug;
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos | Admin Le Grand</title>
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
                    <h1 class="text-xl font-black tracking-[-.03em] sm:text-2xl">Gestión de productos</h1>
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
                            Productos
                        </h2>
                        <p class="mt-4 max-w-2xl text-white/55">
                            Crea, edita, clasifica y controla los productos visibles en la tienda.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur-xl">
                        <p class="text-xs font-black uppercase tracking-[.18em] text-white/45">
                            Total productos
                        </p>
                        <p class="mt-1 text-3xl font-black"><?= e(count($products)); ?></p>
                    </div>
                </div>
            </section>

            <section class="mt-6 rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="text-2xl font-black">
                            <?= $isEdit ? 'Editar producto' : 'Nuevo producto'; ?>
                        </h3>
                        <p class="mt-1 text-sm text-neutral-500">
                            <?= $isEdit ? 'Modifica los datos del producto seleccionado.' : 'Registra un nuevo producto para el catálogo.'; ?>
                        </p>
                    </div>

                    <?php if ($isEdit): ?>
                        <a href="productos.php" class="rounded-full bg-neutral-100 px-5 py-3 text-sm font-black hover:bg-black hover:text-white">
                            Cancelar edición
                        </a>
                    <?php endif; ?>
                </div>

                <form action="save.php" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-5">
                    <input type="hidden" name="type" value="product">
                    <input type="hidden" name="mode" value="<?= $isEdit ? 'update' : 'create'; ?>">
                    <input type="hidden" name="old_slug" value="<?= e($current['slug'] ?? ''); ?>">

                    <?php if ($isEdit && !empty($current['img'])): ?>
                        <div class="rounded-[1.5rem] border border-black/5 bg-[#f7f6f2] p-4">
                            <p class="mb-3 text-sm font-black text-neutral-500">Imagen actual</p>
                            <img src="<?= e(imgAdmin($current['img'])); ?>" class="h-64 w-full rounded-[1.2rem] bg-white object-contain p-5">
                        </div>
                    <?php endif; ?>

                    <div class="grid gap-5 lg:grid-cols-3">
                        <div>
                            <label class="text-sm font-bold">Categoría</label>
                            <select name="category_slug" required class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:ring-4 focus:ring-emerald-100">
                                <option value="">Seleccionar categoría</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= e($cat['slug']); ?>" <?= (($current['category_slug'] ?? '') === ($cat['slug'] ?? '')) ? 'selected' : ''; ?>>
                                        <?= e($cat['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-bold">Nombre</label>
                            <input name="name" required value="<?= e($current['name'] ?? ''); ?>" placeholder="Ej: Reloj Fossil FS9999" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:ring-4 focus:ring-emerald-100">
                        </div>

                        <div>
                            <label class="text-sm font-bold">Meta / etiquetas</label>
                            <input name="meta" value="<?= e($current['meta'] ?? ''); ?>" placeholder="Caballero · Oferta · Relojes" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:ring-4 focus:ring-emerald-100">
                        </div>
                    </div>

                    <div class="grid gap-5 lg:grid-cols-4">
                        <div>
                            <label class="text-sm font-bold">Precio anterior</label>
                            <input name="old_price" value="<?= e($current['old_price'] ?? ''); ?>" placeholder="S/ 600.00" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3">
                        </div>

                        <div>
                            <label class="text-sm font-bold">Precio actual</label>
                            <input name="price" value="<?= e($current['price'] ?? ''); ?>" placeholder="S/ 500.00" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3">
                        </div>

                        <div>
                            <label class="text-sm font-bold">Descuento</label>
                            <input name="discount" value="<?= e($current['discount'] ?? ''); ?>" placeholder="-17%" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3">
                        </div>

                        <div>
                            <label class="text-sm font-bold">Estado</label>
                            <select name="status" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3">
                                <option value="" <?= empty($current['status']) ? 'selected' : ''; ?>>Disponible</option>
                                <option value="Agotado" <?= (($current['status'] ?? '') === 'Agotado') ? 'selected' : ''; ?>>Agotado</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-bold">Descripción</label>
                        <textarea name="description" rows="4" placeholder="Descripción comercial del producto" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:ring-4 focus:ring-emerald-100"><?= e($current['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="grid gap-5 lg:grid-cols-[1fr_220px]">
                        <div>
                            <label class="text-sm font-bold">Imagen principal</label>
                            <input name="img" type="file" accept="image/*" class="mt-2 w-full rounded-2xl border border-black/10 bg-white px-4 py-3">
                            <p class="mt-2 text-xs font-semibold text-neutral-400">
                                Al editar, si no subes una imagen nueva, se conserva la actual.
                            </p>
                        </div>

                        <label class="mt-7 flex items-center gap-3 rounded-2xl border border-black/10 bg-[#f7f6f2] px-4 py-3 font-bold">
                            <input type="checkbox" name="wish" value="1" <?= !empty($current['wish']) ? 'checked' : ''; ?>>
                            Mostrar “Lo deseo”
                        </label>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button class="rounded-2xl bg-black px-6 py-4 font-black text-white hover:bg-[#5FD8AD] hover:text-black">
                            <?= $isEdit ? 'Guardar cambios' : 'Crear producto'; ?>
                        </button>

                        <?php if ($isEdit): ?>
                            <a href="productos.php" class="rounded-2xl bg-neutral-100 px-6 py-4 text-center font-black hover:bg-neutral-200">
                                Cancelar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="mt-6 rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="text-2xl font-black">Listado de productos</h3>
                        <p class="mt-1 text-sm text-neutral-500">Productos registrados en el archivo JSON.</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <?php foreach ($products as $product): ?>
                        <article class="rounded-[2rem] border border-black/5 bg-[#fbfaf7] p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                            <?php if (!empty($product['img'])): ?>
                                <img src="<?= e(imgAdmin($product['img'])); ?>" class="h-56 w-full rounded-[1.5rem] bg-white object-contain p-4">
                            <?php else: ?>
                                <div class="grid h-56 place-items-center rounded-[1.5rem] bg-neutral-100 text-sm font-bold text-neutral-400">
                                    Sin imagen
                                </div>
                            <?php endif; ?>

                            <div class="mt-4">
                                <p class="text-xs font-black uppercase tracking-[.18em] text-[#2D9B6B]">
                                    <?= e(categoryTitle($product['category_slug'] ?? '', $categories)); ?>
                                </p>

                                <h4 class="mt-2 text-xl font-black leading-tight">
                                    <?= e($product['name'] ?? ''); ?>
                                </h4>

                                <p class="mt-1 text-xs font-bold text-neutral-400">
                                    <?= e($product['slug'] ?? ''); ?>
                                </p>

                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <?php if (!empty($product['old_price'])): ?>
                                        <del class="text-sm text-neutral-400"><?= e($product['old_price']); ?></del>
                                    <?php endif; ?>

                                    <span class="text-lg font-black"><?= e($product['price'] ?? ''); ?></span>

                                    <?php if (!empty($product['discount'])): ?>
                                        <span class="rounded-full bg-[#5FD8AD] px-3 py-1 text-xs font-black text-black">
                                            <?= e($product['discount']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="mt-3">
                                    <?php if (!empty($product['status'])): ?>
                                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-black text-red-700">
                                            <?= e($product['status']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">
                                            Disponible
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mt-5 flex gap-2">
                                <a href="productos.php?edit=<?= e($product['slug'] ?? ''); ?>" class="flex-1 rounded-2xl bg-[#5FD8AD] px-4 py-3 text-center text-sm font-black text-black hover:bg-black hover:text-white">
                                    Editar
                                </a>

                                <form action="save.php" method="POST" onsubmit="return confirm('¿Eliminar este producto?')">
                                    <input type="hidden" name="type" value="product">
                                    <input type="hidden" name="mode" value="delete">
                                    <input type="hidden" name="old_slug" value="<?= e($product['slug'] ?? ''); ?>">

                                    <button class="rounded-2xl bg-red-50 px-4 py-3 text-sm font-black text-red-700 hover:bg-red-600 hover:text-white">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>

                    <?php if (empty($products)): ?>
                        <div class="col-span-full rounded-[2rem] bg-[#fbfaf7] p-10 text-center">
                            <h3 class="text-2xl font-black">No hay productos registrados</h3>
                            <p class="mt-2 text-neutral-500">Agrega tu primer producto desde el formulario superior.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </main>
    </div>
</div>

</body>
</html>