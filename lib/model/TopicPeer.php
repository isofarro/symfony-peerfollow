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

}
