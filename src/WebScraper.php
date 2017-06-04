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
    /*
    * @var simplehtmldom_1_5\simple_html_dom $fullHtml contains the base url object
    */
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
    * @param string $keyword tag keyword default is 'Digitalia''
    * @return array
    */
    public function findValidElements($keyword = 'Digitalia')
    {
        $elements = [];
        foreach ($this->fullHtml->find('article') as $key => $article) {
            try {
                if ($this->findKeywordTags($article, $keyword) === false) {
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
    * Iterate through the passed array and return a json encoded array of url information
    *
    * @method generateLinkList
    * @param array $urlList
    * @return string
    */
    public function generateLinkList($urlList)
    {
        $list = [
            'results' => [],
            'total' => 0
        ];
        
        foreach ($urlList as $key => $title) {
            $meta = $this->getMetaTags($key);
            $filesize = $this->getUrlFileSize($key);
            
            $list['total'] += $filesize;

            $list['results'][] = [
                'url' => $key,
                'title' => $title,
                'meta description' => $this->getMetaValue($meta, 'description'),
                'keywords' => $this->getMetaValue($meta, 'keywords'),
                'filesize' => $this->convertToKb($filesize) . 'kb'
            ];
        }

        $list['total'] = $this->convertToKb($list['total']) . 'kb';

        return json_encode($list);
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

    /**
    * Convert passed bytes into kilobytes
    *
    * @method convertToKb
    * @param integer $value
    * @return float
    */
    protected function convertToKb($value)
    {
        if (is_numeric($value) === $value) {
            return 0.00;
        }
        return number_format($value / 1024, 2);
    }

    /**
    * If the meta value exists in the passed array, return it.
    *
    * Returns an empty string if not found
    *
    * @method getMetaValue
    * @param string $url
    * @return string
    */
    protected function getMetaValue($data, $key)
    {
        return (array_key_exists($key, $data)) ? $data[$key] : '';
    }

    /**
    * Return the file size in bytes of the passed url
    *
    * Tries a few techniques to get the filesize:
    * Firstly from the header content size
    * If this fails, then pulls the file contents and checks the length
    *
    * Returns 0 on any errors
    *
    * @method getUrlFileSize
    * @param string $url
    * @return integer
    */
    protected function getUrlFileSize($url)
    {
        try {
            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                throw new Exception();
            }

            static $regex = '/^Content-Length: *+\K\d++$/im';
            if (!$fp = @fopen($url, 'rb')) {
                throw new Exception();
            }
            if (isset($http_response_header) && preg_match($regex, implode("\n", $http_response_header), $matches)) {
                return (int)$matches[0];
            }

            $result = strlen(stream_get_contents($fp));

            if ($result === false) {
                throw new Exception();
            }

            return $result;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
    * Retrieve the meta tags from the passed urls headers
    *
    * @method getMetaTags
    * @param string $url
    * @return array
    */
    protected function getMetaTags($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return [];
        }
        try {
            //note were suppressing the warning here since it grumbles if its a 400 request
            $meta = @get_meta_tags($url);
            return ($meta === false) ? [] : $meta;
        } catch (Exception $e) {
            return [];
        }
    }
}
