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
		echo "\n";
				
		return $following;
	}

	public function addSearchResults($topic, $results) {
		$tweets = $this->formatSearchResults($results);
		//print_r($tweets);
		//print_r($tweets[0]);

	}


	public function formatSearchResults($results) {
		$tweets = array();
		foreach($results as $result) {
			$tweets[] = $this->formatSearchResult($result);
		}
		return $tweets;
	}

	public function formatSearchResult($result) {
		$tweet = (object) NULL;
		$tweet->id      = $result->id;
		$tweet->created = $result->created_at;
		$tweet->text    = $result->text;
		$tweet->user    = $result->from_user;

		if (!empty($result->to_user_id)) {
			$tweet->replyToUserId = $result->to_user_id;
		}

		if (!empty($result->iso_language_code)) {
			$tweet->lang = $result->iso_language_code;
		}

		if (!empty($result->profile_image_url)) {
			$tweet->user_image = $result->profile_image_url;
		}

		return $tweet;
	}
}

?>