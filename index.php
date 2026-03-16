<?php
session_start(); // Pro přenos hlášek pomocí $_SESSION [cite: 73, 80]
require_once 'init.php';

// --- ZPRACOVÁNÍ POŽADAVKŮ (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Přidání zájmu [cite: 44, 46, 47]
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['msg'] = "Pole nesmí být prázdné."; 
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO interests (name) VALUES (?)");
                $stmt->execute([$name]);
                $_SESSION['msg'] = "Zájem byl přidán.";
            } catch (PDOException $e) {
                $_SESSION['msg'] = "Tento zájem už existuje.";
            }
        }
    }

    // Mazání zájmu
    if ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        $stmt = $db->prepare("DELETE FROM interests WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['msg'] = "Zájem byl odstraněn.";
    }

    // Editace zájmu [cite: 55, 57, 58]
    if ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $newName = trim($_POST['name'] ?? '');
        if (!empty($newName)) {
            $stmt = $db->prepare("UPDATE interests SET name = ? WHERE id = ?");
            $stmt->execute([$newName, $id]);
            $_SESSION['msg'] = "Zájem byl upraven.";
        }
    }

    // PRG pattern - přesměrování po odeslání
    header("Location: index.php");
    exit;
}

// Načtení zájmů z databáze
$interests = $db->query("SELECT * FROM interests")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>IT Profil 6.0</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Moje Zájmy</h1>

        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
        <?php endif; ?>

        <form method="post" class="add-form">
            <input type="hidden" name="action" value="add">
            <input type="text" name="name" placeholder="Napište zájem..." required>
            <button type="submit">Přidat</button>
        </form>

        <ul class="interest-list">
            <?php foreach ($interests as $item): ?>
                <li>
                    <form method="post" class="edit-wrap">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>">
                        <button type="submit" class="btn-edit">Upravit</button>
                    </form>
                    
                    <form method="post">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn-delete">Smazat</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>