<?php
$slides = [
    [
        'img' => './media/banner-fossil.jpg',
        'alt' => 'Banner Fossil'
    ],
    [
        'img' => './media/bnr-3.jpg',
        'alt' => 'Banner reloj destacado'
    ],
    [
        'img' => './media/bnr-1.jpg',
        'alt' => 'Banner colección de relojes'
    ],
    [
        'img' => './media/bnr-2.jpg',
        'alt' => 'Banner relojes Le Grand'
    ],
];

$categories = [
    [
        'title' => 'Relojes de caballero',
        'subtitle' => 'Ver colección',
        'img' => './media/categoria-1.jpg',
        'alt' => 'Relojes de caballero',
        'href' => '#'
    ],
    [
        'title' => 'Relojes de dama',
        'subtitle' => 'Ver colección',
        'img' => './media/categoria-2.jpg',
        'alt' => 'Relojes de dama',
        'href' => '#'
    ],
    [
        'title' => 'Correas',
        'subtitle' => 'Ver colección',
        'img' => './media/categoria-3.jpg',
        'alt' => 'Correas',
        'href' => '#correas'
    ],
];

$products = [
    [
        'img' => './media/S241079_main-300x400.jpg',
        'alt' => 'Correa Fossil',
        'discount' => '-6%',
        'status' => '',
        'wish' => false,
        'meta' => 'Correas · Oferta',
        'name' => 'Correa Fossil',
        'old_price' => 'S/ 160.00',
        'price' => 'S/ 150.00',
        'action' => 'Añadir al carrito',
        'href' => '#'
    ],
    [
        'img' => './media/FS4682_main-300x400.jpg',
        'alt' => 'Reloj Fossil FS4682',
        'discount' => '-17%',
        'status' => 'Agotado',
        'wish' => false,
        'meta' => 'Caballero · Oferta · Relojes',
        'name' => 'Reloj Fossil FS4682',
        'old_price' => 'S/ 600.00',
        'price' => 'S/ 500.00',
        'action' => 'Leer más',
        'href' => '#'
    ],
    [
        'img' => './media/FS4735_main-300x400.jpg',
        'alt' => 'Reloj Fossil FS4735',
        'discount' => '-17%',
        'status' => '',
        'wish' => true,
        'meta' => 'Caballero · Oferta · Relojes',
        'name' => 'Reloj Fossil FS4735',
        'old_price' => 'S/ 600.00',
        'price' => 'S/ 500.00',
        'action' => 'Añadir al carrito',
        'href' => '#'
    ],
    [
        'img' => './media/FS4812_main-300x400.jpg',
        'alt' => 'Reloj Fossil FS4812',
        'discount' => '-17%',
        'status' => '',
        'wish' => false,
        'meta' => 'Caballero · Oferta · Relojes',
        'name' => 'Reloj Fossil FS4812',
        'old_price' => 'S/ 600.00',
        'price' => 'S/ 500.00',
        'action' => 'Añadir al carrito',
        'href' => '#'
    ],
    [
        'img' => './media/FS4813_main-300x400.jpg',
        'alt' => 'Reloj Fossil FS4813',
        'discount' => '-17%',
        'status' => '',
        'wish' => false,
        'meta' => 'Caballero · Oferta · Relojes',
        'name' => 'Reloj Fossil FS4813',
        'old_price' => 'S/ 600.00',
        'price' => 'S/ 500.00',
        'action' => 'Añadir al carrito',
        'href' => '#'
    ],
];

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Grand Montres & Bijoux</title>
    <meta name="description" content="Le Grand Montres & Bijoux - relojes, correas y accesorios seleccionados.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui']
                    },
                    colors: {
                        brand: {
                            mint: '#5FD8AD',
                            emerald: '#2D9B6B',
                            ink: '#050505',
                            soft: '#F7F6F2',
                            gold: '#D9B873'
                        }
                    },
                    boxShadow: {
                        soft: '0 20px 70px rgba(0,0,0,.10)',
                        deep: '0 35px 120px rgba(0,0,0,.28)',
                        mint: '0 20px 80px rgba(95,216,173,.24)'
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 5% -10%, rgba(95, 216, 173, .16), transparent 34rem),
                radial-gradient(circle at 95% 10%, rgba(217, 184, 115, .13), transparent 30rem),
                linear-gradient(180deg, #fbfaf7 0%, #ffffff 42%, #f6f5f1 100%);
            overflow-x: hidden;
        }

        ::selection {
            background: rgba(95, 216, 173, .34);
        }

        .glass {
            background: rgba(255, 255, 255, .78);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
        }

        .dark-glass {
            background: rgba(5, 5, 5, .58);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }

        .hero-slide {
            opacity: 0;
            transform: scale(1.08);
            transition: opacity 1s ease, transform 2.2s ease;
            pointer-events: none;
        }

        .hero-slide.is-active {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
        }

        .hero-img {
            transform: translate3d(0, var(--heroShift, 0px), 0) scale(var(--heroScale, 1.04));
            transition: transform .08s linear;
            will-change: transform;
        }

        .reveal {
            opacity: 0;
            transform: translateY(42px) scale(.96);
            filter: blur(10px);
            transition: opacity .85s ease, transform .85s cubic-bezier(.2, .85, .25, 1), filter .85s ease;
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
        }

        .float-card {
            transform: translate3d(0, var(--floatY, 0px), 0) rotateX(var(--rotateX, 0deg)) rotateY(var(--rotateY, 0deg));
            transform-style: preserve-3d;
            will-change: transform;
        }

        .shine::before {
            content: "";
            position: absolute;
            inset: -1px;
            background: linear-gradient(115deg, transparent 20%, rgba(255, 255, 255, .34) 48%, transparent 68%);
            transform: translateX(-130%);
            transition: transform .9s ease;
            z-index: 20;
            pointer-events: none;
        }

        .shine:hover::before {
            transform: translateX(130%);
        }

        .product-action {
            opacity: 0;
            transform: translateY(12px);
        }

        .product-card:hover .product-action {
            opacity: 1;
            transform: translateY(0);
        }

        .scroll-stage {
            perspective: 1200px;
        }

        .orb {
            animation: orbMove 8s ease-in-out infinite alternate;
        }

        .orb:nth-child(2) {
            animation-delay: -2.5s;
            animation-duration: 10s;
        }

        .orb:nth-child(3) {
            animation-delay: -4s;
            animation-duration: 11s;
        }

        @keyframes orbMove {
            from {
                transform: translate3d(-20px, 10px, 0) scale(1);
            }

            to {
                transform: translate3d(28px, -24px, 0) scale(1.14);
            }
        }

        .marquee-track {
            animation: marquee 28s linear infinite;
        }

        @keyframes marquee {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                scroll-behavior: auto !important;
                animation: none !important;
                transition: none !important;
            }

            .reveal {
                opacity: 1;
                transform: none;
                filter: none;
            }
        }
    </style>
