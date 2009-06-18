<?php

require_once dirname(__file__) . '/php5-http-utils/TwitterApi.php';
require_once dirname(__file__) . '/php5-http-utils/CanonicalLink.php';
require_once dirname(__file__) . '/php5-http-utils/HttpUtils.php';
require_once dirname(__file__) . '/php5-http-utils/HttpCache.php';
require_once dirname(__file__) . '/php5-http-utils/HttpRequest.php';
require_once dirname(__file__) . '/php5-http-utils/HttpResponse.php';
require_once dirname(__file__) . '/php5-http-utils/HttpClient.php';

class TwitterManager {
	var $api;
	var $community; // For measuring peers

	// Search map data structures
	var $canonLink;
	var $linkMap     = array(); // Lookup of unshortened links
	var $redirectMap = array(); // Lookup for link-shorteners

	public function __construct() {
		$this->api       = new TwitterApi();
		$this->canonLink = new CanonicalLink();
	}

	public function getFollowing($username) {
		echo "Getting people {$username} is following\n";
		
		// TODO: Correct Twitter API method name: getFollowing($username);
		$following	= $this->api->getFriends($username);
		echo "\n";
				
		return $following;
	}
	
	/**
		search() gets search results for a searchTerm
		Returns an array of tweets
	**/
	public function search($query, $page=false) {
		$results = $this->api->search($query, $page);
		return $results;
	}
	
	public function addSearchResults($topic, $results) {
		// TODO: formatSearchResults should be in TwitterApi
		$tweets = $this->formatSearchResults($results);
		//print_r($tweets);
		//print_r($tweets[0]);

		$keywords = array( 'accessibility', 'a11y' );

		// Where is the community data coming from?
		//		* getCommunity($topic)? -- optional for weighing tweets
		//		* Community members + topicPerson data.
		//			Still a Community object, just without the network data
		// Look for tweets from peerranked people
		// Look for "RT @user" or "via @user"
		// Check if tweet has been retweeted
		foreach($tweets as $tweet) {
			
			$peer = true; // $this->community->isMember($tweet->id);

			$prefix = '';
			if (false && $this->isRetweet($tweet)) {
				//echo " * Retweet\n";
				$prefix .= 'R';
				//break;
			}
			
			if ($this->isLink($tweet)) {
				$prefix .= 'L';
				//echo "{$prefix}[{$tweet->user}] {$tweet->text}\n";
				$links = $this->getLinks($tweet);
				$this->trackLinks($links);
			}

			if ($this->isKeywordTweet($tweet, $keywords)) {
				$prefix .= 'K';
			}

			//echo "{$prefix}[{$tweet->user}] {$tweet->text}\n";

			if (strpos($prefix, 'L') !== false) {
				echo "{$prefix}[{$tweet->user}] {$tweet->text}\n";
			}

		}
		
		//echo "\n";

/****		
		// Dump linkmap
		foreach($this->linkMap as $link=>$occur) {
			if ($occur>0) {
				echo "({$occur}) $link\n";
			}
		}		
****/		
	}
	
	protected function isRetweet($tweet) {
		if (preg_match('/\b(RT|via) \@\b/i', $tweet->text)) {
			return true;
		}
		return false;
	}

	protected function isLink($tweet) {
		if (preg_match('/\bhttp:\/\//i', $tweet->text)) {
			return true;
		}
		return false;
	}

	protected function isKeywordTweet($tweet, $keywords) {
		$reg = implode('|', $keywords);
		if (preg_match("/#({$reg})\b/i", $tweet->text)) {
			return true;
		}
		return false;
	}
	
	protected function getLinks($tweet) {
		$links = array();

		// TODO: Test this with multiple links
		$numMatches = preg_match_all(
			'/\b(http:\/\/[^ ]+)/i', 
			$tweet->text, 
			$matches
		);		
		
		if ($numMatches) {
			foreach($matches as $singleMatch) {
				$links[] = $singleMatch[0];
			}
		}
		return $links;
	}
	
	protected function trackLinks($links) {
		foreach($links as $link) {
			$this->trackLink($link);
		}
	}

	protected function trackLink($link) {
		// Get canonical links
		//$link = $this->canonLink->getCanonicalLink($link);

		if (empty($this->linkMap[$link])) {
			$this->linkMap[$link] = 1;
		} else {
			$this->linkMap[$link]++;
		}
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

class OLDCanonicalLink {
	var $lookup = array();
	
	public function getCanonicalLink($link) {
		if (!empty($this->lookup[$link])) {
			return $this->lookup[$link];
		}
		
		$canonical = $this->followLink($link);
		if ($link != $canonical) {
			$this->lookup[$link] = $canonical;
		}
		return $canonical;
	}
	
	/**
		Gets the link and figures out whether it's a shortened link
		or a canonical link
		
		* Deal with 301 and 302 redirects
		* Deal with Diggbar/StumbleUpon iframes
	**/
	public function followLink($link) {
		return $link;
	}

}

?>