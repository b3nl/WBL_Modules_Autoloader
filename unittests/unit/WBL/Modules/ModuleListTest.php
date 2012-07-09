<?php
	/**
	 * ./unit/modules/WBL/Modules/ModuleListTest.php
	 * @author blange <b.lange@wbl-konzept.de>
	 * @category unittests
	 * @package WBL_Modules
	 * @subpackage oxModuleList
	 * @version $id$
	 */

	require_once realpath(dirname(__FILE__) . '/../TestCase.php');

	/**
	 * Testing of WBL_Modules_ModuleList.
	 * @author blange <b.lange@wbl-konzept.de>
	 * @category unittests
	 * @package WBL_Modules
	 * @subpackage oxModuleList
	 * @version $id$
	 */
	class WBL_Modules_ModuleListTest extends WBL_TestCase {
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

			$this->oFixture = $this->getOXIDModuleForWBL('oxmodulelist', 'WBL_Modules_ModuleList');
		} // function

		/**
		 * (non-PHPdoc)
		 * @see oxid_additionals/unittests/unit/OxidTestCase::tearDown()
		 */
		public function tearDown() {
			parent::tearDown();

			$this->oFixture = null;
		} // function

		/**
		 * Checks the module calls.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testGetDeletedExtensions() {
			$this->oFixture = $this->getMock(get_class($this->oFixture), array('getAllModules', 'removeWBLModules'));

			$this->oFixture
				->expects($this->once())
				->method('getAllModules')
				->will($this->returnValue(array(uniqid() => array(uniqid()))));

			$this->oFixture
				->expects($this->once())
				->method('removeWBLModules')
				->will($this->returnValue($aReturn = array(uniqid())));

			$this->assertEquals($aReturn, $this->oFixture->getDeletedExtensions());
		} // function

		/**
		 * Checks if the parent call is extended with the autoloader.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testGetDisabledModuleClassesExtended() {
			$oConfig = modConfig::getInstance();
			$oConfig->setConfigParam('aDisabledModules', array('WBL_Test'));
			$oConfig->setConfigParam('aModules', array('test' => '\WBL_Test_Dummy'));
			unset($oConfig);

			$this->oFixture->setWBLAutoloader(
				$oMock = $this->getMock('WBL_Modules_Autoloader', array('isIncludeAllowed'))
			);

			$oMock->setAutoloaderNamespaces(array('WBL'));

			$oMock
				->expects($this->once())
				->method('isIncludeAllowed')
				->with($sClass = '\\WBL_Test_Dummy')
				->will($this->returnValue(true));
			unset($oMock);

			$this->assertTrue(
				in_array('\\WBL_Test_Dummy', $this->oFixture->getDisabledModuleClasses()), 'Module was not deactivated.'
			);
		} // function

		/**
		 * Checks if the parent call is made without disabled classes.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testGetDisabledModuleClassesStandardNoDisabledClasses() {
			$this->oFixture = $this->getMock(get_class($this->oFixture), array('getDisabledModules'));

			$this->oFixture->setWBLAutoloader($oLoader = new WBL_Modules_Autoloader());
			$oLoader->setAutoloaderNamespaces(array('WBL'));
			unset($oLoader);

			$this->oFixture
				->expects($this->exactly(2))
				->method('getDisabledModules')
				->will($this->returnValue(array()));

			$this->assertSame(array(), $this->oFixture->getDisabledModuleClasses());
		} // function

		/**
		 * Checks if the parent call is made without an autoloader.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testGetDisabledModuleClassesStandard() {
			$this->oFixture = $this->getMock(
				get_class($this->getProxyClass(get_class($this->oFixture))),
				array('getDisabledModules')
			);

			$this->oFixture->setNonPublicVar('mWBLAutoloader', null);
			$this->oFixture
				->expects($this->once())
				->method('getDisabledModules')
				->will($this->returnValue(array('WBL_Test')));

			$this->assertFalse(
				in_array('\\WBL_Test_Dummy', $this->oFixture->getDisabledModuleClasses()), 'Module was deactivated.'
			);
		} // function

		/**
		 * The WBL-Modules should be removed from the array.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testRemoveWBLModules() {
			$this->oFixture = $this->getProxyClass(get_class($this->oFixture));

			$this->assertEquals(
				array('oxmodulelist' => array('testmodule1/testmodule')),
				$this->oFixture->removeWBLModules(
					array('oxmodulelist' => array('testmodule1/testmodule', 'WBL_Modules_Autoloader'))
				)
			);
		} // function
	} // class