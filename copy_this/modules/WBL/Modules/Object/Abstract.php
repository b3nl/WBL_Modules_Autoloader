<?php
	/**
	 * Basis-Objekt fuer das PayPal-Modul.
	 * @author     Bjoern Simon Lange <code@wbl-konzept.de>
	 * @category   modules
	 * @package    WBL_Object
	 * @version    SVN: $Id$
	 */

	/**
	 * Basis-Objekt fuer das PayPal-Modul.
	 * @author     Bjoern Simon Lange <code@wbl-konzept.de>
	 * @category   modules
	 * @package    WBL_Object
	 * @version    SVN: $Id$
	 */
	abstract class WBL_Modules_Object_Abstract extends \oxSuperCfg
	{
		/**
		 * Starts the method call with this string, the call is mapped for the GET/SET-API.
		 * @var array
		 */
		protected $aWBLObjectAPIPrefixes = array('get', 'has', 'set', 'unset');

		/**
		 * Die eingefuegten Daten,
		 * @var array
		 */
		protected $aWBLOData = array();

		/**
		 * Aktiviert ein High-Level API fuer Getter und Setter.
		 *
		 * Der Name der Eigenschaft kann ueber self::getPropertyNameFromWBLOMethod geparst werden, um
		 * die API sprechender zu gestalten.
		 * @author Bjoern Lange <code@wbl-konzept.de>
		 * @param  string $sName Der Name der Methode.
		 * @param  array  $aArgs Die Parameter der Methode.
		 * @return mixed
		 */
		public function __call($sName, $aArgs)
		{
			foreach ($this->aWBLObjectAPIPrefixes as $sPrefix)
			{
				if (strpos($sName, $sPrefix) === 0)
				{
					$sProp = $this->getPropertyNameFromWBLOMethod($sName, $sPrefix);

					return call_user_func_array(
						array($this, $sPrefix . 'WBLOData'),
						array_merge(array($sProp), $aArgs)
					);
				} // if
			} // if

			return parent::__call($sName, $aArgs);
		} // function

		/**
		 * Destruktor.
		 * @author Bjoern Lange <code@wbl-konzept.de>
		 */
		public function __destruct()
		{
			foreach (get_object_vars($this) as $sName => $mValue)
			{
				unset($mValue, $this->$sName, $sName);
			} // foreach
		} // function

		/**
		 * Low level Getter fuer die Daten mit dem entsprechenden Key.
		 * @author Bjoern Lange <code@wbl-konzept.de>
		 * @param  string $sKey Der Key.
		 * @return mixed
		 */
		public function __get($sKey)
		{
			return $this->getWBLOData($sKey, null);
		} // function

		/**
		 * Returnt true, falls der Wert schon eingefuegt wurden. Null evaluiert nicht zu true.
		 * @author Bjoern Lange <code@wbl-konzept.de>
		 * @param  string $sKey Der Key.
		 * @return bool
		 */
		public function __isset($sKey)
		{
			return isset($this->aWBLOData[$sKey]);
		} // function

		/**
		 * Low level Setter fuer die Daten mit dem entsprechenden Key.
		 * @author Bjoern Lange <code@wbl-konzept.de>
		 * @param  string $sKey   Der Key der Eigenschaft.
		 * @param  mixed  $mValue Der Wert der Eigenschaft.
		 * @return void
		 */
		public function __set($sKey, $mValue)
		{
			$this->setWBLOData($sKey, $mValue);
		} // function

		/**
		 * Entfernt die eingefuegten Daten.
		 * @author Bjoern Lange <code@wbl-konzept.de>
		 * @param  string $sKey Key der Eigenschaft.
		 * @return void
		 */
		public function __unset($sKey)
		{
			$this->unsetWBLOData($sKey);
		} // function

		/**
		 * Returnt den Namen der Eigenschaft fuer die magische GET/SET-API.
		 * @author Bjoern Lange <code@wbl-konzept.de>
		 * @param  string $sMethod Name der Eigenschaft.
		 * @param  string $sPrefix Das gefundene Praefix.
		 * @return string
		 */
		protected function getPropertyNameFromWBLOMethod($sMethod, $sPrefix)
		{
			return str_replace($sPrefix, '', $sMethod);
		} // function

		/**
		 * Low level Getter fuer die Daten mit dem entsprechenden Key.
		 * @author Bjoern Lange <code@wbl-konzept.de>
		 * @param  string $sKey     Der Key.
		 * @param  mixed  $mDefault Der moegliche Standard-Wert, wenn Eigenschaft noch fehlt.
		 * @return mixed
		 */
		public function getWBLOData($sKey = '', $mDefault = null)
		{
			$mReturn = $this->aWBLOData;

			if ($sKey)
			{
				$mReturn = $this->hasWBLOData($sKey) ? $this->aWBLOData[$sKey] : $mDefault;
			} // if

			return $mReturn;
		} // function

		/**
		 * Returnt true, falls die Daten, auch null, schon gesetzt wurden.
		 * @author Bjoern Lange <code@wbl-konzept.de>
		 * @param  string $sKey Der Key.
		 * @return bool
		 */
		public function hasWBLOData($sKey)
		{
			return array_key_exists($sKey, $this->aWBLOData);
		} // function

		/**
		 * Low level Setter fuer die Daten mit dem entsprechenden Key.
		 * @author Bjoern Lange <code@wbl-konzept.de>
		 * @param  string|array $mKeyOrData Der Key der Eigenschaft.
		 * @param  mixed        $mValue     Der Wert der Eigenschaft.
		 * @return \WBL_Modules_Object_Abstract
		 */
		public function setWBLOData($mKeyOrData, $mValue = null)
		{
			if (is_array($mKeyOrData))
			{
				$this->aWBLOData = $mKeyOrData;
			} // if
			elseif (is_string($mKeyOrData))
			{
				$this->aWBLOData[$mKeyOrData] = $mValue;
			} // elseif

			return $this;
		} // function

		/**
		 * Entfernt die Eigenschaft.
		 * @author Bjoern Lange <code@wbl-konzept.de>
		 * @param  string $sKey Falls der Parameter uebergeben wurde, wird nur die Eigenschaft
		 *                      entfernt, ansonsten Alles.
		 * @return \WBL_Modules_Object_Abstract
		 */
		public function unsetWBLOData($sKey = '')
		{
			if ($sKey)
			{
				unset($this->aWBLOData[$sKey]);
			} // if
			else
			{
				$this->aWBLOData = array();
			} // else

			return $this;
		} // function
	} // class