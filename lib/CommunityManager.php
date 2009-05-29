<?php

class CommunityManager {

	/**
	* Gets all the people from wefollow.com who have tagged
	* themselves with the specified topic name
	*
	* @param String - topic tag
	* @returns Array of people data objects
	**/
	public function getSelfTaggers($topic) {
		echo "Getting self-taggers: $topic\n";
		
		$api    = new WeFollowApi();
		$people = array();
		
		// TODO: Condense this into one helper method call
		$page   = $api->getTaggedPeople($topic);
		$people = array_merge($people, $page);
		
		while($api->hasNext()) {
			$page = $api->next();
			$people = array_merge($people, $page);
		}
		
		//print_r($people);
		return $people;	
	}

	/**
	* Calculates the network rank of each of the members of the community
	* 
	* @param network - an array of Node objects
	* @param min - minimum accumulated bonus to pass on (optional, default 1)
	* @returns nothing - the Array of objects passed contains the results
	**/
	public function calculateNetworkRank($network, $min=1, $maxIterations=50) {
		// TODO: Refactor this to allow variations on passing rank
		// PageRank: hold 15% pass on 85% equally to all outbound links
		$iterations    = 0;
		
		$changed = $this->iterateRank($network, $min);
		$iterations++;

		while ($iterations < $maxIterations && $changed) {
			$changed = $this->iterateRank($network, $min);
			$iterations++;
		}

		echo "Iterations: {$iterations}\n";
	}

	/**
	* Iterates through a set of nodes applying the rank algorithm
	* @param network - an array of Nodes
	* @param min - the minimum rank to pass (optional, default 1)
	* @param gravity - the percentage of rank to hold onto (optional, default 15)
	*
	* @returns nothing - the changes are reflected in the network array
	**/
	protected function iterateRank($network, $min=1, $gravity=15) {
		// TODO: prevent the leak of rank when under the minimum	
		// Need to keep track of the remainders.
	
		$hasChanged = false;
		$maxBonus   = 0;
		$inertia    = 100 - $gravity;
		$totalNodes = count($network);
		$otherNodes = $totalNodes - 1;
		$nodeIds    = array_keys($network);
		echo '[';

		foreach($nodeIds as $nodeId) {
			$node = $network[$nodeId];
			if ($node->accum >= $min) {
				// TODO: Refactor this into a variable method name
				//       1.) calculates the rank to pass per node
				//       2.) deals with nodes with no outbound connections
				$damp = round($node->accum * $gravity / 100);
				$node->rank += $damp; // TODO: delay until rank has been passed
				$totalOut = count($node->edges);
				//echo "[$totalOut]";

				if ($totalOut>0) {
					// A node containing outbound edges
					$bonus = round($node->accum * $inertia / 100 / $totalOut);
					//echo "($bonus)";
					if ($bonus >= $min) {
						$hasChanged = true;
						if ($bonus > $maxBonus) {
							$maxBonus = $bonus;
						}
						reset($network);
						foreach($node->edges as $outNodeId) {
							$network[$outNodeId]->accum += $bonus;
						}
						echo '+'; //, $bonus;
						//$node->accum = 0;
					} else {
						echo '.';
						//$node->accum -= $damp;
					}
					$node->accum = 0;
				} else {
					// A node with no outbound edges
					$bonus = round($node->accum * $inertia / 100 / $otherNodes);
					if ($bonus >= $min) {
						$hasChanged = true;
						// Spread the bonus to every other node
						reset($network);
						foreach($network as $outNodeId=>$outNode) {
							if ($outNodeId != $node->nodeId) {
								$network[$outNodeId]->accum += $bonus;
							}
						}
						reset($network);
						echo '*'; //, $bonus;
						//$node->accum = 0;
					} else {
						echo '.';
						//$node->accum -= $damp;
						// This rewards the dead-end multiple times when the
						// reward is less than nodes-1
					}
					$node->accum = 0;
				}

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

	public function generateGraphML($connections) {
		$nodeBuffer = array();
		$edgeBuffer = array();
		
		foreach($connections as $connection) {
			list($to, $from) = explode(',', $connection);
			
			if (empty($nodeBuffer[$to])) {
				$nodeBuffer[$to] = <<<XML
<node id="user-{$to}" />
XML;
			}

			if (empty($nodeBuffer[$from])) {
				$nodeBuffer[$from] = <<<XML
<node id="user-{$from}" />
XML;
			}
			
			$edgeBuffer[] = <<<XML
<edge source="user-{$to}" target="user-{$from}" />
XML;

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