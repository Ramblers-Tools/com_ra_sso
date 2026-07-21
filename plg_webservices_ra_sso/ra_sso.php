<?php
/**
 * @package    Joomla.Plugin
 * @subpackage Webservices.ra_sso
 *
 * @author    East Cheshire Ramblers
 * @copyright Copyright (C) 2026 East Cheshire Ramblers. Based on original work Copyright (C) 2015 miniOrange.
 * @license   GNU General Public License version 3; see LICENSE.txt
 */

 defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\ApiRouter;

class PlgWebservicesRa_sso extends CMSPlugin
{
    /**
     * Load the language file on instantiation.
     *
     * @var   boolean
     * @since 4.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Registers com_content's API's routes in the application
     *
     * @param ApiRouter &$router The API Routing object
     *
     * @return void
     *
     * @since 4.0.0
     */
    public function onBeforeApiRoute(&$router)
    {
        $router->createCRUDRoutes(
            'v1/ra-sso-login',
            'ra_sso',
            ['com_ra_sso'],
            true
        );

        $router->createCRUDRoutes(
            'v1/ra_sso',
            'ra_sso',
            ['com_ra_sso'],
            true
        );
        
        $this->handleOAuthClientRequest($router);
    }


    /**
     * Create contenthistory routes
     *
     * @param ApiRouter &$router The API Routing object
     *
     * @return void
     *
     * @since 4.0.0
     */
    public function handleOAuthClientRequest(&$router)
    {
       
        jimport('ra_sso.utility.RaSsoClientHandler');
        $app = Factory::getApplication();

        if (method_exists($app, 'getInput')) {
            $input = $app->getInput();
        } else { // Joomla 3
            $input = $app->input;
        }

        $queryParams = $input->getArray();
        if (isset($queryParams['rarequest']) && !isset($queryParams['morequest'])) {
            $queryParams['morequest'] = $queryParams['rarequest'];
        }
        
        if(isset($queryParams['error']) && isset($queryParams['error_description']))
        {
            $msg = "<strong>Error: </strong> " . $queryParams['error'] . "<br>" .
               "<strong>Description: </strong> " . $queryParams['error_description'];
            echo $msg; 
            exit();
        }
        
        $raSsoClientHandler = new RaSsoClientHandler();

        if (isset($queryParams['morequest']) and $queryParams['morequest'] == 'testattrmappingconfig') {
            $raSsoClientHandler->handleOAuthRequest($queryParams);
        }
        else if (isset($queryParams['morequest']) and $queryParams['morequest'] == 'oauthredirect') {
            $raSsoClientHandler->handleOAuthRequest($queryParams);
        }
        else if (isset($queryParams['code'])) {
            $raSsoClientHandler->handleOAuthRequest($queryParams);
        }
    }
}
