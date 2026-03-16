<?php
// Připojení k SQLite databázi pomocí PDO 
try {
    $db = new PDO("sqlite:profile.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vytvoření tabulky podle zadání [cite: 25, 26, 27, 28]
    $query = "CREATE TABLE IF NOT EXISTS interests (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE
    )";
    
    $db->exec($query);
} catch (PDOException $e) {
    die("Chyba při inicializaci databáze: " . $e->getMessage());
}