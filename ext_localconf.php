<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutDataProvider']['file']
		= 'GeorgRinger\\BelayoutFileprovider\\Provider\\FileProvider';

//	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutFileProvider']['dir'][]
//		=  'EXT:belayout_fileprovider/Resources/Private/Examples/BackendLayouts/';

}