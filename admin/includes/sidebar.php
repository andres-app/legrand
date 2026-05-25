<?php
$currentPage = basename($_SERVER['PHP_SELF']);

function activeMenu($page, $currentPage)
{
    return $page === $currentPage
        ? 'bg-black text-white'
        : 'text-neutral-600 hover:bg-neutral-100';
}
?>

<aside class="hidden border-r border-black/5 bg-white/90 backdrop-blur-xl lg:block">
    <div class="sticky top-0 flex h-screen flex-col">
        <div class="border-b border-black/5 px-7 py-6">
            <img src="../media/cropped-logo-1.png" class="w-48" alt="Le Grand">
            <p class="mt-3 text-xs font-black uppercase tracking-[.2em] text-[#2D9B6B]">
                Panel administrativo
            </p>
        </div>

        <nav class="flex-1 space-y-2 px-4 py-6 text-sm font-black">
            <a href="dashboard.php" class="block rounded-2xl px-4 py-3 <?= activeMenu('dashboard.php', $currentPage); ?>">
                Dashboard
            </a>

            <a href="categorias.php" class="block rounded-2xl px-4 py-3 <?= activeMenu('categorias.php', $currentPage); ?>">
                Categorías
            </a>

            <a href="productos.php" class="block rounded-2xl px-4 py-3 <?= activeMenu('productos.php', $currentPage); ?>">
                Productos
            </a>

            <a href="../index.php" target="_blank" class="block rounded-2xl px-4 py-3 text-neutral-600 hover:bg-neutral-100">
                Ver tienda
            </a>
        </nav>

        <div class="border-t border-black/5 p-4">
            <a href="logout.php" class="block rounded-2xl bg-red-50 px-5 py-3 text-center text-sm font-black text-red-700 hover:bg-red-600 hover:text-white">
                Cerrar sesión
            </a>
        </div>
    </div>
</aside>