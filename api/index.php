<?php
session_start();

// Handle Form Submission via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'calculate') {
        $num1 = filter_input(INPUT_POST, 'num1', FILTER_VALIDATE_FLOAT);
        $num2 = filter_input(INPUT_POST, 'num2', FILTER_VALIDATE_FLOAT);
        $operator = $_POST['operator'] ?? 'add';

        if ($num1 === false || $num2 === false) {
            $_SESSION['error'] = "Please enter valid numeric values.";
        } else {
            $result = null;
            $symbol = '';
            switch ($operator) {
                case 'add': $result = $num1 + $num2; $symbol = '+'; break;
                case 'sub': $result = $num1 - $num2; $symbol = '-'; break;
                case 'mul': $result = $num1 * $num2; $symbol = '×'; break;
                case 'div':
                    if ($num2 == 0) {
                        $_SESSION['error'] = "Division by zero is not allowed.";
                    } else {
                        $result = $num1 / $num2;
                        $symbol = '÷';
                    }
                    break;
                default:
                    $_SESSION['error'] = "Invalid mathematical operator.";
            }

            if ($result !== null) {
                $expression = "$num1 $symbol $num2 = $result";
                $_SESSION['last_result'] = $result;
                if (!isset($_SESSION['calc_history'])) {
                    $_SESSION['calc_history'] = [];
                }
                array_unshift($_SESSION['calc_history'], $expression);
                if (count($_SESSION['calc_history']) > 5) {
                    array_pop($_SESSION['calc_history']);
                }
            }
        }
    } elseif ($action === 'clear') {
        unset($_SESSION['calc_history'], $_SESSION['last_result'], $_SESSION['error']);
    }

    // Redirect to prevent form re-submission on reload (PRG Pattern)
    header("Location: /");
    exit;
}

// Retrieve flash messages / state for the GET request view
$error = $_SESSION['error'] ?? null;
$result = $_SESSION['last_result'] ?? null;
$history = $_SESSION['calc_history'] ?? [];

// Clear flash error/result after displaying them once
unset($_SESSION['error'], $_SESSION['last_result']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Single-File PHP App on Vercel</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; line-height: 1.6; color: #333; }
        .card { background: #f9f9f9; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 6px; }
        .error { color: #d9534f; background: #f2dede; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .success { color: #3c763d; background: #dff0d8; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        input, select { padding: 8px; margin-right: 5px; }
        button { padding: 8px 15px; background: #0070f3; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005bb5; }
    </style>
</head>
<body>
    <h1>Serverless Single-File PHP Engine</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($result !== null): ?>
        <div class="success">Result: <strong><?php echo htmlspecialchars($result); ?></strong></div>
    <?php endif; ?>

    <div class="card">
        <h3>Calculator Form</h3>
        <form method="POST" action="/">
            <input type="hidden" name="action" value="calculate">
            <input type="number" name="num1" placeholder="Num 1" required style="width: 30%;">
            <select name="operator">
                <option value="add">+</option>
                <option value="sub">-</option>
                <option value="mul">×</option>
                <option value="div">÷</option>
            </select>
            <input type="number" name="num2" placeholder="Num 2" required style="width: 30%;">
            <button type="submit">Calculate</button>
        </form>
    </div>

    <div class="card">
        <h3>Session Calculation History</h3>
        <?php if (empty($history)): ?>
            <p>No history items found.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($history as $item): ?>
                    <li><?php echo htmlspecialchars($item); ?></li>
                <?php endforeach; ?>
            </ul>
            <form method="POST" action="/">
                <input type="hidden" name="action" value="clear">
                <button type="submit" style="background: #d9534f;">Clear History</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
