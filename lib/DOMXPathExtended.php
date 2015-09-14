<?php

/**
 * @package XML
 */

/**
 * DOMXPathExtended implements conveninence functions missing from stock DOMXPath
 *
 * @category PHP
 * @package  XML
 * @author   Vladimir Bashkirtsev <vladimir@bashkirtsev.com>
 * @license  http://www.gefest.com.au/license Gefest proprietary license
 * @link     http://svn.logics.net.au/foundation/XML
 */

class DOMXPathExtended extends DOMXPath
    {

	/**
	 * Make instance of DOMXPath and register namespaces if required
	 *
	 * @param DOMDocument $doc DOMDocument to run DOMXPath over
	 *
	 * @return void
	 *
	 * @untranslatable null
	 * @untranslatable namespace::*
	 */

	public function __construct($doc)
	    {
		parent::__construct($doc);

		$rootNamespace = $doc->lookupNamespaceUri($doc->namespaceURI);
		if ($rootNamespace !== null)
		    {
			$prefix = $doc->lookupPrefix($doc->namespaceURI);
			$prefix = (($prefix === null) ? "null" : $prefix);
			$this->registerNamespace($prefix, $rootNamespace);
		    }

		foreach ($this->query("namespace::*") as $node)
		    {
			$prefix = $doc->lookupPrefix($node->nodeValue);
			if ($prefix !== null)
			    {
				$this->registerNamespace($prefix, $node->nodeValue);
			    }
		    }
	    } //end __construct()


	/**
	 * Check if xpath statement yields any results
	 *
	 * @param string $xpath Statement to test
	 *
	 * @return boolean True if results are found
	 */

	public function exists($xpath)
	    {
		$list = $this->query($xpath);
		return ($list->length > 0);
	    } //end exists()


	/**
	 * Get first node value from DOMNodeList
	 *
	 * @param DOMNodeList $list containing result of XPath query
	 *
	 * @return mixed node value or false
	 */

	public function getFirstItemValueFrom($list)
	    {
		return (($list->length > 0) ? $list->item(0)->nodeValue : false);
	    } //end getFirstItemValueFrom()


    } //end class

?>
