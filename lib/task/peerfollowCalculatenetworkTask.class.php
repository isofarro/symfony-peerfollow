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
		$topicName   = $arguments['topic'];
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
		
		// Serialise this data to a file, for development.
		$ser = serialize($community);
		file_put_contents('/home/user/data/peerfollow/community-obj.ser', $ser);

		//print_r($community->network);

		$manager = new TopicManager();
		$manager->calculateNetworkRank($community->network);

		$this->displayResults($community->network);

	}


	public function displayResults($network) {
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
			$people = array();
			foreach($list as $citizenId) {
				$people[] = $network[$citizenId]->name;
			}
	
			echo str_pad($key, 5, ' ', STR_PAD_LEFT), ' ',
				implode(', ', $people), "\n";
		}
	
	}

}
