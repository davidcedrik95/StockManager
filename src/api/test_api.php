<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/database_connect.php';

// Simulierter Request-Payload (exakt wie vom Frontend)
$payload = [
    'article' => [
        'name' => 'Testprodukt',
        'brand' => 'Testbrand',
        'category' => 'cardio',
        'price' => 99.99,
        'main_image' => 'https://example.com/test.jpg',
        'article_type' => 'treadmill',
        'article_number' => 'TEST-' . time(),
        'description' => 'Testbeschreibung',
        'color' => 'rot',
        'warranty_years' => 2,
        'weight_capacity' => '150 kg',
        'power_supply' => '230V',
        'application_area' => 'Indoor',
        'in_stock' => true,
        'is_new' => false,
        'best_seller' => false
    ],
    'specifics' => [
        'motor_power' => '2,5 kW',
        'max_speed' => '20 km/h',
        'max_inclination' => '15%',
        'display_type' => 'LCD',
        'training_programs' => '12',
        'has_ekg' => false,
        'is_foldable' => true,
        'has_touchscreen' => false,
        'has_bluetooth' => false,
        'has_heart_rate_monitor' => true,
        'has_wifi' => false,
        'has_speaker' => false,
        'power_range' => '2,0–3,0 kW',
        'display_info' => 'Geschwindigkeit, Zeit, Distanz',
        'programs_info' => '12 Programme',
        'comfort_features' => 'Alpin-Funktion'
    ],
    'shipping' => [
        'shipping_cost' => 49.99,
        'free_shipping_threshold' => 500,
        'shipping_method' => 'DHL',
        'estimated_delivery_days' => 3
    ],
    'images' => [
        [
            'url' => 'https://example.com/image1.jpg',
            'type' => 'gallery',
            'image_order' => 1,
            'article_name' => 'Testprodukt'
        ]
    ]
];

$jsonPayload = json_encode($payload);

// Aufruf des eigenen Skripts per cURL
$ch = curl_init('https://alpha-med-care.com/api/stock_manager_products.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP-Status: $httpCode\n";
echo "Antwort des Skripts:\n";
echo $response;
?>
