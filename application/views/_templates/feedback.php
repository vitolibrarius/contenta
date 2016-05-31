<?php

// get the feedback (they are arrays, to make multiple positive/negative messages possible)
$feedback_positive = \http\Session::positiveFeedback();
$feedback_negative = \http\Session::negativeFeedback();

// echo out positive messages
if (isset($feedback_positive)) {
	echo '<section class="feedback success">';
	foreach ($feedback_positive as $feedback) {
		echo '<p class="item">'.$feedback.'</p>';
	}
	echo '</section>';
}

// echo out negative messages
if (isset($feedback_negative)) {
	echo '<section class="feedback error">';
	foreach ($feedback_negative as $feedback) {
		echo '<p class="item">'.$feedback.'</p>';
	}
	echo '</section>';
}
