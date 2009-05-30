<?php

include(dirname(__file__).'/../bootstrap/unit.php');
require_once(dirname(__file__).'/../../lib/Community.php');

require_once(dirname(dirname(__file__)).'/mock/PersonMock.php');
require_once(dirname(dirname(__file__)).'/mock/TopicMock.php');

$t = new lime_test(99, new lime_output_color());

$t->diag('Community');

$community = new Community();
$t->isa_ok($community, 'Community', 'new Community() is of class Community');
$t->is($community->topic, NULL, 'new Community(), checking topic is NULL');
$t->ok(empty($community->people), 'new Community checking people is empty');


$topic = createTopic(1, 'accessibility');
$community = new Community($topic);
$t->isa_ok($community, 'Community', 'new Community() with a Topic constructor');

$ret = $community->setTopic($topic);
$t->ok($ret, 'setTopic() accepts a Topic object');

$ret = $community->setTopic((object)NULL);
$t->ok(!$ret, 'setTopic() doesn\'t accept a plain data object');

$people = createPeople(5);

$community->addPeople($people);
$t->is(count($community->people), 5, 'addPeople() updates people attribute correctly');

$t->ok($community->isPerson(1), 'isPerson() returns true when person id exists');
$t->ok(!$community->isPerson(99), 'isPerson() returns false when person id doesn\'t exist');

$ret = $community->addConnection(999, 998);
$t->ok(!$ret, 'addConnection() rejects links between non-existent people');

$ret = $community->addConnection(1, 998);
$t->ok(!$ret, 'addConnection() rejects links when one person doesn\'t exist');

$ret = $community->addConnection(1, 2);
$t->ok($ret, 'addConnection() sets connection when both people exist');

$t->ok($community->isFollowing(2,1), 'isFollowing() returns true when a person is following another');
$t->ok(!$community->isFollowing(1,2), 'isFollowing() returns false when a person isn\'t following another');

$t->ok($community->isFollowedBy(1,2), 'isFollowedBy() returns true when a person is followed by another');
$t->ok(!$community->isFollowedBy(2,1), 'isFollowedBy() returns false when a person isn\'t followed by another');

$t->ok(!$community->areFriends(2,1), 'areFriends() returns false when two people aren\'t following each other');

$ret = $community->addConnection(3, 4);
$ret = $community->addConnection(4, 3);
$t->ok($community->isFollowing(3,4), 'isFollowing() returns true when a person is following another');
$t->ok($community->isFollowing(4,3), 'isFollowing() returns true when a person is following another');
$t->ok($community->isFollowedBy(3,4), 'isFollowedBy() returns true when a person is followed by another');
$t->ok($community->isFollowedBy(4,3), 'isFollowedBy() returns true when a person is followed by another');
$t->ok($community->areFriends(3,4), 'areFriends() returns true when two people are following each other');


function createTopic($id, $slug) {
	$topic = new Topic();
	$topic->id   = $id;
	$topic->name = $slug;
	$topic->slug = $slug;
	return $topic;
}

function createPeople($number=1) {
	$people = array();
	
	for($i=0; $i < $number; $i++) {
		$person = new Person();
		$person->id = $i;
		$person->username = "user{$i}";
	
		$people[] = $person;
	}	
	
	return $people;
}
?>