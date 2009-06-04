<?php

class TopicpersonPeer extends BaseTopicpersonPeer
{

	static public function getTopicPerson($topicId, $personId) {
		$criteria = new Criteria();
		
		$criteria->add(TopicpersonPeer::PERSON_ID, $personId);		
		$criteria->add(TopicpersonPeer::TOPIC_ID, $topicId);		

		return self::doSelectOne($criteria);			
	}
}
