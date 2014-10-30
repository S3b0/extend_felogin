<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 30.10.14
 * Time: 06:50
 */

	$extensionClassesPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extend_felogin') . 'Classes/';

	return array(
		'S3b0\ExtendFelogin\Hook\UserAuth' => $extensionClassesPath . 'Hooks/UserAuth.php'
	);
