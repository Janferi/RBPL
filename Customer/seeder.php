<?php
include 'koneksi.php';

// Directory containing images
$imageDir = '../images/menu/';
$files = scandir($imageDir);

echo "Starting seeder...\n";

// Optional: Clear table first
mysqli_query($koneksi, "TRUNCATE TABLE menu");
echo "Table truncated.\n";

$categories = [
    'Coffee' => ['Latte', 'Kopi', 'Espresso', 'Americano', 'Cappuccino', 'Mocha'],
    'Non-Coffee' => ['Matcha', 'Chocolate', 'Red Velvet', 'Lavine', 'Night Furry'],
    'Food' => ['Rice', 'Burger', 'Pasta', 'Steak'],
    'Snack' => ['Fries', 'Wings', 'Dimsum', 'Platter']
];

foreach ($files as $file) {
    if ($file === '.' || $file === '..')
        continue;

    $filePath = $imageDir . $file;
    $imageContent = addslashes(file_get_contents($filePath)); // Read image as binary

    // Generate name from filename
    $name = pathinfo($file, PATHINFO_FILENAME);
    $name = str_replace(['_', '-'], ' ', $name);
    $name = ucwords(strtolower($name)); // Title Case

    // Guess Category
    $category = 'Food'; // Default
    foreach ($categories as $cat => $keywords) {
        foreach ($keywords as $keyword) {
            if (stripos($name, $keyword) !== false) {
                $category = $cat;
                break 2;
            }
        }
    }

    // Generate Dummy Price
    $price = rand(25, 65) * 1000;

    // Generate Dummy Description
    $descriptions = [
        "A premium selection crafted with the finest ingredients to deliver an unforgettable taste experience.",
        "Delicate flavors balanced to perfection, offering a moment of pure indulgence.",
        "Rich, bold, and satisfying. The perfect companion for your daily routine.",
        "Expertly prepared by our chefs/baristas using high-quality components for distinct flavor notes.",
        "A signature CRUZ creation that blends tradition with modern culinary techniques."
    ];
    $desc = $descriptions[array_rand($descriptions)];

    // Insert
    $query = "INSERT INTO menu (nama, harga, category, deskripsi, gambar) VALUES ('$name', '$price', '$category', '$desc', '$imageContent')";

    if (mysqli_query($koneksi, $query)) {
        echo "Inserted: $name ($category) - IDR $price\n";
    } else {
        echo "Failed to insert $name: " . mysqli_error($koneksi) . "\n";
    }
}

echo "Seeder completed successfully.";
?>