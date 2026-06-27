<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

class com_miniorange_oauthInstallerScript
{
    public function install($parent)
    {
        $this->repairLoggingSchema();
    }

    public function update($parent)
    {
        $this->repairLoggingSchema();
    }

    public function postflight($type, $parent)
    {
        if ($type !== 'uninstall') {
            $this->repairLoggingSchema();
        }
    }

    protected function repairLoggingSchema()
    {
        $db = $this->getDatabase();

        $query = "CREATE TABLE IF NOT EXISTS " . $db->quoteName('#__miniorange_oauth_logs') . " (
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

        try {
            $columns = $db->getTableColumns($db->replacePrefix('#__miniorange_oauth_config'), false);
        } catch (\Throwable $exception) {
            return;
        }

        if (!isset($columns['loggers_enable'])) {
            $query = "ALTER TABLE " . $db->quoteName('#__miniorange_oauth_config') .
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
}
