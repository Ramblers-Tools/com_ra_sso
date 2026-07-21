<?php
/**
 * Script file of ra_sso_plugin.
 *
 * @author    East Cheshire Ramblers
 * @copyright Copyright (C) 2026 East Cheshire Ramblers. Based on original work Copyright (C) 2015 miniOrange.
 * @license   GNU General Public License version 3; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class pkg_ra_sso_loginInstallerScript
{
    /**
     * This method is called after a component is installed.
     *
     * @param \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function install($parent) 
    {

            
    }

    /**
     * This method is called after a component is uninstalled.
     *
     * @param \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function uninstall($parent) 
    {
        //echo '<p>' . Text::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
    }

    /**
     * This method is called after a component is updated.
     *
     * @param \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function update($parent) 
    {
        //echo '<p>' . Text::sprintf('COM_HELLOWORLD_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
    }

    /**
     * Runs just before any installation action is performed on the component.
     * Verifications and pre-requisites should run in this function.
     *
     * @param string    $type   - Type of PreFlight action. Possible values are:
     *                          - * install
     *                          - * update
     *                          - * discover_install
     * @param \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function preflight($type, $parent) 
    {
        //echo '<p>' . Text::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
    }

    /**
     * Runs right after any installation action is performed on the component.
     *
     * @param string    $type   - Type of PostFlight action. Possible values are:
     *                          - * install
     *                          - * update
     *                          - * discover_install
     * @param \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    function postflight($type, $parent) 
    {
        // echo '<p>' . Text::_('COM_HELLOWORLD_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
        if ($type == 'uninstall') {
            return true;
        }

        $this->enablePlugin('system', 'ra_sso');
        $this->enablePlugin('system', 'ra_sso_error_redirect');
        $this->enablePlugin('webservices', 'ra_sso');
        $this->repairLoggingSchema();

        $this->showInstallMessage('');
    }

    protected function enablePlugin($folder, $element)
    {
        $app = Factory::getApplication();
        $db = method_exists($app, 'getDatabase') ? $app->getDatabase() : Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('enabled') . ' = 1')
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->where($db->quoteName('folder') . ' = ' . $db->quote($folder))
            ->where($db->quoteName('element') . ' = ' . $db->quote($element));

        $db->setQuery($query);
        $db->execute();
    }

    protected function repairLoggingSchema()
    {
        $db = $this->getDatabase();

        $query = "CREATE TABLE IF NOT EXISTS " . $db->quoteName('#__ra_sso_logs') . " (
            " . $db->quoteName('id') . " INT AUTO_INCREMENT PRIMARY KEY,
            " . $db->quoteName('timestamp') . " DATETIME NOT NULL,
            " . $db->quoteName('log_level') . " VARCHAR(10) NOT NULL,
            " . $db->quoteName('message') . " TEXT NOT NULL,
            " . $db->quoteName('file') . " VARCHAR(255),
            " . $db->quoteName('line_number') . " INT,
            " . $db->quoteName('function_call') . " VARCHAR(255)
        ) DEFAULT COLLATE=utf8_general_ci";

        $db->setQuery($query);
        $db->execute();

        $configTable = $db->replacePrefix('#__ra_sso_config');

        try {
            $columns = $db->getTableColumns($configTable, false);
        } catch (\Throwable $exception) {
            return;
        }

        if (!isset($columns['loggers_enable'])) {
            $query = "ALTER TABLE " . $db->quoteName('#__ra_sso_config') .
                " ADD COLUMN " . $db->quoteName('loggers_enable') . " TINYINT(1) NOT NULL DEFAULT 0";

            $db->setQuery($query);
            $db->execute();
        }
    }

    protected function getDatabase()
    {
        $app = Factory::getApplication();

        return method_exists($app, 'getDatabase') ? $app->getDatabase() : Factory::getDbo();
    }

    protected function showInstallMessage($messages=array())
    {
        $lang = Factory::getLanguage();
        $lang->load('pkg_ra_sso', JPATH_SITE) || $lang->load('pkg_ra_sso', JPATH_ADMINISTRATOR);
        ?>
        <style>
        
        .mo-row {
            width: 100%;
            display: block;
            margin-bottom: 2%;
        }
    
        .mo-row:after {
            clear: both;
            display: block;
            content: "";
        }

        .btn {
        display: inline-block;
        font-weight: 300;
        text-align: center;
        vertical-align: middle;
        user-select: none;
        background-color: transparent;
        border: 1px solid transparent;
        padding: 4px 12px;
        font-size: 0.85rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        } 
       
        .btn-cstm {
        background: #001b4c;
        border: none;
        font-size: 1.1rem;
        padding: 0.3rem 1.5rem;
        color: #fff !important;
        cursor: pointer;
      }

      .btn-cstm:hover{
        background: #007DB0;
        color: #ffffff;
      }
            
            /* Dark background button styles */
            :root[data-color-scheme=dark] {
                .btn-cstm {
                    color: white;
                    background-color: #000000;
                    border-color:1px solid #ffffff; 
                }

                .btn-cstm:hover {
                    background-color: #000000;
                    border-color: #ffffff; 
                }
            }
        
    </style>
   
    <h3> <?php echo Text::_('PKG_RA_SSO_STEP_TO_GUIDE'); ?></h3>
    <ul>
    <li> <?php echo Text::_('PKG_RA_SSO_COMPONENT'); ?> </li>
    <li> <?php echo Text::_('PKG_RA_SSO_CONFIGURATION_TAB'); ?></li>
    <li> <?php echo Text::_('PKG_RA_SSO_START_CONFIG'); ?></li>
    </ul>
        <div class="mo-row">
            <a class="btn btn-cstm" href="index.php?option=com_ra_sso&view=accountsetup&tab-panel=configuration"> <?php echo Text::_('PKG_RA_SSO_START_CONFIG_MSG'); ?></a>
        </div>
        <?php
    }
  
}
