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