<?php
/**
 * @package    Joomla.Administrator
 * @subpackage com_ra_sso
 *
 * @author    East Cheshire Ramblers
 * @copyright Copyright (C) 2026 East Cheshire Ramblers. Based on original work Copyright (C) 2015 miniOrange.
 * @license   GNU General Public License version 3; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

class RaSsoControllerAccountSetup extends FormController
{
    function __construct()
    {
        $this->view_list = 'accountsetup';
        parent::__construct();
    }
    
    function saveAdminMail()
    {
        $app = Factory::getApplication();
        if (method_exists($app, 'getInput')) {
            $input = $app->getInput();
        } else { // Joomla 3
            $input = $app->input;
        }
        $post=    $input->post->getArray();
        $db = self::getDBObject();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('contact_admin_email') . ' = '.$db->quote($post['oauth_client_admin_email']),

        );

        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__ra_sso_customer'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();
        $this->setRedirect('index.php?option=com_ra_sso&view=accountsetup', Text::_('COM_RA_SSO_ADMIN_EMAIL_CHANGED'));
        return;
    }

    function saveConfig() 
    { 
        $app = Factory::getApplication();
        if (method_exists($app, 'getInput')) {
            $input = $app->getInput();
        } else { // Joomla 3
            $input = $app->input;
        }

        $post=    $input->post->getArray();
        $appD = new RaSsoCustomer();

        if(count($post)==0) {
            $this->setRedirect('index.php?option=com_ra_sso&view=accountsetup');
            return;
        }
        else if(isset($post['oauth_config_form_step1'])) {
            if(isset($post['callbackurl'])) {
                $callbackurlhttp           = isset($post['callbackurlhttp'])?$post['callbackurlhttp'] : 'http';
                $redirectUri               = isset($post['callbackurl'])? $post['callbackurl'] : '';
                $redirectUri               = $callbackurlhttp."".$redirectUri ;
                $appname                   = isset($post['mo_oauth_app_name'])? $post['mo_oauth_app_name'] : '';
                $db     = self::getDBObject();
                $query  = $db->getQuery(true);
                $fields = array(
                    $db->quoteName('appname') . ' = '.$db->quote($appname),
                    $db->quoteName('redirecturi') . ' = '.$db->quote($redirectUri),
                );

                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );

                $query->update($db->quoteName('#__ra_sso_config'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $result = $db->execute();
                $returnURL  = 'index.php?option=com_ra_sso&view=accountsetup&tab-panel=configuration&moAuthAddApp='.$post['mo_oauth_app_name'].'&progress=step2';
                $errMessage =Text::_('COM_RA_SSO_STEP2_CONFIG_SUCCESS_MSG');
            }
            else
            {
                $returnURL  = 'index.php?option=com_ra_sso&view=accountsetup&tab-panel=configuration&moAuthAddApp='.$post['mo_oauth_app_name'];
                $errMessage = Text::_('COM_RA_SSO_REDIRECT_URI_ALERT');
                $this->setRedirect($returnURL, $errMessage, 'error');
                return;
            }
            
        }
        else if(isset($post['oauth_config_form_step2'])) {
            $clientid                = isset($post['mo_oauth_client_id'])? $post['mo_oauth_client_id'] : '';
            $clientsecret            = isset($post['mo_oauth_client_secret'])? $post['mo_oauth_client_secret'] : '';
            $scope                   = isset($post['mo_oauth_scope'])? $post['mo_oauth_scope'] : '';
            $appname                 = isset($post['mo_oauth_app_name'])? $post['mo_oauth_app_name'] : '';
            $customappname           = isset($post['mo_oauth_custom_app_name'])? $post['mo_oauth_custom_app_name'] : '';
            $appEndpoints            = json_decode($appD->getAppJason(), true);
            $appEndpoints            = $appEndpoints[$appname];  
            $authorizeurl            = isset($post['mo_oauth_authorizeurl'])? $post['mo_oauth_authorizeurl'] : '';
            $accesstokenurl          = isset($post['mo_oauth_accesstokenurl'])? $post['mo_oauth_accesstokenurl'] : '';
            $resourceownerdetailsurl = isset($post['mo_oauth_resourceownerdetailsurl'])? $post['mo_oauth_resourceownerdetailsurl'] : '';
            $current = "";
            if($authorizeurl =="" && $accesstokenurl=="" && $resourceownerdetailsurl == "") {
                $authorizeurl            = isset($appEndpoints['authorize'])? $appEndpoints['authorize'] : '';
                $accesstokenurl          = isset($appEndpoints['token'])? $appEndpoints['token'] : '';
                $resourceownerdetailsurl = isset($appEndpoints['userinfo'])? $appEndpoints['userinfo'] : '';
                $appData                 = json_decode($appD->getAppData(), true);
                $appData                 = explode(",", $appData[$appname]['1']);
                $scope                   = isset($appEndpoints['scope'])? $appEndpoints['scope'] : 'email';
    
                foreach($appData as $key=>$val)
                {
                    if(strpos($post[$val], 'http') !==false && $appname != 'keycloak') {
                        if(strpos($post[$val], 'https://') !== false) {
                            $current = trim($post[$val], "https:// /");
                        }
                        if(strpos($post[$val], 'http://') !== false) {
                            $current = trim($post[$val], "http:// /");
                        }
                    }
                    else{
                        $current = $post[$val];
                    }
                    
                    $authorizeurl            = str_replace("{".strtolower($val)."}", $current, $authorizeurl);
                    $accesstokenurl          = str_replace("{".strtolower($val)."}", $current, $accesstokenurl);
                    $resourceownerdetailsurl = str_replace("{".strtolower($val)."}", $current, $resourceownerdetailsurl);
                }
            }
    
            $in_header               = isset($post['mo_oauth_in_header'])?$post['mo_oauth_in_header']:'';
            $in_body                 = isset($post['mo_oauth_body'])?$post['mo_oauth_body']:'';
            if(isset($post['mo_oauth_option'])) {
                if($post['mo_oauth_option'] == 'body') {
                    $in_body = 1;
                }
                if($post['mo_oauth_option'] == 'header') {
                    $in_header = 1;
                }
            }
            $in_header_or_body       = "inHeader" ;
            if($in_header=='1' && $in_body=='1') {
                $in_header_or_body = "both";
            }
            else if($in_body=='1') {
                $in_header_or_body ="inBody";
            }
    
            $db     = self::getDBObject();
            $query  = $db->getQuery(true);
            $fields = array(
                $db->quoteName('appname') . ' = '.$db->quote($appname),
                $db->quoteName('custom_app') . ' = '.$db->quote($customappname),
                $db->quoteName('client_id') . ' = '.$db->quote(trim($clientid)),
                $db->quoteName('client_secret') . ' = '.$db->quote(trim($clientsecret)),
                $db->quoteName('app_scope') . ' = '.$db->quote($scope),
                $db->quoteName('authorize_endpoint') . ' = '.$db->quote(trim($authorizeurl)),
                $db->quoteName('access_token_endpoint') . ' = '.$db->quote(trim($accesstokenurl)),
                $db->quoteName('user_info_endpoint') . ' = '.$db->quote(trim($resourceownerdetailsurl)),
                $db->quoteName('in_header_or_body').'='.$db->quote($in_header_or_body)

            );
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );
    
            $query->update($db->quoteName('#__ra_sso_config'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            $returnURL  = 'index.php?option=com_ra_sso&view=accountsetup&tab-panel=configuration&moAuthAddApp='.$post['mo_oauth_app_name'].'&progress=step3';
            $errMessage = Text::_('COM_RA_SSO_STEP3_CONFIG_SUCCESS_MSG');
        }
        
        $c_date = RaSsoCustomer::getAccountDetails();

        if($c_date['cd_plugin']=='') {

            $time = time();
            $db = self::getDBObject();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('cd_plugin') . ' = '.$db->quote($time),

            );

            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__ra_sso_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();

        }

        $this->setRedirect($returnURL, $errMessage);
    }

    function saveMapping()
    {
        $app = Factory::getApplication();
        if (method_exists($app, 'getInput')) {
            $input = $app->getInput();
        } else { // Joomla 3
            $input = $app->input;
        }
        $post=    $input->post->getArray();

        $email_attr = isset($post['mo_oauth_email_attr'])? $post['mo_oauth_email_attr'] : '';
        $first_name_attr = isset($post['mo_oauth_first_name_attr'])? $post['mo_oauth_first_name_attr'] : '';

        $db = self::getDBObject();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('email_attr') . ' = '.$db->quote($email_attr),
            $db->quoteName('username_attr') . ' = '.$db->quote($first_name_attr),
        );

        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__ra_sso_config'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();

        $this->setRedirect('index.php?option=com_ra_sso&view=accountsetup&tab-panel=configuration&progress=step4', Text::_('COM_RA_SSO_ATTRIBUTE_MAPPING_SAVED_SUCCESSFULLY'));
    }

    function clearConfig()
    {
        $app = Factory::getApplication();
        if (method_exists($app, 'getInput')) {
            $input = $app->getInput();
        } else { // Joomla 3
            $input = $app->input;
        }

        $post=    $input->post->getArray();

        $redirectUri = "";
        $clientid = "";
        $clientsecret = "";
        $scope = "";
        $appname = "";
        $customappname = "";
        $authorizeurl = "";
        $accesstokenurl = "";
        $resourceownerdetailsurl = "";
        $email_attr="";
        $first_name_attr="";
        $test_attribute_name = "";

        $db = self::getDBObject();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('appname') . ' = '.$db->quote($appname),
            $db->quoteName('custom_app') . ' = '.$db->quote($customappname),
            $db->quoteName('client_id') . ' = '.$db->quote($clientid),
            $db->quoteName('client_secret') . ' = '.$db->quote($clientsecret),
            $db->quoteName('app_scope') . ' = '.$db->quote($scope),
            $db->quoteName('authorize_endpoint') . ' = '.$db->quote($authorizeurl),
            $db->quoteName('access_token_endpoint') . ' = '.$db->quote($accesstokenurl),
            $db->quoteName('user_info_endpoint') . ' = '.$db->quote($resourceownerdetailsurl),
            $db->quoteName('redirecturi') . ' = '.$db->quote($redirectUri),
            $db->quoteName('email_attr') . ' = '.$db->quote($email_attr),
            $db->quoteName('username_attr') . ' = '.$db->quote($first_name_attr),
            $db->quoteName('test_attribute_name') . ' = '.$db->quote($test_attribute_name),
        );

        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__ra_sso_config'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();

        $this->setRedirect('index.php?option=com_ra_sso&view=accountsetup&tab-panel=configuration', Text::_('COM_RA_SSO_APP_CONFIGURATION_RESET'));
    }

    function updateDatabaseQuery($database_name, $updatefieldsarray)
    {
        $db = self::getDBObject();
        $query = $db->getQuery(true);
        foreach ($updatefieldsarray as $key => $value)
        {
            $database_fileds[] = $db->quoteName($key) . ' = ' . $db->quote($value);
        }
        $query->update($db->quoteName($database_name))->set($database_fileds)->where($db->quoteName('id')." = 1");
        $db->setQuery($query);
        $db->execute();
    }
    
    function exportConfiguration()
    {
        $appDetails = $this->retrieveAttributes('#__ra_sso_config');
        $customer_details = $this->retrieveAttributes('#__ra_sso_customer');
        $customapp = $appDetails['appname'];
        $clientid = $appDetails['client_id'];
        $clientsecret = $appDetails['client_secret'];

        if($clientid =='' && $clientsecret =='') {
            $this->setRedirect('index.php?option=com_ra_sso&view=accountsetup&tab-panel=overview', Text::_('COM_RA_SSO_ENTER_CLIENT_ID_BEFORE_DOWNLOADING'), 'error');
            return;
        }

        $plugin_configuration = array();
        array_push($plugin_configuration, $appDetails, $customer_details);
     
        $client_secret = $plugin_configuration[0]['client_secret'];
        $ciphering = "AES-128-CTR";
        $encryption_iv = '4488882453112245';
        $encryption_key = "minOrangeOauth";
        $options = 0;
        $encrepted_client_secret =  openssl_encrypt($client_secret, $ciphering, $encryption_key, $options, $encryption_iv);
        
        $plugin_configuration[0]['client_secret'] = $encrepted_client_secret;
        $filecontentd = json_encode($plugin_configuration, JSON_PRETTY_PRINT);
        
        header('Content-Disposition: attachment; filename=oauth-client.json'); 
        header('Content-Type: application/json'); 
        print_r($filecontentd);

        $this->setRedirect('index.php?option=com_ra_sso&view=accountsetup&tab-panel=configuration&moAuthAddApp='.$customapp, Text::_('COM_RA_SSO_PLUGIN_CONFIGURATION_DOWNLOADED_SUCCESSFULLY'));
        exit;
    }

    function retrieveAttributes($tablename)
    {
        $db = self::getDBObject();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName($tablename));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        return $db->loadAssoc();
    }

    function raSsoProxyConfigReset()
    {
        $nameOfDatabase= '#__ra_sso_config';
        $updateFieldsArray = array('proxy_server_url' => '', 'proxy_server_port' => '80', 'proxy_username' => '', 'proxy_password' => '', 'proxy_set' => '');
        
        $this->updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
        $this->setRedirect('index.php?option=com_ra_sso&view=accountsetup&tab-panel=account', Text::_('COM_RA_SSO_PROXY_SETTING_RESET'));
    }

    function proxyConfig()
    {
        $app = Factory::getApplication();
        if (method_exists($app, 'getInput')) {
            $input = $app->getInput();
        } else { // Joomla 3
            $input = $app->input;
        }
        $post = $input->post->getArray();
        $proxy_host_name = isset($post['mo_proxy_host']) ? $post['mo_proxy_host'] : '';
        $proxy_port_number = isset($post['mo_proxy_port']) ? $post['mo_proxy_port'] : '';
        $proxy_username = isset($post['mo_proxy_username']) ? $post['mo_proxy_username'] : '';
        $proxy_password = isset($post['mo_proxy_password']) ? base64_encode($post['mo_proxy_password']): '';
        $updateFieldsArray = array(
            'proxy_host_name' => $proxy_host_name,
            'port_number'     => $proxy_port_number,
            'username'        => $proxy_username,
            'password'        => $proxy_password,
        );

            $this->updateDatabaseQuery('#__ra_sso_config', $updateFieldsArray);
            $this->setRedirect('index.php?option=com_ra_sso&view=accountsetup&tab-panel=proxy', Text::_('COM_RA_SSO_PROXY_SERVER_SAVED_SUCCESSFULLY'));
    }
    function proxyConfigReset()
    {
        $updateFieldsArray = array(
        'proxy_host_name' => '',
        'port_number'     => '',
        'username'        => '',
        'password'        => ''
        );

           $this->updateDatabaseQuery('#__ra_sso_config', $updateFieldsArray);
           $this->setRedirect('index.php?option=com_ra_sso&view=accountsetup&tab-panel=proxy', Text::_('COM_RA_SSO_PROXY_SETTING_RESET'));
    }
    public function enableSSO()
    {
        $app = Factory::getApplication();
        if (method_exists($app, 'getInput')) {
            $input = $app->getInput();
        } else { // Joomla 3
            $input = $app->input;
        }

        $post = $input->post->getArray();

        $sso_status = isset($post['mo_oauth_enable_sso']) ? 1 : 0;

        $updateFieldsArray = array(
            'sso_enable' => $sso_status,
        );

        $messg = Text::_('COM_RA_SSO_SSO_SETTING_SAVED_SUCCESSFULLY');
        
        $this->updateDatabaseQuery('#__ra_sso_config', $updateFieldsArray);

        $this->setRedirect(
            'index.php?option=com_ra_sso&view=accountsetup&tab-panel=configuration&moAuthAddApp='.$post['mo_oauth_app_name'].'&progress=advance_setting', 
            $messg
        );
    }

    public function moEnableLogs()
    {
        $app = Factory::getApplication();
        if (method_exists($app, 'getInput')) {
            $input = $app->getInput();
        } else { // Joomla 3
            $input = $app->input;
        }
        $post = $input->post->getArray();
        
        $enable_logs = isset($post['mo_enable_logs']) ? ($post['mo_enable_logs'] == 1 ? 1 : 0) : 0;
        
        $updateFieldsArray = array(
            'loggers_enable' => $enable_logs
        );
        
        $this->updateDatabaseQuery('#__ra_sso_config', $updateFieldsArray);

        $messg = $enable_logs == 1 ? Text::_('COM_RA_SSO_LOGS_ENABLED_SUCCESSFULLY') : Text::_('COM_RA_SSO_LOGS_DISABLED_SUCCESSFULY');
        $this->setRedirect(
            'index.php?option=com_ra_sso&view=accountsetup&tab-panel=loggerreport', 
            $messg
        );
    }

    public function moClearLogs()
    {
        if (!RaSsoLogger::clearLogs()) {
            $this->setRedirect(
                'index.php?option=com_ra_sso&view=accountsetup&tab-panel=loggerreport', 
                Text::_('COM_RA_SSO_LOGS_ARE_ALREADY_EMPTY'), 
                'warning'
            );
            return;
        }

        $messg = Text::_('COM_RA_SSO_LOGS_CLEAR_SUCCESSFULLY');
        $this->setRedirect(
            'index.php?option=com_ra_sso&view=accountsetup&tab-panel=loggerreport', 
            $messg
        );
    }

    function moDownloadLogs()
    {
        $all_logs = RaSsoLogger::getAllLogs();
        
        if (empty($all_logs)) {
            $this->setRedirect(
                'index.php?option=com_ra_sso&view=accountsetup&tab-panel=loggerreport',
                Text::_('COM_RA_SSO_LOGS_DOWNLOAD_WARNING'),
                'warning'
            );
            return;
        }

        // Define CSV file name
        $fileName = 'ra_sso_logs_' . date('Y-m-d_H-i-s') . '.csv';

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open PHP output stream as file
        $output = fopen('php://output', 'w');

        // Add CSV column headers
        fputcsv($output, ['  Timestamp  ', '  Log Level  ', '  Message (Code: Issue)  ', ' Location ']);

        // Loop through logs and format each row
        foreach ($all_logs as $log) {
            $messageData = json_decode($log->message, true);
            $code = $messageData['code'] ?? '';
            $issue = $messageData['issue'] ?? $log->message;
            $timestamp    = '  ' .$log->timestamp . '  ';
            $log_level    = '  ' . $log->log_level . '  ';
            $formattedMessage = '  '. $code . ' : ' . $issue . '  ';
            $location = '  '.$log->file . ' in function ' . $log->function_call . '() at ' . $log->line_number;

            fputcsv(
                $output, [
                $timestamp,
                $log_level,
                $formattedMessage,
                $location
                ]
            );
        }

        fclose($output);
        exit; 
    }

    private static function getDBObject()
    {
        $app = Factory::getApplication();

        if (method_exists($app, 'getDatabase')) {
            return $app->getDatabase();
        }
        return Factory::getDbo();
    }
}
