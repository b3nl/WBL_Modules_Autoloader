<?php
	/**
	 * ./modules/WBL/Modules/UtilsObjectTest.php
	 * @author Bjoern Simon Lange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @subpackage oxUtilsObject
	 * @version SVN: $Id$
	 */

	require_once realpath(dirname(__FILE__) . '/../TestCase.php');

	/**
	 * Testing of WBL_Modules_UtilsObject
	 * @author Bjoern Simon Lange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @subpackage oxUtilsObject
	 * @version SVN: $Id$
	 */
	class WBL_Modules_UtilsObjectTest extends WBL_TestCase {
		/**
		 * The fixture.
		 * @var WBL_Modules_ModuleList
		 */
		protected $oFixture = null;

		/**
		 * (non-PHPdoc)
		 * @see unittests/unit/modules/WBL/WBL_TestCase::getGetterAndGetterRules()
		 */
		public function getGetterAndGetterRules() {
			return array(
				array(
					'getWBLAutoloader',
					'setWBLAutoloader',
					array('sWBLTestType' => 'WBL_Modules_Autoloader'),
					array($oMock = $this->getMock('WBL_Modules_Autoloader')),
					$oMock
				)
			);
		} // function

		/**
		 * (non-PHPdoc)
		 * @see oxid_additionals/unittests/unit/OxidTestCase::setUp()
		 */
		public function setUp() {
			parent::setUp();

			if (version_compare(oxConfig::getInstance()->getVersion(), '4.6.0', '<')) {
				$this->markTestSkipped('No need to check this module.');
			} // if

			$this->oFixture = $this->getOXIDModuleForWBL('oxutilsobject', 'WBL_Modules_UtilsObject');
		} // function

		/**
		 * (non-PHPdoc)
		 * @see oxid_additionals/unittests/unit/OxidTestCase::tearDown()
		 */
		public function tearDown() {
			parent::tearDown();

			$this->oFixture = null;
		} // function
	} // class