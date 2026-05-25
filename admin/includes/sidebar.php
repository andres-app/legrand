<?php
$currentPage = basename($_SERVER['PHP_SELF']);

function activeMenu($page, $currentPage)
{
    return $page === $currentPage
        ? 'bg-neutral-950 text-white shadow-xl shadow-black/20'
        : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-950';
}
?>

<aside class="hidden w-[292px] shrink-0 border-r border-black/5 bg-white/95 backdrop-blur-2xl lg:block">
    <div class="sticky top-0 flex h-screen flex-col">

        <div class="px-5 pt-5 pb-4">
            <div class="rounded-[28px] border border-black/5 bg-white p-5 shadow-sm">
                <img src="../media/cropped-logo-1.png" class="mx-auto w-48" alt="Le Grand">

                <div class="mt-5 flex items-center justify-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#2D9B6B] shadow-[0_0_0_5px_rgba(45,155,107,.12)]"></span>
                    <p class="text-[11px] font-black uppercase tracking-[.22em] text-[#2D9B6B]">
                        Panel administrativo
                    </p>
                </div>
            </div>
        </div>

        <nav class="flex-1 space-y-2 px-4 py-4 text-sm font-black">

            <a href="dashboard.php"
               class="group flex items-center gap-3 rounded-2xl px-4 py-3.5 transition-all duration-300 <?= activeMenu('dashboard.php', $currentPage); ?>">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-black/5">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 13h6V4H4v9Zm10 7h6V4h-6v16ZM4 20h6v-3H4v3Z"/>
                    </svg>
                </span>
                <span>Dashboard</span>
            </a>

            <a href="categorias.php"
               class="group flex items-center gap-3 rounded-2xl px-4 py-3.5 transition-all duration-300 <?= activeMenu('categorias.php', $currentPage); ?>">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-black/5">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10"/>
                    </svg>
                </span>
                <span>Categorías</span>
            </a>

            <a href="productos.php"
               class="group flex items-center gap-3 rounded-2xl px-4 py-3.5 transition-all duration-300 <?= activeMenu('productos.php', $currentPage); ?>">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-black/5">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.5 12 3 3 8.5l9 5.5 9-5.5Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.5V16l9 5 9-5V8.5M12 14v7"/>
                    </svg>
                </span>
                <span>Productos</span>
            </a>

            <div class="my-4 border-t border-black/5"></div>

            <a href="../index.php" target="_blank"
               class="group flex items-center gap-3 rounded-2xl px-4 py-3.5 text-neutral-600 transition-all duration-300 hover:bg-emerald-50 hover:text-[#2D9B6B]">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-[#2D9B6B]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h16l-1-5H5l-1 5Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10v9h14v-9M9 19v-5h6v5"/>
                    </svg>
                </span>
                <span>Ver tienda</span>
                <svg class="ml-auto h-4 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M9 7h8v8"/>
                </svg>
            </a>
        </nav>

        <div class="p-4">
            <div class="rounded-[26px] border border-black/5 bg-white p-4 shadow-sm">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-neutral-950 text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 5 6v5c0 4.5 3 8 7 10 4-2 7-5.5 7-10V6l-7-3Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 12.5 11 14l3.5-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-neutral-950">Administrador</p>
                        <p class="text-xs font-bold text-neutral-500">Gestión interna</p>
                    </div>
                </div>

                <a href="logout.php"
                   class="group flex items-center justify-center gap-2 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-black text-red-700 transition-all duration-300 hover:border-red-600 hover:bg-red-600 hover:text-white">
                    <svg class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3m0 0 4-4m-4 4 4 4M9 4h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H9"/>
                    </svg>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </div>

    </div>
</aside>