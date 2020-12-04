<?php namespace DeftCMS\Components\b1tc0re\Mail\Providers;

use DeftCMS\Engine;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * SendMail Provider provider
 *
 * @package     DeftCMS\Components\b1tc0re\Mail
 * @author	    b1tc0re
 * @copyright   2019-2020 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9
 */
class SendMailProvider extends BaseMailProvider
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
}