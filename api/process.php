<?php
session_start();
$action = $_GET['action'] ?? '';

if ($action === 'clear') {
    unset($_SESSION['calc_history']);
}

header("Location: /");
exit;
