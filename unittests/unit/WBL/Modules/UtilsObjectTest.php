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

		/**
		 * The disabled module is part of the chain, because oxid can not unset it.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testGetActiveModuleChainNoAutoloader() {
			$this->oFixture = $this->getProxyClass(get_class($this->oFixture));
			$this->oFixture->setNonPublicVar('mWBLAutoloader', null);

			$oConfig = modConfig::getInstance();
			$oConfig->setConfigParam('aDisabledModules', array('WBL_Test'));
			$oConfig->setConfigParam('aModulePaths', array('WBL_Test' => 'WBL/Test', 'WBL_Test2' => 'WBL/Test2'));
			unset($oConfig);


			$this->assertSame(
				$aModules = array('WBL_Test_Dummy', 'WBL_Test2_Dummy'),
				$this->oFixture->_getActiveModuleChain($aModules)
			);
		} // function

		/**
		 * The module chain should not be changed.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testGetActiveModuleChainNoDisabledClasses() {
			$this->oFixture = $this->getProxyClass(get_class($this->oFixture));
			$this->oFixture->setWBLAutoloader($oLoader = wblNew('WBL_Modules_Autoloader'));
			modConfig::getInstance()->setConfigParam('aDisabledModules', array());

			$oLoader->setAutoloaderNamespaces(array('WBL'));

			$this->assertSame(
				$aModules = array('WBL_Test_Dummy'),
				$this->oFixture->_getActiveModuleChain($aModules)
			);
		} // function

		/**
		 * The parent should not be called, if the module logics removes all modules.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testGetActiveModuleChainNoParentCall() {
			$this->oFixture = $this->getProxyClass(get_class($this->oFixture));
			$this->oFixture->setWBLAutoloader($oMock = $this->getMock('WBL_Modules_Autoloader', array('isIncludeAllowed')));

			$oConfig = modConfig::getInstance();
			$oConfig->setConfigParam('aDisabledModules', array('WBL_Test'));
			$oConfig->setConfigParam('aModulePaths', array('WBL_Test' => 'WBL/Test'));
			unset($oConfig);

			$oMock
				->expects($this->once())
				->method('isIncludeAllowed')
				->with($sClass = 'WBL_Test_Dummy')
				->will($this->returnValue(true));
			$oMock->setAutoloaderNamespaces(array('WBL'));
			unset($oMock);

			$this->assertSame(
				array(),
				$this->oFixture->_getActiveModuleChain(array($sClass))
			);
		} // function

		/**
		 * The parent call should be combined, with the module logics.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testGetActiveModuleChainWithParentCall() {
			$this->oFixture = $this->getProxyClass(get_class($this->oFixture));
			$this->oFixture->setWBLAutoloader($oLoader = wblNew('WBL_Modules_Autoloader'));

			$oConfig = modConfig::getInstance();
			$oConfig->setConfigParam('aDisabledModules', array('WBL_Test'));
			$oConfig->setConfigParam('aModulePaths', array('WBL_Test' => 'WBL/Test', 'WBL_Test2' => 'WBL/Test2'));
			unset($oConfig);

			$oLoader->setAutoloaderNamespaces(array('WBL'));
			unset($oLoader);

			$this->assertSame(
				array(1 => $sClass2 = 'WBL_Test2_Dummy', 2 => $sClass3 = 'dir/module'),
				$this->oFixture->_getActiveModuleChain(array('WBL_Test_Dummy', $sClass2, $sClass3))
			);
		} // function

		/**
		 * Checks the internal calls.
		 * @author blange <code@wbl-konzept.de>
		 * @expectedException RuntimeException
		 * @return void
		 */
		public function testOxNewDelegation() {
			$this->oFixture = $this->getMock(get_class($this->oFixture), array('removeCoreOverridesFromConfig'));

			$this->oFixture
				->expects($this->once())
				->method('removeCoreOverridesFromConfig')
				->with($sClass = 'oxSession')
				->will($this->throwException(new RuntimeException()));

			$this->oFixture->oxNew($sClass);
		} // function

		public function testRemoveCoreOverridesFromConfig() {
			$this->oFixture = $this->getProxyClass(get_class($this->oFixture));

			$this->oFixture->setWBLAutoloader($oAutoloader = new WBL_Modules_Autoloader());
			$oAutoloader
				->addCoreOverride('oxsession', 'Test')
				->addCoreOverride('oxsession', 'Test1');
			unset($oAutoloader);

			modConfig::getInstance()->setConfigParam('aModules', array('oxsession' => 'Test3&Test&Test1'));
			$this->oFixture->removeCoreOverridesFromConfig('oxSession');
			$this->assertEquals(
				array('oxsession' => 'Test3&'),
				modConfig::getInstance()->getConfigParam('aModules')
			);
		} // function

		/**
		 * Checks the type of the class.
		 * @author blange <b.lange@wbl-konzept.de>
		 * @return void
		 */
		public function testType() {
			$this->assertType('WBL_Modules_UtilsObject', $oClass = oxNew('oxutilsobject'));
			$this->assertType('WBL_Modules_UtilsObject_parent', $oClass);
		} // function
	} // class