<?php

require_once 'Zend/Log/Writer/Abstract.php';

class CM5_Log_Writer extends Zend_Log_Writer_Abstract
{
    /**
     * Create a new instance of Zend_Log_Writer_Db
     *
     * @param  array|Zend_Config $config
     * @return Zend_Log_Writer_Db
     * @throws Zend_Log_Exception
     */
    static public function factory($config)
    {
        return new self();
    }

    /**
     * Formatting is not possible on this writer
     */
    public function setFormatter(Zend_Log_Formatter_Interface $formatter)
    {
        require_once 'Zend/Log/Exception.php';
        throw new Zend_Log_Exception(get_class($this) . ' does not support formatting');
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  event data
     * @return void
     */
    protected function _write($event)
    {
        $event['timestamp'] = new DateTime($event['timestamp']);
        Log::create($event);
    }
}


//! CMS Core componment
class CM5_Logger
{
    //! Instance of the logger object
    static $logger = null;
    
    //! Get the instance of this logger
    public static function get_instance()
    {
        if (self::$logger !== null)
            return self::$logger;

        // Simple writer
        $db_writer = new CM5_Log_Writer();
        $db_writer->addFilter(new Zend_Log_Filter_Priority(Zend_Log::INFO));
        
        // Mail writer
        $mail = new Zend_Mail();
        $mail->setFrom(GConfig::get_instance()->email->sender)
             ->addTo(GConfig::get_instance()->email->administrator);
        $mail_writer = new Zend_Log_Writer_Mail($mail);
        $mail_writer->setSubjectPrependText(GConfig::get_instance()->site->title . ' | Needs your attention.');
        
        $mail_format = "User: %user%\nIp: %ip%\nTime: %timestamp%\nType: %priorityName% (%priority%)\n\nMessage: %message%" . PHP_EOL;
        $mail_formatter = new Zend_Log_Formatter_Simple($mail_format);

        $mail_writer->setFormatter($mail_formatter);
        $mail_writer->addFilter(new Zend_Log_Filter_Priority(Zend_Log::WARN));
 
        // Logger
        $logger = new Zend_Log();
        $logger->addwriter($db_writer);
        $logger->addwriter($mail_writer);
        $logger->setEventItem('user', (Authn_Realm::get_identity()?Authn_Realm::get_identity()->id():null));
        $logger->setEventItem('ip', $_SERVER['REMOTE_ADDR']);
        
        self::$logger = $logger;
        return $logger;
    }
}

?>