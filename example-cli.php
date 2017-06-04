<?php
require_once('src/WebScraper.php');
use johnmccuk\WebScraper;

//create an instance of the class
$webScraper = new WebScraper('https://www.black-ink.org/');

//get an array of found links
$elements = $webScraper->findValidElements();

//generate a json string summary of all the selected urls
$output = $webScraper->generateLinkList($elements);
var_dump($output);
