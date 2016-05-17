<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
 */

namespace utilities;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36. */
use \Logger as Logger;
use \Cache as Cache;
use \utilities\ShellCommand as ShellCommand;
use \exceptions\ValidationException as ValidationException;
/* {useStatements} */

class CronEvaluatorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	__construct
	 * 			T_FUNCTION T_PUBLIC __construct ( $minute = null, $hour = null, $dow = null)
	 * @todo	Implement test__construct().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function test__construct()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	__toString
	 * 			T_FUNCTION T_PUBLIC __toString ( )
	 * @todo	Implement test__toString().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function test__toString()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validateExpressionPart
	 * 			T_FUNCTION T_STATIC T_PUBLIC validateExpressionPart ( $partId = CronEvaluatorMINUTE, $value = null)
	 * @todo	Implement testValidateExpressionPart().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testValidateExpressionPart()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	previousDate
	 * 			T_FUNCTION T_PUBLIC previousDate ( $currentTime = 'now', $skip)
	 * @todo	Implement testPreviousDate().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testPreviousDate()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	nextDate
	 * 			T_FUNCTION T_PUBLIC nextDate ( $currentTime = 'now', $skip)
	 * @todo	Implement testNextDate().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testNextDate()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	nextSeriesDates
	 * 			T_FUNCTION T_PUBLIC nextSeriesDates ( $currentTime = 'now', $count)
	 * @todo	Implement testNextSeriesDates().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testNextSeriesDates()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	calcDate
	 * 			T_FUNCTION T_PRIVATE calcDate ( $currentTime = 'now', $skip, $backward = false)
	 * @todo	Implement testCalcDate().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testCalcDate()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	stepDate
	 * 			T_FUNCTION T_PUBLIC stepDate ( DateTime $currentDate, $partId, $backward = false)
	 * @todo	Implement testStepDate().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testStepDate()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	satisfiesDatePart
	 * 			T_FUNCTION T_PUBLIC satisfiesDatePart ( DateTime $currentDate, $partId, $value)
	 * @todo	Implement testSatisfiesDatePart().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testSatisfiesDatePart()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	isRange
	 * 			T_FUNCTION T_PUBLIC isRange ( $value)
	 * @todo	Implement testIsRange().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testIsRange()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	isSlashRange
	 * 			T_FUNCTION T_PUBLIC isSlashRange ( $value)
	 * @todo	Implement testIsSlashRange().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testIsSlashRange()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	isInRange
	 * 			T_FUNCTION T_PUBLIC isInRange ( $date, $value)
	 * @todo	Implement testIsInRange().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testIsInRange()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	isInSlashRange
	 * 			T_FUNCTION T_PUBLIC isInSlashRange ( $idx, $value)
	 * @todo	Implement testIsInSlashRange().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testIsInSlashRange()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
