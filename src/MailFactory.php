<?php namespace DeftCMS\Components\b1tc0re\Mail;

use DeftCMS\Components\b1tc0re\Mail\Providers\IProvider;
use DeftCMS\Components\b1tc0re\Mail\Providers\MailProvider;
use DeftCMS\Components\b1tc0re\Mail\Providers\SendMailProvider;
use DeftCMS\Components\b1tc0re\Mail\Providers\SMTPMailProvider;
use DeftCMS\Engine;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Обработчик отправки Mail сообшений
 *
 * @package	    DeftCMS
 * @category	Model
 * @author	    b1tc0re
 * @copyright   2019-2020 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9
 */
class MailFactory
{
    /**
     * Действительные обработчики
     * @var array
     */
    protected static $validHandlers = [
        'smtp'      => SMTPMailProvider::class,
        'mail'      => MailProvider::class,
        'sendmail'  => SendMailProvider::class
    ];

    /**
     * @var IProvider
     */
    protected static $instance;

    /**
     * Создать желаемый обработчик данных на основе $handler
     *
     * @param string $handler
     * @param array $options
     *
     * @return IProvider
     */
    public static function getHandler(string $handler = null, $options = [])
    {
        if ( ! isset(self::$validHandlers) || ! is_array(self::$validHandlers))
        {
            throw Exceptions\ExceptionFactory::forInvalidHandlers();
        }
        $handler = strtolower($handler);

        if ( !array_key_exists($handler, self::$validHandlers) )
        {
            throw Exceptions\ExceptionFactory::forHandlerNotFound($handler);
        }

        $adapter = new self::$validHandlers[$handler]($options);

        return $adapter;
    }

    /**
     * Инициализация обработчика почты из глобальных настроек
     *
     * @throws Exceptions\ExceptionFactory
     * @return IProvider
     */
    public static function get()
    {
        if( !array_key_exists('mail', Engine::$DT->config->item('settings') ?? []) )
        {
            throw Exceptions\ExceptionFactory::optionNotFound();
        }

        if( self::$instance === null )
        {
            self::$instance = self::getHandler(Engine::$DT->config->item('settings')['mail']['protocol'], Engine::$DT->config->item('settings')['mail']);
        }

        return self::$instance;
    }
}