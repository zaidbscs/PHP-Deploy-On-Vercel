<?php
$supabaseUrl = getenv('SUPABASE_URL');
$supabaseKey = getenv('SUPABASE_KEY');
$error = null;

// Handle Form Actions via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_note') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
            setcookie('note_error', 'Both title and content are required.', time() + 60, '/');
        } else {
            $ch = curl_init("$supabaseUrl/rest/v1/notes");
            $payload = json_encode(['title' => $title, 'content' => $content]);
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "apikey: $supabaseKey",
                "Authorization: Bearer $supabaseKey",
                "Content-Type: application/json",
                "Prefer: return=minimal"
            ]);
            curl_exec($ch);
           curl_close();
        }
    } elseif ($action === 'delete_note') {
        $idToDelete = $_POST['note_id'] ?? '';
        if ($idToDelete) {
            $ch = curl_init("$supabaseUrl/rest/v1/notes?id=eq.$idToDelete");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "apikey: $supabaseKey",
                "Authorization: Bearer $supabaseKey"
            ]);
            curl_exec($ch);
            curl_close();
        }
    }

    // PRG Pattern Redirect
    header("Location: /");
    exit;
}

// Fetch notes from Supabase via GET
$notes = [];
if ($supabaseUrl && $supabaseKey) {
    $ch = curl_init("$supabaseUrl/rest/v1/notes?select=*&order=created_at.desc");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: $supabaseKey",
        "Authorization: Bearer $supabaseKey"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    $notes = json_decode($response, true) ?? [];
}

// Read flash error cookie if any
$error = $_COOKIE['note_error'] ?? null;
if ($error) {
    setcookie('note_error', '', time() - 3600, '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP + Supabase Notes</title>
    <style>
        body { font-family: sans-serif; max-width: 650px; margin: 40px auto; padding: 20px; line-height: 1.6; color: #333; background: #fdfdfd; }
        .card { background: #fff; border: 1px solid #e1e4e8; padding: 20px; margin-bottom: 20px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .error { color: #d9534f; background: #f2dede; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        input, textarea { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 10px 18px; background: #3ecf8e; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #34b27b; }
        .note-item { background: #f6f8fa; border: 1px solid #e1e4e8; padding: 15px; margin-bottom: 10px; border-radius: 4px; position: relative; }
        .note-item h4 { margin: 0 0 8px 0; color: #3ecf8e; }
        .note-item p { margin: 0 0 10px 0; white-space: pre-wrap; }
        .note-item small { color: #586069; }
        .delete-btn { background: #d73a49; padding: 5px 10px; font-size: 12px; float: right; color: white; }
        .delete-btn:hover { background: #cb2431; }
    </style>
</head>
<body>
    <h1>PHP + Supabase Notes App</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Add Note Form -->
    <div class="card">
        <h3>Create a Note</h3>
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
            <p>No notes found in the database.</p>
        <?php else: ?>
            <?php foreach ($notes as $note): ?>
                <div class="note-item">
                    <form method="POST" action="/" style="display:inline;">
                        <input type="hidden" name="action" value="delete_note">
                        <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                    <h4><?php echo htmlspecialchars($note['title']); ?></h4>
                    <p><?php echo htmlspecialchars($note['content']); ?></p>
                    <small>Created at: <?php echo htmlspecialchars($note['created_at']); ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
