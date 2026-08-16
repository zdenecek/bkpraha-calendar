<?php

require_once '../vendor/autoload.php';

require_once 'func.php';


// Determine the output format or view
$viewName = trim($_SERVER['PATH_INFO'] ?? $_GET['view'] ?? '', '/'); // Default to empty if not set

// Define the available views
$views = ['column' => 'column.php', 'month' => 'month.php', 'calendar' => 'calendar.php'];

if (!$viewName || !array_key_exists($viewName, $views)) {
   $viewName = 'json';

} 

if($viewName == 'json') {
    $events = fetchEvents();
    header('Content-Type: application/json');
    echo json_encode($events);
    exit;
}
else {
    include "head.html";
    echo "<body>";
    include $views[$viewName];
    echo "</body></html>";
}
?>