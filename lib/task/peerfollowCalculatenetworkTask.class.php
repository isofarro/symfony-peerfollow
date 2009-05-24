<?php

class peerfollowCalculatenetworkTask extends sfBaseTask
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
    $this->name             = 'calculate-network';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [peerfollow:calculate-network|INFO] task does things.
Call it with:

  [php symfony peerfollow:calculate-network|INFO]
EOF;

		$this->addArgument('topic', sfCommandArgument::REQUIRED, 'The topic to create');
  }

	protected function execute($arguments = array(), $options = array()) {
		// initialize the database connection
		$databaseManager = new sfDatabaseManager($this->configuration);
 		$connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

		// add your code here
		$topic   = $arguments['topic'];
		$topicId = TopicPeer::getTopicId($topic);
		echo "Topic    : {$topic} ({$topicId})\n";

		$citizenList = PersonPeer::getTopicCitizens($topicId);
		
		$citizens    = array();
		$citizenKeys = array();
		$keyIdx      = 0;
		foreach($citizenList as $citizen) {
			//echo $citizen->getUsername(), ', ';
			$citizenObj = (object) NULL;
			$citizenObj->username  = $citizen->getUsername();
			$citizenObj->followers = array();
			$citizenObj->friends   = array();
			$citizenObj->key       = $keyIdx;
			
			$citizens[$citizen->getId()] = $citizenObj;
			$citizenKeys[$keyIdx]        = $citizen->getId();
			$keyIdx++;
		}
		//print_r($citizenList);
		echo 'Citizens : ', count($citizens), "\n";

		// TODO: Is friends a duplication of followers if limited to community?
		//$friends   = RelationPeer::getCommunityFriends($topicId, $citizenKeys);
		//echo 'Friends  : ', count($friends), "\n";

		$followers = RelationPeer::getCommunityFollowers($topicId, $citizenKeys);
		echo 'Followers: ', count($followers), "\n";


		$manager = new TopicManager();
		$links = $manager->calculateLinks($followers);
		//print_r($links);
		$community = array();

		foreach($links as $rel=>$relList) {
			$person = (object) NULL;
			$person->id        = $rel;
			$person->username  = $citizens[$rel]->username;
			$person->followers = $relList['followers']; 
			$person->following = $relList['following']; 
			$person->stats     = (object) NULL;
			
			sort($person->followers);
			sort($person->following);

			$person->stats->followers = count($relList['followers']);
			$person->stats->following = count($relList['following']);
			$ratio = '++';
			if ($person->stats->following!==0) {
				$ratio = (int)(100 * $person->stats->followers / $person->stats->following);
			} else {
				$ratio = $person->stats->followers;
			}

			if ($ratio > 60 && $person->stats->followers > 3) {
				echo str_pad($person->username, 16), ': ';

				echo 'In:',   str_pad($person->stats->followers, 4, ' ', STR_PAD_LEFT), ' ';
				echo 'Out: ', str_pad($person->stats->following, 4, ' ', STR_PAD_LEFT), ' ';
				echo 'In-Score: ', $ratio, "\n";

				if (!empty($person->followers)) {
					echo "\tFollowed by: ";
					foreach($person->followers as $fid) {
						$citizen = $citizens[$fid]->username;
						echo $citizen, ', ';
					}
					echo "\n";
				}
							
				if (!empty($person->following)) {
					echo "\tFollowing  : ";
					foreach($person->following as $fid) {
						$citizen = $citizens[$fid]->username;
						echo $citizen, ', ';
					}
					echo "\n";
				}

				echo "\n";
			}
							
			$community[] = $person;
		}

  }

}
