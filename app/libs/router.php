<?php

class Router {

	public $protocol;	// Website protocol: http:// or https://
	public $url;		// Full actual URL
	public $domain;		// Domain, eg: domain.com
	public $site_path;	// Website path, eg: http://www.domain.com/website
	public $request;	// Array of reqested modules, eg.: /admin/login
	public $query;		// Array of requested query, eg.: ?foo=bar&baz=lorem


	# ==============================================================================
	# CONSTRUCTOR
	# ==============================================================================

	public function __construct() {

		// Set website protocol
		$this->protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';

		// Set full actual URL, eg.: http://domain.com/website/request/?query=true
		$this->url = $this->protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		// Set website domain, eg.: domain.com
		$this->domain = $_SERVER['HTTP_HOST'];

		// Create website URL, eg.: http://domain.com/website
		$this->site_path = $this->protocol . $_SERVER['SERVER_NAME'] . dirname($_SERVER["SCRIPT_NAME"]);

		$dirname = basename($this->site_path); // Return string after domain from physical URL, eg.: 'website' from http://domain.com/website/request
		$requested_str = $_SERVER["REQUEST_URI"];

		// Check if website is in subdirectory.
		// If yes remove everything from start to occurence of subfolder name
		$pos = strpos($requested_str, $dirname); // Return occurence of 'website' in requested URI
		if ($pos !== false) {
			$requested_str = substr($requested_str, $pos + strlen($dirname));
		}

		$requested_str = explode('?', $requested_str);
		$this->request = array_values(array_filter(explode('/', $requested_str[0]))); // Get request params

		// Get query params
		if (isset($requested_str[1])) parse_str($requested_str[1], $this->query);
	}


	# ==============================================================================
	# MOVE REQUESTS FORWARD
	# ==============================================================================

	public function request_shift() {
		return array_shift($this->request);
	}


	# ==============================================================================
	# CHECK IF DOMAIN HAS WWW. IN FRONT AND IF NO REDIRECT
	# ==============================================================================

	public function redirect_to_www() {
		$domain = explode('.', $this->domain);
		if (!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')) && $domain[0] != 'www') {
			header('location: ' . str_replace($this->protocol, $this->protocol . 'www.', $this->url));
		}
	}

}