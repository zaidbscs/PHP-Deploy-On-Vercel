<?php
session_start();

// Handle Form Actions via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_note') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
            $_SESSION['error'] = "Both title and content are required.";
        } else {
            if (!isset($_SESSION['notes'])) {
                $_SESSION['notes'] = [];
            }
            // Add new note to the beginning of the array
            array_unshift($_SESSION['notes'], [
                'id' => uniqid(),
                'title' => htmlspecialchars($title),
                'content' => htmlspecialchars($content),
                'date' => date('Y-m-d H:i:s')
            ]);
        }
    } elseif ($action === 'delete_note') {
        $idToDelete = $_POST['note_id'] ?? '';
        if (isset($_SESSION['notes'])) {
            $_SESSION['notes'] = array_filter($_SESSION['notes'], function($note) use ($idToDelete) {
                return $note['id'] !== $idToDelete;
            });
            // Re-index array
            $_SESSION['notes'] = array_values($_SESSION['notes']);
        }
    } elseif ($action === 'clear_all') {
        unset($_SESSION['notes'], $_SESSION['error']);
    }

    // PRG Pattern Redirect
    header("Location: /");
    exit;
}

$error = $_SESSION['error'] ?? null;
$notes = $_SESSION['notes'] ?? [];
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Serverless Notes App</title>
    <style>
        body { font-family: sans-serif; max-width: 650px; margin: 40px auto; padding: 20px; line-height: 1.6; color: #333; background: #fdfdfd; }
        .card { background: #fff; border: 1px solid #e1e4e8; padding: 20px; margin-bottom: 20px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .error { color: #d9534f; background: #f2dede; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        input, textarea { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 10px 18px; background: #2ea44f; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #2c974b; }
        .note-item { background: #f6f8fa; border: 1px solid #e1e4e8; padding: 15px; margin-bottom: 10px; border-radius: 4px; position: relative; }
        .note-item h4 { margin: 0 0 8px 0; color: #0366d6; }
        .note-item p { margin: 0 0 10px 0; }
        .note-item small { color: #586069; }
        .delete-btn { background: #d73a49; padding: 5px 10px; font-size: 12px; float: right; }
        .delete-btn:hover { background: #cb2431; }
    </style>
</head>
<body>
    <h1>Serverless PHP Notes</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Add Note Form -->
    <div class="card">
        <h3>Create a New Note</h3>
        <form method="POST" action="/">
            <input type="hidden" name="action" value="add_note">
            <input type="text" name="title" placeholder="Note Title..." required>
            <textarea name="content" placeholder="Write your note content here..." rows="3" required></textarea>
            <button type="submit">Save Note</button>
        </form>
    </div>

    <!-- Notes List -->
    <div class="card">
        <h3>Your Saved Notes (<?php echo count($notes); ?>)</h3>
        <?php if (empty($notes)): ?>
            <p>No notes created yet. Add one above!</p>
        <?php else: ?>
            <?php foreach ($notes as $note): ?>
                <div class="note-item">
                    <form method="POST" action="/" style="display:inline;">
                        <input type="hidden" name="action" value="delete_note">
                        <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                    <h4><?php echo $note['title']; ?></h4>
                    <p><?php echo nl2br($note['content']); ?></p>
                    <small>Created at: <?php echo $note['date']; ?></small>
                </div>
            <?php endforeach; ?>
            <br>
            <form method="POST" action="/">
                <input type="hidden" name="action" value="clear_all">
                <button type="submit" style="background: #d73a49; width: 100%;">Clear All Notes</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
