<?php
	/**
	 * ./unittests/unit/modules/WBL/TestCase.php
	 * @author blange <code@wbl-konzept.de>
	 * @category unittests
	 * @package WBL
	 * @version SVN: $Id$
	 */

	$sBaseTestClassDir = dirname(__FILE__);
	require_once realpath($sBaseTestClassDir . '/../../OxidTestCase.php');
	require_once realpath($sBaseTestClassDir . '/../../test_config.inc.php');
	require_once realpath(getShopBasePath() . '/modules/functions.php');
	unset($sBaseTestClassDir);

	/**
	 * Baseclass for tests.
	 * @author blange <code@wbl-konzept.de>
	 * @category unittests
	 * @package WBL
	 * @version SVN: $Id$
	 */
	class WBL_TestCase extends OxidTestCase {
		/**
		 * The tested class.
		 * @var object
		 */
		protected $oFixture = null;

		/**
		 * Returnt Regeln um die Getter und Setter zu testen.
		 * @return array
		 * @author Bjoern Simon Lange <code@wbl-konzept.de>
		 */
		public function getGetterAndGetterRules() {
			return array();
		} // function

		/**
		 * Testet Getter und Setter.
		 * @dataProvider getGetterAndGetterRules
		 * @param  string $sGetterName          Der Gettername.
		 * @param  string $sSetterName          Der Settername.
		 * @param  mixed  $mDefaultGetterReturn Der Standard-Return des Getter.
		 * @param  array  $aSetterParams        Die Parameter des Setters.
		 * @param  mixed  $mGetterReturn        Welcher Return-Wert hat der Getter nach dem Setter.
		 * @param  mixed  $mGetterParams        Die Parameter des Getters.
		 * @param  mixed  $mSetterReturn        Welcher Returnwert hat der Setter. Wird dieser
		 *                                      Parameter nicht genutzt, wird das fluent-Interface
		 *                                      kontrolliert.
		 * @return void
		 * @author Bjoern Simon Lange <code@wbl-konzept.de>
		 */
		public function testGetterAndSetters($sGetterName, $sSetterName, $mDefaultGetterReturn,
			array $aSetterParams, $mGetterReturn, $mGetterParams = null, $mSetterReturn = null)
		{
			$this->oFixture = $this->getProxyClass(get_class($this->oFixture));

			if (func_num_args() < 7) {
				$mSetterReturn = $this->oFixture;
			} // if

			if (is_array($mDefaultGetterReturn) &&
				(array_key_exists($sKey = 'sWBLTestType', $mDefaultGetterReturn)))
			{
				$this->assertType(
					$mDefaultGetterReturn[$sKey],
					call_user_func_array(
						array($this->oFixture, $sGetterName),
						!$mGetterParams ? array() : $mGetterParams
					),
					'Default check failed.'
				);
			} else {
				$this->assertSame(
					$mDefaultGetterReturn,
					call_user_func_array(
						array($this->oFixture, $sGetterName),
						!$mGetterParams ? array() : $mGetterParams
					),
					'Default check failed.'
				);
			} // else

			$this->assertSame(
				$mSetterReturn,
				call_user_func_array(
					array($this->oFixture, $sSetterName),
					$aSetterParams
				),
				'Setter return check failed.'
			);

			$this->assertSame(
				$mGetterReturn,
				call_user_func_array(
					array($this->oFixture, $sGetterName),
					!$mGetterParams ? array() : $mGetterParams
				),
				'Getter Check Failed.'
			);
		} // function

		/**
		 * Mapper um ein Modul zu laden, sollte es im Backend noch nicht eingetragen sein.
		 * @param  string $sOriginalClass Der Originalklasse von OXID.
		 * @param  string $sModuleClass   Der Klassenname des Moduls.
		 * @return object
		 * @author blange <code@wbl-konzept.de>
		 */
		protected function getOXIDModuleForWBL($sOriginalClass, $sModuleClass) {
			if (!is_subclass_of($oObject = wblNew($sOriginalClass), $sModuleClass)) {
				if (!class_exists($sModuleClass, false)) {
					eval('class ' . $sModuleClass . '_parent extends ' . $sOriginalClass . ' { }');

					spl_autoload_call($sModuleClass);
				} // if

				$oObject = wblNew($sModuleClass);
			} // if

			return $oObject;
		} // function
	} // class