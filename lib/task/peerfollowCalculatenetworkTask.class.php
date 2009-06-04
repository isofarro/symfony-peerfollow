<?php

class peerfollowCalculatenetworkTask extends sfBaseTask
{
	protected function configure() {
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
The [peerfollow:calculate-network|INFO] calculates the peer rank for all 
people in the selected network, or community.
Call it with:

  [php symfony peerfollow:calculate-network community-name|INFO] 
EOF;

		$this->addArgument('topic', sfCommandArgument::REQUIRED, 'The topic to create');
	}

	protected function execute($arguments = array(), $options = array()) {
		// initialize the database connection
		$databaseManager = new sfDatabaseManager($this->configuration);
 		$connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

		

		// Get the topic id - this will save complicated joins in other queries
		$topicName   = $arguments['topic'];
		$community = $this->getCommunity($topicName);


		// Serialise this data to a file, for development.
		$ser = serialize($community);
		file_put_contents('/home/user/data/peerfollow/community-obj.ser', $ser);

		//print_r($community->network);

		$manager = new CommunityManager();
		$manager->calculateNetworkRank($community->network);

		$this->displayResults($community->network);

		// Save the results
	}


	protected function displayResults($network) {
		$karmaTotal = 0;
		$ranked = array();

		foreach($network as $node) {
			$karmaTotal += $node->rank;
			
			if (empty($ranked[$node->rank])) {
				$ranked[$node->rank] = array();
			}
			$ranked[$node->rank][] = $node->nodeId;
		}
		
		echo "Karma total: {$karmaTotal} ~ ", 
			(int)($karmaTotal / count($network)), "\n";

		krsort($ranked, SORT_NUMERIC);
		echo "\n";
		foreach ($ranked as $key=>$list) {
			
			/**
				150 - the rank every node receives for free
				 93 - the number of nodes in the network
				 
				 TODO: Break the range into 11 segments where
				 150  is section 0
				 1000 is section 5
				 
				 score = 2^(rank-offset)
			**/
/****
			$log = (log($key - 150, 93) - 1) * 10;
			$log = max( 0, $log);
			$log = min(10, $log);
			$log = round($log);
****/
			// Try base 2 logs
			//$log = round(log($key, 2), 2) - 7;
			$percent = ( ($key-150) * 100 / 92462 ); // attention score
			$log     = floor(log($percent, 2) + 5);
			$log     = min( 10, max( 0, $log) );

			/****
				0.85 - 1 ==> 5
				8.4      ==> 9 or 10
				4.2
				2.1
				1.05
			****/

			foreach($list as $citizenId) {
				$node = $network[$citizenId];

				echo str_pad($key, 5, ' ', STR_PAD_LEFT), ' ',
					'(', str_pad($log, 2, ' ', STR_PAD_LEFT), ') ',
					str_pad($node->name, 16), ' ',
					'[', str_pad(count($node->inbound), 3, ' ', STR_PAD_LEFT), ']',
					'[', str_pad(count($node->twoway),  3, ' ', STR_PAD_LEFT), ']', 
					"\n";
			}

			
		}
	
	}

	protected function getCommunity($topicName) {
		$topic = TopicPeer::getTopic($topicName);
		$topicId = $topic->getId();
		echo "Topic      : {$topicName} ({$topicId})\n";

		$community = new Community($topic);

		// Returns all the people who have tagged themselves with the topic
		$citizenList = PersonPeer::getTopicCitizens($topicId);
		$community->addPeople($citizenList);
		$citizenKeys = $community->getPeopleKeys();
		echo 'Citizens   : ', count($citizenKeys), "\n";

		// Get all the connections inside the community
		$connections = RelationPeer::getCommunityConnections($topicId, $citizenKeys);
		echo 'Connections: ', count($connections), "\n";

		// Maps all the relations into follower/following lists
		$community->addConnections($connections);

		/**
			At this point:
			$community->people is an array of people,
			$community->connections is a 2D hash of connections
		**/
		
		return $community;
	}

}
