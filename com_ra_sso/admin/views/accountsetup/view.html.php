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
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\HtmlView;
HTMLHelper::_('jquery.framework');
/**
 * Account Setup View
 *
 * @since 0.0.1
 */
class RaSsoViewAccountSetup extends HtmlView
{
    function display($tpl = null)
    {
        // Get data from the model
        $this->lists        = $this->get('List');
        //$this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            Factory::getApplication()->enqueueMessage(500, implode('<br />', $errors));

            return false;
        }
        $this->setLayout('accountsetup');
        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @since 1.6
     */
    protected function addToolBar()
    {
        ToolBarHelper::title(Text::_('COM_RA_SSO_PLUGIN_TITLE'));
    }
}
