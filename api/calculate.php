<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num1 = filter_input(INPUT_POST, 'num1', FILTER_VALIDATE_FLOAT);
    $num2 = filter_input(INPUT_POST, 'num2', FILTER_VALIDATE_FLOAT);
    $operator = $_POST['operator'] ?? 'add';

    if ($num1 === false || $num2 === false) {
        die("Invalid numbers provided. <a href='/'>Go back</a>");
    }

    $result = 0;
    $symbol = '';

    switch ($operator) {
        case 'add':
            $result = $num1 + $num2;
            $symbol = '+';
            break;
        case 'sub':
            $result = $num1 - $num2;
            $symbol = '-';
            break;
        case 'mul':
            $result = $num1 * $num2;
            $symbol = '×';
            break;
        case 'div':
            if ($num2 == 0) {
                die("Division by zero error. <a href='/'>Go back</a>");
            }
            $result = $num1 / $num2;
            $symbol = '÷';
            break;
        default:
            die("Invalid operator. <a href='/'>Go back</a>");
    }

    $expression = "$num1 $symbol $num2 = $result";

    // Store in session (Note: serverless environments handle sessions via stateless cookies)
    if (!isset($_SESSION['calc_history'])) {
        $_SESSION['calc_history'] = [];
    }
    array_unshift($_SESSION['calc_history'], $expression);
    if (count($_SESSION['calc_history']) > 5) {
        array_pop($_SESSION['calc_history']); // Keep last 5
    }

    header("Location: /");
    exit;
} else {
    header("Location: /");
    exit;
}
