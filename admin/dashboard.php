<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

date_default_timezone_set('America/Lima');

$data = loadStoreData();

$categories = $data['categories'] ?? [];
$products   = $data['products'] ?? [];
$orders     = $data['orders'] ?? ($data['sales'] ?? ($data['ventas'] ?? []));

function first_value(array $array, array $keys, $default = null) {
    foreach ($keys as $key) {
        if (isset($array[$key]) && $array[$key] !== '') {
            return $array[$key];
        }
    }
    return $default;
}

function parse_amount($value): float {
    if (is_int($value) || is_float($value)) {
        return (float)$value;
    }

    $value = trim((string)$value);
    if ($value === '') {
        return 0;
    }

    $clean = preg_replace('/[^0-9,\.\-]/u', '', $value);
    if ($clean === '' || $clean === '-' || $clean === null) {
        return 0;
    }

    $lastComma = strrpos($clean, ',');
    $lastDot   = strrpos($clean, '.');

    if ($lastComma !== false && $lastDot !== false) {
        if ($lastComma > $lastDot) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } else {
            $clean = str_replace(',', '', $clean);
        }
    } elseif ($lastComma !== false) {
        $clean = str_replace(',', '.', $clean);
    }

    return (float)$clean;
}

function money_format_pe($value): string {
    return 'S/ ' . number_format((float)$value, 2, '.', ',');
}

function int_format_pe($value): string {
    return number_format((float)$value, 0, '.', ',');
}

function product_name(array $product): string {
    return (string)first_value($product, ['name', 'title', 'nombre', 'product_name'], 'Producto sin nombre');
}

function product_price(array $product): float {
    return parse_amount(first_value($product, ['final_price', 'price_final', 'sale_price', 'precio_venta', 'price', 'precio'], 0));
}

function product_cost(array $product): float {
    return parse_amount(first_value($product, ['cost', 'costo', 'purchase_price', 'precio_compra', 'cost_price'], 0));
}

function product_stock(array $product) {
    $stock = first_value($product, ['stock', 'qty', 'quantity', 'cantidad', 'inventory', 'inventario'], null);

    if ($stock === null) {
        $status = strtolower(trim((string)($product['status'] ?? $product['estado'] ?? '')));
        if (in_array($status, ['agotado', 'sin stock', 'sold out', 'out_of_stock'], true)) {
            return 0;
        }
        return null;
    }

    return max(0, (int)parse_amount($stock));
}

function product_sold(array $product): int {
    return max(0, (int)parse_amount(first_value($product, ['sold', 'total_sold', 'ventas', 'vendidos', 'quantity_sold', 'sales_count'], 0)));
}

function is_sold_out(array $product): bool {
    $status = strtolower(trim((string)($product['status'] ?? $product['estado'] ?? '')));
    $stock = product_stock($product);

    return in_array($status, ['agotado', 'sin stock', 'sold out', 'out_of_stock'], true) || $stock === 0;
}

function is_cancelled_order(array $order): bool {
    $status = strtolower(trim((string)first_value($order, ['status', 'estado'], '')));
    return in_array($status, ['cancelado', 'cancelada', 'anulado', 'anulada', 'rechazado', 'rechazada', 'cancelled', 'void'], true);
}

function normalize_date($value): string {
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }

    $timestamp = strtotime(str_replace('/', '-', $value));
    return $timestamp ? date('Y-m-d', $timestamp) : substr($value, 0, 10);
}

$today = date('Y-m-d');
$todayLabel = date('d/m/Y');

$categoryMap = [];
foreach ($categories as $cat) {
    $slug = (string)($cat['slug'] ?? $cat['id'] ?? '');
    if ($slug !== '') {
        $categoryMap[$slug] = (string)($cat['title'] ?? $cat['name'] ?? $slug);
    }
}

$totalCategories = count($categories);
$totalProducts   = count($products);

$productsWithImage = 0;
$productsWithDiscount = 0;
$soldOutProducts = 0;
$availableProducts = 0;
$lowStockProducts = 0;
$totalStock = 0;
$stockCounted = 0;
$inventoryValue = 0;

$categoryStats = [];
$topSoldFromProducts = [];
$lowStockList = [];
$latestProducts = array_slice(array_reverse($products), 0, 5);

