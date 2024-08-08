<?php


// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$dotenv->required(['GOOGLE_CALENDAR_ID', 'GOOGLE_KEY_FILE']);

$calendarId = $_ENV['GOOGLE_CALENDAR_ID'];
$keyFile = dirname(__DIR__) . "/" . $_ENV['GOOGLE_KEY_FILE'];

// Google API configuration
$client = new Google_Client();
$client->setAuthConfig($keyFile);
$client->addScope(Google_Service_Calendar::CALENDAR_READONLY);

// Get access token using the service account
if ($client->isAccessTokenExpired()) {
    $client->refreshTokenWithAssertion();
}

// Setup Google Calendar service
$service = new Google_Service_Calendar($client);


// Fetch events from the calendar
function fetchEvents($calendarId, $year, $month = null) {
    global $service;
    $events = [];

    // Time min and max for the specific year
    if ($month) {
        $timeMin = "{$year}-{$month}-01T00:00:00Z";
        $timeMax = "{$year}-{$month}-31T23:59:59Z";
    } else {
        $timeMin = "{$year}-01-01T00:00:00Z";
        $timeMax = "{$year}-12-31T23:59:59Z";
    }

    // Parameters for the API call
    $optParams = [
        'timeMin' => $timeMin,
        'timeMax' => $timeMax,
        'singleEvents' => true,
        'orderBy' => 'startTime'
    ];

    $results = $service->events->listEvents($calendarId, $optParams);

    foreach ($results->getItems() as $event) {
        $start = $event->start->dateTime;
        if (empty($start)) {
            $start = $event->start->date;
        }
        
        $processedTitle = processEventTitle($event->getSummary());

        $events[] = array_merge([
            'titleShort' => $event->getSummary(), 
            'start' => new DateTime($start)
        ],        $processedTitle);
    }

    return $events;
}

// Function to map event codes to friendly names and extract round number
function processEventTitle($title) {
    $mapping = [
        'PBL' => 'Pražská bridžová liga',
        'SKA' => 'Skupinovka A',
        'SKB' => 'Skupinovka B',
        'SKS' => 'Švýcarská skupinovka',
        'MS' => 'Malá skupinovka',
        'PT' => 'Párový turnaj',
        'LPT' => 'Letní párový turnaj',
        'CBT' => 'Czech Bridge Tour',
        'VC' => 'Velká cena',
        'BT' => 'Bridžový týden',
        'CSL' => 'Celostátní liga',
        'MČR' => 'Mistrovství České republiky',
        'MBF' => 'Mezinárodní bridžový festival',
        'MM' => 'Mezinárodní mistrovství'
    ];

    $genitive_mapping = [
        'PBL' => 'Pražské bridžová liga',
        'SKA' => 'Skupinovky A',
        'SKB' => 'Skupinovky B',
        'SKS' => 'Švýcarské skupinovky',
    ];


    if (preg_match('/([A-Z]+) #(\d+)/', $title, $matches)) {
        $code = $matches[1];
        $round = $matches[2];
        $res = [
            'title' => $mapping[$code] ?? $code,
            'round' => $round,
        ];

            $res['titleFull'] = array_key_exists($code, $genitive_mapping) ?
                 $round . ". kolo " . $genitive_mapping[$code]
                 : $res['title'];


        return $res;
    } else {
        // For custom titles, use the title as is and set round to null
        return [
            'title' => $mapping[$title] ?? $title,
        ];
    }
}
