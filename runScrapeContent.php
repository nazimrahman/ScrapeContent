<?php
include_once('ScrapeContent.php');

$sc = new ScrapeContent;
$sc->setSourceAddress('linkspages/phpdeveloper1');
$sc->getSourceAddress();
//print_r($sc->getLinks('/\/r\//'));
print($sc->getLinks());
?>