foreach ($categories as $cat) {
    $slug = (string)($cat['slug'] ?? $cat['id'] ?? '');
    $categoryStats[$slug] = [
        'title' => (string)($cat['title'] ?? $cat['name'] ?? $slug),
        'slug' => $slug,
        'count' => 0,
        'value' => 0,
    ];
}

foreach ($products as $product) {
    $name = product_name($product);
    $price = product_price($product);
    $cost = product_cost($product);
    $stock = product_stock($product);
    $sold = product_sold($product);
    $slug = (string)($product['category_slug'] ?? $product['category'] ?? $product['categoria'] ?? 'sin-categoria');
    $catTitle = $categoryMap[$slug] ?? 'Sin categoría';

    if (!isset($categoryStats[$slug])) {
        $categoryStats[$slug] = [
            'title' => $catTitle,
            'slug' => $slug,
            'count' => 0,
            'value' => 0,
        ];
    }

    $categoryStats[$slug]['count']++;
    $categoryStats[$slug]['value'] += $price;

    if (!empty($product['img']) || !empty($product['image']) || !empty($product['imagen'])) {
        $productsWithImage++;
    }

    if (!empty($product['discount']) || !empty($product['descuento']) || !empty($product['old_price']) || !empty($product['precio_anterior'])) {
        $productsWithDiscount++;
    }

    if (is_sold_out($product)) {
        $soldOutProducts++;
    } else {
        $availableProducts++;
    }

    if ($stock !== null) {
        $stockCounted++;
        $totalStock += $stock;
        $inventoryValue += $stock * $cost;

        if ($stock <= 5) {
            $lowStockProducts++;
            $lowStockList[] = [
                'name' => $name,
                'category' => $catTitle,
                'stock' => $stock,
                'price' => $price,
            ];
        }
    }

    if ($sold > 0) {
        $topSoldFromProducts[] = [
            'name' => $name,
            'category' => $catTitle,
            'sold' => $sold,
            'revenue' => $sold * $price,
            'profit' => $sold * max($price - $cost, 0),
        ];
    }
}

$categoryStats = array_values($categoryStats);
usort($categoryStats, function ($a, $b) {
    return $b['count'] <=> $a['count'];
});

usort($topSoldFromProducts, function ($a, $b) {
    return $b['sold'] <=> $a['sold'];
});

usort($lowStockList, function ($a, $b) {
    return $a['stock'] <=> $b['stock'];
});

$topSold = array_slice($topSoldFromProducts, 0, 5);
$lowStockTop = array_slice($lowStockList, 0, 5);

$todayRevenue = 0;
$todayProfit = 0;
$todayOrders = 0;
$todayItems = 0;
$totalRevenue = 0;
$totalProfit = 0;
$totalOrders = 0;
$soldFromOrders = [];

foreach ($orders as $order) {
    if (!is_array($order) || is_cancelled_order($order)) {
        continue;
    }

    $orderDate = normalize_date(first_value($order, ['date', 'fecha', 'created_at', 'createdAt', 'fecha_venta'], ''));
    $items = $order['items'] ?? $order['products'] ?? $order['productos'] ?? [];

    $orderRevenue = parse_amount(first_value($order, ['total', 'amount', 'monto', 'subtotal', 'total_amount'], 0));
    $orderCost = parse_amount(first_value($order, ['cost', 'costo', 'total_cost'], 0));
    $orderProfit = parse_amount(first_value($order, ['profit', 'ganancia', 'utility', 'utilidad'], 0));
    $orderQty = 0;

    if (is_array($items) && !empty($items)) {
        $calcRevenue = 0;
        $calcCost = 0;

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $qty = max(1, (int)parse_amount(first_value($item, ['qty', 'quantity', 'cantidad'], 1)));
            $name = (string)first_value($item, ['name', 'title', 'nombre', 'product_name'], 'Producto');
            $price = parse_amount(first_value($item, ['price', 'precio', 'sale_price', 'precio_venta'], 0));
            $cost = parse_amount(first_value($item, ['cost', 'costo', 'purchase_price', 'precio_compra'], 0));

            $calcRevenue += $qty * $price;
            $calcCost += $qty * $cost;
            $orderQty += $qty;

            if (!isset($soldFromOrders[$name])) {
                $soldFromOrders[$name] = [
                    'name' => $name,
                    'category' => (string)first_value($item, ['category', 'category_slug', 'categoria'], 'Venta'),
                    'sold' => 0,
                    'revenue' => 0,
                    'profit' => 0,
                ];
            }

            $soldFromOrders[$name]['sold'] += $qty;
            $soldFromOrders[$name]['revenue'] += $qty * $price;
            $soldFromOrders[$name]['profit'] += $qty * max($price - $cost, 0);
        }

        if ($orderRevenue <= 0) {
            $orderRevenue = $calcRevenue;
        }
        if ($orderCost <= 0) {
            $orderCost = $calcCost;
        }
    }

    if ($orderProfit <= 0 && ($orderRevenue > 0 || $orderCost > 0)) {
        $orderProfit = $orderRevenue - $orderCost;
    }

    $totalOrders++;
    $totalRevenue += $orderRevenue;
    $totalProfit += $orderProfit;

    if ($orderDate === $today) {
        $todayOrders++;
        $todayItems += $orderQty;
        $todayRevenue += $orderRevenue;
        $todayProfit += $orderProfit;
    }
}

