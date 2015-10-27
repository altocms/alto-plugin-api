<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 *
 * @package plugin AltoApi
 */

$config['$root$']['module']['api']['get'] = true;
$config['$root$']['module']['api']['post'] = true;
$config['$root$']['module']['api']['put'] = true;
$config['$root$']['module']['api']['delete'] = true;


$config['$root$']['module']['api']['applications'] = array(
    '1234567890' => array(
        'name' => 'Some application',
        //'secret_key' => 'qwerty',
    ),
);

// EOF