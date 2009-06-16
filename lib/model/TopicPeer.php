<?php

class TopicPeer extends BaseTopicPeer
{

	public function getTopic($topicName) {
		$topicCriteria = new Criteria();
		$topicCriteria->add(TopicPeer::SLUG, $topicName);
		
		return TopicPeer::doSelectOne($topicCriteria);
	}

	public function getTopicId($topicName) {
		$topic = self::getTopic($topicName);
		return $topic->getId();	
	}
	
	public function getCommunity($topicName) {
		$topic = TopicPeer::getTopic($topicName);
		$topicId = $topic->getId();
		echo "Topic      : {$topicName} ({$topicId})\n";
	
		$community = new Community($topic);

		// Returns all the people who have tagged themselves with the topic
		$citizenList = PersonPeer::getTopicCitizens($topicId);
		$community->addPeople($citizenList);
		$citizenKeys = $community->getPeopleKeys();
		echo 'Citizens   : ', count($citizenKeys), "\n";

		// Get all the connections inside the community
		$connections = RelationPeer::getCommunityConnections($topicId, $citizenKeys);
		echo 'Connections: ', count($connections), "\n";

		// Maps all the relations into follower/following lists
		$community->addConnections($connections);

		/**
			At this point:
			$community->people is an array of people,
			$community->connections is a 2D hash of connections
		**/
		
		return $community;
	}

}
