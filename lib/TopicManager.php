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
		// TODO: Add the start capital to the first node
		$first = reset($community);
		if($first && $start) {
			$first->calc->accum = $start;
		}

		$changed = $this->iterateRank($community, $min);
		$limit   = 10;
		while ($limit && $changed) {
			$limit--;
			if ($limit) {
				//echo "The flow has changed a rank\n";
				$changed = $this->iterateRank($community, $min);
			}
		}
	}

	protected function iterateRank($community, $min=1, $gravity=20) {
		$hasChanged = false;
		$maxBonus   = 0;
		$inertia    = 100 - $gravity;
		
		$personKeys = array_keys($community);
		echo '[';
		foreach($personKeys as $key) {
			$person = $community[$key];
			if ($person->calc->accum >= $min) {
				//echo "$key: {$person->username} = {$person->calc->accum}\n";
				$person->calc->rank += round($person->calc->accum * $gravity / 100);
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
					}
				} else {
					$bonus = round($inertia * $person->calc->accum / 100 / 25);
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
}

?>