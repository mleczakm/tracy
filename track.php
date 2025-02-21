<?php
// Load GPX file
$gpxFile = __DIR__ . '/resources/track1.gpx';
$xml = simplexml_load_file($gpxFile);

$namespace = 'http://www.topografix.com/GPX/1/1';
$xml->registerXPathNamespace('gpx', $namespace);

$trackPoints = $xml->xpath('//gpx:trkpt');

// Initialize variables
$previousPoint = null;
$stayTime = 0;
$minDistance = 10; // Minimum distance in meters to consider a point as different

// Iterate through track points
foreach ($trackPoints as $point) {
    // Get current point's timestamp and coordinates
    $currentTimestamp = strtotime($point->time);
    $currentLat = (float) $point['lat'];
    $currentLon = (float) $point['lon'];

    // If this is not the first point, calculate stay time and distance
    if ($previousPoint !== null) {
        $previousTimestamp = strtotime($previousPoint->time);
        $stayTime = $currentTimestamp - $previousTimestamp;

        // Calculate distance between current and previous points
        $previousLat = (float) $previousPoint['lat'];
        $previousLon = (float) $previousPoint['lon'];
        $distance = calculateDistance($currentLat, $currentLon, $previousLat, $previousLon);

        // Check if stay time is longer than 5 minutes and distance is small
        if ($stayTime > 5 && $distance <= $minDistance) {
            echo "Tracked object stayed longer than 5 minutes at point ($currentLat, $currentLon) from " . date('Y-m-d H:i:s', $previousTimestamp) . " to " . date('Y-m-d H:i:s', $currentTimestamp) . " with a distance of $distance meters\n";
        }
    }

    // Update previous point
    $previousPoint = $point;
}

// Function to calculate distance between two points on Earth
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000; // Radius of the Earth in meters

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $lat1 = deg2rad($lat1);
    $lat2 = deg2rad($lat2);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos($lat1) * cos($lat2) *
        sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distance = $earthRadius * $c;

    return $distance;
}