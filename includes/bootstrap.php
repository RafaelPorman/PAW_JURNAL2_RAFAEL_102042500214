<?php
require __DIR__ . '/../functions.php';
require __DIR__ . '/../koneksi.php';

$entities = entities();
$page = input($_GET, 'halaman');
$page = $page === '' ? 'beranda' : $page;
$entity = input($_GET, 'entitas');
$idRaw = input($_GET, 'id');
$id = valid_id($idRaw);
$errors = [];
$formData = [];
$flash = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);
