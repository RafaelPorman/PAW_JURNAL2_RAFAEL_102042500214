<?php
require __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/actions/process.php';
}

require __DIR__ . '/includes/prepare_page.php';
require __DIR__ . '/views/layout.php';
