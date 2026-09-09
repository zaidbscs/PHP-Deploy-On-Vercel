<?php
echo "<h1>Task Manager</h1>";
// Example of handling a task or form data
$taskName = $_GET['name'] ?? 'Default Task';
echo "<p>Processing task: <strong>" . htmlspecialchars($taskName) . "</strong></p>";
echo '<a href="/">Back to Home</a>';
?>
