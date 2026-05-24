<?php
session_start();

if (!empty($_SESSION['admin_logged'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';

    if ($user === 'admin' && $pass === 'admin') {
        $_SESSION['admin_logged'] = true;
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Usuario o clave incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin | Le Grand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#f7f6f2] grid place-items-center px-4">

<form method="POST" class="w-full max-w-md rounded-[2rem] bg-white p-8 shadow-[0_30px_100px_rgba(0,0,0,.12)]">
    <img src="../media/cropped-logo-1.png" class="mx-auto mb-8 w-48" alt="Le Grand">

    <h1 class="text-center text-3xl font-black">Panel Admin</h1>
    <p class="mt-2 text-center text-sm text-neutral-500">Gestión de tienda en JSON</p>

    <?php if ($error): ?>
        <div class="mt-5 rounded-2xl bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <div class="mt-6">
        <label class="text-sm font-bold">Usuario</label>
        <input name="user" value="admin" class="mt-2 w-full rounded-2xl border px-4 py-3 outline-none focus:ring-4 focus:ring-emerald-100">
    </div>

    <div class="mt-4">
        <label class="text-sm font-bold">Clave</label>
        <input name="pass" value="admin" type="password" class="mt-2 w-full rounded-2xl border px-4 py-3 outline-none focus:ring-4 focus:ring-emerald-100">
    </div>

    <button class="mt-6 w-full rounded-2xl bg-black px-5 py-4 font-black text-white transition hover:bg-[#5FD8AD] hover:text-black">
        Ingresar
    </button>
</form>

</body>
</html>