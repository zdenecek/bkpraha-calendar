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
function fetchEvents($limit=50, $date_from=null, $date_to=null ) {
    global $service, $calendarId;
    $events = [];
// Set default date_from to today if not provided
if ($date_from === null) {
    $date_from = date('Y-m-d') . 'T00:00:00Z';  // Start from today
} else {
    $date_from = date('Y-m-d', strtotime($date_from)) . 'T00:00:00Z';  // Ensure correct formatting
}

// Set default date_to to three years from today if not provided
if ($date_to === null) {
    $date_to = date('Y-m-d', strtotime('+3 years')) . 'T23:59:59Z';  // End three years from now
} else {
    $date_to = date('Y-m-d', strtotime($date_to)) . 'T23:59:59Z';  // Ensure correct formatting
}

// Prepare parameters for the Google Calendar API request
$optParams = [
    'timeMin' => $date_from,
    'timeMax' => $date_to,
    'singleEvents' => true,
    'orderBy' => 'startTime',
    'maxResults' => $limit
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
        'MM' => 'Mezinárodní mistrovství',
        'MŠ' => 'Malý švýcarák',
        'Š' => 'Švýcarská skupinovka',
        'A' => 'Skupinovka A',
        'B' => 'Skupinovka B',
        'PL' => 'Pražská liga',
        'IMP' => 'Impový přebor BKP',
        'TOP' => 'Topový přebor BKP',
        'K' => 'Kurz Zdeňka Frabši pro pokročilé'
    ];

    $genitive_mapping = [
        'PBL' => 'Pražské bridžová liga',
        'SKA' => 'Skupinovky A',
        'SKB' => 'Skupinovky B',
        'SKS' => 'Švýcarské skupinovky',
        'Š' => 'Švýcarské skupinovky',
        'A' => 'Skupinovky A',
        'B' => 'Skupinovky B',
        'PL' => 'Pražské ligy'
    ];

    $verb_mapping = [
        'K' => 'se koná'
    ];


    if (preg_match('/(A|B|PL|Š)(\d+)/', $title, $matches)) {
        $code = $matches[1];
        $round = $matches[2];
        $res = [
            'title' => $mapping[$code] ?? $code,
            'round' => $round,
            'verb' => $verb_mapping[$code] ?? 'se hraje',
        ];

        if ($round) {

            $res['titleFull'] = array_key_exists($code, $genitive_mapping) ?
                 $round . ". kolo " . $genitive_mapping[$code]
                 : $res['title'];
        }
        else {
            $res['titleFull'] = $res['title'];
        }

        return $res;
    } else {
        // For custom titles, use the title as is and set round to null
        return [
            'title' => $mapping[$title] ?? $title,
            'titleFull' => $mapping[$title] ?? $title,
            'verb' => $verb_mapping[$title] ?? 'se hraje',
        ];
    }
}
