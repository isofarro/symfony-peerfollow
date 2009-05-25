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
foreach($community as $person) {
	echo str_pad($person->username, 16), ': ', $person->calc->rank, "\n";
	$karmaTotal += $person->calc->rank;
}
echo "Karma total: {$karmaTotal} ~ ", (int)($karmaTotal / count($community)), "\n";
//****/

?>