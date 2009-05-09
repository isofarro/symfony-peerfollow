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

}

?>