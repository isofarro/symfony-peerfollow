<?php

require_once dirname(__file__) . '/php5-http-utils/TwitterApi.php';
require_once dirname(__file__) . '/php5-http-utils/HttpUtils.php';
require_once dirname(__file__) . '/php5-http-utils/HttpCache.php';
require_once dirname(__file__) . '/php5-http-utils/HttpRequest.php';
require_once dirname(__file__) . '/php5-http-utils/HttpResponse.php';
require_once dirname(__file__) . '/php5-http-utils/HttpClient.php';

class TwitterManager {
	var $api;

	public function __construct() {
		$this->api = new TwitterApi();
	}

	public function getFollowing($username) {
		echo "Getting people {$username} is following\n";
		
		// TODO: Correct Twitter API method name: getFollowing($username);
		$following	= $this->api->getFriends($username);
		
		return $following;
	}

}

?>