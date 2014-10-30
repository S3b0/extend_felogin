<?php
	if ( !defined('TYPO3_MODE') ) {
		die('Access denied.');
	}

	$extKey = 'extend_felogin';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp'][$extKey] = 'S3b0\\ExtendFelogin\\Hooks\\UserAuth->hook_postUserLookUp';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['beforeRedirect'][$extKey] = 'S3b0\\ExtendFelogin\\Hooks\\FeLogin->hook_beforeRedirect';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['login_confirmed'][$extKey] = 'S3b0\\ExtendFelogin\\Hooks\\FeLogin->hook_login_confirmed';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['logout_confirmed'][$extKey] = 'S3b0\\ExtendFelogin\\Hooks\\FeLogin->hook_logout_confirmed';