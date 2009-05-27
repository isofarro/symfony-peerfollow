<?php

class Community {
	const FOLLOWER  = 1;
	const FOLLOWING = 2;
	const FRIEND    = 4;

	var $topic;
	var $people      = array();
	var $peopleKeys  = array();
	var $connections = array();


	public function __construct($topic=false) {
		if ($topic) {
			$this->setTopic($topic);
		}
	}

	public function setTopic($topic) {
		if (is_a($topic, 'Topic')) {
			$this->topic = $topic;
			return true;
		}
		return false;
	}
	
	public function getTopic() {
		return $this->topic;
	}

	public function addPeople($people) {
		//$this->people = array_merge($this->people, $people);
		foreach($people as $person) {
			$this->people[$person->getId()] = $person;
			$this->peopleKeys[] = $person->getId();
		}
	}
	
	public function getPeopleKeys() {
		return $this->peopleKeys;
	}

	public function addConnections($connections) {
		if (is_array($connections)) {
			foreach($connections as $connection) {
				$this->addRelationship(
					$connection->getPersonId(),
					$connection->getFollowingId(),
					self::FOLLOWING
				);
			}
		}
	}

	public function isFollowing($person1, $person2) {
		return (!empty($this->connections[$person1][$person2]));
	}
	
	public function isFollowedBy($person1, $person2) {
		return (!empty($this->connections[$person2][$person1]));
	}
	
	public function areFriends($person1, $person2) {
		return (
			!empty($this->connections[$person1][$person2]) &&
			!empty($this->connections[$person2][$person1])
		);
	}


	protected function addRelationship($person1, $person2, $relationship) {
		switch($relationship) {
			case self::FOLLOWER:
				$this->connectFollower($person1, $person2);
				break;
			case self::FOLLOWING:
				$this->connectFollower($person2, $person1);
				break;
			case self::FRIEND:
				$this->connectFollower($person1, $person2);
				$this->connectFollower($person2, $person1);
				break;
			default:
				break;
		}
	}
	
	protected function connectFollower($person1, $person2) {
		if (empty($this->connections[$person1])) {
			$this->connections[$person1] = array();
		}
		$this->connections[$person1][$person2] = 1;
	}
}

?>