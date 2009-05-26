<?php

class Community {
	static $FOLLOWER  = 1;
	static $FOLLOWING = 2;

	var $name;
	var $people;
	var $connections = array();

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