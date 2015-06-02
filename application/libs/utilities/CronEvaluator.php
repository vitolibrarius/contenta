<?php

namespace utilities;

use \Logger as Logger;
use \Cache as Cache;
use utilities\ShellCommand as ShellCommand;
use exceptions\ValidationException as ValidationException;

class CronEvaluator
{
    const MINUTE	= 2;
    const HOUR		= 1;
    const DAYOFWEEK = 0;

    private $cronParts;

	public function __construct($minute = null, $hour = null, $dow = null) {
		isset($minute) || die("No minute expression");
		isset($hour) || die("No hour expression");
		isset($dow) || die("No day of week expression");

        $dow = str_ireplace(
            array('SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'),
            range(0, 6),
            $dow
        );

		CronEvaluator::validateExpressionPart( CronEvaluator::MINUTE, $minute);
		CronEvaluator::validateExpressionPart( CronEvaluator::HOUR, $hour);
		CronEvaluator::validateExpressionPart( CronEvaluator::DAYOFWEEK, $dow);

		$this->cronParts = array(
			CronEvaluator::DAYOFWEEK => array_map('trim', explode(',', $dow)),
			CronEvaluator::HOUR => array_map('trim', explode(',', $hour)),
			CronEvaluator::MINUTE => array_map('trim', explode(',', $minute)),
		);
	}

	public function __toString()
	{
		return get_short_class($this) . ' ' . var_export($this->cronParts, true);
	}

	public static function validateExpressionPart( $partId = CronEvaluator::MINUTE, $value = null)
	{
		if (isset($value) == false || strlen($value) == 0)
		{
			throw new ValidationException( "FIELD_EMPTY", $partId );
		}

		switch( $partId ) {
			case CronEvaluator::MINUTE:
			case CronEvaluator::HOUR:
				if ( !preg_match('/^[\*,\/\-0-9]+$/', $value) ) {
					throw new ValidationException( "DOES_NOT_FIT_PATTERN", $partId );
				}
				break;
			case CronEvaluator::DAYOFWEEK:
				$value = str_ireplace(
					array('SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'),
					range(0, 6),
					$value
				);
				foreach (explode(',', $value) as $expr) {
	            	if ( ! preg_match('/^[\*,\/\-0-6]+$/', $expr) ) {
						throw new ValidationException( "DOES_NOT_FIT_PATTERN", $partId );
	            	}
	    	    }
	    	    break;
    	    default:
    			throw new ValidationException( "INVALID_EXPRESSION_PART", $partId );
    			break;
		}
	}

	public function previousDate( $currentTime = 'now', $skip = 0 )
	{
		return $this->calcDate( $currentTime, $skip, true );
	}

	public function nextDate( $currentTime = 'now', $skip = 0 )
	{
		return $this->calcDate( $currentTime, $skip, false );
	}

	public function nextSeriesDates( $currentTime = 'now', $count = 1 )
	{
        $matches = array();
        for ($skip = 0; $skip < max(0, $count); $skip++) {
            $matches[] = $this->calcDate($currentTime, $skip, false);
        }

        return $matches;
	}

	private function calcDate( $currentTime = 'now', $skip = 0, $backward = false )
	{
        if ($currentTime instanceof \DateTime) {
            $currentDate = clone $currentTime;
        }
        else {
            $currentDate = new \DateTime($currentTime ? : 'now');
            $currentDate->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        }

        $currentDate->setTime($currentDate->format('H'), $currentDate->format('i'), 0);
        $nextDate = clone $currentDate;
        $skip = (int) $skip;

		for ($i = 0; $i < 1000; $i++) {
			$partSatisfied = array( false, false, false );

			foreach( $this->cronParts as $partId => $partsArray ) {
				foreach( $partsArray as $part ) {
					if ( $this->satisfiesDatePart( $nextDate, $partId, $part ) ) {
						$partSatisfied[$partId] = true;
						break;
					}
				}

				if ( $partSatisfied[$partId] == false ) {
					$this->stepDate( $nextDate, $partId, $backward );
					continue 2;
				}

				if ( $partSatisfied[0] && $partSatisfied[1] && $partSatisfied[2] ) {
					if ( $skip > 0 ) {
						$skip --;
						$this->stepDate( $nextDate, CronEvaluator::MINUTE, $backward );
						continue 2;
					}
					else {
						return $nextDate;
					}
				}
			}
		}
		throw new ValidationException( "INVALID_EXPRESSION", -1 );
	}

