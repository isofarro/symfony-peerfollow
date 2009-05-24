<?php

class PersonPeer extends BasePersonPeer
{

	public function getTopicCitizens($topicId) {
		$personCriteria = new Criteria();
		
		$personCriteria->addJoin(
			PersonPeer::ID,
			TopicpersonPeer::PERSON_ID,
			Criteria::LEFT_JOIN
		);
		
		$personCriteria->add(TopicpersonPeer::TOPIC_ID, $topicId);		
		$citizens = PersonPeer::doSelect($personCriteria);

		return $citizens;
	}
}
