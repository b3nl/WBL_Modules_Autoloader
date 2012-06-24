<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2011
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

if (getenv('OXID_TEST_UTF8')) {
    define ('oxTESTSUITEDIR', 'unitUtf8');
} else {
    define ('oxTESTSUITEDIR', 'unit');
}

require_once 'PHPUnit/Framework/TestSuite.php';
error_reporting( (E_ALL ^ E_NOTICE) | E_STRICT );
ini_set('display_errors', true);

echo "=========\nrunning php version ".phpversion()."\n\n============\n";

require_once 'unit/test_config.inc.php';
require_once realpath(getShopBasePath() . '/modules/functions.php');

/**
 * PHPUnit_Framework_TestCase implemetnation for adding and testing all unit tests from unit dir
 */
class WBLModulesTests extends PHPUnit_Framework_TestCase
{
    /**
     * Test suite
     *
     * @return object
     */
	static function suite()
	{
		$oSuite = new PHPUnit_Framework_TestSuite( 'PHPUnit' );

		$sBasePath    = realpath(dirname(__FILE__) . '/unit/modules') . DIRECTORY_SEPARATOR;
		$sPath        = realpath($sBasePath . '/WBL/Modules') . DIRECTORY_SEPARATOR;
		$oDirectoryIt = new RecursiveDirectoryIterator($sPath);
		$oRegexIt     = new RegexIterator(
			new RecursiveIteratorIterator($oDirectoryIt),
			'/^.+Test\.php$/i',
			RecursiveRegexIterator::GET_MATCH
		);

		foreach ( $oRegexIt as $aFiles) {
			require_once $aFiles[0];

			$sFileName = str_replace($sBasePath, '', $aFiles[0]);
			error_reporting( (E_ALL ^ E_NOTICE) | E_STRICT );
			ini_set('display_errors', true);

			$sClassName = str_replace( array('/', '.php'), array('_', ''), $sFileName);
			if ( class_exists( $sClassName ) ) {
				echo "\nAdding {$sClassName}\n";
				$oSuite->addTestSuite( $sClassName );
			} else {
				echo "\n\nWarning: class not found: $sClassName in $sFilename\n\n\n ";
			}
		}

		return $oSuite;
	} // function
} // class
