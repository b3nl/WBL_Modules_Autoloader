<?php
	/**
	 * ./unit/modules/WBL/Modules/AutoloaderTest.php
	 * @author blange <b.lange@wbl-konzept.de>
	 * @category unittests
	 * @package WBL_Modules
	 * @version $id$
	 */

	require_once realpath(dirname(__FILE__) . '/../TestCase.php');

	/**
	 * Testing of the Autoloader.
	 * @author blange <b.lange@wbl-konzept.de>
	 * @category unittests
	 * @package WBL_Modules
	 * @version $id$
	 */
	class WBL_Modules_AutoloaderTest extends WBL_TestCase {
		/**
		 * The fixture.
		 * @var WBL_Modules_Autoloader
		 */
		protected $oFixture = null;

		/**
		 * (non-PHPdoc)
		 * @see unittests/unit/modules/WBL/WBL_TestCase::getGetterAndGetterRules()
		 */
		public function getGetterAndGetterRules() {
			return array(
				array(
					'getAutoloaderNamespaces',
					'setAutoloaderNamespaces',
					array(),
					array($aReturn = array(uniqid())),
					$aReturn
				),
				array(
					'getBaseDir',
					'setBaseDir',
					realpath(getShopBasePath() . '/modules') . DIRECTORY_SEPARATOR,
					array($sReturn = getShopBasePath()),
					realpath($sReturn) . DIRECTORY_SEPARATOR
				),
				array(
					'getCoreOverrides',
					'addCoreOverride',
					array(),
					array($sClass = 'Test', $sModule = uniqid()),
					array('test' => $sModule)
				),
				array(
					'getCoreOverrides',
					'setCoreOverrides',
					array(),
					array($aReturn = array(uniqid())),
					$aReturn
				),
				array(
					'getFileEndings',
					'setFileEndings',
					array(WBL_Modules_Autoloader::DEFAULT_FILE_ENDING),
					array($aReturn = array(uniqid())),
					$aReturn
				)
			);
		} // function

		/**
		 * (non-PHPdoc)
		 * @see oxid_additionals/unittests/unit/OxidTestCase::setUp()
		 */
		public function setUp() {
			parent::setUp();

			$this->oFixture = wblNew('WBL_Modules_Autoloader');
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
		 * Checks if the overrides are added as strings.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testAddAndGetMultipleCoreOverrides() {
			$this->oFixture->addCoreOverride('oxsession', 'Test');
			$this->oFixture->addCoreOverride('oxSession', 'Test1');

			$this->assertSame(
				array('oxsession' => 'Test&Test1'),
				$this->oFixture->getCoreOverrides()
			);
		} // function

		/**
		 * Checks the constants.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testConstants() {
			$this->assertEquals('.php', WBL_Modules_Autoloader::DEFAULT_FILE_ENDING);
			$this->assertEquals(
				'aEosNeoAutoloaderFilePaths',
				WBL_Modules_Autoloader::FILE_CACHE_KEY
			);
		} // function

		/**
		 * Testing of the inclide with a valid class.
		 * @return void
		 */
		public function testIncludeClass() {
			$this->oFixture = $this->getProxyClass('WBL_Modules_Autoloader');

			$this->oFixture
				->setAutoloaderNamespaces(array('WBL'))
				->setBaseDir(realpath(dirname(__FILE__) . '/../../') . DIRECTORY_SEPARATOR);

			$this->assertTrue($this->oFixture->includeClass('WBL_Modules_Test_Class'));
		} // function

		/**
		 * Koennen PHP-Namespaces korrekt aufgeloest werden?
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testIncludeClassAndPHPNamespaces() {
			if (version_compare(PHP_VERSION, '5.3.0', '<'))
			{
				$this->markTestSkipped('No matching php version.');
			} // if

			$this->oFixture = $this->getProxyClass('WBL_Modules_Autoloader');

			$this->oFixture
				->setAutoloaderNamespaces(array('WBL'))
				->setBaseDir(realpath(dirname(__FILE__) . '/../../') . DIRECTORY_SEPARATOR);

			$this->assertTrue($this->oFixture->includeClass('\WBL\Modules\Test\Namespaces\Class'));
		} // function

		/**
		 * Checks if a core class overwrite is working.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testIncludeClassCoreOverride() {
			$this->oFixture
				->addCoreOverride($sDummy = uniqid(), 'WBL_Modules_Test_Article')
				->setAutoloaderNamespaces(array('WBL'))
				->setBaseDir(realpath(dirname(__FILE__) . '/../../') . DIRECTORY_SEPARATOR);

			$this->oFixture->includeClass($sDummy);
			$this->assertTrue(class_exists('WBL_Modules_Test_Article', false), 'Existing check failed.');
			$this->assertTrue(
				array_key_exists($sDummy, oxConfig::getInstance()->getConfigParam('aModules')), 'Config check failed.'
			);
		} // function

		/**
		 * False must be retuned, when the class can not be found.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function testIncludeClassDefault() {
			$this->oFixture = $this->getProxyClass('WBL_Modules_Autoloader');

			$this->oFixture
				->setAutoloaderNamespaces(array('WBL'))
				->setBaseDir(realpath(dirname(__FILE__) . '/../../') . DIRECTORY_SEPARATOR);

			$this->assertFalse($this->oFixture->includeClass('WBL_Modules_Test_Classnonexisting'));
		} // function

		/**
		 * Checks if the file is loaded from the OXID-Cache.
		 * @return void
		 */
		public function testIncludeClassFromCache() {
			$this->oFixture = $this->getProxyClass('WBL_Modules_Autoloader');

			$this->oFixture
				->setAutoloaderNamespaces(array('WBL'))
				->setBaseDir(realpath(dirname(__FILE__) . '/../../') . DIRECTORY_SEPARATOR);

			modInstances::addMod(
				'oxutils',
				$oMock = $this->getMock(
					'oxUtils', array('fromPhpFileCache'), array(), '', false
				)
			);

			$oMock
				->expects($this->AtLeastOnce())
				->method('fromPhpFileCache')
				->with(WBL_Modules_Autoloader::FILE_CACHE_KEY)
				->will(
					$this->returnValue(
						array(
							'WBL_Modules_Test_Class' =>
								$this->oFixture->getBaseDir() . 'WBL/Modules/Test/Class.php'
						)
					)
				);

			unset($oMock);

			$this->assertTrue($this->oFixture->includeClass('WBL_Modules_Test_Class'));
		} // function

		/**
		 * False should be returned, if the cache is disabled.
		 * @return void
		 */
		public function testIncludeClassFromCacheWithoutCache() {
			$this->oFixture = $this->getProxyClass('WBL_Modules_Autoloader');

			$this->oFixture->withFileCaching(false);
			$this->assertFalse($this->oFixture->includeClassFromCache('WBL_Modules_Test_Class'));
		} // function

		/**
		 * Checks if *_parent-Classes are ignored.
		 * @return void
		 */
		public function testIncludeClassParent() {
			$this->oFixture->setAutoloaderNamespaces(array('WBL'));

			$this->assertFalse($this->oFixture->includeClass('WBL_Modules_Test_Class_parent'));
		} // function

		/**
		 * There should be no actions without namespaces to check.
		 * @return void
		 */
		public function testIncludeClassWithoutNamespaces() {
			$this->assertFalse($this->oFixture->includeClass(uniqid()));
		} // function

		/**
		 * Tries to load a non existing class.
		 * @return void
		 * @expectedException oxSystemComponentException
		 */
		public function testWBLNew() {
			wblNew('WBL_Modules_Dummy_' . mt_rand(1, 999999));
		} // function

		/**
		 * Checks the default return.
		 * @return void
		 */
		public function testWithFileCacheWithChange() {
			$this->assertTrue($this->oFixture->withFileCaching(), '1. Check failed.');
			$this->assertTrue($this->oFixture->withFileCaching(false), '2. Check failed.');
			$this->assertFalse($this->oFixture->withFileCaching(), '3. Check failed.');
			$this->assertFalse($this->oFixture->withFileCaching(true), '4. Check failed.');
			$this->assertTrue($this->oFixture->withFileCaching(), '5. Check failed.');
		} // function
	} // class