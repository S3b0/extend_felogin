<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 29.10.14
 * Time: 16:00
 */

namespace S3b0\ExtendFelogin\Hooks;


class UserAuth extends \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication {

	/**
	 * Will force the session cookie to be set every time (lifetime must be 0)
	 * @var bool
	 */
	public $forceSetCookie = TRUE;

	/**
	 * Login type, used for services.
	 * @var string
	 */
	public $loginType = '';

	/**
	 * @param array                                                     $params
	 * @param \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication $abstractUserAuthentication
	 */
	public function hook_postUserLookUp(array $params = array(), \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication $abstractUserAuthentication = NULL) {
		if ($abstractUserAuthentication->loginType === 'FE') {
			$this->loginType = $abstractUserAuthentication->loginSessionStarted ? 'login' : 'logout';
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

			if ( $this->loginType === 'login' ) {
				$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extend_felogin']);
				if (strpos(',' . $abstractUserAuthentication->user['usergroup'] . ',', ',' . $extConf['pnGroup'] . ',') !== FALSE && !$this->isCookieSet() ) {
					// Do not set cookie if cookieSecure is set to "1" (force HTTPS) and no secure channel is used:
					if ((int)$settings['cookieSecure'] !== 1 || \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SSL')) {
						setcookie('typo_user_pn', $this->id, $cookieExpire, $cookiePath, $cookieDomain, $cookieSecure, $cookieHttpOnly);
						$this->cookieWasSetOnCurrentRequest = TRUE;
					}
				}
			} elseif ( $this->loginType === 'logout' && $this->isCookieSet() ) {
				$cookieDomain = $this->getCookieDomain();
				// If no cookie domain is set, use the base path
				$cookiePath = $cookieDomain ? '/' : \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');
				setcookie('typo_user_pn', NULL, -1, $cookiePath, $cookieDomain);
			}
		}
	}

}