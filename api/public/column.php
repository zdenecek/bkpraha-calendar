<?php 
require_once 'datefunc.php';
$limit = 10;
?>

<div class="column">

<?php foreach (array_slice($events, 0, $limit) as $event):  ?>

<div class="line"><hr></div>
<div class="event">
    <h3>
        <?= $event['title'] ?>
    </h3>

    <?= formatDateToCzechIn($event['start']) ?> se hraje <?= $event['titleFull']  ?>.
</div>

<?php endforeach; ?>

</div>
<style>

    .column {
        background-color: #ECEFF1;
    }

 .line {

    color: rgba(0,0,0,.7);
    width: 100%;
    display: block;
    margin: 0 auto;
    padding: 1.25rem;
    padding-top: .625rem;
    padding-bottom: .625rem;
 }

 .line hr {
    font-style: normal;
    font-weight: 400;
    color: rgba(0,0,0,.7);
    font-size: 1.075rem;
    -webkit-tap-highlight-color: transparent!important;
    overflow: visible;
    box-sizing: border-box;
    height: 0;
    margin: 0;
    padding: 0;
    border: none;
    border-bottom-style: solid;
    border-bottom-width: 1px;
    border-color: rgba(0,0,0,.2);
 }

 .event {

    font-family: Roboto Flex,Roboto-Flex-Fallback,Apple Color Emoji,Noto Color Emoji,sans-serif;
    line-height: 1.6;
    -webkit-font-smoothing: subpixel-antialiased;
    font-style: normal;
    font-weight: 400;
    color: rgba(0,0,0,.7);
    font-size: 1.075rem;
    -webkit-tap-highlight-color: transparent!important;
    box-sizing: border-box;
    position: relative;
    overflow: hidden;
    margin: 0 auto;
    word-wrap: break-word;
    word-break: break-word;
    padding: 1.25rem;
    padding-top: .75rem;
    padding-bottom: .75rem;
    max-width: 100%!important;
 }

 .event h3 {
    color: #1a881a;
    word-wrap: break-word;
    word-break: break-word;
    font-family: Roboto Flex,Roboto-Flex-Fallback,Apple Color Emoji,Noto Color Emoji,sans-serif;
    line-height: 1.5;
    text-align: left;
    font-style: normal;
    font-size: 1.1875rem;
    -webkit-tap-highlight-color: transparent!important;
    box-sizing: border-box;
    font-weight: 700;
    padding: 0 0 .5rem;
    margin: 0;
 }

</style>