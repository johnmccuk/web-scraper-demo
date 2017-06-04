<?php
require_once('src/WebScraper.php');

use johnmccuk\WebScraper;
use PHPUnit\Framework\TestCase;

class WebScraperTest extends TestCase
{
    public function __construct()
    {
        $this->webScraper = new WebScraper('https://www.google.co.uk');
    }

    /**
    * @expectedException Exception
    */
    public function testGetDocumnetHtmlReturnsException()
    {
        $webScraper = new WebScraper('');
    }

    public function testGetDocumnetHtmlReturnsExpected()
    {
        $this->assertEquals('johnmccuk\WebScraper', get_class($this->webScraper));
    }

    public function testFindValidElementsReturnsAnArray()
    {
        $results = $this->webScraper->findValidElements();
        $this->assertTrue(is_array($results));
    }

    public function testFindValidElementsReturnsValidUrls()
    {
        $results = $this->webScraper->findValidElements();
        foreach ($results as $key => $result) {
            $this->assertTrue(filter_var($result, FILTER_VALIDATE_URL));
        }
    }

}
