<?php namespace DeftCMS\Components\b1tc0re\Mail\Providers;

use DeftCMS\Engine;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * SMTP mail provider
 *
 * @package     DeftCMS\Components\b1tc0re\Mail
 * @author	    b1tc0re
 * @copyright   2019-2020 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9
 */
class SMTPMailProvider extends BaseMailProvider
{

    /**
     * SMTPMailProvider constructor.
     * @param $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($options)
    {
        parent::__construct($options);

        $this->initialize($options);
    }

    /**
     * Инииализация настроек
     * @param array $options
     * @throws \InvalidArgumentException
     */
    protected function initialize(array $options)
    {
        foreach (['smtp_host', 'smtp_user', 'smtp_pass'] as $option)
        {
            if( !array_key_exists($options, $options) )
            {
                Engine::$Log->critical('Option [name] has required', [ '[name]' => $option ]);
                throw new \InvalidArgumentException(sprintf('Option ' . $option . ' has required'));
            }
        }

        Engine::$DT->load->library('email');

        Engine::$DT->email->initialize([
            'protocol'      => 'smtp',
            'useragent'     => 'DeftCMS v' . Engine::DT_VERSION,
            'smtp_host'     => $options['smtp_host'],
            'smtp_user'     => $options['smtp_user'],
            'smtp_pass'     => $options['smtp_pass'],
            'smtp_port'     => array_key_exists('smtp_port', $options) ? $options['smtp_port'] : '25',
            'smtp_timeout'  => array_key_exists('smtp_timeout', $options) ? $options['smtp_timeout'] : 5,
            'smtp_crypto'   => array_key_exists('smtp_crypto', $options) ? $options['smtp_crypto'] : '',
            'mailtype'      => 'html',
            'validate'      => TRUE,
            'priority'      => array_key_exists('priority', $options) ? $options['priority'] : 3,
        ]);

        $this->options = $options;
    }

    /**
     * Отправить email сообшение
     *
     * @param string $subject       - Тема письма
     * @param string $message       - Сообшение
     * @param string $to            - Email получателя
     * @param string|null $from     - От кого отправляется почта
     * @param string|null $name     - Имя отправителя
     * @return bool
     */
    public function send($subject, $message, $to, $from = null, $name = null)
    {
        return parent::send($subject, $message, $to);
    }
}