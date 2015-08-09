<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mo\Helper;

/**
 * Nice Helper Functions
 *
 * @author Maurice Prosper <maurice.prosper@ttu.edu>
 */
abstract class Functions {
	/**
	 * Class casting
	 *
	 * @link http://stackoverflow.com/a/9812023
	 * @param string|object $destination
	 * @param object $sourceObject
	 * @return object
	 */
	function cast($destination, $sourceObject) {
		if (is_string($destination)) {
			$destination = new $destination();
		}
		$sourceReflection = new ReflectionObject($sourceObject);
		$destinationReflection = new ReflectionObject($destination);
		$sourceProperties = $sourceReflection->getProperties();
		foreach ($sourceProperties as $sourceProperty) {
			$sourceProperty->setAccessible(true);
			$name = $sourceProperty->getName();
			$value = $sourceProperty->getValue($sourceObject);
			if ($destinationReflection->hasProperty($name)) {
				$propDest = $destinationReflection->getProperty($name);
				$propDest->setAccessible(true);
				$propDest->setValue($destination,$value);
			} else {
				$destination->$name = $value;
			}
		}
		return $destination;
	}
}