	public function stepDate( \DateTime $currentDate, $partId, $backward = false )
	{
		switch( $partId ) {
			case CronEvaluator::MINUTE:
				if ($backward) {
		            $currentDate->modify('-1 minute');
		        }
		        else {
		        	$currentDate->modify('+1 minute');
		        }
		        break;

			case CronEvaluator::HOUR:
				$timezone = $currentDate->getTimezone();
				$currentDate->setTimezone(new \DateTimeZone('UTC'));
				if ($backward) {
					$currentDate->modify('-1 hour');
					$currentDate->setTime($currentDate->format('H'), 59);
				}
				else {
					$currentDate->modify('+1 hour');
					$currentDate->setTime($currentDate->format('H'), 0);
				}
				$currentDate->setTimezone($timezone);
				break;

			case CronEvaluator::DAYOFWEEK:
				if ($backward) {
					$currentDate->modify('-1 day');
					$currentDate->setTime(23, 59, 0);
				}
				else {
					$currentDate->modify('+1 day');
					$currentDate->setTime(0, 0, 0);
				}
	    	    break;
    	    default:
    			throw new ValidationException( "INVALID_EXPRESSION_PART", $partId );
    			break;
		}
	}

	public function satisfiesDatePart( \DateTime $currentDate, $partId, $value )
	{
		$satisfied = false;
		if ( is_null($currentDate) == false ) {
			switch ( $partId ) {
				case CronEvaluator::MINUTE:
					if ($this->isSlashRange($value)) {
						$satisfied = $this->isInSlashRange( $currentDate->format('i'), $value);
					}
					else if ($this->isRange($value)) {
						$satisfied = $this->isInRange( $currentDate->format('i'), $value);
					}
					else {
						$satisfied = ( $value == '*' || $currentDate->format('i') == $value);
					}
					break;
				case CronEvaluator::HOUR:
					if ($this->isSlashRange($value)) {
						$satisfied = $this->isInSlashRange( $currentDate->format('H'), $value);
					}
					else if ($this->isRange($value)) {
						$satisfied = $this->isInRange( $currentDate->format('H'), $value);
					}
					else {
						$satisfied = ( $value == '*' || $currentDate->format('H') == $value);
					}
					break;
				case CronEvaluator::DAYOFWEEK:
					if ($this->isSlashRange($value)) {
						$satisfied = $this->isInSlashRange( $currentDate->format('w'), $value);
					}
					else if ($this->isRange($value)) {
						$satisfied = $this->isInRange( $currentDate->format('w'), $value);
					}
					else {
						$satisfied = ( $value == '*' || $currentDate->format('w') == $value);
					}
					break;
				default:
			}
		}

		return $satisfied;
	}

    public function isRange($value)			{ return strpos($value, '-') !== false; }
	public function isSlashRange($value)	{ return strpos($value, '/') !== false; }

    public function isInRange($date, $value)
    {
        $parts = array_map('trim', explode('-', $value, 2));
        return $date >= $parts[0] && $date <= $parts[1];
    }

    public function isInSlashRange($idx, $value)
    {
        $parts = array_map('trim', explode('/', $value, 2));
        $range = (isset($parts[0]) && strlen($parts[0]) > 0) ? $parts[0] : '*';
        $divisor = isset($parts[1]) ? $parts[1] : 0;

		if ( 0 === $divisor )  {
			return false;
		}

        if (($range == '*' || $range === '0') && 0 !== $divisor) {
            return (int) $idx % $divisor == 0;
        }

        $range = explode('-', $range, 2);
        $rangeStart = $range[0];
        $rangeEnd = isset($range[1]) ? $range[1] : $rangeStart;

        if ( $idx >= $rangeStart && $idx <= $rangeEnd ) {
			for ($i = $rangeStart; $i <= $rangeEnd; $i += $divisor) {
				if ($i == $idx) {
					return true;
				}
			}
        }

        return false;
    }
}
