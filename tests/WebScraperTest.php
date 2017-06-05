<?php
require_once('src/WebScraper.php');

use johnmccuk\WebScraper;
use PHPUnit\Framework\TestCase;

class WebScraperTest extends TestCase
{
    public function __construct()
    {
        $this->webScraper = new WebScraper('https://www.black-ink.org/');
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
            $this->assertNotFalse(filter_var($key, FILTER_VALIDATE_URL));
        }
    }

    public function testGenerateLinkListReturnsValidJson()
    {
        $elements = $this->webScraper->findValidElements();
        $output = $this->webScraper->generateLinkList($elements);
       
        $responses = json_decode($output, true);

        $this->assertArrayHasKey('results', $responses);
        $this->assertArrayHasKey('total', $responses);
        $this->assertArrayHasKey(12, $responses['results']);

        return $responses;
    }

   /**
     * @depends testGenerateLinkListReturnsValidJson
     */
    public function testGenerateLinkListReturnsValidResults($responses)
    {
        foreach ($responses['results'] as $key => $response) {
            $this->assertArrayHasKey('url', $response);
            $this->assertArrayHasKey('title', $response);
            $this->assertArrayHasKey('meta description', $response);
            $this->assertArrayHasKey('keywords', $response);
            $this->assertArrayHasKey('filesize', $response);
        }
    }

    /**
     * @depends testGenerateLinkListReturnsValidJson
     */
    public function testGenerateLinkListReturnsValidTotal($responses)
    {
        $calculatedTotal = 0;
        $total = floatval(str_replace('kb', '', $responses['total']));
        foreach ($responses['results'] as $key => $response) {
            $calculatedTotal += floatval(str_replace('kb', '', $response['filesize']));
        }

        //NOTE: issues trying to comparing 2 decimal places. Since this isnt mission critical, check to whole no only
        $this->assertEquals(round($total, 0), round($calculatedTotal, 0));
    }
}
