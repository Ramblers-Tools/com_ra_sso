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
use Joomla\CMS\MVC\Controller\BaseController;
class RaSsoController extends BaseController
{
    /**
     * The default view for the display method.
     *
     * @var   string
     * @since 12.2
     */
    protected $default_view = 'accountsetup';
}
