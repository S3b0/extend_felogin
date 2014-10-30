<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 29.10.14
 * Time: 10:02
 */

namespace S3b0\ExtendFelogin\Hooks;


class FeLogin extends \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication {

	public $name = 'typo_user_pn';
	public $forceSetCookie = TRUE;
	public $loginType = '';

	public function hook_login_confirmed(array $params = array(), \TYPO3\CMS\Felogin\Controller\FrontendLoginController $frontendLoginController = NULL) {
		if ( TYPO3_MODE === 'FE' ){
			$this->loginType = 'login';
			/** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe */
			$tsfe = $GLOBALS['TSFE'];
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extend_felogin']);

			if ( $tsfe->loginUser && strpos(',' . $tsfe->fe_user->user['usergroup'] . ',', ',' . $extConf['pnGroup'] . ',') !== FALSE && !$this->isCookieSet() ) {
				$this->start();
				$this->setSessionCookie();
			}
		}
	}

	public function hook_logout_confirmed(array $params = array(), \TYPO3\CMS\Felogin\Controller\FrontendLoginController $frontendLoginController = NULL) {
		if ( TYPO3_MODE === 'FE' ) {
			$this->loginType = 'logout';

			if ( $this->isCookieSet() ) {
				$this->start();
				$this->removeCookie($this->name);
			}
		}
	}

	public function hook_beforeRedirect(array $params = array(), \TYPO3\CMS\Felogin\Controller\FrontendLoginController $frontendLoginController = NULL) {
		if ( TYPO3_MODE === 'FE' ) {
			$this->loginType = $params['loginType'];

			if ( $this->loginType === 'login' ) {
				/** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe */
				$tsfe = $GLOBALS['TSFE'];
				$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extend_felogin']);

				if ( $tsfe->loginUser && strpos(',' . $tsfe->fe_user->user['usergroup'] . ',', ',' . $extConf['pnGroup'] . ',') !== FALSE && !$this->isCookieSet() ) {
					$this->start();
					$this->setSessionCookie();
				}
			} elseif ( $this->loginType === 'logout' && $this->isCookieSet() ) {
				$this->start();
				$this->removeCookie($this->name);
			}
		}
	}

}