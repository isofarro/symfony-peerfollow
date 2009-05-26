<?php

class TopicManager {

	public function getSelfTaggers($topic) {
		echo "Getting self-taggers: $topic\n";
		
		$api = new WeFollowApi();

		$people = array();
		
		$page = $api->getTaggedPeople($topic);
		$people = array_merge($people, $page);
		
		while($api->hasNext()) {
			$page = $api->next();
			$people = array_merge($people, $page);
		}
		
		
		//print_r($people);
		return $people;	
	}


	public function calculateLinks($relations) {
		$links = array();
		
		foreach($relations as $relation) {
			$from = $relation->getPersonId();
			$to   = $relation->getFollowingId();
			
			// Increment following count
			if (empty($links[$from])) {
				$links[$from] = array(
					'following' => array(),
					'followers' => array()
				);
			}
			$links[$from]['following'][] = $to;
			
			// Increment follower count
			if (empty($links[$to])) {
				$links[$to] = array(
					'following' => array(),
					'followers' => array()
				);
			}
			$links[$to]['followers'][] = $from;
		}
	
		return $links;
	}
	
	public function calculateCommunityRank($community, $start=1000, $min=1) {

		$changed = $this->iterateRank($community, $min);
		$limit   = 50;
		$iterations = 1;
		
		while ($limit && $changed) {
			$limit--;
			if ($limit) {
				//echo "The flow has changed a rank\n";
				$changed = $this->iterateRank($community, $min);
				$iterations++;
			}
		}
		echo "Iterations: {$iterations}\n";
	}

	protected function iterateRank($community, $min=1, $gravity=15) {
		$hasChanged = false;
		$maxBonus   = 0;
		$inertia    = 100 - $gravity;
		$totalNodes = count($community);
		$otherNodes = $totalNodes - 1;
		$personKeys = array_keys($community);
		echo '[';

		foreach($personKeys as $key) {
			$person = $community[$key];
			if ($person->calc->accum >= $min) {
				//echo "$key: {$person->username} = {$person->calc->accum}\n";
				$damp = round($person->calc->accum * $gravity / 100);
				$person->calc->rank += $damp;
				$totalOut = $person->stats->following;
				//echo "[$totalOut]";

				if ($totalOut>0) {
					$bonus = round($person->calc->accum * $inertia / 100 / $totalOut);
					//echo "($bonus)";
					if ($bonus >= $min) {
						$hasChanged = true;
						if ($bonus > $maxBonus) {
							$maxBonus = $bonus;
						}
						reset($community);
						foreach($person->following as $fkey=>$followerId) {
							$community[$followerId]->calc->accum += $bonus;
						}
						echo '+'; //, $bonus;
						//$person->calc->accum = 0;
					} else {
						echo '.';
						//$person->calc->accum -= $damp;
					}
				} else {
					$bonus = round($person->calc->accum * $inertia / 100 / $otherNodes);
					if ($bonus >= $min) {
						$hasChanged = true;
						// Spread the bonus to every other node
						reset($community);
						foreach($community as $fkey=>$follower) {
							if ($fkey != $person->id) {
								$community[$fkey]->calc->accum += $bonus;
							}
						}
						reset($community);
						echo '*'; //, $bonus;
						//$person->calc->accum = 0;
					} else {
						echo '.';
						//$person->calc->accum -= $damp;
					}
				}

				$person->calc->accum = 0;

				
			} else {
				echo '.';
			}
			//break;
		}
		echo "]({$maxBonus})\n";
		return $hasChanged;
	}
	
	public function processRelations($community) {
		$relHash = array();
		
		foreach($community as $person) {
			foreach($person->following as $following) {
				$rel = array($person->id, $following);
				$key1 = "{$person->id},{$following}";
				$key2 = "{$following},{$person->id}";
				
				/**
				 1 - first follows the second
				 2 - second follows the first
				 3 - both follow each other
				**/				
				
				// Following
				if (empty($relHash[$key1])) {
					$relHash[$key1] = 1;
				} else {
					$relHash[$key1] += 1;
				}

				// Follower
				if (empty($relHash[$key2])) {
					$relHash[$key2] = 2;
				} else {
					$relHash[$key2] += 2;
				}
			}
		}

		// Sort data into groups
		
		$friends = array();
		$followers = array();
		$following = array();	

		foreach($relHash as $rel=>$val) {
			switch($val) {
				case 1:
					break;
					$following[] = $rel;
				case 2:
					break;
					$followers[] = $rel;
				case 3:
					// sort them to dedupe
					$key = $this->sortRelationKey($rel);
					$friends[$key] = 1;
					break;
				default:
					break;
			}
		}
	
		return array_keys($friends);
	}

	protected function sortRelationKey($rel) {
		$ids = explode(',', $rel);
		sort($ids);
		return implode(',', $ids);
	}
	
	public function renderGraphML($topic, $community) {
		$nodeBuffer = array();
		$edgeBuffer = array();

		foreach($community as $person) {
			$nodeBuffer[] = <<<XML
<node id="user-{$person->id}" />		
XML;
			foreach($person->following as $following) {
				$edgeBuffer[] = <<<XML
<edge source="user-{$person->id}" target="user-{$following}" />
XML;
			}
			
		}


		$nodes = implode("\n\t\t", $nodeBuffer);
		$edges = implode("\n\t\t", $edgeBuffer);
		$xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns">	
	<graph id="topic-{$topic}" edgedefault="directed">
		{$nodes}

		{$edges}
	</graph>
</graphml>
XML;
		return $xml;
	}
}

?>