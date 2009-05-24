<?php

class TopicManager {

	public function getSelfTaggers($topic) {
		echo "Getting self-taggers: $topic\n";
		
		$api = new WeFollowApi();

		$people = array();
		
		$page = $api->getTaggedPeople($topic);
		$people = array_merge($people, $page);
		
		while($api->hasNext()) {
			$page = $api->next();
			$people = array_merge($people, $page);
		}
		
		
		//print_r($people);
		return $people;	
	}


	public function calculateLinks($relations) {
		$links = array();
		
		foreach($relations as $relation) {
			$from = $relation->getPersonId();
			$to   = $relation->getFollowingId();
			
			// Increment following count
			if (empty($links[$from])) {
				$links[$from] = array(
					'following' => array(),
					'followers' => array()
				);
			}
			$links[$from]['following'][] = $to;
			
			// Increment follower count
			if (empty($links[$to])) {
				$links[$to] = array(
					'following' => array(),
					'followers' => array()
				);
			}
			$links[$to]['followers'][] = $from;
		}
	
		return $links;
	}
}

?>