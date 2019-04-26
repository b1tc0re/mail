<?php namespace DeftCMS\Components\b1tc0re\Mail\Providers;


defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Interface IProvider
 *
 * @package     DeftCMS\Components\b1tc0re\Mail
 * @author	    b1tc0re
 * @copyright   2018-2019 DeftCMS (https://deftcms.org/)
 * @since	    Version 0.0.2
 */
interface IProvider
{

    /**
     * Отправить e-mail сообшений
     * @param string $subject
     * @param string $message
     * @param string $to
     * @param string|null $from
     * @param string|null $name
     * @return bool
     */
    public function send($subject, $message, $to, $from = null, $name = null);

    /**
     * Отпавить сообшение по шаблону
     *
     * @param string $name название шаблона
     * @param string $to Кому отправить
     * @param array $extra Параметры для заменыв письме
     * @return bool
     */
    public function template($name, $to, $extra = []);
}