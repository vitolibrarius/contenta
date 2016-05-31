<?php

// get the feedback (they are arrays, to make multiple positive/negative messages possible)
$feedback_positive = \http\Session::positiveFeedback();
$feedback_negative = \http\Session::negativeFeedback();

// echo out positive messages
if (isset($feedback_positive)) {
	echo 'Success'.PHP_EOL;;
	foreach ($feedback_positive as $feedback) {
		echo '- '.$feedback.PHP_EOL;;
	}
	echo ''.PHP_EOL;;
}

// echo out negative messages
if (isset($feedback_negative)) {
	echo 'Errors'.PHP_EOL;;
	foreach ($feedback_negative as $feedback) {
		echo '- '.$feedback.PHP_EOL;;
	}
}