</head>

<body class="min-h-screen text-neutral-950 antialiased">

    <div class="hidden bg-brand-ink text-white lg:block">
        <div class="mx-auto flex h-11 w-[min(1180px,92%)] items-center justify-between text-[13px]">
            <div class="flex items-center gap-5 text-white/76">
                <a href="#" class="font-bold transition hover:text-brand-mint" aria-label="Facebook">f</a>
                <a href="#" class="font-bold transition hover:text-brand-mint" aria-label="Instagram">◎</a>
                <a href="#" class="font-bold transition hover:text-brand-mint" aria-label="Correo">✉</a>
            </div>

            <a href="#wishlist" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 font-semibold text-white/78 transition hover:bg-white/10 hover:text-white">
                <span class="text-brand-mint">♡</span>
                Lista de deseos
                <span class="rounded-full bg-brand-mint px-2 py-0.5 text-[11px] font-black text-black">0</span>
            </a>
        </div>
    </div>

    <header class="sticky top-0 z-50 border-b border-black/5 bg-white/82 backdrop-blur-2xl">
        <div class="mx-auto flex min-h-[78px] w-[min(1180px,92%)] items-center justify-between gap-4">
            <a href="#inicio" class="flex items-center" aria-label="Le Grand Montres & Bijoux">
                <img src="./media/cropped-logo-1.png" alt="Le Grand Montres & Bijoux" class="h-auto w-[168px] sm:w-[196px]">
            </a>

            <nav class="hidden items-center gap-1 rounded-full border border-black/5 bg-neutral-50 p-1 shadow-sm lg:flex" aria-label="Navegación principal">
                <a href="#relojes" class="rounded-full px-5 py-2.5 text-sm font-bold text-neutral-600 transition hover:bg-white hover:text-black hover:shadow-sm">Relojes</a>
                <a href="#correas" class="rounded-full px-5 py-2.5 text-sm font-bold text-neutral-600 transition hover:bg-white hover:text-black hover:shadow-sm">Correas</a>
                <a href="#ofertas" class="rounded-full px-5 py-2.5 text-sm font-bold text-neutral-600 transition hover:bg-white hover:text-black hover:shadow-sm">Ofertas</a>
                <a href="#tienda" class="rounded-full px-5 py-2.5 text-sm font-bold text-neutral-600 transition hover:bg-white hover:text-black hover:shadow-sm">Tienda</a>
            </nav>

            <div class="flex items-center gap-2">
                <a href="#buscar" class="hidden h-11 w-11 place-items-center rounded-full border border-black/5 bg-neutral-50 text-lg text-neutral-700 transition hover:bg-black hover:text-white sm:grid" aria-label="Buscar">
                    ⌕
                </a>

                <a href="#carrito" class="relative grid h-11 w-11 place-items-center rounded-full bg-black text-lg text-white shadow-soft transition hover:-translate-y-0.5 hover:shadow-mint" aria-label="Carrito">
                    🛒
                    <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-brand-mint px-1 text-[11px] font-black text-black">0</span>
                </a>

                <button id="menuToggle" type="button" class="grid h-11 w-11 place-items-center rounded-full border border-black/5 bg-neutral-50 text-xl text-black transition hover:bg-black hover:text-white lg:hidden" aria-label="Abrir menú" aria-expanded="false">
                    ☰
                </button>
            </div>
        </div>

        <div id="mobileMenu" class="hidden border-t border-black/5 bg-white/95 px-4 py-4 shadow-soft lg:hidden">
            <nav class="mx-auto grid w-[min(1180px,92%)] gap-2 text-sm font-bold text-neutral-700" aria-label="Navegación móvil">
                <a href="#relojes" class="rounded-2xl bg-neutral-50 px-4 py-3 transition hover:bg-black hover:text-white">Relojes</a>
                <a href="#correas" class="rounded-2xl bg-neutral-50 px-4 py-3 transition hover:bg-black hover:text-white">Correas</a>
                <a href="#ofertas" class="rounded-2xl bg-neutral-50 px-4 py-3 transition hover:bg-black hover:text-white">Ofertas</a>
                <a href="#tienda" class="rounded-2xl bg-neutral-50 px-4 py-3 transition hover:bg-black hover:text-white">Tienda</a>
                <a href="#buscar" class="rounded-2xl bg-neutral-50 px-4 py-3 transition hover:bg-black hover:text-white">Buscar</a>
            </nav>
        </div>
    </header>

    <main>
        <section id="inicio" class="relative overflow-hidden bg-black">
            <div class="relative h-[68vh] min-h-[460px] w-full overflow-hidden md:h-[78vh] md:min-h-[620px]">
                <?php foreach ($slides as $index => $slide): ?>
                    <article class="hero-slide absolute inset-0 <?= $index === 0 ? 'is-active' : ''; ?>" data-slide="<?= e($index); ?>">
                        <img src="<?= e($slide['img']); ?>" alt="<?= e($slide['alt']); ?>" class="hero-img h-[112%] w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-b from-black/12 via-transparent to-black/48"></div>
                        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-black/55 to-transparent"></div>
                    </article>
                <?php endforeach; ?>

                <div class="absolute bottom-6 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2 rounded-full border border-white/15 bg-black/30 px-3 py-2 backdrop-blur-xl">
                    <?php foreach ($slides as $index => $slide): ?>
                        <button type="button" class="slide-dot h-2.5 rounded-full transition-all <?= $index === 0 ? 'w-9 bg-brand-mint' : 'w-2.5 bg-white/45 hover:bg-white'; ?>" data-go="<?= e($index); ?>" aria-label="Ir al banner <?= e($index + 1); ?>"></button>
                    <?php endforeach; ?>
                </div>

                <button id="prevSlide" type="button" class="absolute left-4 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 place-items-center rounded-full border border-white/15 bg-black/25 text-3xl text-white backdrop-blur-xl transition hover:bg-white hover:text-black md:grid" aria-label="Anterior">
                    ‹
                </button>

                <button id="nextSlide" type="button" class="absolute right-4 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 place-items-center rounded-full border border-white/15 bg-black/25 text-3xl text-white backdrop-blur-xl transition hover:bg-white hover:text-black md:grid" aria-label="Siguiente">
                    ›
                </button>
            </div>
        </section>

        <section class="relative overflow-hidden bg-white py-4">
            <div class="marquee-track flex w-max gap-3 whitespace-nowrap">
                <?php for ($i = 0; $i < 2; $i++): ?>
                    <div class="flex items-center gap-3 px-3 text-xs font-black uppercase tracking-[.24em] text-neutral-400">
                        <span>Le Grand</span><span class="h-1.5 w-1.5 rounded-full bg-brand-mint"></span>
                        <span>Montres & Bijoux</span><span class="h-1.5 w-1.5 rounded-full bg-brand-mint"></span>
                        <span>Relojes</span><span class="h-1.5 w-1.5 rounded-full bg-brand-mint"></span>
                        <span>Correas</span><span class="h-1.5 w-1.5 rounded-full bg-brand-mint"></span>
                        <span>Ofertas</span><span class="h-1.5 w-1.5 rounded-full bg-brand-mint"></span>
                        <span>Accesorios</span><span class="h-1.5 w-1.5 rounded-full bg-brand-mint"></span>
                    </div>
                <?php endfor; ?>
            </div>
        </section>

        <section id="relojes" class="relative overflow-hidden bg-white px-4 py-20 sm:py-24">
            <div class="pointer-events-none absolute -left-32 top-24 h-80 w-80 rounded-full bg-brand-mint/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-32 bottom-10 h-80 w-80 rounded-full bg-brand-gold/10 blur-3xl"></div>

            <div class="mx-auto w-[min(1180px,100%)]">
                <div class="reveal mx-auto max-w-3xl text-center">
                    <span class="inline-flex rounded-full bg-brand-mint/14 px-4 py-2 text-xs font-black uppercase tracking-[.22em] text-brand-emerald">
                        Nuestros productos
                    </span>
                    <h2 class="mt-5 text-4xl font-black tracking-[-.055em] text-neutral-950 sm:text-5xl">
                        Colecciones destacadas
                    </h2>
                </div>

                <div class="scroll-stage mt-12 grid gap-5 md:grid-cols-3">
                    <?php foreach ($categories as $index => $category): ?>
                        <a href="<?= e($category['href']); ?>" id="<?= $index === 2 ? 'correas' : ''; ?>" class="tilt-card reveal float-card shine group relative min-h-[430px] overflow-hidden rounded-[2rem] bg-black shadow-soft transition duration-500 hover:shadow-deep sm:min-h-[520px]" data-float="<?= e(($index + 1) * 10); ?>">
                            <img src="<?= e($category['img']); ?>" alt="<?= e($category['alt']); ?>" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/86 via-black/24 to-black/4"></div>

                            <div class="absolute inset-x-5 top-5 z-10 flex justify-between">
                                <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-black uppercase tracking-[.16em] text-white/85 backdrop-blur-xl">
                                    Le Grand
                                </span>
                                <span class="grid h-10 w-10 place-items-center rounded-full bg-white text-black transition group-hover:rotate-45">
                                    ↗
                                </span>
                            </div>

                            <div class="absolute bottom-0 left-0 right-0 z-10 p-6 sm:p-8">
                                <h3 class="max-w-[14rem] text-3xl font-black uppercase leading-none tracking-[-.045em] text-white">
                                    <?= e($category['title']); ?>
                                </h3>
                                <div class="mt-5 inline-flex items-center gap-2 text-sm font-black uppercase tracking-[.16em] text-brand-mint">
                                    <?= e($category['subtitle']); ?>
                                    <span class="transition group-hover:translate-x-1">→</span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="ofertas" class="relative overflow-hidden bg-brand-ink px-4 py-20 text-white sm:py-24">
            <div class="orb pointer-events-none absolute left-[8%] top-[8%] h-72 w-72 rounded-full bg-brand-mint/16 blur-3xl"></div>
            <div class="orb pointer-events-none absolute right-[4%] top-[28%] h-80 w-80 rounded-full bg-brand-gold/12 blur-3xl"></div>
            <div class="orb pointer-events-none absolute bottom-[4%] left-[36%] h-72 w-72 rounded-full bg-white/7 blur-3xl"></div>

            <div class="relative mx-auto w-[min(1380px,100%)]">
                <div class="reveal mx-auto max-w-3xl text-center">
                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-black uppercase tracking-[.22em] text-brand-mint">
                        Últimas ofertas
                    </span>
                    <h2 class="mt-5 text-4xl font-black tracking-[-.055em] sm:text-5xl">
                        Rebajas disponibles
                    </h2>
                    <p class="mx-auto mt-4 max-w-2xl text-base font-medium leading-7 text-white/52">
                        Aprovecha ahora las rebajas, acaban pronto.
                    </p>
                </div>

                <div id="tienda" class="scroll-stage mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                    <?php foreach ($products as $index => $product): ?>
                        <article class="product-card reveal float-card group rounded-[1.7rem] border border-white/10 bg-white/[.045] p-3 shadow-2xl shadow-black/20 transition duration-500 hover:border-brand-mint/30 hover:bg-white/[.075] hover:shadow-mint" data-float="<?= e(($index % 3 + 1) * 8); ?>">
                            <div class="relative grid aspect-[4/5] place-items-center overflow-hidden rounded-[1.35rem] bg-[#f6f5f1]">
                                <?php if (!empty($product['status'])): ?>
                                    <span class="absolute left-3 top-3 z-10 rounded-full bg-black px-3 py-1.5 text-[11px] font-black uppercase tracking-[.14em] text-white">
                                        <?= e($product['status']); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($product['discount'])): ?>
                                    <span class="absolute right-3 top-3 z-10 rounded-full bg-brand-mint px-3 py-1.5 text-[11px] font-black text-black shadow-mint">
                                        <?= e($product['discount']); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($product['wish'])): ?>
                                    <button type="button" class="absolute bottom-3 right-3 z-10 inline-flex items-center gap-1 rounded-full bg-white/90 px-3 py-2 text-xs font-bold text-neutral-700 shadow-sm backdrop-blur-xl transition hover:bg-black hover:text-white">
                                        ♡ Lo deseo
                                    </button>
                                <?php endif; ?>

                                <img src="<?= e($product['img']); ?>" alt="<?= e($product['alt']); ?>" class="h-full w-full object-contain p-5 transition duration-700 group-hover:scale-105">
                            </div>

                            <div class="px-2 pb-3 pt-5 text-center">
                                <p class="text-xs font-bold uppercase tracking-[.18em] text-brand-mint/90">
                                    <?= e($product['meta']); ?>
                                </p>

                                <h3 class="mt-3 min-h-[3rem] text-base font-black uppercase leading-6 tracking-[-.015em] text-white">
                                    <?= e($product['name']); ?>
                                </h3>

                                <div class="mt-3 flex items-center justify-center gap-2 text-sm">
                                    <del class="text-white/36"><?= e($product['old_price']); ?></del>
                                    <span class="text-base font-black text-white"><?= e($product['price']); ?></span>
                                </div>

                                <a href="<?= e($product['href']); ?>" class="product-action mt-5 inline-flex items-center justify-center rounded-full bg-brand-mint px-5 py-3 text-xs font-black uppercase tracking-[.13em] text-black shadow-mint transition hover:bg-white lg:opacity-0">
                                    <?= e($product['action']); ?>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="relative overflow-hidden bg-black px-4 py-20">
            <div class="absolute inset-0 opacity-40">
                <div class="absolute left-0 top-0 h-96 w-96 rounded-full bg-brand-mint/20 blur-3xl"></div>
                <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-brand-gold/14 blur-3xl"></div>
            </div>

            <div class="relative mx-auto grid w-[min(1180px,100%)] items-center gap-8 lg:grid-cols-[.82fr_1.18fr]">
                <div class="reveal">
                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-black uppercase tracking-[.22em] text-brand-mint">
                        Le Grand
                    </span>
                    <h2 class="mt-5 max-w-xl text-4xl font-black leading-none tracking-[-.055em] text-white sm:text-5xl">
                        Montres & Bijoux
                    </h2>

                    <div class="mt-8 grid gap-3">
                        <div class="rounded-3xl border border-white/10 bg-white/[.045] p-5">
                            <p class="text-sm font-bold uppercase tracking-[.18em] text-white/42">Categorías</p>
                            <p class="mt-2 text-2xl font-black text-white">Relojes · Correas · Bijoux</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-white/[.045] p-5">
                            <p class="text-sm font-bold uppercase tracking-[.18em] text-white/42">Atención</p>
                            <p class="mt-2 text-2xl font-black text-white">Selección personalizada</p>
                        </div>
                    </div>
                </div>

                <div class="reveal float-card group relative min-h-[360px] overflow-hidden rounded-[2.4rem] border border-white/10 shadow-deep sm:min-h-[560px]" data-float="18">
                    <div class="absolute inset-0 bg-[url('./media/bnr-3.jpg')] bg-cover bg-center transition duration-700 group-hover:scale-105"></div>
                    <div class="absolute inset-0 bg-gradient-to-tr from-black/76 via-black/22 to-transparent"></div>

                    <div class="absolute left-6 top-6 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[.2em] text-white/80 backdrop-blur-xl">
                        Colección
                    </div>

                    <button type="button" class="absolute left-1/2 top-1/2 grid h-20 w-20 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/15 text-2xl text-white backdrop-blur-xl transition hover:scale-105 hover:bg-white hover:text-black" aria-label="Reproducir video">
                        ▶
                    </button>

                    <div class="absolute bottom-6 left-6 right-6 rounded-[1.7rem] border border-white/12 bg-black/32 p-5 backdrop-blur-2xl">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[.2em] text-brand-mint">Le Grand</p>
                                <p class="mt-1 text-xl font-black text-white">Relojes y accesorios</p>
                            </div>
                            <a href="#ofertas" class="inline-flex items-center justify-center rounded-full bg-brand-mint px-5 py-3 text-xs font-black uppercase tracking-[.14em] text-black transition hover:bg-white">
                                Ver ofertas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-[#191919] text-white">
        <div class="mx-auto grid w-[min(1180px,92%)] gap-10 py-16 md:grid-cols-3">
            <div class="reveal">
                <h3 class="flex items-center gap-3 text-sm font-black uppercase tracking-[.18em]">
                    <span class="h-8 w-1 rounded-full bg-brand-mint"></span>
                    Sobre nosotros
                </h3>

                <div class="mt-7 grid gap-3 text-sm font-semibold text-white/62">
                    <a href="#" class="rounded-2xl border border-white/8 bg-white/[.035] px-4 py-3 transition hover:border-brand-mint/30 hover:bg-white/[.07] hover:text-white">Quienes Somos</a>
                    <a href="#" class="rounded-2xl border border-white/8 bg-white/[.035] px-4 py-3 transition hover:border-brand-mint/30 hover:bg-white/[.07] hover:text-white">Contacto</a>
                    <a href="#" class="rounded-2xl border border-white/8 bg-white/[.035] px-4 py-3 transition hover:border-brand-mint/30 hover:bg-white/[.07] hover:text-white">Preguntas Frecuentes</a>
                </div>
            </div>

            <div class="reveal" id="buscar">
                <h3 class="flex items-center gap-3 text-sm font-black uppercase tracking-[.18em]">
                    <span class="h-8 w-1 rounded-full bg-brand-mint"></span>
                    Recibir ofertas
                </h3>

                <form action="#" method="post" class="mt-7 rounded-[1.7rem] border border-white/8 bg-white/[.035] p-4">
                    <label for="email" class="mb-2 block text-sm font-semibold text-white/64">
                        Correo electrónico <span class="text-brand-mint">*</span>
                    </label>
                    <input id="email" name="email" type="email" placeholder="Escribe acá tu correo" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-4 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-brand-mint/60 focus:ring-4 focus:ring-brand-mint/10">
                    <button type="submit" class="mt-4 w-full rounded-2xl bg-brand-mint px-5 py-4 text-sm font-black uppercase tracking-[.14em] text-black transition hover:bg-white">
                        Enviar
                    </button>
                </form>
            </div>

            <div class="reveal">
                <h3 class="flex items-center gap-3 text-sm font-black uppercase tracking-[.18em]">
                    <span class="h-8 w-1 rounded-full bg-brand-mint"></span>
                    Síguenos en Facebook
                </h3>

                <div class="mt-7 rounded-[1.7rem] border border-white/8 bg-white/[.035] p-4">
                    <div class="flex items-center gap-4">
                        <div class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-white p-2">
                            <img src="./media/cropped-logo-1.png" alt="Le Grand Facebook" class="h-full w-full object-contain">
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-lg font-black">Le Grand Montres & Bijoux</p>
                            <p class="mt-1 text-sm text-white/48">Novedades y colecciones.</p>
                        </div>
                    </div>

                    <a href="#" class="mt-5 inline-flex w-full items-center justify-center rounded-2xl border border-white/10 bg-white/8 px-5 py-3 text-sm font-black transition hover:bg-white hover:text-black">
                        f Seguir página
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-white/8 bg-black/22">
            <div class="mx-auto flex w-[min(1180px,92%)] flex-col items-center justify-between gap-4 py-6 text-center text-sm text-white/48 md:flex-row md:text-left">
                <p>Copyright <?= date('Y'); ?> - Powered by Appsauri Perú</p>

                <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-sm font-semibold text-white/70">
                    <a href="#" class="transition hover:text-brand-mint">Aviso Legal</a>
                    <span class="text-white/20">|</span>
                    <a href="#" class="transition hover:text-brand-mint">Políticas de Cookies</a>
                    <span class="text-white/20">|</span>
                    <a href="#" class="transition hover:text-brand-mint">Política de Privacidad</a>
                </div>
            </div>
        </div>
    </footer>

    <button id="toTop" type="button" class="fixed bottom-5 right-5 z-50 grid h-12 w-12 translate-y-5 place-items-center rounded-full bg-black text-xl text-white opacity-0 shadow-soft transition hover:-translate-y-0.5 hover:bg-brand-mint hover:text-black" aria-label="Volver arriba">
        ↑
    </button>

    <script>
        const slides = Array.from(document.querySelectorAll('.hero-slide'));
        const dots = Array.from(document.querySelectorAll('.slide-dot'));
        const heroImages = Array.from(document.querySelectorAll('.hero-img'));
        const prevSlide = document.getElementById('prevSlide');
        const nextSlide = document.getElementById('nextSlide');
        const menuToggle = document.getElementById('menuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const toTop = document.getElementById('toTop');
        const revealItems = Array.from(document.querySelectorAll('.reveal'));
        const floatCards = Array.from(document.querySelectorAll('.float-card'));
        const tiltCards = Array.from(document.querySelectorAll('.tilt-card'));

        let currentSlide = 0;
        let slideTimer = null;
        let ticking = false;

        function setSlide(index) {
            if (!slides.length) return;

            slides[currentSlide].classList.remove('is-active');
            dots[currentSlide]?.classList.remove('w-9', 'bg-brand-mint');
            dots[currentSlide]?.classList.add('w-2.5', 'bg-white/45');

            currentSlide = (index + slides.length) % slides.length;

            slides[currentSlide].classList.add('is-active');
            dots[currentSlide]?.classList.add('w-9', 'bg-brand-mint');
            dots[currentSlide]?.classList.remove('w-2.5', 'bg-white/45');
        }

        function startSlider() {
            stopSlider();
            slideTimer = window.setInterval(() => {
                setSlide(currentSlide + 1);
            }, 4800);
        }

        function stopSlider() {
            if (slideTimer) {
                window.clearInterval(slideTimer);
            }
        }

        prevSlide?.addEventListener('click', () => {
            setSlide(currentSlide - 1);
            startSlider();
        });

        nextSlide?.addEventListener('click', () => {
            setSlide(currentSlide + 1);
            startSlider();
        });

        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                setSlide(Number(dot.dataset.go || 0));
                startSlider();
            });
        });

        menuToggle?.addEventListener('click', () => {
            const isOpen = !mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            menuToggle.setAttribute('aria-expanded', String(!isOpen));
            menuToggle.textContent = isOpen ? '☰' : '×';
        });

        mobileMenu?.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.textContent = '☰';
            });
        });

        toTop?.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.16,
            rootMargin: '0px 0px -60px 0px'
        });

        revealItems.forEach((item, index) => {
            item.style.transitionDelay = `${Math.min(index * 45, 260)}ms`;
            revealObserver.observe(item);
        });

        function updateScrollEffects() {
            const y = window.scrollY;
            const vh = window.innerHeight;

            heroImages.forEach((img) => {
                const shift = Math.min(y * 0.22, 130);
                const scale = Math.max(1.02, 1.07 - y / 9000);
                img.style.setProperty('--heroShift', `${shift}px`);
                img.style.setProperty('--heroScale', scale.toFixed(3));
            });

            floatCards.forEach((card) => {
                const rect = card.getBoundingClientRect();
                const intensity = Number(card.dataset.float || 10);
                const center = rect.top + rect.height / 2;
                const distance = (center - vh / 2) / vh;
                const move = Math.max(Math.min(distance * -intensity, intensity), -intensity);
                card.style.setProperty('--floatY', `${move}px`);
            });

            const showTop = y > 420;
            toTop?.classList.toggle('opacity-0', !showTop);
            toTop?.classList.toggle('translate-y-5', !showTop);
            toTop?.classList.toggle('opacity-100', showTop);
            toTop?.classList.toggle('translate-y-0', showTop);

            ticking = false;
        }

        function requestScrollEffects() {
            if (!ticking) {
                window.requestAnimationFrame(updateScrollEffects);
                ticking = true;
            }
        }

        window.addEventListener('scroll', requestScrollEffects, {
            passive: true
        });

        window.addEventListener('resize', requestScrollEffects, {
            passive: true
        });

        tiltCards.forEach((card) => {
            card.addEventListener('mousemove', (event) => {
                const rect = card.getBoundingClientRect();
                const x = event.clientX - rect.left;
                const y = event.clientY - rect.top;
                const rotateY = ((x / rect.width) - .5) * 9;
                const rotateX = ((y / rect.height) - .5) * -9;

                card.style.setProperty('--rotateX', `${rotateX.toFixed(2)}deg`);
                card.style.setProperty('--rotateY', `${rotateY.toFixed(2)}deg`);
            });

            card.addEventListener('mouseleave', () => {
                card.style.setProperty('--rotateX', '0deg');
                card.style.setProperty('--rotateY', '0deg');
            });
        });

        updateScrollEffects();
        startSlider();
    </script>
</body>

</html>