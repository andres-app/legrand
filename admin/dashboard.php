<?php
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: index.php');
    exit;
}

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

$categories = $data['categories'] ?? [];
$products = $data['products'] ?? [];

$totalCategories = count($categories);
$totalProducts = count($products);
$totalAvailable = count(array_filter($products, fn($p) => empty($p['status'])));
$totalSoldOut = count(array_filter($products, fn($p) => strtolower($p['status'] ?? '') === 'agotado'));

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function imgPath($path)
{
    return '../' . str_replace('./', '', $path);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin | Le Grand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background:
                radial-gradient(circle at 5% -10%, rgba(95, 216, 173, .12), transparent 34rem),
                radial-gradient(circle at 95% 0%, rgba(217, 184, 115, .13), transparent 30rem),
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
                <a href="#resumen" class="flex items-center gap-3 rounded-2xl bg-black px-4 py-3 text-white">
                    <span>📊</span> Resumen
                </a>
                <a href="#categorias" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-neutral-600 transition hover:bg-neutral-100 hover:text-black">
                    <span>🗂️</span> Categorías
                </a>
                <a href="#productos" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-neutral-600 transition hover:bg-neutral-100 hover:text-black">
                    <span>⌚</span> Productos
                </a>
                <a href="../index.php" target="_blank" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-neutral-600 transition hover:bg-neutral-100 hover:text-black">
                    <span>🌐</span> Ver tienda
                </a>
            </nav>

            <div class="border-t border-black/5 p-4">
                <a href="logout.php" class="flex items-center justify-center rounded-2xl bg-red-50 px-5 py-3 text-sm font-black text-red-700 transition hover:bg-red-600 hover:text-white">
                    Cerrar sesión
                </a>
            </div>
        </div>
    </aside>

    <div>
        <header class="sticky top-0 z-40 border-b border-black/5 bg-white/85 backdrop-blur-xl">
            <div class="flex min-h-[76px] items-center justify-between px-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.22em] text-[#2D9B6B]">
                        Le Grand
                    </p>
                    <h1 class="text-xl font-black tracking-[-.03em] sm:text-2xl">
                        Administración de tienda
                    </h1>
                </div>

                <div class="flex items-center gap-2">
                    <a href="../index.php" target="_blank" class="hidden rounded-full bg-neutral-100 px-5 py-3 text-sm font-black transition hover:bg-black hover:text-white sm:inline-flex">
                        Ver tienda
                    </a>

                    <a href="logout.php" class="rounded-full bg-black px-5 py-3 text-sm font-black text-white transition hover:bg-[#5FD8AD] hover:text-black">
                        Salir
                    </a>
                </div>
            </div>
        </header>

        <main class="px-4 py-8 sm:px-6 lg:px-8">

            <section id="resumen">
                <div class="rounded-[2rem] bg-black p-6 text-white shadow-[0_30px_100px_rgba(0,0,0,.18)] sm:p-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[.24em] text-[#5FD8AD]">
                                Dashboard
                            </p>
                            <h2 class="mt-3 text-4xl font-black tracking-[-.06em] sm:text-5xl">
                                Gestión de catálogo
                            </h2>
                            <p class="mt-4 max-w-2xl text-white/55">
                                Administra categorías, productos, precios, estados e imágenes desde archivos JSON.
                            </p>
                        </div>

                        <div class="rounded-3xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur-xl">
                            <p class="text-xs font-black uppercase tracking-[.18em] text-white/45">
                                Acceso
                            </p>
                            <p class="mt-1 text-xl font-black">admin</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-[1.7rem] bg-white p-6 shadow-[0_20px_70px_rgba(0,0,0,.07)]">
                        <p class="text-sm font-bold text-neutral-500">Categorías</p>
                        <p class="mt-3 text-4xl font-black"><?= e($totalCategories); ?></p>
                    </div>

                    <div class="rounded-[1.7rem] bg-white p-6 shadow-[0_20px_70px_rgba(0,0,0,.07)]">
                        <p class="text-sm font-bold text-neutral-500">Productos</p>
                        <p class="mt-3 text-4xl font-black"><?= e($totalProducts); ?></p>
                    </div>

                    <div class="rounded-[1.7rem] bg-white p-6 shadow-[0_20px_70px_rgba(0,0,0,.07)]">
                        <p class="text-sm font-bold text-neutral-500">Disponibles</p>
                        <p class="mt-3 text-4xl font-black text-[#2D9B6B]"><?= e($totalAvailable); ?></p>
                    </div>

                    <div class="rounded-[1.7rem] bg-white p-6 shadow-[0_20px_70px_rgba(0,0,0,.07)]">
                        <p class="text-sm font-bold text-neutral-500">Agotados</p>
                        <p class="mt-3 text-4xl font-black text-red-600"><?= e($totalSoldOut); ?></p>
                    </div>
                </div>
            </section>

            <section class="mt-8 grid gap-6 xl:grid-cols-[.9fr_1.1fr]">

                <div id="categorias" class="rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                    <h2 class="text-2xl font-black">Nueva categoría</h2>
                    <p class="mt-1 text-sm text-neutral-500">Crea una categoría para agrupar productos.</p>

                    <form action="save.php" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-4">
                        <input type="hidden" name="type" value="category">

                        <div>
                            <label class="text-sm font-bold">Nombre</label>
                            <input name="title" required placeholder="Ej: Relojes de dama" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:ring-4 focus:ring-emerald-100">
                        </div>

                        <div>
                            <label class="text-sm font-bold">Subtítulo</label>
                            <input name="subtitle" value="Ver colección" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:ring-4 focus:ring-emerald-100">
                        </div>

                        <div>
                            <label class="text-sm font-bold">Imagen</label>
                            <input name="img" type="file" accept="image/*" class="mt-2 w-full rounded-2xl border border-black/10 bg-white px-4 py-3">
                        </div>

                        <button class="rounded-2xl bg-black px-5 py-4 font-black text-white transition hover:bg-[#5FD8AD] hover:text-black">
                            Guardar categoría
                        </button>
                    </form>
                </div>

                <div id="productos" class="rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                    <h2 class="text-2xl font-black">Nuevo producto</h2>
                    <p class="mt-1 text-sm text-neutral-500">Agrega productos al catálogo de la tienda.</p>

                    <form action="save.php" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-4">
                        <input type="hidden" name="type" value="product">

                        <div>
                            <label class="text-sm font-bold">Categoría</label>
                            <select name="category_slug" required class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:ring-4 focus:ring-emerald-100">
                                <option value="">Seleccionar categoría</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= e($cat['slug']); ?>">
                                        <?= e($cat['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-bold">Nombre</label>
                            <input name="name" required placeholder="Ej: Reloj Fossil FS9999" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:ring-4 focus:ring-emerald-100">
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-bold">Precio anterior</label>
                                <input name="old_price" placeholder="S/ 600.00" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3">
                            </div>

                            <div>
                                <label class="text-sm font-bold">Precio actual</label>
                                <input name="price" placeholder="S/ 500.00" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3">
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-bold">Descuento</label>
                                <input name="discount" placeholder="-17%" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3">
                            </div>

                            <div>
                                <label class="text-sm font-bold">Estado</label>
                                <select name="status" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3">
                                    <option value="">Disponible</option>
                                    <option value="Agotado">Agotado</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-bold">Meta</label>
                            <input name="meta" placeholder="Caballero · Oferta · Relojes" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3">
                        </div>

                        <div>
                            <label class="text-sm font-bold">Descripción</label>
                            <textarea name="description" rows="3" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3"></textarea>
                        </div>

                        <div>
                            <label class="text-sm font-bold">Imagen principal</label>
                            <input name="img" type="file" accept="image/*" class="mt-2 w-full rounded-2xl border border-black/10 bg-white px-4 py-3">
                        </div>

                        <button class="rounded-2xl bg-[#5FD8AD] px-5 py-4 font-black text-black transition hover:bg-black hover:text-white">
                            Guardar producto
                        </button>
                    </form>
                </div>

            </section>

            <section class="mt-8 rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black">Categorías registradas</h2>
                        <p class="text-sm text-neutral-500">Listado de categorías creadas.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <?php foreach ($categories as $cat): ?>
                        <div class="rounded-2xl border border-black/5 bg-[#fbfaf7] p-4">
                            <?php if (!empty($cat['img'])): ?>
                                <img src="<?= e(imgPath($cat['img'])); ?>" class="h-36 w-full rounded-xl object-cover">
                            <?php else: ?>
                                <div class="grid h-36 place-items-center rounded-xl bg-neutral-100 text-sm font-bold text-neutral-400">
                                    Sin imagen
                                </div>
                            <?php endif; ?>

                            <h3 class="mt-3 font-black"><?= e($cat['title']); ?></h3>
                            <p class="text-sm text-neutral-500"><?= e($cat['slug']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="mt-8 rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black">Productos registrados</h2>
                        <p class="text-sm text-neutral-500">Vista rápida del catálogo actual.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <?php foreach ($products as $product): ?>
                        <div class="rounded-2xl border border-black/5 bg-[#fbfaf7] p-4">
                            <?php if (!empty($product['img'])): ?>
                                <img src="<?= e(imgPath($product['img'])); ?>" class="h-44 w-full rounded-xl bg-neutral-100 object-contain p-3">
                            <?php else: ?>
                                <div class="grid h-44 place-items-center rounded-xl bg-neutral-100 text-sm font-bold text-neutral-400">
                                    Sin imagen
                                </div>
                            <?php endif; ?>

                            <div class="mt-3 flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-black"><?= e($product['name']); ?></h3>
                                    <p class="text-sm text-neutral-500"><?= e($product['category_slug']); ?></p>
                                </div>

                                <?php if (!empty($product['status'])): ?>
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-black text-red-700">
                                        <?= e($product['status']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="mt-3 flex items-center gap-2">
                                <?php if (!empty($product['old_price'])): ?>
                                    <del class="text-sm text-neutral-400"><?= e($product['old_price']); ?></del>
                                <?php endif; ?>
                                <span class="font-black"><?= e($product['price']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

        </main>
    </div>
</div>

</body>
</html>