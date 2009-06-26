<?php

class peerfollowExportcommunityTask extends sfBaseTask
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
		$this->name             = 'export-community';
		$this->briefDescription = 'Exports a community to a serialised object';
		$this->detailedDescription = <<<EOF
The [peerfollow:export-community|INFO] exports the network data
for the specified community.
Call it with:

	[php symfony peerfollow:export-community community-name|INFO]
EOF;

		$this->addArgument('topic', sfCommandArgument::REQUIRED, 'The community to export');
	}

	protected function execute($arguments = array(), $options = array()) {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();


		// Get the topic id - this will save complicated joins in other queries
		$topicName   = $arguments['topic'];
		$community = TopicPeer::getCommunity($topicName);
		
		//print_r($community);

		$export = (object) NULL;
		$export->topic = $topicName;
		$export->members = array();
		
		$lookup = array();
		
		foreach($community->people as $person) {
			//echo $person->getUsername(), "\n";

			$member = (object) NULL;
			$member->username    = $person->getUsername();
			$member->fullname    = $person->getFullname();
			
			if ($person->getBio()) {
				$member->bio      = $person->getBio();
			}
			
			if ($person->getWebsite()) {
				$member->website  = $person->getWebsite();
			}

			//if ($person->getLocation()) {
			//	$member->location = $person->getLocation();
			//}

			$member->image       = $person->getImage();
			$member->noFollowers = $person->getNoFollowers();
			//$member->noFriends   = $person->getNoFriends();

			// Create connections
			$member->inbound  = array();
			$member->outbound = array();
			
			$node = $community->network[$person->getId()];
			foreach($node->inbound as $nodeId) {
				$member->inbound[] = $community->network[$nodeId]->name;
			}

			foreach($node->outbound as $nodeId) {
				$member->outbound[] = $community->network[$nodeId]->name;
			}

			$export->members[$member->username] = $member;
			$lookup[$person->getId()] = $member->username;
		}
		
		// Tackle the connections

		//print_r($community->people[5]);
		//print_r($export->members['laura_carlson']);
		//print_r($lookup);
		
		$filename = "/tmp/{$export->topic}.ser";
		$ser = serialize($export);
		file_put_contents($filename, $ser);
		echo "Exported to {$filename} (", strlen($ser), " bytes)\n";
	}

}
