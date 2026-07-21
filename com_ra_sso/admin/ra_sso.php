<?php
/**
 * @package    Joomla.Administrator
 * @subpackage com_ra_sso
 *
 * @author    East Cheshire Ramblers
 * @copyright Copyright (C) 2026 East Cheshire Ramblers. Based on original work Copyright (C) 2015 miniOrange.
 * @license   GNU General Public License version 3; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

require_once JPATH_COMPONENT . '/helpers/ra_sso_customer_setup.php';
require_once JPATH_COMPONENT . '/helpers/ra_sso_utility.php';
require_once JPATH_COMPONENT . '/helpers/oauth_handler.php';
require_once JPATH_COMPONENT . '/helpers/ra_sso_logger.php';

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_ra_sso')) {
    throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('ra_sso', JPATH_COMPONENT_ADMINISTRATOR);
 
// Get an instance of the controller prefixed by JoomlaIdp
$controller = BaseController::getInstance('RaSso');
 
// Perform the Request task
$app = Factory::getApplication();
if (method_exists($app, 'getInput')) {
    $input = $app->getInput();
} else { // Joomla 3
    $input = $app->input;
}
$controller->execute($input->get('task'));

// Redirect if set by the controller
$controller->redirect();
