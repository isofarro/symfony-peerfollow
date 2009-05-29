<?php

require_once 'CommunityManager.php';

$serFile = '/home/user/data/peerfollow/community.ser';
$xmlFile = '/home/user/data/peerfollow/community.xml';

$ser = file_get_contents($serFile);
$community = unserialize($ser);

$manager = new CommunityManager();
$doc = $manager->renderGraphML('accessibility', $community);

echo $doc, "\n";
file_put_contents($xmlFile, $doc);

?>