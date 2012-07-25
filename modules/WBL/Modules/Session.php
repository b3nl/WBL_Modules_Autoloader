<?php
	/**
	 * ./modules/WBL/Modules/Session.php
	 * @author Bjoern Simon Lange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @subpackage oxSession
	 * @version SVN: $Id$
	 */

	/**
	 * Bufixing for OXID 4.6.2.
	 * @author Bjoern Simon Lange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @subpackage oxSession
	 * @version SVN: $Id$
	 */
	class WBL_Modules_Session extends oxSession {
		/**
		 * (non-PHPdoc)
		 * @see http/core/oxSession::getBasket()
		 */
		public function getBasket() {
			if (!$this->_oBasket) {
				// include against bug #4262
				oxNew('oxbasketitem');
				oxNew('oxbasket');
			} // if

			return parent::getBasket();
		} // function
	} // class