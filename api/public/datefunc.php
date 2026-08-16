<?php 
$formatter = new IntlDateFormatter(
    'cs_CZ',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE,
    'Europe/Prague',
    IntlDateFormatter::GREGORIAN,
    '' 
);

$daysWithPrepositions = [
    'pondělí' => 'V pondělí',
    'úterý' => 'V úterý',
    'středa' => 'Ve středu',
    'čtvrtek' => 'Ve čtvrtek',
    'pátek' => 'V pátek',
    'sobota' => 'V sobotu',
    'neděle' => 'V neděli'
];

$daysInGenitive = [
    'pondělí' => 'pondělí',
    'úterý' => 'úterý',
    'středa' => 'středy',
    'čtvrtek' => 'čtvrtka',
    'pátek' => 'pátku',
    'sobota' => 'soboty',
    'neděle' => 'neděle'
];

function spansMoreDays($start, $end) {
    return $end && $start->format('Y-m-d') !== $end->format('Y-m-d');
}

function formatDateRangeToCzech($start, $end) {

    global $formatter, $daysInGenitive;

    $formatter->setPattern('EEEE');
    $startDay = $daysInGenitive[$formatter->format($start)] ?? '';
    $endDay = $daysInGenitive[$formatter->format($end)] ?? '';

    $formatter->setPattern('d. M.');

    return 'Od ' . ($startDay ? $startDay . ' ' : '') . $formatter->format($start)
        . ' do ' . ($endDay ? $endDay . ' ' : '') . $formatter->format($end);
}

function formatDateToCzechIn($date)  {

    global $formatter, $daysWithPrepositions;

    $formatter->setPattern('EEEE');
    $dayOfWeek = $formatter->format($date);

    // Replace the day of the week with its prepositional form
    if (array_key_exists($dayOfWeek, $daysWithPrepositions)) {
        $formattedDay = $daysWithPrepositions[$dayOfWeek];
    } else {
        $formattedDay = $dayOfWeek;  // Fallback in case something unexpected happens
    }

    // Now format the rest of the date
    $formatter->setPattern('d. M.');
    $formattedDate = $formatter->format($date);

    // Combine the strings
    $fullFormattedDate = $formattedDay . ' ' . $formattedDate;
    return $fullFormattedDate;
}