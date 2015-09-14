<?php

/**
 * @package Tests
 */

require_once "statemachine/StateMachine.php";

/**
 * Class for testing of StateMachine
 *
 * @category PHP
 * @package  Tests
 * @author   Konstantin Afanasuk <kansey@logics.net.au>
 * @license  http://www.gefest.com.au/license Gefest proprietary license
 * @link     http://svn.logics.net.au/foundation/tests
 *
 * @donottranslate
 */

class StateMachineTest extends PHPUnit_Framework_TestCase
    {

	/**
	 * Test for adding and selecting states
	 *
	 * @return void
	 */

	public function testCanSetNewStateOrGetAnyKnownStateOfAMachine()
	    {
		$stateMachine = new StateMachine();
		$this->assertSame(null, $stateMachine->getCurrentState());
		$stateMachine->addState(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"1\" index=\"1\">First</item><item id=\"2\">Second</item></listofitems>"
		);

		$this->assertXmlStringEqualsXmlString(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"1\" index=\"1\">First</item><item id=\"2\">Second</item></listofitems>",
		    $stateMachine->getCurrentState()
		);

		$stateMachine->addState(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>"
		);

		$this->assertXmlStringEqualsXmlString(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>",
		    $stateMachine->getCurrentState()
		);

		$this->assertXmlStringEqualsXmlString(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"1\" index=\"1\">First</item><item id=\"2\">Second</item></listofitems>",
		    $stateMachine->getState(1)
		);

		$this->assertXmlStringEqualsXmlString(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>",
		    $stateMachine->getState(2)
		);

		$this->assertEquals(false, $stateMachine->getState(100));
	    } //end testCanSetNewStateOrGetAnyKnownStateOfAMachine()


	/**
	 * Test for selecting first and last indexes of state machine
	 *
	 * @return void
	 */

	public function testCanReturnRangeOfIndexesOfAllKnownStates()
	    {
		$stateMachine = new StateMachine();
		$this->assertEquals(0, $stateMachine->getFirstIndex());
		$this->assertEquals(0, $stateMachine->getLastIndex());

		$stateMachine->addState(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"1\" index=\"1\">First</item><item id=\"2\">Second</item></listofitems>"
		);
		$this->assertEquals(0, $stateMachine->getFirstIndex());
		$this->assertEquals(1, $stateMachine->getLastIndex());

		$stateMachine->addState(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>"
		);
		$this->assertEquals(0, $stateMachine->getFirstIndex());
		$this->assertEquals(2, $stateMachine->getLastIndex());

		$stateMachine->checkPoint(1);
		$this->assertEquals(1, $stateMachine->getFirstIndex());
		$this->assertEquals(2, $stateMachine->getLastIndex());
	    } //end testCanReturnRangeOfIndexesOfAllKnownStates()


	/**
	 * Test to select the previous, the next state machines and similar  state machines work on indices
	 *
	 * @return void
	 */

	public function testCanSelectNextOrPreviousOrRandomIndexState()
	    {
		$stateMachine = new StateMachine();
		$stateMachine->addState(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"1\" index=\"1\">First</item><item id=\"2\">Second</item></listofitems>"
		);
		$stateMachine->addState(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>"
		);

		$this->assertEquals(0, $stateMachine->getFirstIndex());
		$this->assertEquals(2, $stateMachine->getLastIndex());
		$stateMachine->checkPoint(1);

		$this->assertEquals(false, $stateMachine->getState(0));
		$this->assertEquals(1, $stateMachine->getFirstIndex());
		$this->assertEquals(2, $stateMachine->getLastIndex());
		$this->assertEquals(false, $stateMachine->getState(3));

		$this->assertXmlStringEqualsXmlString(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"1\" index=\"1\">First</item><item id=\"2\">Second</item></listofitems>",
		    $stateMachine->getState(1)
		);

		$this->assertXmlStringEqualsXmlString(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>",
		    $stateMachine->getState(2)
		);

		$stateMachine->addState(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"4\">fourth</item></listofitems>"
		);

		$this->assertEquals(1, $stateMachine->getFirstIndex());
		$this->assertXmlStringEqualsXmlString(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"1\" index=\"1\">First</item><item id=\"2\">Second</item></listofitems>",
		    $stateMachine->getState($stateMachine->getFirstIndex())
		);

		$this->assertEquals(3, $stateMachine->getLastIndex());
		$this->assertXmlStringEqualsXmlString(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"4\">fourth</item></listofitems>",
		    $stateMachine->getState($stateMachine->getLastIndex())
		);
	    } //end testCanSelectNextOrPreviousOrRandomIndexState()


	/**
	 * Test to verify the integrity of the state machine
	 *
	 * @return void
	 */

	public function testIsAbleToConfirmIntegrityOfStateMachine()
	    {
		$stateMachine = new StateMachine();
		$this->assertEquals(true, $stateMachine->verifyIntegrity());
		$stateMachine->addState(
		    "<?xml version=\"1.0\"?>\n<listofitems><item id=\"1\" index=\"1\">First</item><item id=\"2\">Second</item></listofitems>"
		);
		$stateMachine->addState(
		    "<?xml version=\"1.0\"?>\n<listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>"
		);
		$stateMachine->addState(
		    "<?xml version=\"1.0\"?>\n<listofitems><item id=\"4\">Fourth</item></listofitems>"
		);
		$stateMachine->checkPoint(1);
		$this->assertEquals(true, $stateMachine->verifyIntegrity());

		$stateMachine->checkPoint(100);
		$this->assertEquals(true, $stateMachine->verifyIntegrity());

		$stateMachine->checkPoint(2);
		$this->assertEquals(true, $stateMachine->verifyIntegrity());

		$stateMachine->addState(
		    "<?xml version=\"1.0\"?><listofitems><item id=\"5\">fifth</item><item id=\"3\">Third</item></listofitems>"
		);

		$reflection = new ReflectionProperty("StateMachine", "_fsm");
		$reflection->setAccessible(true);
		$_fsm    = $reflection->getValue($stateMachine);
		$_fsm[4] = $_fsm[2];
		$reflection->setValue($stateMachine, $_fsm);

		$this->assertEquals(false, $stateMachine->verifyIntegrity());
	    } //end testIsAbleToConfirmIntegrityOfStateMachine()


	/**
	 * Test for to set check point and forget all previous state of state machine
	 *
	 * @return void
	 */

	public function testShouldBeAbleToSetCheckpointAndForgetAllPreviousStatesOfAMachine()
	    {
		$stateMachine = new StateMachine();
		$stateMachine->addState(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"1\" index=\"1\">First</item><item id=\"2\">Second</item></listofitems>"
		);
		$stateMachine->addState(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>"
		);
		$stateMachine->addState(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"1\" index=\"1\">First</item><item id=\"2\">Second</item></listofitems>"
		);
		$stateMachine->addState(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"4\">Fourth</item></listofitems>"
		);
		$stateMachine->addState(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>"
		);

		$this->assertXmlStringEqualsXmlString(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>",
		    $stateMachine->getCurrentState()
		);

		$stateMachine->checkPoint(2);
		$this->assertXmlStringEqualsXmlString(
		    "<?xml version=\"1.0\" encoding=\"utf-8\"?><listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>",
		    $stateMachine->getState($stateMachine->getFirstIndex())
		);

		$stateMachine->checkPoint(4);
		$this->assertXmlStringEqualsXmlString(
		    "<?xml version=\"1.0\" ?><listofitems><item id=\"4\">Fourth</item></listofitems>",
		    $stateMachine->getState($stateMachine->getFirstIndex())
		);
	    } //end testShouldBeAbleToSetCheckpointAndForgetAllPreviousStatesOfAMachine()


	/**
	 * New states are added to the end only
	 *
	 * @return void
	 */

	public function testNewStatesAreAddedToTheEnd()
	    {
		$stateMachine = new StateMachine();
		$stateMachine->addState(
		    "<?xml version=\"1.0\"?><listofitems><item id=\"1\" index=\"1\">First</item><item id=\"2\">Second</item></listofitems>"
		);
		$stateMachine->addState(
		    "<?xml version=\"1.0\"?><listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>"
		);
		$stateMachine->addState(
		    "<?xml version=\"1.0\"?><listofitems><item id=\"1\" index=\"1\">First</item><item id=\"2\">Second</item></listofitems>"
		);
		$stateMachine->addState(
		    "<?xml version=\"1.0\"?><listofitems><item id=\"4\">Fourth</item></listofitems>"
		);

		$this->assertEquals(true, $stateMachine->verifyIntegrity());

		$stateMachine->addState(
		    "<?xml version=\"1.0\"?><listofitems><item id=\"5\">fifth</item><item id=\"3\">Third</item></listofitems>"
		);

		$this->assertXmlStringEqualsXmlString(
		    "<?xml version=\"1.0\"?><listofitems><item id=\"2\">second</item><item id=\"3\">Third</item></listofitems>",
		    $stateMachine->getState(2)
		);

		$this->assertEquals(true, $stateMachine->verifyIntegrity());
	    } //end testNewStatesAreAddedToTheEnd()


    } //end class

?>
