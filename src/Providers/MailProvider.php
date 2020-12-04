<?php namespace DeftCMS\Components\b1tc0re\Mail\Providers;

use DeftCMS\Engine;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Mail function provider
 *
 * @package     DeftCMS\Components\b1tc0re\Mail
 * @author	    b1tc0re
 * @copyright   2019-2020 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9
 */
class MailProvider extends BaseMailProvider
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
            'protocol'      => 'mail',
            'useragent'     => 'DeftCMS v' . Engine::DT_VERSION,
            'mailtype'      => 'html',
            'validate'      => TRUE,
            'priority'      => array_key_exists('priority', $options) ? $options['priority'] : 3,
        ]);

        $this->options = $options;
    }
}