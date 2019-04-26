<?php namespace DeftCMS\Components\b1tc0re\Mail\Providers;

use DeftCMS\Engine;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SendMail Provider provider
 *
 * @package     DeftCMS\Components\b1tc0re\Mail
 * @author	    b1tc0re
 * @copyright   2018-2019 DeftCMS (https://deftcms.org/)
 * @since	    Version 0.0.2
 */
class SendMailProvider extends BaseMailProvider implements IProvider
{

    /**
     * SMTPMailProvider constructor.
     * @param $options
     */
    public function __construct($options)
    {
        parent::__construct($options);

        $this->initialize($options);
    }

    /**
     * Инииализация настроек
     * @param array $options
     */
    protected function initialize(array $options)
    {
        Engine::$DT->load->library('email');

        Engine::$DT->email->initialize([
            'protocol'      => 'sendmail',
            'useragent'     => 'DeftCMS v' . Engine::DT_VERSION,
            'mailtype'      => 'html',
            'validate'      => TRUE,
            'priority'      => array_key_exists('priority', $options) ? $options['priority'] : 3,
            'mailpath'      => array_key_exists('mailpath', $options) ? $options['mailpath'] : '/usr/sbin/sendmail',
        ]);

        $this->options = $options;
    }

    /**
     * Отправка сообшений
     * @param string $subject
     * @param string $message
     * @param string $to
     * @param string|null $from
     * @param string|null $name
     * @return bool
     */
    public function send($subject, $message, $to, $from = null, $name = null)
    {
        if( $from === null && !array_key_exists('form', $this->options) )
        {
            Engine::$Log->critical('Option form has required');
            throw new \InvalidArgumentException(sprintf('Option form has required'));
        }
        elseif( $from === null && array_key_exists('form', $this->options) )
        {
            $from = $this->options['form'];
        }

        if( $name === null && !array_key_exists('name', $this->options) )
        {
            $name = '';
        }

        return Engine::$DT->email
            ->from($from, $name)->to($to)
            ->subject($subject)
            ->message($message)->send();
    }
}