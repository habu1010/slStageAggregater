<?php
$settings = parse_ini_file("setting.ini");

try {
    $pdo = new PDO ( 'sqlite:' . $settings['sqlite3_db_path']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch ( PDOException $e ) {
    exit ( 'connection unsuccess' . $e->getMessage () );
}

if (array_key_exists('hourly', $_GET)) {
    $stmt = $pdo->query("SELECT * FROM slstage_aggregater ORDER BY time ASC");
} else {
    $stmt = $pdo->query("SELECT * FROM slstage_aggregater_daily ORDER BY time ASC");
}
$array = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($array, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
