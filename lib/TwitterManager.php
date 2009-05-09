<?php

class TwitterManager {
	// TODO: fix symfony task to get Friends.
	public function getFollowers($username) {
		echo "Getting twitter followers for {$username}\n";
		
		$api = new TwitterApi();

		$friends	= $api->getFriends($username);
		
		return $friends;
	}

}

?>