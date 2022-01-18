<?php namespace DeftCMS\Components\b1tc0re\Mail\Providers;

use DeftCMS\Core\Controllers\Frontend\ModuleController;
use DeftCMS\Core\Exceptions\ModuleNotFound;
use DeftCMS\Engine;
use DeftCMS\Libraries\FileManager\Handlers\ElFinderHandler;
use Modules;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Base mail provider
 *
 * @package     DeftCMS\Components\b1tc0re\Mail
 * @author	    b1tc0re
 * @copyright   2019-2022 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9a
 */
class BaseMailProvider implements IProvider
{
    /**
     * Mail templates
     *
     * @var array
     */
    protected $templates = [];

    /**
     * Options
     * @var array
     */
    protected $options = [];

    /**
     * Название папки с шаблонами
     * @var string
     */
    protected $folderName = 'mail-templates';

    /**
     * Расширение файлов с шаблонами
     * @var string
     */
    protected $fileExt = '.eml';

    /**
     * Параметры для сбора статистики
     * @var array
     */
    protected $statOptions = [];

    /**
     * BaseMailProvider constructor.
     * @param array $options
     */
    public function __construct(&$options = [])
    {
        if( !array_key_exists('service_email_title', $options) )
        {
            $options['service_email_title'] = Engine::$DT->config->item('company')['s_company'];
        }
    }

    /**
     * Инициализировать данные статистики
     * @return $this
     */
    public function setStatParams($options)
    {
        $this->statOptions = $options;
        return $this;
    }

    /**
     * Отпавить сообшение по шаблону
     *
     * @param string $name название шаблона
     * @param string $to Email получателя
     * @param array $extra Параметры для заменыв письме
     *
     * @return bool
     */
    public function template($name, $to, $extra = [])
    {
        if( !array_key_exists($name, $this->getMailings()) )
        {
            Engine::$Log->critical('Mail templates [name] has not found', [ '[name]' => $name ]);
            throw new \InvalidArgumentException(sprintf('Mail templates %s has not found', $name));
        }

        $template = $this->getMailings()[$name];
        $this->setStatParams([ 'mailing' => $name ]);

        Engine::$DT->email->set_priority($template['i_priority']);
        $extra['recipient'] = $to;

        return $this->send(
            $template['s_subject'],
            $this->getEmailBody($name, $to, $extra),
            $to
        );
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
        if ($from === null && !array_key_exists('service_email', $this->options)) {
            Engine::$Log->critical('Option form has required');
            throw new \InvalidArgumentException(sprintf('Option form has required'));
        }

        if($from === null && array_key_exists('service_email', $this->options)) {
            $from = $this->options['service_email'];
        }

        if( $name === null && array_key_exists('service_email_title', $this->options) )
        {
            $name = $this->options['service_email_title'];
        }

        $message = $this->interpolate($to, $message);
        $message .= '<img alt="1px" height="1" width="1" src="' . $this->getWatchUrl($to) . '" />';
        Engine::$DT->email->set_header('List-Unsubscribe', $this->getUnsubscriptionUrl($to));

        return  Engine::$DT->email
            ->from($from, $name)
            ->to($to)
            ->subject($subject)
            ->message($message)
            ->send();
    }

    /**
     * Получить тело сообшения
     * @param string $name       - Название шаблона
     * @param array $extra       - Дополнительные данные для змены в шаблоне (переменные __%name%__)
     * @return string
     */
    protected function getEmailBody($name = 'noop', $to, $extra = [])
    {
        $template = $this->getMailings()[$name];

        if( (int) $template['has_ignore_eml'] === 1 )
        {
            return $template['s_message'];
        }

        $message = $this->templates[$name]['s_message'];

        if( Engine::$DT->template->hasViewExist($path = fn_path_join($this->folderName,  $name . $this->fileExt)) )
        {
            $extra = $this->appendGlobals($to, $extra);
            $extra['body'] = $this->interpolate($to, $message, $extra);
            return Engine::$DT->template->renderLayer($path, $extra, true);
        }

        if( Engine::$DT->template->hasViewExist($path = fn_path_join($this->folderName, 'main' . $this->fileExt)) )
        {
            $extra = $this->appendGlobals($to, $extra);
            $extra['body'] = $this->interpolate($to, $message, $extra);

            return Engine::$DT->template->renderLayer($path, $extra, true);
        }

        return $this->interpolate($to, $message, $extra);
    }

