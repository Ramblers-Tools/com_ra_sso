<?php
/**
 * Installer script for RA SSO Error Redirect.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class PlgSystemRaSsoErrorRedirectInstallerScript
{
	public function postflight($type, $parent)
	{
		if ($type === 'uninstall') {
			return true;
		}

		$app = Factory::getApplication();
		$db = method_exists($app, 'getDatabase') ? $app->getDatabase() : Factory::getDbo();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . ' = 1')
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('element') . ' = ' . $db->quote('ra_sso_error_redirect'));

		$db->setQuery($query);
		$db->execute();

		return true;
	}
}
