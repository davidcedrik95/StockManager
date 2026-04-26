<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test 1: Lade database_connect.php</h2>";
require_once __DIR__ . '/database_connect.php';
echo "OK – Datei geladen.<br>";

echo "<h2>Test 2: Prüfe \$pdo</h2>";
if (isset($pdo) && $pdo !== null) {
    echo "OK – \$pdo ist vorhanden.<br>";
    $stmt = $pdo->query("SELECT 1");
    echo "OK – Datenbankabfrage erfolgreich.<br>";
} else {
    echo "FEHLER – \$pdo ist null. Datenbankverbindung fehlgeschlagen.<br>";
}
?>
