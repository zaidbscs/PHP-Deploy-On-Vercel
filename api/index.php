<?php
session_start();
$history = $_SESSION['calc_history'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced Vercel PHP App</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; line-height: 1.6; }
        .card { background: #f4f4f4; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        a { color: #0070f3; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Vercel PHP Advanced Engine</h1>
    <div class="card">
        <h3>Interactive Calculator Form</h3>
        <form action="/calculate" method="POST">
            <input type="number" name="num1" placeholder="First Number" required style="padding: 8px; width: 40%;">
            <select name="operator" style="padding: 8px;">
                <option value="add">+</option>
                <option value="sub">-</option>
                <option value="mul">×</option>
                <option value="div">÷</option>
            </select>
            <input type="number" name="num2" placeholder="Second Number" required style="padding: 8px; width: 40%;">
            <br><br>
            <button type="submit" style="padding: 8px 15px; background: #0070f3; color: white; border: none; border-radius: 3px; cursor: pointer;">Calculate</button>
        </form>
    </div>

    <div class="card">
        <h3>Session Calculation History</h3>
        <?php if (empty($history)): ?>
            <p>No calculations performed yet in this session.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($history as $item): ?>
                    <li><?php echo htmlspecialchars($item); ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="/process?action=clear">Clear History</a>
        <?php endif; ?>
    </div>
</body>
</html>
