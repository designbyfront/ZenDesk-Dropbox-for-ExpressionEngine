<?php  if ( ! defined('EXT')) exit('No direct script access allowed');
/**
 * ZenDesk for the Control Panel
 *
 * ### EE 1.6 version ###
 * 
 * Based on: jQuery for the Control Panel
 *           http://www.ngenworks.com/software/ee/cp_jquery/
 *           Copyright (c) 2008, nGen Works and EllisLab, Inc.
 *
 * An ExpressionEngine Extension that allows the loading of ZenDesk dropbox for use in the ExpressionEngine Control Panel
 * All customisation options are configuable from the extension settings page
 * Compatible with both ZenDesk Dropbox version 1 and version 2
 *
 * Dependencies:
 *  - Language file
 *  - Images - contained in "themes/cp_global_images/zendesk_tabs/"
 *  - jQuery for the Control Panel (uses jQuery to edit tab CSS)
 *
 * @package DesignByFront
 * @author  Alistair Brown 
 * @author  nGen Works and the ExpressionEngine Dev Team
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 * @link    http://github.com/designbyfront/ZenDesk-Dropbox-for-ExpressionEngine
 * @since   Version 0.2
 * 
 * This work is licensed under the Creative Commons Attribution-Share Alike 3.0 Unported.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/
 * or send a letter to Creative Commons, 171 Second Street, Suite 300,
 * San Francisco, California, 94105, USA.
 *
 * Images used are remixed versions of the default ZenDesk images - these may be dictated by their own license information
 * 
 */

class Cp_zendesk
{

	var $settings       = array();
	var $name           = 'ZenDesk for the Control Panel';
	var $version        = '0.2';
	var $description    = 'Adds the ZenDesk dropbox for use in the control panel.';
	var $settings_exist = 'y';
	var $docs_url       = 'http://github.com/designbyfront/ZenDesk-Dropbox-for-ExpressionEngine';

