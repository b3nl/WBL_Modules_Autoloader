<?php
	/**
	 * Module-Metadata for the WBL Autoloader.
	 * @author blange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @subpackage oxAutoload
	 * @version SVN: $Id$
	 */

	$sMetadataVersion = '1.0';

	$aModule = array(
		'author'       => 'WBL Konzept',
		'description'  => array(
			'de' => 'Spezieller Autoloader um ZEND Framework Strukturen zu erlauben',
			'en' => 'Special Autoloader to allow ZEND Framework structures'
		),
		'email'        => 'code@wbl-konzept.de',
		'extend' => array(
			'oxmodulelist'  => 'WBL_Modules_ModuleList'
		),
		'id'           => 'WBL_Modules',
		'title'        => 'WBL Modules Autoloader',
		'thumbnail'    => 'wbl_logo.jpg',
		'url'          => 'http://wbl-konzept.de',
		'version'      => '1.0.0'
	);