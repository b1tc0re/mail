<?php namespace DeftCMS\Components\b1tc0re\Mail\Providers;

use DeftCMS\Engine;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SMTP mail provider
 *
 * @package     DeftCMS\Components\b1tc0re\Mail
 * @author	    b1tc0re
 * @copyright   2019 DeftCMS (https://deftcms.org/)
 * @since	    Version 0.0.1
 */
class SMTPMailProvider extends BaseMailProvider implements IProvider
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
}