<?php

$dbFile = __DIR__ . '/database/database.sqlite';
$pdo = new PDO("sqlite:$dbFile");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $pdo->exec("ALTER TABLE properties ADD COLUMN latitude DECIMAL(10,8)");
    $pdo->exec("ALTER TABLE properties ADD COLUMN longitude DECIMAL(11,8)");
} catch (Exception $e) {
    // Columns might already exist
}

// Fetch all properties
$stmt = $pdo->query("SELECT id FROM properties");
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

$updateStmt = $pdo->prepare("UPDATE properties SET latitude = :lat, longitude = :lng WHERE id = :id");

foreach ($properties as $property) {
    $lat = 6.7486 + (rand(-200, 200) / 10000);
    $lng = 125.3556 + (rand(-200, 200) / 10000);
    
    $updateStmt->execute([
        ':lat' => $lat,
        ':lng' => $lng,
        ':id' => $property['id']
    ]);
}

echo "Database updated successfully.";
