<?php
	if ( !defined('TYPO3_MODE') ) {
		die('Access denied.');
	}

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp']['extend_felogin'] = 'S3b0\\ExtendFelogin\\Hooks\\UserAuth->hook_postUserLookUp';