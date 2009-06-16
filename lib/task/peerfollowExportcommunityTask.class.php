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
		$this->briefDescription = 'Exports a community to JSON';
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
		$community = $this->getCommunity($topicName);

		$export = (object) NULL;
		$export->topic = $topicName;
		$export->members = array();
		
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

			$export->members[] = $member;
		}

		//print_r($community->people[5]);
		print_r($export->members[6]);
	}

	protected function getCommunity($topicName) {
		// TODO: refactor this into a separate non-task class
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
