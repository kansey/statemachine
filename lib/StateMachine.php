<?php

/**
 * @package StateMachine
 */

require_once "XML/XMLdiff.php";

/**
 * Class of StateMachine
 *
 * @category PHP
 * @package  StateMachine
 * @author   Konstantin Afanasuk <kansey@logics.net.au>
 * @license  http://www.gefest.com.au/license Gefest proprietary license
 * @link     http://svn.logics.net.au/foundation/statemachine
 */

class StateMachine
    {

	private $_fsm = array();

	private $_currentState;

	private $_currentIndex = 0;

	private $_initialState;

	private $_zeroState;

	private $_object;
	/**
	 * Adding a new state and receive the difference
	 *
	 * @param string $stateXML with new state
	 *
	 * @return string with final state
	 */


	public function addState($stateXML)
	    {
		if (isset($this->_currentState) === false)
		    {
			$dom = new DOMDocument();
			$body = $dom->createElement("listofitems");
			$body = $dom->appendChild($body);
			$this->_zeroState    = $dom->saveXML();
			$this->_initialState = $this->_zeroState;
			$this->_currentState = $this->_initialState;
		    }

		$this->_object = new XMLdiff();
		$this->_currentIndex++;
		$diff = $this->_object->diff($this->_currentState, $stateXML);
		$this->_fsm[$this->_currentIndex] = $diff;
		$this->_currentState = $stateXML;
	    } //end addState()


	 /**
	  * Getting current state
	  *
	  * @return string with current state
	  */

	public function getcurrentState()
	    {
		return $this->_currentState;
	    } //end getcurrentState()


	 /**
	  * Getting  state
	  *
	  * @param int $index to get state
	  *
	  * @return mixed string with state by its index
	  */

	public function getState($index)
	    {
		if (array_key_exists($index, $this->_fsm) === false)
		    {
			return false;
		    }
		else
		    {
			$state = $this->_currentState;

			foreach (array_reverse($this->_fsm, true) as $key => $diff)
			    {
				if ($key === $index)
				    {
					break;
				    }

				$state = $this->_object->mergereverse($state, $diff);
			    }

			return $state;
		    }
	    } //end getState()


	 /**
	  * Getting index of the first state
	  *
	  * @return first index
	  */

	public function getFirstIndex()
	    {
		if ($this->_initialState === $this->_zeroState)
		    {
			return 0;
		    }
		else
		    {
			reset($this->_fsm);
			$first = key($this->_fsm);
			return ((count($this->_fsm) === 0) ? 0 : $first);
		    }
	    } //end getFirstIndex()


	 /**
	  *	Getting index of the last state
	  *
	  * @return last index
	  */

	public function getLastIndex()
	    {
		if (count($this->_fsm) > 0)
		    {
			end($this->_fsm);
			$last = key($this->_fsm);
			return $last;
		    }
		else
		    {
			return 0;
		    }
	    }  //end getLastIndex()


	/**
	 * Checks the integrity of the state
	 *
	 * @return string with the initial state or boolean value false
	 */

	public function verifyIntegrity()
	    {
		$state = $this->_initialState;

		foreach ($this->_fsm as $diff)
		    {
			$state = $this->_object->merge($state, $diff);
		    }

		if (isset($state) === true)
		    {
			$dom = new DOMDocument();
			$dom->formatOutput = false;
			$dom->loadXML($state);
			$state = $dom->saveXML();

			$dom->loadXML($this->_currentState);
			$this->_currentState = $dom->saveXML();
		    }

		return (($this->_currentState === $state) ? true : false);
	    } //end verifyIntegrity()


	/**
	 * Set the new initial state and new index of state
	 *
	 * @param int $index index for initial check point
	 *
	 * @return mixed string with the initial
	 */

	public function checkPoint($index)
	    {
		$state = $this->getState($index);
		if ($state !== false)
		    {
			reset($this->_fsm);
			$first = key($this->_fsm);

			for ($i = $first; $i < $index; $i++)
			    {
				unset($this->_fsm[$i]);
			    }

			$this->_initialState = $state;
		    }
		else
		    {
			return false;
		    }
	    } //end checkPoint()


    } //end class

?>
