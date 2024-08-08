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