if (!empty($soldFromOrders)) {
    $topSold = array_values($soldFromOrders);
    usort($topSold, function ($a, $b) {
        return $b['sold'] <=> $a['sold'];
    });
    $topSold = array_slice($topSold, 0, 5);
}

$coverageImage = $totalProducts > 0 ? round(($productsWithImage / $totalProducts) * 100) : 0;
$soldOutRate = $totalProducts > 0 ? round(($soldOutProducts / $totalProducts) * 100) : 0;
$availableRate = $totalProducts > 0 ? round(($availableProducts / $totalProducts) * 100) : 0;
$discountRate = $totalProducts > 0 ? round(($productsWithDiscount / $totalProducts) * 100) : 0;
$avgTicket = $todayOrders > 0 ? $todayRevenue / $todayOrders : 0;
$profitMarginToday = $todayRevenue > 0 ? round(($todayProfit / $todayRevenue) * 100) : 0;

$topCategory = $categoryStats[0] ?? [
    'title' => 'Sin categoría dominante',
    'count' => 0,
];

$categoryLabels = array_map(function ($item) {
    return $item['title'];
}, $categoryStats);
$categoryValues = array_map(function ($item) {
    return (int)$item['count'];
}, $categoryStats);

$topSoldLabels = !empty($topSold) ? array_map(function ($item) {
    return $item['name'];
}, $topSold) : ['Sin ventas'];
$topSoldValues = !empty($topSold) ? array_map(function ($item) {
    return (int)$item['sold'];
}, $topSold) : [0];

$stockLabels = !empty($lowStockTop) ? array_map(function ($item) {
    return $item['name'];
}, $lowStockTop) : ['Sin stock registrado'];
$stockValues = !empty($lowStockTop) ? array_map(function ($item) {
    return (int)$item['stock'];
}, $lowStockTop) : [0];

$statusLabels = ['Disponibles', 'Agotados', 'Con descuento'];
$statusValues = [$availableProducts, $soldOutProducts, $productsWithDiscount];

