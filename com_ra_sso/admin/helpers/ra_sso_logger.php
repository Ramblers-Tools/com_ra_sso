<?php
/**
 * @package    Joomla.Administrator
 * @subpackage com_ra_sso
 *
 * @author    East Cheshire Ramblers
 * @copyright Copyright (C) 2026 East Cheshire Ramblers. Based on original work Copyright (C) 2015 miniOrange.
 * @license   GNU General Public License version 3; see LICENSE.txt
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;

defined('_JEXEC') or die;

class RaSsoLogger
{
    public static function addLog($message, $type = 'INFO', $category = 'ra_sso_logs')
    {
        // Check if logging is enabled
        if (!self::isLoggingEnabled()) {
            return;
        }
        
        static $loggerInitialized = false;
        if (!$loggerInitialized) {
            Log::addLogger(array('text_file' => 'ra_sso_logs.log', 'text_entry_format' => '{DATE} {TIME} {CATEGORY} [{PRIORITY}] {MESSAGE}'), Log::ALL, array($category));
            $loggerInitialized = true;
        }

        $priorityMap = [
            'INFO'     => Log::INFO,
            'NOTICE'   => Log::NOTICE,
            'WARNING'  => Log::WARNING,
            'ERROR'    => Log::ERROR,
            'ALERT'    => Log::ALERT,
            'CRITICAL' => Log::CRITICAL,
        ];

        $priority = $priorityMap[$type] ?? Log::INFO;

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $trace[1] ?? $trace[0];

        $file = $caller['file'] ?? 'Unknown file';
        $function = $caller['function'] ?? 'Unknown function';
        $line = $caller['line'] ?? 'Unknown line';

        $maxMessageLength = 1000;

        if (strlen($message) > $maxMessageLength) {
            $message = substr($message, 0, $maxMessageLength) . '... [truncated]';
        }

        $formattedMessage = sprintf("[%s:%s] [%s] - %s", basename($file), $line, $function, $message);

        Log::add($formattedMessage, $priority, $category);

        self::saveLogToDatabase($message, $type, basename($file), $line, $function);
    }
    
    private static function saveLogToDatabase($message, $type = 'info', $file = null, $line = null, $function = null)
    {
        self::ensureLogSchema();

        $db    = self::getDBObject();
        $query = $db->getQuery(true);

        $maxLogs = 10000; // currently unused in your code

        $logCode = self::getLogCode($message);

        $fields = array(
        $db->quoteName('timestamp')    . ' = ' . $db->quote(date('Y-m-d H:i:s')),
        $db->quoteName('log_level')    . ' = ' . $db->quote($type),
        $db->quoteName('message')      . ' = ' . $db->quote(json_encode($logCode)),
        $db->quoteName('file')         . ' = ' . $db->quote($file),
        $db->quoteName('line_number')  . ' = ' . $db->quote($line),
        $db->quoteName('function_call'). ' = ' . $db->quote($function)
        );

        $query->insert($db->quoteName('#__ra_sso_logs'))->set($fields);

        $db->setQuery($query);
        $db->execute();

        $query = $db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__ra_sso_logs'));

        $db->setQuery($query);
        $totalLogs = (int)$db->loadResult();

        // Delete the oldest logs if the limit is exceeded
        if ($totalLogs > $maxLogs) {
            $logsToDelete = $totalLogs - $maxLogs;
            $query = $db->getQuery(true)->delete($db->quoteName('#__ra_sso_logs'))->order($db->quoteName('timestamp') . ' ASC')->setLimit($logsToDelete);

            $db->setQuery($query);
            $db->execute();
        }
    }


    private static function getLogCode(string $message)
    {
        $app = Factory::getApplication();
        $language = $app->getLanguage();
        $language->load('com_ra_sso', JPATH_ADMINISTRATOR, null, false, true);

        $logDetails = [
            'Client ID, Client secret or scope is missing'  => ['code' => 'RASSO-001', 'issue' => Text::_('COM_RA_SSO_LOGS_ISSUE_RASSO_001')],
            'SSO is Disable'                                => ['code' => 'RASSO-002', 'issue' => Text::_('COM_RA_SSO_LOGS_ISSUE_RASSO_002')],
            'No request found for this application'         => ['code' => 'RASSO-003', 'issue' => Text::_('COM_RA_SSO_LOGS_ISSUE_RASSO_003')],
            'Application not configured'                    => ['code' => 'RASSO-004', 'issue' => Text::_('COM_RA_SSO_LOGS_ISSUE_RASSO_004')],
            'Authentication limit reached'                  => ['code' => 'RASSO-005', 'issue' => Text::_('COM_RA_SSO_LOGS_ISSUE_RASSO_005')],
            'Auto creation not available'                   => ['code' => 'RASSO-006', 'issue' => Text::_('COM_RA_SSO_LOGS_ISSUE_RASSO_006')],
            'Test Configuration Success'                    => ['code' => 'RASSO-007', 'issue' => Text::_('COM_RA_SSO_LOGS_ISSUE_RASSO_007')],
            'email not received'                            => ['code' => 'RASSO-008', 'issue' => Text::_('COM_RA_SSO_LOGS_ISSUE_RASSO_008')],
            'Error Invalid Response'                        => ['code' => 'RASSO-009', 'issue' => Text::_('COM_RA_SSO_LOGS_ISSUE_RASSO_009')],
        ];
        
        if (isset($logDetails[$message])) {
            return $logDetails[$message];
        }
        
        // Otherwise, return a generated code with the original message as the issue
        return [
            'code' => 'RASSO_' . str_pad(mt_rand(100, 999), 3, '0', STR_PAD_LEFT),
            'issue' => $message
        ];
    }

    public static function getAllLogs()
    {
        self::ensureLogSchema();

        $db = self::getDBObject();
        $query = $db->getQuery(true)->select($db->quoteName(['timestamp', 'log_level', 'message', 'file', 'line_number', 'function_call']))->from($db->quoteName('#__ra_sso_logs'))->order($db->quoteName('timestamp') . ' DESC');

        return $db->setQuery($query)->loadObjectList() ?: [];
    }

    public static function clearLogs()
    {
        self::ensureLogSchema();

        $db = self::getDBObject();
        $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__ra_sso_logs');
        $db->setQuery($query);
        $totalLogs = (int) $db->loadResult();

        if ($totalLogs === 0) {
            return false;
        }

        $query = "TRUNCATE TABLE " . $db->quoteName('#__ra_sso_logs');
        $db->setQuery($query);
        $db->execute();

        return true;
    }

    private static function isLoggingEnabled()
    {
        self::ensureLogSchema();

        $db = self::getDBObject();
        $query = $db->getQuery(true)
            ->select($db->quoteName('loggers_enable'))
            ->from($db->quoteName('#__ra_sso_config'))
            ->setLimit(1);

        $db->setQuery($query);
        $result = $db->loadResult();

        return (bool)$result;
    }

    public static function ensureLogSchema()
    {
        static $schemaChecked = false;

        if ($schemaChecked) {
            return;
        }

        $db = self::getDBObject();

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

        $columns = $db->getTableColumns($db->replacePrefix('#__ra_sso_config'), false);

        if (!isset($columns['loggers_enable'])) {
            $query = "ALTER TABLE " . $db->quoteName('#__ra_sso_config') .
                " ADD COLUMN " . $db->quoteName('loggers_enable') . " TINYINT(1) NOT NULL DEFAULT 0";
            $db->setQuery($query);
            $db->execute();
        }

        $schemaChecked = true;
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
