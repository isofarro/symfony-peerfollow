<?php

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