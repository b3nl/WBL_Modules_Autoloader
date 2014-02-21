<?php
    /**
     * Basis-API fuer das PayPal-Modul.
     * @author     Bjoern Simon Lange <code@wbl-konzept.de>
     * @category   modules
     * @version    SVN: $Id$
     */

    /**
     * Basis-API fuer das PayPal-Modul.
     * @author     Bjoern Simon Lange <code@wbl-konzept.de>
     * @category   modules
     * @package    WBL_Object
     * @subpackage Object
     * @version    SVN: $Id$
     */
    interface WBL_Modules_Object_Object_Interface
    {
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
        public function __call($sName, $aArgs); // function

        /**
         * Low level Getter fuer die Daten mit dem entsprechenden Key.
         * @author Bjoern Lange <code@wbl-konzept.de>
         * @param  string $sKey Der Key.
         * @return mixed
         */
        public function __get($sKey); // function

        /**
         * Returnt true, falls der Wert schon eingefuegt wurden. Null evaluiert nicht zu true.
         * @author Bjoern Lange <code@wbl-konzept.de>
         * @param  string $sKey Der Key.
         * @return bool
         */
        public function __isset($sKey); // function

        /**
         * Low level Setter fuer die Daten mit dem entsprechenden Key.
         * @author Bjoern Lange <code@wbl-konzept.de>
         * @param  string $sKey   Der Key der Eigenschaft.
         * @param  mixed  $mValue Der Wert der Eigenschaft.
         * @return void
         */
        public function __set($sKey, $mValue); // function

        /**
         * Entfernt die eingefuegten Daten.
         * @author Bjoern Lange <code@wbl-konzept.de>
         * @param  string $sKey Key der Eigenschaft.
         * @return void
         */
        public function __unset($sKey); // function

        /**
         * Low level Getter fuer die Daten mit dem entsprechenden Key.
         * @author Bjoern Lange <code@wbl-konzept.de>
         * @param  string $sKey     Der Key.
         * @param  mixed  $mDefault Der moegliche Standard-Wert, wenn Eigenschaft noch fehlt.
         * @return mixed
         */
        public function getWBLOData($sKey = '', $mDefault = null); // function

        /**
         * Returnt true, falls die Daten, auch null, schon gesetzt wurden.
         * @author Bjoern Lange <code@wbl-konzept.de>
         * @param  string $sKey Der Key.
         * @return bool
         */
        public function hasWBLOData($sKey); // function

        /**
         * Low level Setter fuer die Daten mit dem entsprechenden Key.
         * @author Bjoern Lange <code@wbl-konzept.de>
         * @param  string|array $mKeyOrData Der Key der Eigenschaft.
         * @param  mixed        $mValue     Der Wert der Eigenschaft.
         * @return \WBL_Modules_Object_Abstract
         */
        public function setWBLOData($mKeyOrData, $mValue = null); // function

        /**
         * Entfernt die Eigenschaft.
         * @author Bjoern Lange <code@wbl-konzept.de>
         * @param  string $sKey Falls der Parameter uebergeben wurde, wird nur die Eigenschaft
         *                      entfernt, ansonsten Alles.
         * @return \WBL_Modules_Object_Abstract
         */
        public function unsetWBLOData($sKey = ''); // function
    } // class