    /**
     * Поиск переменных в тексте для замены
     *
     * @param string $to        - Почта отправителя
     * @param string $message   - Текст с переменными
     * @param array $extra      - Переменные для шаблона
     * @return string
     */
    protected function interpolate($to, $message, $extra = [])
    {
        Engine::$DT->load->library('parser');

        Engine::$DT->parser->l_delim = '__%';
        Engine::$DT->parser->r_delim = '%__';

        return Engine::$DT->parser->parse_string($message, $this->appendGlobals($to, $extra), true);
    }

    /**
     * Получить глобальные переменные
     *
     * @param string $to    - Адресс отправителя
     * @param array $extra  - Дополнительные переменные для шаблона
     * @return array    Массив с глобальными переменными
     */
    protected function appendGlobals($to, $extra = [])
    {
        $company = Engine::$DT->config->item('company');

        $extra['domain']            = $company['s_front_host'];
        $extra['company']           = $company['s_company'];
        $extra['base_url']          = base_url('/');
        $extra['recipient']         = $to;
        $extra['unsubscriptions']   = $this->getUnsubscriptionUrl($to);

        return $extra;
    }

    /**
     * Создать ссылку для сбора статистики
     * @param string $to - Адресс отправителя
     * @return string
     */
    protected function getWatchUrl($to)
    {
        $watch = [
            'sent'      => time(),
            'email'     => $to,
            'mailing'   => array_key_exists('mailing', $this->statOptions) ? $this->statOptions['mailing'] : 'system',
        ];

        return base_url('sender/watch/' . $this->encode($watch));
    }

    /**
     * Создать ссылку для отписки от рассылки
     * @param string $to
     * @return string
     */
    protected function getUnsubscriptionUrl($to)
    {
        $params = [
            'email'      => $to,
            'time'      => time()
        ];

        return base_url('sender/unsubscriptions/' . $this->encode($params));
    }

    /**
     * Получить список рассылок
     * @return array|mixed
     */
    protected function getMailings()
    {
        if( is_array($this->templates) && count($this->templates) !== 0)
        {
            return $this->templates;
        }

        if( !$this->templates = Engine::$DT->cache->get('mail.templates') )
        {
            /**
             * @var \DT_Controller $class
             */
            if( ($class = Modules::load('mail')) instanceof ModuleController )
            {
                $class->load->model('MailModel', 'MailModel');

                if( $template = $class->MailModel->getMailings() )
                {
                    Engine::$DT->cache->save('mail.templates', $this->templates = $template, TIME_MONTH);
                }
            }
            else
            {
                throw new ModuleNotFound('Failed to load module mail');
            }
        }

        return $this->templates;
    }

    /**
     * Кодирование данных
     *
     * @param array $params - Параметры для кодирование
     * @return string Закодированная строка
     */
    protected function encode(array $params)
    {
        // Инициализация класс для кодирования
        Engine::$DT->load->library('encryption');
        $salt = random_string('alnum', 5);

        Engine::$DT->encryption->initialize([
            'cipher' => 'aes-256',
            'mode'   => 'ctr',
            'key'    => md5(Engine::$DT->config->item('encryption_key') . $salt )
        ]);

        $chiper = Engine::$DT->encryption->encrypt(implode(':', $params));
        $chiper = strtr(base64_encode($chiper . ':' . $salt), '+/=', '-_.');
        return rtrim($chiper, '.');
    }

    /**
     * Декодирование данных
     *
     * @param string $cipher - Закодированная строка
     * @return array
     */
    public function decode($cipher)
    {
        $cipher = base64_decode(strtr($cipher, '-_.', '+/='));

        if( count($exp = explode(":", $cipher)) !== 2 )
        {
            return [];
        }

        [$cipher, $salt] = $exp;

        if( $cipher === null || $salt === null || empty($cipher) ||  empty($salt) )
        {
            return [];
        }

        // Инициализация класс для декодирования
        Engine::$DT->load->library('Encryption');
        Engine::$DT->encryption->initialize([
            'cipher' => 'aes-256',
            'mode' => 'ctr',
            'key' => md5(Engine::$DT->config->item('encryption_key') . $salt)
        ]);

        return explode(':', Engine::$DT->encryption->decrypt($cipher));
    }
}