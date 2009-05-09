<?php

class TopicManager {

	public function getSelfTaggers($topic) {
		echo "Getting self-taggers: $topic\n";
		
		$api = new WeFollowApi();

		$people = array();
		
		// TODO: Temporary while wifi sucks
		$file = '/home/isofarro/Documents/savedPages/wefollow-accessibility.html';
		
		$page = $api->getTaggedPeople($file); //$topic);
		$people = array_merge($people, $page);
		
		// TODO: try when we have wifi
		/****
		while($api->hasNext()) {
			$page = $api->next();
		}
		****/
		
		
		//print_r($people);
		return $people;	
	}

}

?>