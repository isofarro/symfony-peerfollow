<?php

class RelationPeer extends BaseRelationPeer
{

	public function getCommunityConnections($topicId, $community) {
		$relationCriteria = new Criteria();
		$relationCriteria->addJoin(
			RelationPeer::PERSON_ID,
			TopicpersonPeer::PERSON_ID,
			Criteria::LEFT_JOIN		
		);
		
		$relationCriteria->add(
			TopicpersonPeer::TOPIC_ID, 
			$topicId
		);

		$relationCriteria->add(
			RelationPeer::FOLLOWING_ID, 
			$community,
			Criteria::IN
		);
				
		$connections = self::doSelect($relationCriteria);
		return $connections;
	}

}
