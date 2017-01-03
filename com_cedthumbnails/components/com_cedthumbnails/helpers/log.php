<?php

jimport('joomla.log.log');


class comCedThumbnailsLog
{
    static $logger;

    public static function log($message, $level = JLog::DEBUG)
    {
        self::initLogger();

        if (!is_string($message)) {
            $message = print_r($message, true);
        }
        JLog::add($message, $level, 'com_cedthumbnails');
    }

    private static function initLogger()
    {
        if (is_null(comCedThumbnailsLog::$logger)) {
            $options['text_file_path'] = JPATH_SITE . "/logs/";
            $options['text_file'] = 'com_cedthumbnails.php';
            JLog::addLogger($options, JLog::ALL, array('com_cedthumbnails'));
        }
    }

} 