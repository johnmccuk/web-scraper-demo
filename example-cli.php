<?php

require_once('src/WebScraper.php');
use johnmccuk\WebScraper;

//create an instance of the class
$webScraper = new WebScraper('https://www.black-ink.org/');

$elements = $webScraper->findValidElements();
var_dump($elements);exit;

//scrape the website
$initialHtml = $scraper->getDocumentContents('https://www.black-ink.org/');


//filter the scraped data for the correct sections
$requiredHtmlSections = $scraper->extractRequiredSections($initialHtml);

//generate a json string summary of all the selected urls
$data = $scraper->generateLinkList($requiredHtmlSections);

echo $data;