	/**
	 * Constructor
	 */
	function Cp_zendesk($settings = '')
	{
		$this->settings = $settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Register hooks by adding them to the database
	 */
	function activate_extension()
	{
		global $DB;

		// default settings
		$settings = array();
		$settings['zendesk_dropbox_version']       = 'version_1';
		$settings['zendesk_param_url']             = '{name}.zendesk.com'; // Change {name} to your organisations name
		$settings['zendesk_css']                   = '';
		$settings['zendesk_js']                    = '';
		$settings['zendesk_param_tab_id']          = 'support';
		$settings['zendesk_param_tab_color']       = '#8AB446';
		$settings['zendesk_param_tab_hover']       = '#6D942D';
		$settings['zendesk_param_tab_position']    = 'left';
		$settings['zendesk_param_subject']         = 'Help, I\'m stuck';
		$settings['zendesk_param_title']           = 'Support';
		$settings['zendesk_param_subject_header']  = 'Subject';
		$settings['zendesk_param_email_header']    = 'Your email address';
		$settings['zendesk_param_text']            = 'Hi <b>{screen_name}</b>, how can we help you?'."\n".'Please fill in details below, and we\'ll get back to you as soon as possible!';
		$settings['zendesk_param_tag']             = 'dropbox';
		$settings['zendesk_param_drobox_id']       = '{dropbox_id}';
		$settings['zendesk_param_description']     = '';
		$settings['zendesk_param_loading_message'] = 'Support form loading...';
		$settings['zendesk_param_close_message']   = 'Close';

		$hook = array(
						'extension_id' => '',
						'class'        => __CLASS__,
						'method'       => 'add_js',
						'hook'         => 'show_full_control_panel_end',
						'settings'     => serialize($settings),
						'priority'     => 1,
						'version'      => $this->version,
						'enabled'      => 'y'
					);

		$DB->query($DB->insert_string('exp_extensions',	$hook));
	}

	// --------------------------------------------------------------------

	/**
	 * No updates yet.
	 * Manual says this function is required.
	 * @param string $current currently installed version
	 */
	function update_extension($current = '')
	{
		global $DB, $EXT;

		if ($current == '' OR $current == $this->version)
		{
				return FALSE;
		}

		$query = $DB->query("SELECT settings FROM exp_extensions WHERE class = '".$DB->escape_str(__CLASS__)."'");

		$this->settings = unserialize($query->row['settings']);
		unset($this->settings['zendesk_dropbox_version']);
		unset($this->settings['zendesk_param_url']);
		unset($this->settings['zendesk_css']);
		unset($this->settings['zendesk_js']);
		unset($this->settings['zendesk_param_tab_id']);
		unset($this->settings['zendesk_param_tab_color']);
		unset($this->settings['zendesk_param_tab_hover']);
		unset($this->settings['zendesk_param_tab_position']);
		unset($this->settings['zendesk_param_subject']);
		unset($this->settings['zendesk_param_title']);
		unset($this->settings['zendesk_param_subject_header']);
		unset($this->settings['zendesk_param_email_header']);
		unset($this->settings['zendesk_param_text']);
		unset($this->settings['zendesk_param_tag']);
		unset($this->settings['zendesk_param_drobox_id']);
		unset($this->settings['zendesk_param_description']);
		unset($this->settings['zendesk_param_loading_message']);
		unset($this->settings['zendesk_param_close_message']);

		$DB->query($DB->update_string('exp_extensions', array('settings' => serialize($this->settings), 'version' => $this->version), array('class' => __CLASS__)));

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Uninstalls extension
	 */
	function disable_extension()
	{
		global $DB;
		$DB->query("DELETE FROM exp_extensions WHERE class = '".__CLASS__."'");
	}

	// --------------------------------------------------------------------

	/**
	 * EE extension settings
	 * @return array
	 */
	function settings()
	{
		$settings = array();

		$settings['zendesk_dropbox_version']       = array('r', array('version_1' => "zendesk_dropbox_version_one",
		                                                               'version_2' => "zendesk_dropbox_version_two"),
		                                                    'version_1');
		$settings['zendesk_param_url']             = '';
		$settings['zendesk_css']                   = '';
		$settings['zendesk_js']                    = '';
		$settings['zendesk_param_tab_id']          = array('r', array('support'   => "zendesk_param_tab_support",
		                                                               'feedback'  => "zendesk_param_tab_feedback",
		                                                               'help'      => "zendesk_param_tab_help",
		                                                               'service'   => "zendesk_param_tab_service",
		                                                               'questions' => "zendesk_param_tab_questions",
		                                                               'comments'  => "zendesk_param_tab_comments",
		                                                               'ask_us'    => "zendesk_param_tab_ask_us"),
		                                                    'support');
		$settings['zendesk_param_tab_color']       = '';
		$settings['zendesk_param_tab_hover']       = '';
		$settings['zendesk_param_tab_position']    = array('r', array('left' => "zendesk_param_tab_position_left", 'right' => "zendesk_param_tab_position_right"), 'left');
		$settings['zendesk_param_subject']         = '';
		$settings['zendesk_param_title']           = '';
		$settings['zendesk_param_subject_header']  = '';
		$settings['zendesk_param_email_header']    = '';
		$settings['zendesk_param_text']            = array('t', '');
		$settings['zendesk_param_tag']             = '';
		$settings['zendesk_param_drobox_id']       = '';
		$settings['zendesk_param_description']     = '';
		$settings['zendesk_param_loading_message'] = '';
		$settings['zendesk_param_close_message']   = '';

		return $settings;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds script tags to the head of CP pages
	 * 
	 * We add the script into the head tag (at the bottom - it may rely on other scripts)
	 * 
	 * @param string $html Final html of the control panel before display
	 * @return string Modified HTML
	 */

	function add_js($html)
	{
		global $EXT, $SESS;

		$html = ($EXT->last_call !== FALSE) ? $EXT->last_call : $html;

		$version1 = false;
		$version2 = false;
		$find = '</head>';

		$replace = "\n";

		if ($this->settings['zendesk_dropbox_version'] == 'version_1') {
			$version1 = true;
			$zendesk_js = (empty($this->settings['zendesk_js']) ? 'http://assets0.zendesk.com/external/zenbox/overlay.js' : $this->settings['zendesk_js']);

			// Allow session variables to be displayed in the dropbox input
			$this->settings['zendesk_param_text'] = str_replace('{username}', $SESS->userdata['username'], $this->settings['zendesk_param_text']); 
			$this->settings['zendesk_param_text'] = str_replace('{screen_name}', $SESS->userdata['screen_name'], $this->settings['zendesk_param_text']); 
			$this->settings['zendesk_param_text'] = str_replace('{email}', $SESS->userdata['email'], $this->settings['zendesk_param_text']); 
			$this->settings['zendesk_param_text'] = str_replace('{site_name}', $SESS->userdata['email'], $this->settings['zendesk_param_text']); 
			$this->settings['zendesk_param_text'] = str_replace(array("\n", "\r"), array('<br />', ''), $this->settings['zendesk_param_text']); 
			$this->settings['zendesk_param_subject'] = str_replace('{current_page}', '"+document.URL+"', $this->settings['zendesk_param_subject']); 

			// Create the necessary JS code -------
			$replace .= '<script type="text/javascript">'."\n";
			$replace .= '   var zenbox_params = {};'."\n";
			$replace .= '   zenbox_params.tab_id = "'.         $this->settings['zendesk_param_tab_id']         .'";'."\n";
			$replace .= '   zenbox_params.tab_color = "'.      $this->settings['zendesk_param_tab_color']      .'";'."\n";
			$replace .= '   zenbox_params.title = "'.          $this->settings['zendesk_param_title']          .'";'."\n";
			$replace .= '   zenbox_params.text = "'.           $this->settings['zendesk_param_text']           .'";'."\n";
			$replace .= '   zenbox_params.tag = "'.            $this->settings['zendesk_param_tag']            .'";'."\n";
			$replace .= '   zenbox_params.url = "'.            $this->settings['zendesk_param_url']            .'";'."\n";
			$replace .= '   zenbox_params.email = "'.          $SESS->userdata['email']                        .'";'."\n";
			$replace .= '   zenbox_params.subject_header = "'. $this->settings['zendesk_param_subject_header'] .'";'."\n";
			$replace .= '   zenbox_params.subject = "'.        $this->settings['zendesk_param_subject']        .'";'."\n";
			$replace .= '   zenbox_params.email_header = "'.   $this->settings['zendesk_param_email_header']   .'";'."\n";
			$replace .= '</script>'."\n";
			$replace .= '<script type="text/javascript" src="'.$zendesk_js.'"></script>'."\n";


		} else if ($this->settings['zendesk_dropbox_version'] == 'version_2') {
			$version2 = true;
			$zendesk_js = (empty($this->settings['zendesk_js']) ? 'http://asset0.zendesk.com/external/zenbox/zenbox-2.0.js' : $this->settings['zendesk_js']);

			$replace .= '<script type="text/javascript" src="'.$zendesk_js.'"></script>'."\n";
			$replace .= '<script type="text/javascript">'."\n";
			$replace .= '   $(document).ready(function() {';
			$replace .= '      if (typeof(Zenbox) !== "undefined") {'."\n";
			$replace .= '         Zenbox.init({'."\n";
			$replace .= '            dropboxID:           "'.$this->settings['zendesk_param_drobox_id'].'",'."\n";
			$replace .= '            url:                 "'.$this->settings['zendesk_param_url'].'",'."\n";
			$replace .= '            assetHost:           "asset0.zendesk.com",'."\n";
			$replace .= '            tabID:               "support",'."\n";
			$replace .= '            tabText:             "Support",'."\n";
			$replace .= '            tabColor:            "'.$this->settings['zendesk_param_tab_color'].'",'."\n";
			$replace .= '            tabPosition:         "'.$this->settings['zendesk_param_tab_position'].'",'."\n";
			$replace .= '            tabImageURL:         "'.'http'.(isset($_SERVER["HTTPS"]) ? 's' : '').'://'.$_SERVER["SERVER_NAME"].constant('PATH_CP_IMG').'zendesk_tabs/'.$this->settings['zendesk_param_tab_position'].'/tab_'.$this->settings['zendesk_param_tab_id'].'_front.png'.'",'."\n";
			$replace .= '            loadingText:         "'.$this->settings['zendesk_param_loading_message'].'",'."\n";
			$replace .= '            closeText:           "'.$this->settings['zendesk_param_close_message'].'",'."\n";
			$replace .= '            request_subject:     "'.$this->settings['zendesk_param_subject'].'",'."\n";
			$replace .= '            request_description: "'.$this->settings['zendesk_param_description'].'",'."\n";
			$replace .= '            requester_name:      "'.$SESS->userdata['screen_name'].'",'."\n";
			$replace .= '            requester_email:     "'.$SESS->userdata['email'].'"'."\n";
			$replace .= '         });'."\n";
			$replace .= '      }'."\n";
			$replace .= '   });'."\n";
			$replace .= '</script>'."\n";

		} else {
			$replace .= '   alert("ZenDesk Dropbox Extension - Invalid dropbox version supplied, please check your settings")'."\n";
		}

		$tab_offset = 16;
		$zendesk_css = (empty($this->settings['zendesk_css']) ? ($version1 ? 'http://assets0.zendesk.com/external/zenbox/overlay.css' : ($version2 ? 'http://asset0.zendesk.com/external/zenbox/zenbox-2.0.css' : '') ) : $this->settings['zendesk_css']);
		$replace .= '<style type="text/css" media="screen, projection">'."\n";
		$replace .= '   @import url(\''.$zendesk_css.'\');'."\n";
		if ($this->settings['zendesk_param_tab_position'] == 'right' && $version1) {
			$replace .= '   a#zenbox_tab { left: auto; right: 0px;}'."\n";
			$tab_offset += 11;
		} else if ($version2) {
			$tab_offset += 11;
		}
		$replace .= '</style>'."\n";

		$replace .= '<script type="text/javascript">'."\n";
		$replace .= '   $("document").ready(function() {'."\n";
		$replace .= '      // Hack settings page - add titles above v1/v2 specific inputs and ** message'."\n";
		$replace .= '      $("#zendesk_param_title").parent().parent().before("<tr><td class=\"tableHeadingAlt\" colspan=\"2\">Version 1 specific inputs:</td></tr>");'."\n";
		$replace .= '      $("#zendesk_param_drobox_id").parent().parent().before("<tr><td class=\"tableHeadingAlt\" colspan=\"2\">Version 2 specific inputs:</td></tr>");'."\n\n";
		$replace .= '      $("#settings_cp_zendesk").before("<div style=\"margin-left: 10px;\">** &nbsp;=&nbsp; Values which must be entered for the ZenDesk dropbox to load</div>");'."\n\n";
		if ($version1)
			$replace .= '      Zenbox.init();'."\n";
		$replace .= '      $("#zenbox_tab").css({"background-image": "url('.constant('PATH_CP_IMG').'zendesk_tabs/'.$this->settings['zendesk_param_tab_position'].'/tab_'.$this->settings['zendesk_param_tab_id'].'_front.png)",'."\n";
		$replace .= '                            "width": "45px",'."\n";
		$replace .= '                            "z-index": "10",'."\n";
		$replace .= '                            "'.$this->settings['zendesk_param_tab_position'].'": "-'.$tab_offset.'px"});'."\n";
		$tab_out = 0 + (16 - $tab_offset);
		$replace .= '      $("#zenbox_tab").hover('."\n";
		$replace .= '         function() { $(this).css({"background-color": "'.$this->settings['zendesk_param_tab_hover'].'"}); $(this).animate({"'.$this->settings['zendesk_param_tab_position'].'": "'.$tab_out.'px",}, 300); },'."\n";
		$replace .= '         function() { $(this).css({"background-color": "'.$this->settings['zendesk_param_tab_color'].'"}); $(this).animate({"'.$this->settings['zendesk_param_tab_position'].'": "-'.$tab_offset.'px",}, 300); }'."\n";
		$replace .= '      );'."\n";
		$replace .= '   });'."\n";
		$replace .= '</script>'."\n";

		$replace .= $find."\n";
		// ---------------------------------------------

		// Inject the necessary HTML, JS and CSS code
		$html = str_replace($find, $replace, $html);

		// Return new output
		return $html;
	}

	// --------------------------------------------------------------------

}
// END CLASS Cp_zendesk

/* End of file ext.cp_zendesk.php */
/* Location: ./system/extensions/ext.cp_zendesk.php */