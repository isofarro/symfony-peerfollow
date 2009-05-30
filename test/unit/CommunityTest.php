<?php

include(dirname(__file__).'/../bootstrap/unit.php');
require_once(dirname(__file__).'/../../lib/Community.php');

$t = new lime_test(7, new lime_output_color());

$t->diag('Community');

$community = new Community();
$t->isa_ok($community, 'Community', 'new Community() is of class Community');


?>