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
    
	$limit = -1;
    
	foreach($people as $person) {
		//echo " * ", $person->getUsername(), ': ', $person->getFullname(), "\n";
		$following = $twitter->getFollowing($person->getUsername());

		if (is_null($following)) {
			echo "INFO: Reached our Twitter request ratelimit.\n";
			break;
		} 

		$total = $this->_processFollowing($person->getId(), $following);
		
		echo "* ", $person->getUsername(), ": added {$total} new relationships\n";
		$person->setStatus('A');
		$person->save();

		$limit--;
		if ($limit==0) { break; }

    }
  }
  
  protected function _processFollowing($person_id, $following) {
		$personCriteria = new Criteria();
		
		$count = 0;
		
		foreach($following as $followingObj) {
			$personCriteria->add(
				PersonPeer::USERNAME, 
				$followingObj->username,
				Criteria::EQUAL
			);
			$follower = PersonPeer::doSelectOne($personCriteria);
			
			if ($follower) {
				$relationCriteria = new Criteria();
				$relationCriteria->add(
					RelationPeer::PERSON_ID,
					$person_id,
					Criteria::EQUAL
				);
				$relationCriteria->add(
					RelationPeer::FOLLOWING_ID,
					$follower->getId(),
					Criteria::EQUAL
				);
				$num = RelationPeer::doCount($relationCriteria);
				
				if ($num==0) {
					$relation = new Relation();
					$relation->setPersonId($person_id);
					$relation->setPersonRelatedByFollowingId($follower);
					$relation->save();
					$count++;
				}				
			}
		}
		
		return $count;
  }
}
