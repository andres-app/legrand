<?php
function uploadImage($file, $prefix = 'img')
{
    if (empty($file['name'])) {
        return '';
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
    if (!in_array($file['type'], $allowed)) {
        return '';
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = $prefix . '-' . date('YmdHis') . '-' . rand(1000, 9999) . '.' . $ext;

    $targetDir = __DIR__ . '/../media/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $targetPath = $targetDir . $name;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return './media/' . $name;
    }

    return '';
}