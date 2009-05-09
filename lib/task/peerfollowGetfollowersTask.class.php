<?php

class peerfollowGetfollowersTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      // add your own options here
    ));

    $this->namespace        = 'peerfollow';
    $this->name             = 'get-followers';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [peerfollow:get-followers|INFO] task does things.
Call it with:

  [php symfony peerfollow:get-followers|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array()) {
	// initialize the database connection
	$databaseManager = new sfDatabaseManager($this->configuration);
	$connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

	// add your code here
	$personCriteria = new Criteria();
	$personCriteria->add(PersonPeer::STATUS, 'N', Criteria::EQUAL);
	$people = PersonPeer::doSelect($personCriteria);
    
	$twitter = new TwitterManager();
    
	$limit = 5;
    
	foreach($people as $person) {
		//echo " * ", $person->getUsername(), ': ', $person->getFullname(), "\n";
		$friends = $twitter->getFollowers($person->getUsername());
		$total = $this->_processFriends($person->getId(), $friends);
		
		echo "\n* ", $person->getUsername(), ": added {$total} new relationships\n";
		$person->setStatus('A');
		$person->save();

		$limit--;
		
    	
		if ($limit==0) { break; }
    }
  }
  
  protected function _processFriends($person_id, $friends) {
		$personCriteria = new Criteria();
		
		$count = 0;
		
		foreach($friends as $friendObj) {
			$personCriteria->add(
				PersonPeer::USERNAME, 
				$friendObj->username,
				Criteria::EQUAL
			);
			$friend = PersonPeer::doSelectOne($personCriteria);
			
			if ($friend) {
				$relationCriteria = new Criteria();
				$relationCriteria->add(
					RelationPeer::PERSON_ID,
					$person_id,
					Criteria::EQUAL
				);
				$relationCriteria->add(
					RelationPeer::FOLLOWING_ID,
					$friend->getId(),
					Criteria::EQUAL
				);
				$num = RelationPeer::doCount($relationCriteria);
				
				if ($num==0) {
					$relation = new Relation();
					$relation->setPersonId($person_id);
					$relation->setPersonRelatedByFollowingId($friend);
					$relation->save();
					$count++;
				}				
			}
		}
		
		return $count;
  }
}
