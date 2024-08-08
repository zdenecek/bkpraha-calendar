<?php

require_once '../vendor/autoload.php';

require_once 'func.php';

setlocale(LC_TIME, "cs_CZ");

$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? null;
$events = fetchEvents($calendarId, $year);

// Determine the output format or view
$viewName = trim($_SERVER['PATH_INFO'] ?? $_GET['view'] ?? '', '/'); // Default to empty if not set

// Define the available views
$views = ['column' => 'column.php', 'month' => 'month.php', 'calendar' => 'calendar.php'];

if ($viewName && array_key_exists($viewName, $views)) {
    // Render PHP view
    include "head.html";
    echo "<body>";
    include $views[$viewName];
    echo "</body></html>";

} else {
    // Output the filtered events as JSON
    header('Content-Type: application/json');
    echo json_encode($events);
}

?>