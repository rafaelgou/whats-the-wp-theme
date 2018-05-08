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
     * @var string
     */
    protected $htmlContent;

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
        try {
            $this->uri         = $uri;
            $this->remoteIp    = $remoteIp;
            $this->client      = new Client([
              'base_uri' => $uri,
            ]);
            $this->htmlContent = $this->get();
            $this->dom         = $this->parse($this->htmlContent);
        } catch (\Exception $e) {
            $this->search = Search::createByArray([
                'uri'               => $this->uri,
                'title'             => 'Not Available',
                'success'           => false,
                'wordpress_version' => null,
                'ip'                => $this->remoteIp,
                'main_theme_id'     => null,
                'child_theme_id'    => null,
                'error'             => $e->getMessage(),
            ]);

            if (preg_match('/cURL error 51\: SSL/', $e)) {
                throw new \Exception("SSL Certificate verify error.<br/>For security reasons, this site cannot be search.<br/>Sorry for that.", 400);
            }

            throw new \Exception("We cannot reach this site.<br/>Sorry for that.", 400);
        }
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
        try {
            $this->search = Search::createByArray([
                'uri'               => $this->uri,
                'title'             => $siteInfo['title'],
                'success'           => null !== $this->mainTheme,
                'wordpress_version' => $siteInfo['wordpress_version'],
                'ip'                => $this->remoteIp,
                'main_theme_id'     => null !== $this->mainTheme ? $this->mainTheme->id : null,
                'child_theme_id'    => null !== $this->childTheme ? $this->childTheme->id : null,
            ]);
        } catch (\Exception $e) {
            $this->search = Search::createByArray([
                'uri'               => $this->uri,
                'title'             => array_key_exists('title', $siteInfo) ? $siteInfo['title'] : 'Not Available',
                'success'           => false,
                'wordpress_version' => null,
                'ip'                => $this->remoteIp,
                'main_theme_id'     => null,
                'child_theme_id'    => null,
                'error'             => $e->getMessage(),
            ]);
        }

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

        // HTML info search
        if (!$main) {
            $main = $this->discoverInHTML();
        }

        // Minimized
        if (!$main) {
            $main = $this->discoverInStylesheet();
        }

        // Parent
        if ($main && $main->hasParent()) {
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

        $content = $this->fileGetContents($uri);
        $data = $this->getFileData($content);
        preg_match('/^.*\/themes\/(.*)\/style\.css/', $uri, $matches);
        $data['theme_id']       = count($matches) > 0 ? $matches[1] : null;
        $data['style_uri']      = $uri;
        $data['screenshot_uri'] = str_replace('style.css', 'screenshot.png', $uri);

        if (!$this->remoteFileExists($data['screenshot_uri'])) {
          $data['screenshot_uri'] = null;
        }

        $theme = Theme::createByArray($data);

        return $theme;
    }

    /**
     * Discover Theme
     * @return App\Model\Theme
     */
    public function discoverInHTML()
    {
        $uri = null;
        preg_match('/wp-content\/themes\/(.*?)\//m', $this->htmlContent, $themesWpContent);
        preg_match('/app\/themes\/(.*?)\//m', $this->htmlContent, $themesApp);

        if (count($themesWpContent) > 0 ) {
            $themeName = $themesWpContent[1];
            $uri = "{$this->uri}/wp-content/themes/{$themesWpContent[1]}/style.css";
        }
        if (count($themesApp) > 0 ) {
            $themeName = $themesApp[1];
            $uri = "{$this->uri}/app/themes/{$themesApp[1]}/style.css";
        }

        if (null === $uri) {
            return null;
        }
        if (!$this->remoteFileExists($uri)) {
            return null;
        }

        $content = $this->fileGetContents($uri);
        $data = $this->getFileData($content);

        $data['theme_id']       = $themeName;
        $data['style_uri']      = $uri;
        $data['screenshot_uri'] = str_replace('style.css', 'screenshot.png', $uri);

        if (!$this->remoteFileExists($data['screenshot_uri'])) {
          $data['screenshot_uri'] = null;
        }

        $theme = Theme::createByArray($data);

        return $theme;
    }

    /**
     * Discover Theme
     * @return App\Model\Theme
     */
    public function discoverInStylesheet()
    {
        $uri = null;

        $uri = null;
        $xpath = new DOMXpath($this->dom);

        // Search by default id (precise, but not always available)
        $nodes = $xpath->query("*/link[@type='text/css']");

        foreach($nodes as $node) {
            $cssUri = $node->getAttribute('href');
            if ($this->remoteFileExists($cssUri)) {
              $cssContent = $this->fileGetContents($cssUri);
              preg_match('/wp-content\/themes\/(.*?)\//m', $cssContent, $themesWpContent);
              preg_match('/app\/themes\/(.*?)\//m', $cssContent, $themesApp);
              if (count($themesWpContent) > 0 ) {
                $themeName = $themesWpContent[1];
                $uri = "{$this->uri}/wp-content/themes/{$themesWpContent[1]}/style.css";
              }
              if (count($themesApp) > 0 ) {
                $themeName = $themesApp[1];
                $uri = "{$this->uri}/app/themes/{$themesApp[1]}/style.css";
              }
            }
        }

        if (null === $uri) {
            return null;
        }
        if (!$this->remoteFileExists($uri)) {
            return null;
        }

        $content = $this->fileGetContents($uri);
        $data = $this->getFileData($content);

        $data['theme_id']       = $themeName;
        $data['style_uri']      = $uri;
        $data['screenshot_uri'] = str_replace('style.css', 'screenshot.png', $uri);

        if (!$this->remoteFileExists($data['screenshot_uri'])) {
          $data['screenshot_uri'] = null;
        }

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
        if (!$this->remoteFileExists($uri)) {
            return null;
        }
        $content = $this->fileGetContents($uri);
        $data = $this->getFileData($content);
        preg_match('/^.*\/themes\/(.*)\/style\.css/', $uri, $matches);
        $data['theme_id']       = $child->template;
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

    /**
     * Check if remote file exists
     * @param  string $uri
     * @return boolean
     */
    protected function remoteFileExists($uri)
    {
        $curl = curl_init($uri);

        //don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, true);

        //do request
        $result = curl_exec($curl);

        $ret = false;

        //if request did not fail
        if ($result !== false) {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($statusCode == 200) {
                $ret = true;
            }
        }

        curl_close($curl);

        return $ret;
    }

    /**
     * Get remote file
     * @param  string $uri
     * @return string
     */
    protected function fileGetContents($uri)
    {
        $options=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        return file_get_contents($uri, false, stream_context_create($options));
    }
}
