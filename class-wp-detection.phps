<?php

/**
 * WP_Detection
 *
 * Give it a site url and it'll detect if WordPress is on that site, and try to figure out the version
 * Uses WordPress functions to do the work, so must be run from inside WordPress. Like in a plugin or something.
 *
 */

/* Example usage:

$testsite = new WP_Detection('http://example.com');
var_dump($testsite->getVersion());
var_dump($testsite->isWordPress());
var_dump($testsite->getMethod());

*/

class WP_Detection {
	var $siteurl = '';
	var $wordpress = false;
	var $version = '';
	var $content = '';
	var $feedcontent = '';
	var $username = '';
	var $password = '';
	var $method = '';

	function WP_Detection() {
		$args = func_get_args();
		call_user_func_array( array(&$this, '__construct'), $args );
	}

	// passing in a username and password to the site will make it try to get version via xml-rpc
	function __construct( $url = '', $user = '', $pass = '' ) {
		
		add_action('wp_detection_checks',array(&$this, 'xmlrpc_check'),1);
		add_action('wp_detection_checks',array(&$this, 'get_content'),10);
		add_action('wp_detection_checks',array(&$this, 'meta_check'),15);
		add_action('wp_detection_checks',array(&$this, 'get_feed_content'),20);
		add_action('wp_detection_checks',array(&$this, 'feed_gen_check'),25);
		add_action('wp_detection_checks',array(&$this, 'readme_check'),40);
		
		if ( !empty($url) ) {
			$this->doit($url, $user, $pass);
		}
	}
	
	function isWordPress() {
		return $this->wordpress;
	}

	function getVersion() {
		return $this->version;
	}

	function getMethod() {
		return $this->method;
	}
	
	function doit( $url, $user = '', $pass = '' ) {
		$this->siteurl = $url;
		$this->username = $user;
		$this->password = $pass;
		do_action('wp_detection_checks');
	}
	
	function xmlrpc_check() {
		if ( !empty($this->version) || empty($this->siteurl) ) return;
		if ( empty ($this->username) || empty($this->password) ) return;
		
		include_once(ABSPATH . WPINC . '/class-IXR.php');
		include_once(ABSPATH . WPINC . '/class-wp-http-ixr-client.php');
		
		$client = new WP_HTTP_IXR_Client(trailingslashit($this->siteurl). 'xmlrpc.php');
		$client->query('wp.getOptions', 0, $this->username, $this->password, 'software_version');
		
		if ($client->isError()) return;
		
		$response = $client->getResponse();
		$this->version = $response['software_version']['value'];
		$this->wordpress = true;
		$this->method = 'xmlrpc';
	}
	
	function get_content() {
		if ( !empty($this->content) || empty($this->siteurl) ) return;
		$this->content = wp_remote_retrieve_body(wp_remote_get($this->siteurl));
		
		if ( empty($this->content) ) 
			$this->siteurl = ''; // blank it out to prevent later filters from trying to get stuff from it
	}
	
	function get_feed_content() {
		if ( empty($this->content) || !empty($this->version) || empty($this->siteurl) ) return;
		
		if ( preg_match_all('/<link (.+?)>/', $this->content, $matches) ) {
			$link = array();
			foreach ($matches[1] as $match) {
				foreach ( wp_kses_hair($match, array('http')) as $attr) {
					$link[$attr['name']] = $attr['value'];
				}

				if ($link['type'] == 'application/rss+xml') 
					break;
				else 
					$link = array();
			}
		}

		if ( !empty($link) && !empty($link['href']) ) {
			$this->feedcontent = wp_remote_retrieve_body(wp_remote_get($link['href']));
		} else { 
			// try just adding /feed to the url.. worth a shot
			$this->feedcontent = wp_remote_retrieve_body(wp_remote_get(trailingslashit($this->siteurl). 'feed'));
		}
	}
	
	function meta_check() {
		if ( empty($this->content) || !empty($this->version) ) return;
		
		if ( preg_match('/generator.*content=[\'"]([^\'"]*)[\'"]/i', $this->content, $matches) ) {
			if (false !== stripos($matches[1], 'WordPress')) {
				$a = explode(' ', $matches[1]);
				$this->version = trim($a[1]);
				$this->wordpress = true;
				$this->method = 'meta';
			}
		}
	}
	
	function feed_gen_check() {
		if ( empty($this->feedcontent) || !empty($this->version) ) return;
	
		if ( preg_match('|<generator>([^<]*)</generator>|i', $this->feedcontent, $matches) ) {
			if (false !== stripos($matches[1], 'WordPress')) {
				$a = explode('?v=', $matches[1]);
				$this->version = trim($a[1]);
				$this->wordpress = true;
				$this->method = 'feedgenerator';
			}
		}
	}
	
	function readme_check() {
		if ( empty($this->siteurl) || !empty($this->version) ) return;

		// look for a readme.html
		$readme = wp_remote_retrieve_body(wp_remote_get(trailingslashit($this->siteurl). 'readme.html'));
		
		if ( empty($readme) ) return;
		
		if ( preg_match('|<br /> Version (.*)|', $readme, $matches) ) {
			$this->version = trim($matches[1]);
			$this->wordpress = true;
			$this->method = 'readme';
		}
	}
}
