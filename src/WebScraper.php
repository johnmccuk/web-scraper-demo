<?php 
namespace johnmccuk;

require 'vendor/autoload.php';

use Exception;
use Sunra\PhpSimple\HtmlDomParser;

/**
 * Class for scraping a site for links
 *
 * @class WebScraper
 * @since 04/06/2017
 * @author John McCracken <johnmccuk@gmail.com>
 * @link https://github.com/johnmccuk/scraper-demo
 */
class WebScraper
{
    //TODO add dockblock
    protected $fullHtml;

    /**
    * @method __construct
    * @param string $url
    * @throws Exception on failure to retrieve content
    */
    public function __construct($url = '')
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new Exception('invalid url');
        }
        $this->fullHtml = HtmlDomParser::file_get_html($url);
        if ($this->fullHtml === false) {
            throw new Exception('unable to retrive url:'. $url);
        }
    }

    /**
    * Return an array of links found within valid articles on the specified page
    *
    * returns the url as the array key, title as the value
    * @method findValidElements
    * @return array
    */
    public function findValidElements()
    {
        $elements = [];
        foreach ($this->fullHtml->find('article') as $key => $article) {
            try {
                if ($this->findKeywordTags($article, 'Digitalia') === false) {
                        continue;
                }
                $elements = array_merge($elements, $this->getArticleLinks($article));
            } catch (Exception $e) {
                continue;
            }
        }
        return $elements;
    }

    /**
    * If the keyword is found in the footer of the passed node
    *
    * @method findKeywordTags
    * @param simplehtmldom_1_5\simple_html_dom_node $article
    * @param string $keyword
    * @return boolean
    */
    protected function findKeywordTags(\simplehtmldom_1_5\simple_html_dom_node $article, $keyword)
    {
        foreach ($article->find('a[rel="tag"]') as $footer) {
            if ($footer->innertext == $keyword) {
                return true;
            }
        }
        return false;
    }

    /**
    * Return an array of links listed in the passed node
    *
    * @method getArticleLinks
    * @param simplehtmldom_1_5\simple_html_dom_node $article
    * @return array
    */
    protected function getArticleLinks(\simplehtmldom_1_5\simple_html_dom_node $article)
    {
        $foundLinks = [];
        foreach ($article->find('ul li a') as $key => $links) {
            if (filter_var($links->href, FILTER_VALIDATE_URL) === false) {
                continue;
            }
            $foundLinks[$links->href] = $links->innertext;
        }
        return $foundLinks;
    }
}