$chartPayload = [
    'categoryLabels' => !empty($categoryLabels) ? $categoryLabels : ['Sin categorías'],
    'categoryValues' => !empty($categoryValues) ? $categoryValues : [0],
    'topSoldLabels' => $topSoldLabels,
    'topSoldValues' => $topSoldValues,
    'stockLabels' => $stockLabels,
    'stockValues' => $stockValues,
    'statusLabels' => $statusLabels,
    'statusValues' => $statusValues,
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Ejecutivo | Le Grand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background:
                radial-gradient(circle at 0% 0%, rgba(95, 216, 173, .18), transparent 32rem),
                radial-gradient(circle at 100% 0%, rgba(217, 184, 115, .18), transparent 34rem),
                linear-gradient(180deg, #fbfaf7 0%, #f5f2eb 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, .82);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }
        .soft-grid {
            background-image:
                linear-gradient(rgba(255,255,255,.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.08) 1px, transparent 1px);
            background-size: 26px 26px;
        }
        .chart-box {
            position: relative;
            height: 330px;
        }
        @media (max-width: 640px) {
            .chart-box { height: 280px; }
        }
    </style>
</head>

<body class="min-h-screen text-neutral-950 antialiased">

<div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">

<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div class="min-w-0">
        <header class="sticky top-0 z-40 border-b border-black/5 bg-white/85 backdrop-blur-xl">
            <div class="flex min-h-[76px] items-center justify-between px-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.22em] text-[#2D9B6B]">Le Grand</p>
                    <h1 class="text-xl font-black tracking-[-.03em] sm:text-2xl">Dashboard ejecutivo</h1>
                </div>

                <div class="flex items-center gap-2">
                    <a href="productos.php" class="hidden rounded-full bg-neutral-100 px-5 py-3 text-sm font-black hover:bg-black hover:text-white sm:inline-flex">
                        Productos
                    </a>
                    <a href="../index.php" target="_blank" class="hidden rounded-full bg-neutral-100 px-5 py-3 text-sm font-black hover:bg-black hover:text-white md:inline-flex">
                        Ver tienda
                    </a>
                    <a href="logout.php" class="rounded-full bg-black px-5 py-3 text-sm font-black text-white hover:bg-[#5FD8AD] hover:text-black">
                        Salir
                    </a>
                </div>
            </div>
        </header>

        <main class="px-4 py-7 sm:px-6 lg:px-8">

            <section class="relative overflow-hidden rounded-[2.2rem] bg-black p-6 text-white shadow-[0_35px_110px_rgba(0,0,0,.22)] sm:p-8 soft-grid">
                <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-[#5FD8AD]/25 blur-3xl"></div>
                <div class="absolute -bottom-24 left-1/2 h-72 w-72 rounded-full bg-[#D9B873]/20 blur-3xl"></div>

                <div class="relative grid gap-8 xl:grid-cols-[1.2fr_.8fr] xl:items-end">
                    <div>
                        <div class="inline-flex rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[.22em] text-[#5FD8AD] backdrop-blur-xl">
                            Resumen comercial · <?= e($todayLabel); ?>
                        </div>
                        <h2 class="mt-5 max-w-3xl text-4xl font-black tracking-[-.07em] sm:text-5xl lg:text-6xl">
                            Control premium de ventas, stock y catálogo.
                        </h2>
                        <p class="mt-4 max-w-2xl text-sm font-medium leading-7 text-white/55 sm:text-base">
                            Vista ejecutiva para detectar productos líderes, falta de stock, margen del día, rotación comercial y calidad del catálogo.
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                        <div class="rounded-[1.7rem] border border-white/10 bg-white/10 p-5 backdrop-blur-xl">
                            <p class="text-xs font-black uppercase tracking-[.18em] text-white/45">Ganancia hoy</p>
                            <p class="mt-2 text-4xl font-black tracking-[-.05em] text-[#5FD8AD]">
                                <?= e(money_format_pe($todayProfit)); ?>
                            </p>
                            <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold text-white/55">
                                <span class="rounded-full bg-white/10 px-3 py-1">Margen <?= e($profitMarginToday); ?>%</span>
                                <span class="rounded-full bg-white/10 px-3 py-1"><?= e(int_format_pe($todayOrders)); ?> pedidos</span>
                            </div>
                        </div>

                        <div class="rounded-[1.7rem] border border-white/10 bg-white/10 p-5 backdrop-blur-xl">
                            <p class="text-xs font-black uppercase tracking-[.18em] text-white/45">Categoría líder</p>
                            <p class="mt-2 text-2xl font-black tracking-[-.04em]"><?= e($topCategory['title']); ?></p>
                            <p class="mt-1 text-sm font-bold text-white/45"><?= e(int_format_pe($topCategory['count'])); ?> productos registrados</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="glass-card rounded-[1.7rem] border border-white p-5 shadow-[0_20px_70px_rgba(0,0,0,.07)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-neutral-500">Venta de hoy</p>
                            <p class="mt-3 text-3xl font-black tracking-[-.04em]"><?= e(money_format_pe($todayRevenue)); ?></p>
                        </div>
                        <div class="grid h-11 w-11 place-items-center rounded-2xl bg-emerald-50 text-emerald-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m7 14 4-4 4 4 5-6"/></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs font-bold text-neutral-400">Ticket promedio: <?= e(money_format_pe($avgTicket)); ?></p>
                </article>

                <article class="glass-card rounded-[1.7rem] border border-white p-5 shadow-[0_20px_70px_rgba(0,0,0,.07)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-neutral-500">Cantidad de productos</p>
                            <p class="mt-3 text-3xl font-black tracking-[-.04em]"><?= e(int_format_pe($totalProducts)); ?></p>
                        </div>
                        <div class="grid h-11 w-11 place-items-center rounded-2xl bg-neutral-100 text-neutral-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs font-bold text-neutral-400"><?= e(int_format_pe($availableProducts)); ?> disponibles · <?= e(int_format_pe($soldOutProducts)); ?> agotados</p>
                </article>

                <article class="glass-card rounded-[1.7rem] border border-white p-5 shadow-[0_20px_70px_rgba(0,0,0,.07)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-neutral-500">Falta stock</p>
                            <p class="mt-3 text-3xl font-black tracking-[-.04em] text-red-600"><?= e(int_format_pe($lowStockProducts)); ?></p>
                        </div>
                        <div class="grid h-11 w-11 place-items-center rounded-2xl bg-red-50 text-red-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs font-bold text-neutral-400">Umbral usado: 5 unidades o menos</p>
                </article>

                <article class="glass-card rounded-[1.7rem] border border-white p-5 shadow-[0_20px_70px_rgba(0,0,0,.07)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-neutral-500">Valor inventario</p>
                            <p class="mt-3 text-3xl font-black tracking-[-.04em]"><?= e(money_format_pe($inventoryValue)); ?></p>
                        </div>
                        <div class="grid h-11 w-11 place-items-center rounded-2xl bg-amber-50 text-amber-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs font-bold text-neutral-400"><?= e(int_format_pe($totalStock)); ?> unidades contabilizadas</p>
                </article>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[1fr_1.15fr]">
                <article class="glass-card rounded-[2rem] border border-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-black tracking-[-.04em]">Composición del catálogo</h3>
                            <p class="mt-1 text-sm font-medium text-neutral-500">Pie por categorías registradas.</p>
                        </div>
                        <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-black text-neutral-600"><?= e(int_format_pe($totalCategories)); ?> categorías</span>
                    </div>
                    <div class="chart-box mt-6">
                        <canvas id="categoryPie"></canvas>
                    </div>
                </article>

                <article class="glass-card rounded-[2rem] border border-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-black tracking-[-.04em]">Productos más vendidos · Top 5</h3>
                            <p class="mt-1 text-sm font-medium text-neutral-500">Ranking por unidades vendidas.</p>
                        </div>
                        <a href="productos.php" class="rounded-full bg-black px-4 py-2 text-xs font-black text-white hover:bg-[#5FD8AD] hover:text-black">
                            Gestionar
                        </a>
                    </div>
                    <div class="chart-box mt-6">
                        <canvas id="topSoldChart"></canvas>
                    </div>
                </article>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
                <article class="glass-card rounded-[2rem] border border-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-black tracking-[-.04em]">Falta stock · Top 5</h3>
                            <p class="mt-1 text-sm font-medium text-neutral-500">Productos con menor inventario para reposición rápida.</p>
                        </div>
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-black text-red-700">Alerta</span>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-3xl border border-black/5 bg-white">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-[#f7f6f2] text-xs uppercase tracking-[.16em] text-neutral-500">
                                <tr>
                                    <th class="px-5 py-4">Producto</th>
                                    <th class="px-5 py-4">Categoría</th>
                                    <th class="px-5 py-4 text-right">Stock</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5">
                                <?php foreach ($lowStockTop as $item): ?>
                                    <tr class="hover:bg-neutral-50/70">
                                        <td class="px-5 py-4 font-black"><?= e($item['name']); ?></td>
                                        <td class="px-5 py-4 text-neutral-500"><?= e($item['category']); ?></td>
                                        <td class="px-5 py-4 text-right">
                                            <span class="rounded-full <?= $item['stock'] === 0 ? 'bg-red-100 text-red-700' : 'bg-amber-50 text-amber-700'; ?> px-3 py-1 text-xs font-black">
                                                <?= e(int_format_pe($item['stock'])); ?> und.
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($lowStockTop)): ?>
                                    <tr>
                                        <td colspan="3" class="px-5 py-10 text-center">
                                            <p class="font-black text-neutral-700">Sin stock registrado todavía</p>
                                            <p class="mt-1 text-sm font-medium text-neutral-400">Agrega el campo <b>stock</b> a tus productos para activar este ranking.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="chart-box mt-6 h-[260px]">
                        <canvas id="stockChart"></canvas>
                    </div>
                </article>

                <article class="glass-card rounded-[2rem] border border-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                    <h3 class="text-2xl font-black tracking-[-.04em]">Salud del negocio</h3>
                    <p class="mt-1 text-sm font-medium text-neutral-500">Lectura rápida para decisión gerencial.</p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-3xl bg-[#f7f6f2] p-5">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-bold text-neutral-500">Cobertura de imágenes</p>
                                    <p class="mt-1 text-2xl font-black"><?= e($coverageImage); ?>%</p>
                                </div>
                                <p class="text-xs font-black text-neutral-400"><?= e(int_format_pe($productsWithImage)); ?>/<?= e(int_format_pe($totalProducts)); ?></p>
                            </div>
                            <div class="mt-4 h-3 overflow-hidden rounded-full bg-white">
                                <div class="h-full rounded-full bg-[#5FD8AD]" style="width: <?= e($coverageImage); ?>%"></div>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-3xl bg-white p-5 ring-1 ring-black/5">
                                <p class="text-sm font-bold text-neutral-500">Disponibilidad</p>
                                <p class="mt-1 text-3xl font-black text-[#2D9B6B]"><?= e($availableRate); ?>%</p>
                            </div>
                            <div class="rounded-3xl bg-white p-5 ring-1 ring-black/5">
                                <p class="text-sm font-bold text-neutral-500">En descuento</p>
                                <p class="mt-1 text-3xl font-black"><?= e($discountRate); ?>%</p>
                            </div>
                        </div>

                        <div class="rounded-3xl bg-black p-5 text-white">
                            <p class="text-xs font-black uppercase tracking-[.18em] text-[#5FD8AD]">Recomendación</p>
                            <p class="mt-3 text-sm font-semibold leading-6 text-white/65">
                                <?php if ($lowStockProducts > 0): ?>
                                    Prioriza reposición: tienes <?= e(int_format_pe($lowStockProducts)); ?> productos con stock crítico.
                                <?php elseif ($soldOutRate >= 25): ?>
                                    Revisa abastecimiento: el porcentaje de agotados supera el nivel ideal.
                                <?php elseif ($coverageImage < 85): ?>
                                    Mejora la conversión subiendo imágenes a los productos incompletos.
                                <?php elseif ($todayOrders === 0): ?>
                                    Aún no hay ventas registradas hoy; revisa campañas, vitrina y productos destacados.
                                <?php else: ?>
                                    El negocio mantiene indicadores saludables para el cierre del día.
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="chart-box h-[230px]">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </article>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[1fr_1fr]">
                <article class="glass-card rounded-[2rem] border border-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-black tracking-[-.04em]">Ranking comercial</h3>
                            <p class="mt-1 text-sm font-medium text-neutral-500">Top 5 productos con mayor salida.</p>
                        </div>
                    </div>

                    <div class="mt-5 overflow-hidden rounded-3xl border border-black/5 bg-white">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-[#f7f6f2] text-xs uppercase tracking-[.16em] text-neutral-500">
                                <tr>
                                    <th class="px-5 py-4">Producto</th>
                                    <th class="px-5 py-4 text-right">Vendidos</th>
                                    <th class="px-5 py-4 text-right">Ganancia</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5">
                                <?php foreach ($topSold as $item): ?>
                                    <tr class="hover:bg-neutral-50/70">
                                        <td class="px-5 py-4">
                                            <p class="font-black"><?= e($item['name']); ?></p>
                                            <p class="text-xs font-bold text-neutral-400"><?= e($item['category']); ?></p>
                                        </td>
                                        <td class="px-5 py-4 text-right font-black"><?= e(int_format_pe($item['sold'])); ?></td>
                                        <td class="px-5 py-4 text-right font-black text-[#2D9B6B]"><?= e(money_format_pe($item['profit'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($topSold)): ?>
                                    <tr>
                                        <td colspan="3" class="px-5 py-10 text-center">
                                            <p class="font-black text-neutral-700">Sin ventas registradas</p>
                                            <p class="mt-1 text-sm font-medium text-neutral-400">Agrega <b>sold</b> en productos o registra <b>orders</b>/<b>sales</b> para activar el ranking.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="glass-card rounded-[2rem] border border-white p-6 shadow-[0_24px_80px_rgba(0,0,0,.08)]">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-black tracking-[-.04em]">Últimos productos</h3>
                            <p class="mt-1 text-sm font-medium text-neutral-500">Control rápido del catálogo reciente.</p>
                        </div>
                        <a href="productos.php" class="rounded-full bg-black px-4 py-2 text-xs font-black text-white hover:bg-[#5FD8AD] hover:text-black">
                            Ver todo
                        </a>
                    </div>

                    <div class="mt-5 overflow-hidden rounded-3xl border border-black/5 bg-white">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-[#f7f6f2] text-xs uppercase tracking-[.16em] text-neutral-500">
                                <tr>
                                    <th class="px-5 py-4">Producto</th>
                                    <th class="px-5 py-4">Categoría</th>
                                    <th class="px-5 py-4 text-right">Precio</th>
                                    <th class="px-5 py-4 text-right">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5">
                                <?php foreach ($latestProducts as $product): ?>
                                    <?php
                                        $stock = product_stock($product);
                                        $categorySlug = (string)($product['category_slug'] ?? $product['category'] ?? $product['categoria'] ?? '');
                                        $categoryName = $categoryMap[$categorySlug] ?? $categorySlug;
                                    ?>
                                    <tr class="hover:bg-neutral-50/70">
                                        <td class="px-5 py-4 font-black"><?= e(product_name($product)); ?></td>
                                        <td class="px-5 py-4 text-neutral-500"><?= e($categoryName ?: 'Sin categoría'); ?></td>
                                        <td class="px-5 py-4 text-right font-black"><?= e(money_format_pe(product_price($product))); ?></td>
                                        <td class="px-5 py-4 text-right">
                                            <?php if (is_sold_out($product)): ?>
                                                <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-black text-red-700">Agotado</span>
                                            <?php elseif ($stock !== null && $stock <= 5): ?>
                                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-700">Stock bajo</span>
                                            <?php else: ?>
                                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">Disponible</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($latestProducts)): ?>
                                    <tr>
                                        <td colspan="4" class="px-5 py-10 text-center font-bold text-neutral-400">
                                            No hay productos registrados.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>

        </main>
    </div>
</div>

<script>
const dashboardData = <?= json_encode($chartPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

const palette = [
    '#111111', '#2D9B6B', '#5FD8AD', '#D9B873', '#EF4444',
    '#64748B', '#A3E635', '#F59E0B', '#0EA5E9', '#A855F7'
];

Chart.defaults.font.family = "Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
Chart.defaults.color = '#525252';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.boxWidth = 8;
Chart.defaults.plugins.tooltip.backgroundColor = '#111111';
Chart.defaults.plugins.tooltip.padding = 12;
Chart.defaults.plugins.tooltip.cornerRadius = 14;

new Chart(document.getElementById('categoryPie'), {
    type: 'doughnut',
    data: {
        labels: dashboardData.categoryLabels,
        datasets: [{
            data: dashboardData.categoryValues,
            backgroundColor: palette,
            borderColor: '#ffffff',
            borderWidth: 4,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 18, font: { weight: 800 } }
            }
        }
    }
});

new Chart(document.getElementById('topSoldChart'), {
    type: 'bar',
    data: {
        labels: dashboardData.topSoldLabels,
        datasets: [{
            label: 'Unidades vendidas',
            data: dashboardData.topSoldValues,
            backgroundColor: '#111111',
            borderRadius: 14,
            maxBarThickness: 46
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: { grid: { display: false }, ticks: { font: { weight: 800 } } },
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.06)' }, ticks: { precision: 0 } }
        },
        plugins: {
            legend: { display: false }
        }
    }
});

new Chart(document.getElementById('stockChart'), {
    type: 'bar',
    data: {
        labels: dashboardData.stockLabels,
        datasets: [{
            label: 'Stock disponible',
            data: dashboardData.stockValues,
            backgroundColor: '#EF4444',
            borderRadius: 12,
            maxBarThickness: 34
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.06)' }, ticks: { precision: 0 } },
            y: { grid: { display: false }, ticks: { font: { weight: 800 } } }
        },
        plugins: {
            legend: { display: false }
        }
    }
});

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: dashboardData.statusLabels,
        datasets: [{
            data: dashboardData.statusValues,
            backgroundColor: ['#2D9B6B', '#EF4444', '#D9B873'],
            borderColor: '#ffffff',
            borderWidth: 4,
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 14, font: { weight: 800 } }
            }
        }
    }
});
</script>

</body>
</html>
