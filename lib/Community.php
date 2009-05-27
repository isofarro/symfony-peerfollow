<?php

class Community {
	static $FOLLOWER  = 1;
	static $FOLLOWING = 2;
	static $FRIEND    = 4;

	var $topic;
	var $people;
	var $connections = array();

	public function __construc($topic=false) {
		if ($topic) {
			$this->setTopic($topic);
		}
	}

	public function setTopic($topic) {
		$this->topic = $topic;
	}
	
	public function getTopic() {
		return $this->topic;
	}


	// TODO: Getters, setters and aggregates for:
	// topic, people, connections

	public function isFollowing($person1, $person2) {
	
	}
	
	public function isFollowedBy($person1, $person2) {
	
	}
	
	public function areFriends($person1, $person2) {
	
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