<?php

class Community {
	const FOLLOWER  = 1;
	const FOLLOWING = 2;
	const FRIEND    = 4;

	var $topic;
	var $people      = array();
	var $peopleKeys  = array();
	var $connections = array();

	// For network set calculations
	var $network = array();
	

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
		foreach($people as $person) {
			$this->people[$person->getId()] = $person;
			$this->peopleKeys[] = $person->getId();
			$this->network[$person->getId()] = $this->createNode($person, 1000);
		}
	}
	
	public function getPeopleKeys() {
		return $this->peopleKeys;
	}

	public function getPerson($key) {
		return $this->people[$key];
	}

	public function addConnections($connections) {
		if (is_array($connections)) {
			foreach($connections as $connection) {
				$this->addConnection(
					$connection->getPersonId(),
					$connection->getFollowingId()
				);
			}
		}
	}
	
	public function addConnection($fromId, $toId) {
		if ($this->isPerson($fromId) && $this->isPerson($toId)) { 
			$this->addRelationship($fromId, $toId, self::FOLLOWING);
			return true;
		}
		return false;
	}
	
	public function isPerson($personId) {
		return !empty($this->people[$personId]);
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


	public function addRelationship($person1, $person2, $relationship) {
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
		
		if (!empty($this->network[$person1])) {
			$this->network[$person1]->edges[] = $person2;
		} else {
			echo "WARN: No network node for {$person1}\n";
		}
 	}
	
	protected function createNode($person, $bonus = 0) {
		$node = new Node();
		
		$node->nodeId = $person->getId();
		$node->name   = $person->getUsername();
		$node->accum  += $bonus;
		
		return $node;
	}
}

class Node {
	var $nodeId = 0;
	var $name   = '';
	var $rank   = 0;
	var $accum  = 0;

	var $edges  = array();
}

?>