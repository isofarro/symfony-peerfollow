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
			
			if (empty($links[$from])) {
				$links[$from] = array();
			}
			if (empty($links[$from][$to])) {
				$links[$from][$to] = 0;
			}
			$links[$from][$to] = 1;
		}
	
		return $links;
	}
}

?>