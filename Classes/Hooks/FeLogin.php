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
		$this->setPNCookieName();
		if ( TYPO3_MODE === 'FE' ){
			/** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe */
			$tsfe = $GLOBALS['TSFE'];
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extend_felogin']);

			if ( $tsfe->loginUser && strpos(',' . $tsfe->fe_user->user['usergroup'] . ',', ',' . $extConf['pnGroup'] . ',') !== FALSE && !$this->isCookieSet() ) {
				$this->loginType = 'login';
				$this->start();
				$settings = $GLOBALS['TYPO3_CONF_VARS']['SYS'];
				// Get the domain to be used for the cookie (if any):
				$cookieDomain = $this->getCookieDomain();
				// If no cookie domain is set, use the base path:
				$cookiePath = $cookieDomain ? '/' : \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');
				// If the cookie lifetime is set, use it:
				$cookieExpire = $this->lifetime ? $GLOBALS['EXEC_TIME'] + $this->lifetime : 0;
				// Use the secure option when the current request is served by a secure connection:
				$cookieSecure = (bool) $settings['cookieSecure'] && \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SSL');
				// Deliver cookies only via HTTP and prevent possible XSS by JavaScript:
				$cookieHttpOnly = (bool) $settings['cookieHttpOnly'];

				// Do not set cookie if cookieSecure is set to "1" (force HTTPS) and no secure channel is used:
				if ((int)$settings['cookieSecure'] !== 1 || \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SSL')) {
					setcookie($this->name, $this->id, $cookieExpire, $cookiePath, $cookieDomain, $cookieSecure, $cookieHttpOnly);
					$this->cookieWasSetOnCurrentRequest = TRUE;
				}
			}
		}
	}

	public function hook_logout_confirmed(array $params = array(), \TYPO3\CMS\Felogin\Controller\FrontendLoginController $frontendLoginController = NULL) {
		$this->setPNCookieName();
		if ( TYPO3_MODE === 'FE' ) {
			$this->loginType = 'logout';

			if ( $this->isCookieSet() ) {
				$this->start();
				$settings = $GLOBALS['TYPO3_CONF_VARS']['SYS'];
				// Get the domain to be used for the cookie (if any):
				$cookieDomain = $this->getCookieDomain();
				// If no cookie domain is set, use the base path:
				$cookiePath = $cookieDomain ? '/' : \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');

				setcookie($this->name, NULL, -1, $cookiePath, $cookieDomain);
			}
		}
	}

	public function hook_beforeRedirect(array $params = array(), \TYPO3\CMS\Felogin\Controller\FrontendLoginController $frontendLoginController = NULL) {
		$this->setPNCookieName();
		if ( TYPO3_MODE === 'FE' ) {
			$this->loginType = $params['loginType'];

			if ( $this->loginType === 'login' ) {
				/** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe */
				$tsfe = $GLOBALS['TSFE'];
				$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extend_felogin']);

				if ( $tsfe->loginUser && strpos(',' . $tsfe->fe_user->user['usergroup'] . ',', ',' . $extConf['pnGroup'] . ',') !== FALSE && !$this->isCookieSet() ) {
					$this->start();
					$settings = $GLOBALS['TYPO3_CONF_VARS']['SYS'];
					// Get the domain to be used for the cookie (if any):
					$cookieDomain = $this->getCookieDomain();
					// If no cookie domain is set, use the base path:
					$cookiePath = $cookieDomain ? '/' : \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');
					// If the cookie lifetime is set, use it:
					$cookieExpire = $this->lifetime ? $GLOBALS['EXEC_TIME'] + $this->lifetime : 0;
					// Use the secure option when the current request is served by a secure connection:
					$cookieSecure = (bool) $settings['cookieSecure'] && \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SSL');
					// Deliver cookies only via HTTP and prevent possible XSS by JavaScript:
					$cookieHttpOnly = (bool) $settings['cookieHttpOnly'];

					// Do not set cookie if cookieSecure is set to "1" (force HTTPS) and no secure channel is used:
					if ((int)$settings['cookieSecure'] !== 1 || \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SSL')) {
						setcookie($this->name, $this->id, $cookieExpire, $cookiePath, $cookieDomain, $cookieSecure, $cookieHttpOnly);
						$this->cookieWasSetOnCurrentRequest = TRUE;
					}
				}
			} elseif ( $this->loginType === 'logout' && $this->isCookieSet() ) {
				$this->start();
				$settings = $GLOBALS['TYPO3_CONF_VARS']['SYS'];
				// Get the domain to be used for the cookie (if any):
				$cookieDomain = $this->getCookieDomain();
				// If no cookie domain is set, use the base path:
				$cookiePath = $cookieDomain ? '/' : \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');

				setcookie($this->name, NULL, -1, $cookiePath, $cookieDomain);
			}
		}
	}

	public function setPNCookieName() {
		$this->name = 'typo_user_pn';
	}

}