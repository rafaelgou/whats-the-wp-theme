<?php

namespace App\Services;

use Illuminate\Support\ServiceProvider;
use App\Models\Search;
use App\Models\Theme;
use GuzzleHttp\Client;
use DOMDocument;
use DOMNodeList;
use DOMXpath;

class ThemeFinderService extends ServiceProvider
{

    /**
  	 * Headers for style.css files.
  	 *
  	 * @static
  	 * @access private
  	 * @var array
  	 * @see https://developer.wordpress.org/reference/classes/wp_theme/
  	 */
  	private static $fileHeaders = array(
    		'Name'        => 'Theme Name',
    		'ThemeURI'    => 'Theme URI',
    		'Description' => 'Description',
    		'Author'      => 'Author',
    		'AuthorURI'   => 'Author URI',
    		'Version'     => 'Version',
    		'Template'    => 'Template',
    		'Status'      => 'Status',
    		'Tags'        => 'Tags',
    		'TextDomain'  => 'Text Domain',
    		'DomainPath'  => 'Domain Path',
        'License'     => 'License',
        'LicenseURI'  => 'License URI',
  	);

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var DOMDocument
     */
    protected $dom;

    /**
    * @var App/Models/Search
    */
    protected $search;

    /**
     * @var App/Models/Theme
     */
    protected $mainTheme;

    /**
     * @var App/Models/Theme
     */
    protected $childTheme;

    public function __construct($uri, $remoteIp)
    {
        $this->uri      = $uri;
        $this->remoteIp = $remoteIp;
        $this->client   = new Client(['base_uri' => $uri]);
        $htmlContent    = $this->get();
        $this->dom      = $this->parse($htmlContent);
    }

    /**
     * Search
     * @return App/Models/Theme
     */
    public function search()
    {
        $results          = $this->discover();
        $this->mainTheme  = $results['main'];
        $this->childTheme = $results['child'];
        $siteInfo         = $this->getSiteInfo();

        $this->search = Search::createByArray([
            'uri'               => $this->uri,
            'title'             => $siteInfo['title'],
            'success'           => null !== $this->mainTheme,
            'wordpress_version' => $siteInfo['wordpress_version'],
            'ip'                => $this->remoteIp,
            'main_theme_id'     => null !== $this->mainTheme ? $this->mainTheme->id : null,
            'child_theme_id'    => null !== $this->childTheme ? $this->childTheme->id : null,
        ]);

        return $this->search;
    }

    /**
     * Get remote content from $uri/$path
     * @param  string $path
     * @return DOMDocument
     */
    public function get($path = '')
    {
        $path = str_replace($this->uri, '', $path);
        $response = $this->client->request('GET', $path);

        return $response->getBody();
    }

    /**
     * Get remote content from $uri
     * @param  string $htmlContent
     * @return DOMDocument
     */
    public function parse($htmlContent)
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent);

        return $dom;
    }

    /**
     * Get sit info
     * @return array
     */
    public function getSiteInfo()
    {
        $siteInfo = [
          'lang'              => null,
          'title'             => null,
          'wordpress_version' => null,
        ];
        $xpath = new DOMXpath($this->dom);

        $htmlNodes = $xpath->query("*/html");
        if ($htmlNodes->length > 0) {
            $siteInfo['lang'] = $htmlNodes[0]->getAttribute('lang');
        }

        $generatorNodes = $xpath->query("*/meta[@name='generator']");
        if ($generatorNodes->length > 0) {
            $siteInfo['wordpress_version'] = $generatorNodes[0]->getAttribute('content');
        }

        $titleNodes = $xpath->query("*/title");
        if ($titleNodes->length > 0) {
            $siteInfo['title'] = $titleNodes[0]->nodeValue;
        }

        return $siteInfo;
    }

    /**
     * Extract stylesheet uris from an array of <link />'s
     * @param  DOMDocument $dom
     * @return array
     */
    public function findStylesheetUrls(DOMDocument $dom)
    {
        $xpath    = new DOMXpath($dom);
        $nodes = $xpath->query("*/link[@type='text/css']");
        $links    = [];
        foreach($nodes as $node) {
          $links[] = $node->getAttribute('href');
        }

        // $links = $dom->getElementsByTagName('link');
        return $links;
    }

    /**
     * Discover Theme
     * @return App\Model\Theme
     */
    public function discover()
    {
        // Basic
        $main = $this->discoverDefault();
        $child = null;

        // // Minimized
        // if (!$main) {
        //   $main = $this->discoverMinimized();
        // }

        // Parent
        if ($main->hasParent()) {
            $child = $main;
            $main = $this->discoverParent($child);
        }

        return [
            'main'  => $main,
            'child' => $child,
        ];
    }

    /**
     * Discover Theme
     * @return App\Model\Theme
     */
    public function discoverDefault()
    {
        try {

        } catch (\Exception $e) {

        }

        $uri = null;
        $xpath = new DOMXpath($this->dom);

        // Search by default id (precise, but not always available)
        $nodes = $xpath->query("*/link[@id='wt-style-css']");

        // Found
        if ($nodes->length > 0) {
            $uri = $nodes[0]->getAttribute('href');
        }

        // Not found, try deeply
        if (null === $uri) {
            $nodes = $xpath->query("*/link[contains(@href, 'style.css')]");
            foreach($nodes as $node) {
                if (preg_match('/^.*\/themes\/(.*)\/style\.css/', $node->getAttribute('href'))) {
                    $uri = $node->getAttribute('href');
                }
            }
        }

        if (null === $uri) {
          return null;
        }

        $content = file_get_contents($uri);
        $data = $this->getFileData($content);
        var_dump($uri);
        preg_match('/^.*\/themes\/(.*)\/style\.css/', $uri, $matches);
        print_r($matches);
        $data['theme_id']       = $matches > 0 ? $matches[1] : null;
        $data['style_uri']      = $uri;
        $data['screenshot_uri'] = str_replace('style.css', 'screenshot.png', $uri);

        $theme = Theme::createByArray($data);

        return $theme;
    }

    /**
     * Discover Theme
     * @param Theme $child
     * @return App\Model\Theme
     */
    public function discoverParent(Theme $child)
    {
        $uri = str_replace("themes/{$child->theme_id}/style.css", "themes/{$child->template}/style.css", $child->style_uri);
        $content = file_get_contents($uri);
        $data = $this->getFileData($content);
        preg_match('/^.*\/themes\/(.*)\/style\.css/', $uri, $matches);
        $data['theme_id']       = $matches > 0 ? $matches[1] : null;
        $data['style_uri']      = $uri;
        $data['screenshot_uri'] = str_replace('style.css', 'screenshot.png', $uri);

        $theme = Theme::createByArray($data);

        $child->setParent($theme);

        return $theme;
    }

    /**
     * Get file data
     * @see https://developer.wordpress.org/reference/functions/get_file_data/
     * @param  string $content
     * @return array
     */
    public function getFileData($content = '' )
    {
        $headers = self::$fileHeaders;

        // Make sure we catch CR-only line endings.
        $content = str_replace("\r", "\n", $content);

        foreach ($headers as $field => $regex) {
            if (preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $content, $match) && $match[1]) {
                $headers[$field] = $this->cleanup_header_comment($match[1]);
            } else {
                $headers[$field] = '';
            }
        }

        return $headers;
    }

    /**
     * Clean up header comment
     * @see https://developer.wordpress.org/reference/functions/_cleanup_header_comment/
     * @param  string $str
     * @return string
     */
    protected function cleanup_header_comment($str)
    {
        return trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $str));
    }
}
