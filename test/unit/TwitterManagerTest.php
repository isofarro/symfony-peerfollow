<?php

include(dirname(__file__).'/../bootstrap/unit.php');
require_once(dirname(__file__).'/../../lib/TwitterManager.php');

$t = new lime_test(99, new lime_output_color());

$t->diag('TwitterManager');

$twitter = new TwitterManager();

$page = getPage('search.json');

$twitter->addSearchResults('accessibility', $page->results);

/**
// Dealing with the left overs
$page->results = 'Array of results';
print_r($page);

stdClass Object
(
    [results] => Array of results
    [since_id] => 2000383174
    [max_id] => 2086393153
    [refresh_url] => ?since_id=2086393153&q=accessibility+OR+a11y
    [results_per_page] => 15
    [next_page] => ?page=2&max_id=2086393153&lang=en&q=accessibility+OR+a11y
    [warning] => adjusted since_id, it was older than allowedsince_id removed for pagination.
    [completed_in] => 0.055557
    [page] => 1
    [query] => accessibility+OR+a11y
)
**/




#
#
#


function getPage($page) {
	$file = "/home/user/Documents/twitter-search-accessibility/{$page}";
	$json = file_get_contents($file);
	return json_decode($json);
}
?>