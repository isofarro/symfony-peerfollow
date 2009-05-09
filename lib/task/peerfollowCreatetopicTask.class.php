<?php

class peerfollowCreatetopicTask extends sfBaseTask
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
    $this->name             = 'create-topic';
    $this->briefDescription = 'Initialises a new topic';
    $this->detailedDescription = <<<EOF
The [peerfollow:create-topic|INFO] task does things.
Call it with:

  [php symfony peerfollow:create-topic|INFO]
EOF;

		$this->addArgument('topic', sfCommandArgument::REQUIRED, 'The topic to create');
  }

	protected function execute($arguments = array(), $options = array()) {
		// initialize the database connection
		$databaseManager = new sfDatabaseManager($this->configuration);
 		$connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

		// add your code here
		$topic = $arguments['topic'];
		
		$topicObj = new Topic();
		$topicObj->setName($topic);
		$topicObj->setSlug($topic);
		$topicObj->save();
		
		
		$manager = new TopicManager();
		
		$people = $manager->getSelfTaggers($topic);
		
		$this->processPeople($topicObj, $people);
  }
  
  	public function processPeople($topic, $people) {
		//print_r($people);
		
		$personCriteria = new Criteria();
		$tagCriteria    = new Criteria();
		
		$count = 0;
		
		foreach($people as $personObj) {

			// Check whether this person already exists
			$personCriteria->add(
				PersonPeer::USERNAME, 
				$personObj->username, 
				Criteria::EQUAL
			);
			$num = PersonPeer::doCount($personCriteria);
		
			if ($num>0) {
				continue;
			}
			
			$person = new Person();
			$person->fromArray(array(
				'Username'    => $personObj->username,
				'Image'       => $personObj->image,
				'NoFollowers' => $personObj->followers,
				'Status'      => 'N'
			));
			
			if (!empty($personObj->bio)) {
				$person->setBio($personObj->bio);
			}
		
			if (!empty($personObj->fullname)) {
				$person->setFullname($personObj->fullname);
			}

			if (!empty($personObj->website)) {
				$person->setWebsite($personObj->website);
			}
			
			// Save their tags
			foreach($personObj->tags as $tagObj) {
				$tag = new Tag();
				$tag->setSlug($tagObj);
				
				$personTag = new Persontag();

				$tagCriteria->add(TagPeer::SLUG, $tagObj);
				$existingTag = TagPeer::doSelectOne($tagCriteria);
				
				if ($existingTag) {
					//print_r($existingTag);
					$personTag->setTag($existingTag);
				} else {
					$newTag = new Tag();
					$newTag->setSlug($tagObj);
					$personTag->setTag($newTag);
				}
				
				$person->addPersonTag($personTag);

			}

			
			$tp = new Topicperson();
			$tp->setPerson($person);
			$tp->setTopic($topic);

			$person->addTopicperson($tp);			
			$person->save();
			
			echo '.';
			$count++;
		}		

		echo "\nAdded {$count} new people\n";
  	}


}
