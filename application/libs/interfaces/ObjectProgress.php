<?php

namespace interfaces;

interface ObjectProgress
{
	public function progressMaximum();
	public function progressMinimum();
	public function progressValue();
}
