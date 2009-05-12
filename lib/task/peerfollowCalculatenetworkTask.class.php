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
		$topic = $arguments['topic'];
		
		// Get all the relationships in a topic
		$relations = RelationPeer::doSelect(new Criteria());
		
		$manager = new TopicManager();
		
		$links = $manager->calculateLinks($relations);
		//print_r($links);
		
		foreach($links as $from=>$fromLinks) {
			echo "{$from}: ";
			foreach($fromLinks as $to=>$value) {
				echo $value;
			}
			echo "\n";
		}
  }

}
