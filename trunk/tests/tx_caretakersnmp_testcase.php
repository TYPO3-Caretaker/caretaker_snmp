<?php 

require_once (t3lib_extMgm::extPath('caretaker').'classes/nodes/class.tx_caretaker_Test.php');
require_once (t3lib_extMgm::extPath('caretaker').'classes/nodes/class.tx_caretaker_Instance.php');
require_once (t3lib_extMgm::extPath('caretaker_snmp').'services/class.tx_caretakersnmp.php');

class tx_caretakersnmp_testcase extends tx_phpunit_testcase  {

	/*	
	var $browser;
	var $selenium_host;
	var $test_host;
	var $test_query;
	*/
	
	function setUp(){
		$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker_snmp']);
		/*
		$this->browser         = $confArray['unittest.']['browser'];
		$this->selenium_host   = $confArray['unittest.']['host'];
		$this->baseURL         = $confArray['unittest.']['baseURL'];
		$this->testQuery       = $confArray['unittest.']['testQuery'];
		*/
	}
	
	function test_valueComparison(){
		$snmp_test_service = new tx_caretakersnmp();
	
		$this->assertEquals(true,  $snmp_test_service->isValueInRange('15.8' , '< 16') );
		$this->assertEquals(false, $snmp_test_service->isValueInRange('15.8' , '< 12') );
		$this->assertEquals(true,  $snmp_test_service->isValueInRange('17' ,   '< 20') );
		
		$this->assertEquals(true,  $snmp_test_service->isValueInRange('17'   , '> 16') );
		$this->assertEquals(false, $snmp_test_service->isValueInRange('15'   , '> 16') );
		$this->assertEquals(true,  $snmp_test_service->isValueInRange('15.8' , '> 15') );
		
		$this->assertEquals(true,  $snmp_test_service->isValueInRange('15.8' , '14..16') );
		$this->assertEquals(true,  $snmp_test_service->isValueInRange('12' ,   '12..16') );
		$this->assertEquals(true,  $snmp_test_service->isValueInRange('16' ,   '14..16') );
		
		$this->assertEquals(true,  $snmp_test_service->isValueInRange('6' ,    '= 6') );
		$this->assertEquals(true,  $snmp_test_service->isValueInRange('8.12' , '= 8.12') );
		$this->assertEquals(false,  $snmp_test_service->isValueInRange('8.123', '= 4') );
		
		$this->assertEquals(true,  $snmp_test_service->isValueInRange('12' ,    '=6:10..14') );
		$this->assertEquals(true,  $snmp_test_service->isValueInRange('32' ,    '=6:10..14:>30') );
		$this->assertEquals(false, $snmp_test_service->isValueInRange('8' ,     '=6:10..14:>30') );
				
	}
}

?>