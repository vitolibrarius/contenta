<?php
	$system_path = dirname(dirname(__FILE__));
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).DIRECTORY_SEPARATOR;
	}

	define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));
	define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

	require SYSTEM_PATH .'application/config/bootstrap.php';
	require SYSTEM_PATH .'application/config/autoload.php';
	require SYSTEM_PATH .'application/config/common.php';
	require SYSTEM_PATH .'application/config/errors.php';
	require SYSTEM_PATH .'application/libs/Config.php';
	require SYSTEM_PATH .'application/libs/Cache.php';

	require SYSTEM_PATH .'tests/_ResetConfig.php';
	require SYSTEM_PATH .'tests/_Data.php';

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
