<?php

class TopicPeer extends BaseTopicPeer
{

	public function getTopicId($topic) {
		$topicCriteria = new Criteria();
		$topicCriteria->add(TopicPeer::SLUG, $topic);
		
		$topic = TopicPeer::doSelectOne($topicCriteria);
		return $topic->getId();	
	}
}
