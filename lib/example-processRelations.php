<?php

require_once 'CommunityManager.php';

$serFile = '/home/user/data/peerfollow/community.ser';
$xmlFile = '/home/user/data/peerfollow/community-friends.xml';

$ser = file_get_contents($serFile);
$community = unserialize($ser);

$manager = new CommunityManager();
$rel = $manager->processRelations($community);

//print_r($rel);

$doc = $manager->generateGraphML($rel);
echo $doc, "\n";
file_put_contents($xmlFile, $doc);

?>