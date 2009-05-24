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
		foreach($citizenList as $citizen) {
			//echo $citizen->getUsername(), ', ';
			$citizenObj = (object) NULL;
			$citizenObj->username  = $citizen->getUsername();
			$citizenObj->followers = array();
			$citizenObj->friends   = array();
			
			$citizens[$citizen->getId()] = $citizenObj;
			$citizenKeys[]               = $citizen->getId();
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


		foreach($links as $rel=>$relList) {
			echo str_pad($citizens[$rel]->username, 16), ': ';

			$followers = count($relList['followers']);
			$following = count($relList['following']);
			$ratio = 0;
			if ($following!==0) {
				$ratio     = (float) $followers / $following;
			}
			
			echo str_pad($followers, 4, ' ', STR_PAD_LEFT), ' ';
			echo str_pad($following, 4, ' ', STR_PAD_LEFT), ' ';
			echo 'Score: ', $ratio;
//			foreach($fromLinks as $to=>$value) {
//				echo $value;
//			}
			echo "\n";
		}

  }

}
