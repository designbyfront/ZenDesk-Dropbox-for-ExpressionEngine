<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['display_override'][] = array(
                                'class'    => 'Cp_zendesk_ext',
                                'function' => 'add_js',
                                'filename' => 'ext.cp_zendesk.php',
                                'filepath' => 'third_party/cp_zendesk'
                                );

/* End of file hooks.php */
/* Location: ./system/expressionengine/config/hooks.php */