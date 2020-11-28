<?php namespace DeftCMS\Components\b1tc0re\Mail;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Interface IProvider
 *
 * @package     DeftCMS\Components\b1tc0re\Mail
 * @author	    b1tc0re
 * @copyright   2019-2020 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9
 */
interface IMailModel
{

    /**
     * Get mail templates form database
     *
     * @return array
     */
    public function getMailTemplates();

}