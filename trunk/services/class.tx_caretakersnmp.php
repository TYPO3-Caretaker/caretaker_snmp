<?php
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Patrick Kollodzik	<patrick@work.de>
 * 
 * $$Id: class.tx_caretaker_typo3_extensions.php 33 2008-06-13 14:00:38Z thomas $$
 */

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Patrick Kollodzik <patrick@work.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once (t3lib_extMgm::extPath('caretaker').'/services/class.tx_caretaker_TestServiceBase.php');

class tx_caretakersnmp extends tx_caretaker_TestServiceBase {
	
	function __construct(){
		$this->valueDescription = $this->getConfigValue('snmp_description');
	}
	
	public function runTest(){

		$snmp_description    = $this->getConfigValue('snmp_description');
		$snmp_community      = $this->getConfigValue('snmp_community');
		$snmp_object_id      = $this->getConfigValue('snmp_object_id');
		$snmp_timeout        = $this->getConfigValue('snmp_timeout',   1000000 );
		$snmp_retries        = $this->getConfigValue('snmp_retries',   1 );
		
		$value_range_error   = $this->getConfigValue('value_range_error');
		$value_range_warning = $this->getConfigValue('value_range_warning');
		
		
		if ( !function_exists('snmpget') ){
			return  tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR, 0, 'snmpget is not available');
		}
		
		if ( !$snmp_community || !$snmp_object_id  ){
			return  tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR, 0, 'Community and Object ID must be specified');
		}
		
		if ( !$value_range_error && !$value_range_warning ){
			return  tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR, 0, 'Error or Warnig Ranges must be defined');
		}
		
		// @todo maybe we have to parse the return value
		$value = snmpget  ( $this->instance->getHost() ,  $snmp_community  , $snmp_object_id, $snmp_timeout, $snmp_retries  );
		
		if (!$result || $this->isValueInRange ($value, $value_range_error ) ) {
			$testResult = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_ERROR, $value, 'Error '.$snmp_description);
		} else if ($this->isValueInRange ($value, $value_range_warning ) ) {
			$testResult = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_WARNING, $value, 'Warning '.$snmp_description);
		} else {
			$testResult = tx_caretaker_TestResult::create(TX_CARETAKER_STATE_OK, $value, 'OK '.$snmp_description );
		}

		return $testResult;
	}
	
	/**
	 * 
	 * @param float Value to check
	 * @param string Value range: '<2:12.4..18:=25:>6' 
	 * @rturn bolean
	 */
	function isValueInRange ($value , $range_definition){
		
		$result = false;
		$value  = (float)$value;
		$ranges = explode(':',$range_definition);
		foreach ( $ranges as $range	){
			$range = trim($range);
				// 12.3..456.89
			if ( strpos( $range, '..' ) !== FALSE ) {
				list($min, $max) = explode('..',$range, 2);
				if ( $value >= (float)$min && $value <= (float)$max ){
					$result = true;
				} 
				// < 6.25
			} else if ( substr($range,0,1) == '<' ){
				$min = (float)substr($range,1);
				if ($value < $min ){
					$result = true;
				} 
				// > 6.25
			} else if ( substr($range,0,1) == '>' ){
				$max = (float)substr($range,1);
				if ($value > $max ){
					$result = true;
				} 
				// = 6.25
			} else if ( substr($range,0,1) == '=' ){
				$target = (float)substr($range,1);
				if ($target == $value ){
					$result = true;
				} 
			}	
		}
		return $result;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/services/class.tx_caretaker_typo3_extensions.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/services/class.tx_caretaker_typo3_extensions.php']);
}
?>