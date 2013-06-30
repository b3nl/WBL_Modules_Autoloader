<?php
	require_once realpath(dirname(__FILE__) . '/WBL/Modules/Autoloader.php');
	$oWBLAutoloader = new WBL_Modules_Autoloader();

	spl_autoload_register(
		array(
			$oWBLAutoloader
				/*
				 * Remove after update to oxid 4.7 or add to config.inc.php if you instantiate your modules directly
				 * through the config file.
				 */
				->addCoreOverride('oxsession', 'WBL_Modules_Session')
				->addCoreOverride('oxutilsobject', 'WBL_Modules_UtilsObject')

				->setAutoloaderNamespaces(array('WBL'))
				->setFileEndings(array('.php')),
			'includeClass'
		)
	);
	unset($oWBLAutoloader);
