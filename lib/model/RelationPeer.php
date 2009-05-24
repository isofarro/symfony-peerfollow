<?php

class RelationPeer extends BaseRelationPeer
{

	public function getCommunityFriends($topicId, $community) {
		$relationCriteria = new Criteria();
		$relationCriteria->addJoin(
			RelationPeer::FOLLOWING_ID,
			TopicpersonPeer::PERSON_ID,
			Criteria::LEFT_JOIN		
		);
		
		$relationCriteria->add(TopicpersonPeer::TOPIC_ID, $topicId);

		$relationCriteria->add(
			RelationPeer::PERSON_ID, 
			$community,
			Criteria::IN
		);
				
		$friends = self::doSelect($relationCriteria);
		return $friends;
	}

	public function getCommunityFollowers($topicId, $community) {
		$relationCriteria = new Criteria();
		$relationCriteria->addJoin(
			RelationPeer::PERSON_ID,
			TopicpersonPeer::PERSON_ID,
			Criteria::LEFT_JOIN		
		);
		
		$relationCriteria->add(TopicpersonPeer::TOPIC_ID, $topicId);

		$relationCriteria->add(
			RelationPeer::FOLLOWING_ID, 
			$community,
			Criteria::IN
		);
				
		$followers = self::doSelect($relationCriteria);
		return $followers;
	}

}
