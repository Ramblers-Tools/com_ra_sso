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
use Joomla\CMS\MVC\Model\AdminModel;
/**
 * AccountSetup Model
 *
 * @since 0.0.1
 */
class RaSsoModelAccountSetup extends AdminModel
{
    
    /**
     * Method to get the record form.
     *
     * @param array   $data     Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return mixed    A JForm object on success, false on failure
     *
     * @since 1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_ra_sso.accountsetup',
            'accountsetup',
            array(
            'control' => 'jform',
            'load_data' => $loadData
            )
        );
 
        if (empty($form)) {
            return false;
        }
 
        return $form;
    }
}
