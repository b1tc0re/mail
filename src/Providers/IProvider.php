<?php namespace DeftCMS\Components\b1tc0re\Mail\Providers;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Interface IProvider
 *
 * @package     DeftCMS\Components\b1tc0re\Mail
 * @author	    b1tc0re
 * @copyright   2019-2020 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9
 */
interface IProvider
{

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
    public function send($subject, $message, $to, $from = null, $name = null);

    /**
     * Отпавить сообшение по шаблону
     *
     * @param string $name название шаблона
     * @param string $to Email получателя
     * @param array $extra Параметры для заменыв письме
     *
     * @return bool
     */
    public function template($name, $to, $extra = []);
}