<?php namespace DeftCMS\Components\b1tc0re\Mail;


defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Interface IProvider
 *
 * @package     DeftCMS\Components\b1tc0re\Mail
 * @author	    b1tc0re
 * @copyright   2018-2019 DeftCMS (https://deftcms.org/)
 * @since	    Version 0.0.2
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