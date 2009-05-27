<?php

require_once dirname(__file__) . '/php5-http-utils/TwitterApi.php';
require_once dirname(__file__) . '/php5-http-utils/HttpUtils.php';
require_once dirname(__file__) . '/php5-http-utils/HttpCache.php';
require_once dirname(__file__) . '/php5-http-utils/HttpRequest.php';
require_once dirname(__file__) . '/php5-http-utils/HttpResponse.php';
require_once dirname(__file__) . '/php5-http-utils/HttpClient.php';

class TwitterManager {

	public function getFollowing($username) {
		echo "Getting people {$username} is following\n";
		
		$api = new TwitterApi();

		// TODO: Correct Twitter API method name: getFollowing($username);
		$following	= $api->getFriends($username);
		
		return $following;
	}

}

?>