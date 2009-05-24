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

		// Get the topic id - this will save complicated joins in other queries
		$topic   = $arguments['topic'];
		$topicId = TopicPeer::getTopicId($topic);
		echo "Topic    : {$topic} ({$topicId})\n";

		// Returns all the people who have tagged themselves with the topic
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

		// Get all the followers inside the topic
		$followers = RelationPeer::getCommunityFollowers($topicId, $citizenKeys);
		echo 'Followers: ', count($followers), "\n";


		$manager = new TopicManager();

		// Maps all the relations into follower/following lists
		$links = $manager->calculateLinks($followers);
		//print_r($links);
		
		$community = array();

		// Aggregate all the follower data with their respective people.
		// and calculate some basic metrics for each person.
		foreach($links as $rel=>$relList) {
			$person = (object) NULL;
			$person->id        = $rel;
			$person->username  = $citizens[$rel]->username;
			$person->followers = $relList['followers']; 
			$person->following = $relList['following']; 
			$person->stats     = (object) NULL;
			$person->stats->followers = count($relList['followers']);
			$person->stats->following = count($relList['following']);

			// For peer rank calculations
			$person->calc        = (object) NULL;
			$person->calc->rank  = 0;
			$person->calc->accum = 0;

			sort($person->followers);
			sort($person->following);

			$ratio = '++';
			if ($person->stats->following!==0) {
				$ratio = (int)(100 * $person->stats->followers / $person->stats->following);
			} else {
				$ratio = $person->stats->followers;
			}

			if ($ratio > 60 && $person->stats->followers > 3) {
				echo str_pad($person->username, 16), ': ';

				// Display simple metrics of the current person
				echo 'In:',   str_pad($person->stats->followers, 4, ' ', STR_PAD_LEFT), ' ';
				echo 'Out: ', str_pad($person->stats->following, 4, ' ', STR_PAD_LEFT), ' ';
				echo 'In-Score: ', $ratio, "\n";

				// Display who is following the current person
				if (!empty($person->followers)) {
					echo "\tFollowed by: ";
					foreach($person->followers as $fid) {
						$citizen = $citizens[$fid]->username;
						echo $citizen, ', ';
					}
					echo "\n";
				}
							
				// Display who the current person is following
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
							
			$community[$person->id] = $person;
		}

		/**
			At this point:
			$community is an array of people,
			$citizenKey is an array of peopleIds (for later sorting)
		**/
		
		// Serialise this data to a file, for development.
		$ser = serialize($community);
		file_put_contents('/home/user/data/peerfollow/community.ser', $ser);
  }

}
