<?php namespace DeftCMS\Components\b1tc0re\Mail\Providers;

use DeftCMS\Components\b1tc0re\Mail\IMailModel;
use DeftCMS\Engine;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Base mail provider
 *
 * @package     DeftCMS\Components\b1tc0re\Mail
 * @author	    b1tc0re
 * @copyright   2019-2020 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9
 */
class BaseMailProvider implements IProvider
{
    /**
     * Mail model
     *
     * @var IMailModel
     */
    protected $model;

    /**
     * Mail templates
     *
     * @var array
     */
    protected $templates;

    /**
     * Options
     * @var array
     */
    protected $options = [];

    /**
     * BaseMailProvider constructor.
     * @param array $options
     */
    public function __construct(&$options = [])
    {
        if( !array_key_exists('model', $options) )
        {
            Engine::$DT->load->model('Core/BaseEmailTemplate', 'BaseEmailTemplate');
            $this->model = Engine::$DT->BaseEmailTemplate;
        }

        if( !array_key_exists('service_email_title', $options) )
        {
            $options['service_email_title'] = Engine::$DT->config->item('company')['s_company'];
        }
    }

    /**
     * Отпавить сообшение по шаблону
     *
     * @param string $name название шаблона
     * @param string $to Кому отправить
     * @param array $from От кого
     * @param array $extra Параметры для заменыв письме
     *
     * @return bool
     */
    public function template($name, $to, $from = null, $extra = [])
    {
        if( !$this->templates = Engine::$DT->cache->get('mail.templates') )
        {
            $this->templates = $this->model->getMailTemplates();
            Engine::$DT->cache->save('mail.templates', $this->templates, TIME_MONTH);
        }

        if( !array_key_exists($name, $this->templates) )
        {
            Engine::$Log->critical('Mail templates [name] has not found', [ '[name]' => $name ]);
            throw new \InvalidArgumentException(sprintf('Mail templates %s has not found', $name));
        }

        Engine::$DT->email->set_priority($this->templates[$name]['i_priority']);

        return $this->send(
            $this->templates[$name]['s_subject'],
            $this->interpolate($this->templates[$name]['s_message'], $extra),
            $to,
            $from
        );
    }

    /**
     * Отправить email сообшение
     * @param string $subject
     * @param string $message
     * @param string $to
     * @param string|null $from
     * @param string|null $name
     * @return bool
     */
    public function send($subject, $message, $to, $from = null, $name = null)
    {
        if ($from === null && !array_key_exists('from', $this->options)) {
            Engine::$Log->critical('Option form has required');
            throw new \InvalidArgumentException(sprintf('Option form has required'));
        }

        if($from === null && array_key_exists('from', $this->options)) {
            $from = $this->options['from'];
        }

        if( $name === null && array_key_exists('service_email_title', $this->options) )
        {
            $name = $this->options['service_email_title'];
        }

        return Engine::$DT->email
            ->from($from, $name)->to($to)
            ->subject($subject)
            ->message($message)->send();
    }

    /**
     * Поиск переменных в тексте для замены
     *
     * @param string $message
     * @param array $extra
     * @return string
     */
    protected function interpolate($message, $extra = [])
    {
        Engine::$DT->load->library('parser');
        Engine::$DT->parser->l_delim = '__%';
        Engine::$DT->parser->r_delim = '%__';

        $extra = $this->implement($extra);

        return Engine::$DT->parser->parse_string($message, $extra, TRUE);
    }

    /**
     * Имплементировать глобальные переменные
     * @param array $extra
     * @return array
     */
    protected function implement($extra)
    {
        $company = Engine::$DT->config->item('company');
        $extra['domain']    = $company['s_front_host'];
        $extra['company']   = $company['s_company'];
        $extra['base_url']  = base_url('/');

        return $extra;
    }
}