<?php

require_once 'TopicManager.php';

$ser = file_get_contents('/home/user/data/peerfollow/community.ser');
$community = unserialize($ser);


foreach($community as $person) {
	$person->calc->accum = 1000;
}
//print_r($community);

$manager = new TopicManager();

$manager->calculateCommunityRank($community);

//****
$karmaTotal = 0;
$ranked = array();
foreach($community as $person) {
	//echo str_pad($person->username, 16), ': ', $person->calc->rank, "\n";
	$karmaTotal += $person->calc->rank;
	
	if (empty($ranked[$person->calc->rank])) {
		$ranked[$person->calc->rank] = array();
	}
	$ranked[$person->calc->rank][] = $person->username;
}
echo "Karma total: {$karmaTotal} ~ ", (int)($karmaTotal / count($community)), "\n";
//****/

krsort($ranked, SORT_NUMERIC);

foreach ($ranked as $key=>$list) {
	echo str_pad($key, 5, ' ', STR_PAD_LEFT), ' ',
		implode(',', $list), "\n";
}


?>