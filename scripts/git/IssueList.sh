#! /usr/bin/env php
<?php
	$TTL = 60;
	$user=exec('whoami');

	$cached = "/tmp/contenta-issues-$user.json";
	$use_cache = false;
	if (@file_exists($cached) && filemtime($cached) > (time() - ($TTL))) {
		$use_cache = true;
	}

	if ( $use_cache == true ) {
		$data = file_get_contents($cached);
		if ( $data != false ) {
			$json = json_decode($data, true);
			if ( json_last_error() != 0 ) {
				@unlink( $cached );
			}
		}
		else {
			@unlink( $cached );
		}
	}

	if (isset($json) == false) {
		$url = "https://api.github.com/repos/vitolibrarius/contenta/issues";
		$context = array(
			'http' => array(
				'ignore_errors' => true,
				'method' => "GET",
				'header'=>	"Accept-language: en\r\n" .
							"User-Agent: PHP/5.2.9\r\n",
							"Content-Type: application/json\r\n"
			)
		);


		$data = file_get_contents($url, false, stream_context_create($context));
		if ( $data != false ) {
			$json = json_decode($data, true);
			if ( json_last_error() == 0 ) {
				file_put_contents( $cached, json_encode($json, JSON_PRETTY_PRINT) );
			}
		}
	}

	if (isset($json) && is_array($json)) {
		echo "# Outstanding issues" .PHP_EOL;
		foreach( $json as $issue ) {
			echo "#\t" . $issue['number'] . "\t" . $issue['title'] .PHP_EOL;
		}
	}
?>
