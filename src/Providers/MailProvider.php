<?php namespace DeftCMS\Components\b1tc0re\Mail\Providers;

use DeftCMS\Engine;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mail function provider
 *
 * @package     DeftCMS\Components\b1tc0re\Mail
 * @author	    b1tc0re
 * @copyright   2019 DeftCMS (https://deftcms.org/)
 * @since	    Version 0.0.1
 */
class MailProvider extends BaseMailProvider implements IProvider
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