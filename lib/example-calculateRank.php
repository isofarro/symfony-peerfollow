<?php

require_once 'TopicManager.php';
require_once 'Community.php';

$ser = file_get_contents('/home/user/data/peerfollow/community-obj.ser');
$community = unserialize($ser);

//print_r($community->network);

$manager = new TopicManager();
$manager->calculateNetworkRank($community->network);

$karmaTotal = 0;
$ranked = array();

//print_r($community->network);

foreach($community->network as $node) {
	//echo str_pad($person->username, 16), ': ', $person->calc->rank, "\n";
	$karmaTotal += $node->rank;
	
	if (empty($ranked[$node->rank])) {
		$ranked[$node->rank] = array();
	}
	$ranked[$node->rank][] = $node->nodeId;
}

echo "Karma total: {$karmaTotal} ~ ", 
	(int)($karmaTotal / count($community->network)), "\n";

krsort($ranked, SORT_NUMERIC);

foreach ($ranked as $key=>$list) {
	$people = array();
	foreach($list as $citizenId) {
		$people[] = $community->network[$citizenId]->name;
	}
	
	echo str_pad($key, 5, ' ', STR_PAD_LEFT), ' ',
		implode(', ', $people), "\n";
}

?>