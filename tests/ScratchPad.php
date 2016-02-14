<?php

date_default_timezone_set('Canada/Mountain');

for ( $i = 0; $i < 10; $i++ ) {
		$start_date = new DateTime();
		$start_date->modify( '-'.$i.' month' );
		$end_date = clone $start_date;
		$start_date->modify('first day of this month');
		$end_date->modify('last day of this month');
		echo $i . ' = ' . $start_date->format('Y-m-d'). " - " . $end_date->format('Y-m-d') . PHP_EOL;

}
	// $date->modify('first day of this month');
// 	echo $date->format('Y-m-d') . PHP_EOL;
// echo time() . ' ' . $date->getTimestamp() . ' ' . time() . PHP_EOL;


exit;
		$order = array( "unknown", "publication", "story_arc", "series" );
		$fullMetal = array(
			"publication" => array( "publication_56", 56 ),
			"story_arc" => array( "story_arc_87", 87 ),
			"series" => array( "series_111", 111 ),
			"publication" => array( "publication_56", 56 ),
			"trees" => array( "trees_1289", 56 ),
		);
		if ( is_array($fullMetal) && count($fullMetal) > 0 ) {
			$alchemist = array_keys($fullMetal);
			usort($alchemist, function ($a, $b) use ($order) {
				$pos_a = intval(array_search($a, $order));
				$pos_b = intval(array_search($b, $order));
				echo " sort $a($pos_a) -> $b($pos_b) \n";
				return $pos_a - $pos_b;
			});

			var_dump($alchemist);
		}
?>
