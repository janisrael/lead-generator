<?php

$location = $_GET['location'] ?? '';
$radius = $_GET['radius'] ?? '10000';
$types = $_GET['types'] ?? '';
$has_website = $_GET['has_website'] ?? 'both';


$format = $_GET['format'] ?? 'json';


$flask_url = "http://127.0.0.1:5001/crawl?" . http_build_query([
    'location' => $location,
    'radius' => $radius,
    'place_types' => $types,
    'has_website' => $has_website
]);


$response = file_get_contents($flask_url);
if (!$response) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to connect to backend API']);
    exit;
}

$data = json_decode($response, true);
$results = $data['results'] ?? [];


if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="businesses.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'Address', 'Phone', 'Website']);
    foreach ($results as $row) {
        fputcsv($output, [
            $row['name'] ?? '',
            $row['address'] ?? '',
            $row['phone'] ?? '',
            $row['website'] ?? '',
            $row['rating'] ?? '',
            $row['status'] ?? ''
        ]);
    }
    fclose($output);
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode($results);
}
