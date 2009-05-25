<?php

require_once 'TopicManager.php';

$serFile = '/home/user/data/peerfollow/community.ser';
$xmlFile = '/home/user/data/peerfollow/community.xml';

$ser = file_get_contents($serFile);
$community = unserialize($ser);

$manager = new TopicManager();
$doc = $manager->renderGraphML('accessibility', $community);

echo $doc, "\n";
file_put_contents($xmlFile, $doc);

?>