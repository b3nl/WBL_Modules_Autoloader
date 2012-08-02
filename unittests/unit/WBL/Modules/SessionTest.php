<?php
	/**
	 * ./unit/modules/WBL/Modules/SessionTest.php
	 * @author blange <b.lange@wbl-konzept.de>
	 * @category unittests
	 * @package WBL_Modules
	 * @version $id$
	 */

	require_once realpath(dirname(__FILE__) . '/../TestCase.php');

	/**
	 * Testing of the Session-Fix.
	 * @author blange <b.lange@wbl-konzept.de>
	 * @category unittests
	 * @package WBL_Modules
	 * @version $id$
	 */
	class WBL_Modules_SessionTest extends WBL_TestCase {
		/**
		 * The fixture.
		 * @var WBL_Modules_Session
		 */
		protected $oFixture = null;

		/**
		 * (non-PHPdoc)
		 * @see oxid_additionals/unittests/unit/OxidTestCase::setUp()
		 */
		public function setUp() {
			parent::setUp();

			$this->oFixture = $this->getOXIDModuleForWBL('oxsession', 'WBL_Modules_Session');
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
		 * Checks the return of the method.
		 * @author blange <b.lange@wbl-konzept.de>
		 * @return void
		 */
		public function testGetBasket() {
			$this->assertType('oxbasket', $this->oFixture->getBasket());
		} // function

		/**
		 * Checks the type of the class.
		 * @author blange <b.lange@wbl-konzept.de>
		 * @return void
		 */
		public function testType() {
			$this->assertType('WBL_Modules_Session', $oSession = oxNew('oxsession'));
			$this->assertType('WBL_Modules_Session_parent', $oSession);
		} // function
	} // class