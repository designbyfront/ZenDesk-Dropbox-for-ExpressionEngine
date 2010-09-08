<?php  if ( ! defined('EXT')) exit('No direct script access allowed');
/**
 * ZenDesk for the Control Panel
 *
 * ### EE 2.1 version ###
 *
 * Based on: jQuery for the Control Panel
 *           http://www.ngenworks.com/software/ee/cp_jquery/
 *           Copyright (c) 2008, nGen Works and EllisLab, Inc.
 *
 * Inspiration from: QuickSave
 *           http://www.vayadesign.net/code/addon/quicksave
 *           [Use of CI hooks to bypass removal of extensions hooks in EE2]
 *
 * An ExpressionEngine Extension that allows the loading of ZenDesk dropbox for use in the ExpressionEngine Control Panel
 * All customisation options are configuable from the extension settings page
 *
 * Local Dependencies:
 *  - Language file 
 *  - Images : contained in "themes\cp_global_images\zendesk_tabs"
 *
 * @package DesignByFront
 * @author  Alistair Brown 
 * @author  nGen Works and the ExpressionEngine Dev Team
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 * @link    http://github.com/designbyfront/ZenDesk-Dropbox-for-ExpressionEngine
 * @since   Version 0.1
 *
 * This work is licensed under the Creative Commons Attribution-Share Alike 3.0 Unported.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/
 * or send a letter to Creative Commons, 171 Second Street, Suite 300,
 * San Francisco, California, 94105, USA.
 *
 * Images used are remixed versions of the default ZenDesk images - these may be dictated by their own license information
 *
 */

class Cp_zendesk_ext
{

	var $settings       = array();
	var $name           = 'ZenDesk for the Control Panel';
	var $version        = '0.1';
	var $description    = 'Adds the ZenDesk dropbox for use in the control panel.';
	var $settings_exist = 'y';
	var $docs_url       = 'http://github.com/designbyfront/ZenDesk-Dropbox-for-ExpressionEngine';

	/**
	 * Constructor
	 */
	function Cp_zendesk_ext($settings = '')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Register hooks by adding them to the database
	 */
	function activate_extension()
	{
		// Default settings
		$settings =	array();
		$settings['zendesk_css']                  = 'http://assets0.zendesk.com/external/zenbox/overlay.css';
		$settings['zendesk_js']	                  = 'http://assets0.zendesk.com/external/zenbox/overlay.js';
		$settings['zendesk_param_tab_id']         = 'support';
		$settings['zendesk_param_tab_color']      = '#D91350';
		$settings['zendesk_param_tab_hover']      = '#BB1A4C';
		$settings['zendesk_param_tab_position']   = 'left';
		$settings['zendesk_param_title']          = 'Support';
		$settings['zendesk_param_subject_header'] = 'Subject';
		$settings['zendesk_param_subject']        = 'Help, I\'m stuck';
		$settings['zendesk_param_email_header']   = 'Your email address';
		$settings['zendesk_param_text']           = 'Hi <b>{screen_name}</b>, how can we help you?'."\n".'Please fill in details below, and we\'ll get back to you as soon as possible!';
		$settings['zendesk_param_tag']            = 'dropbox';
		$settings['zendesk_param_url']            = 'name.zendesk.com'; // Change 'name' to your organisations name

		$hook = array(
						'extension_id'	=> '',
						'class'			=> __CLASS__,
						'method'		=> 'add_js',
						'hook'			=> '', //'show_full_control_panel_end',
						'settings'	=> serialize($settings),
						'priority'	=> 1,
						'version'		=> $this->version,
						'enabled'		=> 'y'
					);
	
		$this->EE->db->query($this->EE->db->insert_string('exp_extensions',	$hook));
	}

	// --------------------------------------------------------------------

	/**
	 * No updates yet.
	 * Manual says this function is required.
	 * @param string $current currently installed version
	 */
	function update_extension($current = '')
	{

		if ($current == '' OR $current == $this->version)
		{
				return FALSE;
		}

		$query = $this->EE->db->query("SELECT settings FROM exp_extensions WHERE class = '".$this->EE->db->escape_str(__CLASS__)."'");

		$this->settings = unserialize($query->row['settings']);
		unset($this->settings['zendesk_css']);
		unset($this->settings['zendesk_js']);
		unset($this->settings['zendesk_param_tab_id']);
		unset($this->settings['zendesk_param_tab_color']);
		unset($this->settings['zendesk_param_tab_hover']);
		unset($this->settings['zendesk_param_tab_position']);
		unset($this->settings['zendesk_param_title']);
		unset($this->settings['zendesk_param_subject_header']);
		unset($this->settings['zendesk_param_subject']);
		unset($this->settings['zendesk_param_email_header']);
		unset($this->settings['zendesk_param_text']);
		unset($this->settings['zendesk_param_tag']);
		unset($this->settings['zendesk_param_url']);

		$this->EE->db->query($this->EE->db->update_string('exp_extensions', array('settings' => serialize($this->settings), 'version' => $this->version), array('class' => __CLASS__)));

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Uninstalls extension
	 */
	function disable_extension()
	{
		$this->EE->db->query("DELETE FROM exp_extensions WHERE class = '".__CLASS__."'");
	}

	// --------------------------------------------------------------------

	/**
	 * EE extension settings
	 * @return array
	 */
	function settings()
	{
		$settings = array();

		$settings['zendesk_css']                  = '';
		$settings['zendesk_js']	                  = '';
		$settings['zendesk_param_tab_id']         = array('r', array('support'   => "zendesk_param_tab_support",
		                                                              'feedback'  => "zendesk_param_tab_feedback",
		                                                              'help'      => "zendesk_param_tab_help",
		                                                              'service'   => "zendesk_param_tab_service",
		                                                              'questions' => "zendesk_param_tab_questions",
		                                                              'comments'  => "zendesk_param_tab_comments",
		                                                              'ask_us'    => "zendesk_param_tab_ask_us"),
		                                                  'support');
		$settings['zendesk_param_tab_color']      = '';
		$settings['zendesk_param_tab_hover']      = '';
		$settings['zendesk_param_tab_position']   = array('r', array('left' => "zendesk_param_tab_position_left", 'right' => "zendesk_param_tab_position_right"), 'left');
		$settings['zendesk_param_title']          = '';
		$settings['zendesk_param_subject_header'] = '';
		$settings['zendesk_param_subject']        = '';
		$settings['zendesk_param_email_header']   = '';
		$settings['zendesk_param_text']           = array('t', '');
		$settings['zendesk_param_tag']            = '';
		$settings['zendesk_param_url']            = '';
		
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
	// ### CI hook ###
	// As EE 2 has eliminated control panel hooks, use of lower level CodeIgniter hooks must be used
	// This hook it is called for every page, and so must be restricted to only the control panel

	function add_js()
	{
		// Get an instance of the current output
		$this->CI =& get_instance();

		// Check if the request is from the control panel
		if(!isset($this->EE->cp) || empty($this->EE->session->userdata['username']))
		{
			// If not, simply return output
			$this->CI->output->_display();
			return;
		}

		// Check if the extension is installed and enabled
		$status = $this->EE->db->query("SELECT enabled, settings FROM exp_extensions WHERE class = '".$this->EE->db->escape_str(__CLASS__)."'");
		if($status->row('enabled') != 'y')
		{
			// If not, simply return output
			$this->CI->output->_display();
			return;
		}

		// Get the ouput page
		$html = $this->CI->output->get_output();

		// Get extension settings from the DB
		$this->settings = unserialize($status->row('settings'));

		$find = '</head>';

		// Allow session details to be displayed in the dropbox input
		$this->settings['zendesk_param_text'] = str_replace('{username}', $this->EE->session->userdata['username'], $this->settings['zendesk_param_text']); 
		$this->settings['zendesk_param_text'] = str_replace('{screen_name}', $this->EE->session->userdata['screen_name'], $this->settings['zendesk_param_text']); 
		$this->settings['zendesk_param_text'] = str_replace('{email}', $this->EE->session->userdata['email'], $this->settings['zendesk_param_text']); 
		$this->settings['zendesk_param_text'] = str_replace('{site_name}', $this->EE->session->userdata['email'], $this->settings['zendesk_param_text']); 
		$this->settings['zendesk_param_text'] = str_replace(array("\n", "\r"), array('<br />', ''), $this->settings['zendesk_param_text']); 
		$this->settings['zendesk_param_subject'] = str_replace('{current_page}', '"+document.URL+"', $this->settings['zendesk_param_subject']); 

		// Create the necessary HTML, JS and CSS code -------
		$replace = "\n".'<script type="text/javascript">'."\n";
		$replace .= 'var zenbox_params = {};'."\n";
		$replace .= 'zenbox_params.tab_id = "'.         $this->settings['zendesk_param_tab_id']         .'";'."\n";
		$replace .= 'zenbox_params.tab_color = "'.      $this->settings['zendesk_param_tab_color']      .'";'."\n";
		$replace .= 'zenbox_params.title = "'.          $this->settings['zendesk_param_title']          .'";'."\n";
		$replace .= 'zenbox_params.text = "'.           $this->settings['zendesk_param_text']           .'";'."\n";
		$replace .= 'zenbox_params.tag = "'.            $this->settings['zendesk_param_tag']            .'";'."\n";
		$replace .= 'zenbox_params.url = "'.            $this->settings['zendesk_param_url']            .'";'."\n";
		$replace .= 'zenbox_params.email = "'.          $this->EE->session->userdata['email']           .'";'."\n";
		$replace .= 'zenbox_params.subject_header = "'. $this->settings['zendesk_param_subject_header'] .'";'."\n";
		$replace .= 'zenbox_params.subject = "'.        $this->settings['zendesk_param_subject']        .'";'."\n";
		$replace .= 'zenbox_params.email_header = "'.   $this->settings['zendesk_param_email_header']   .'";'."\n";
		$replace .= '</script>'."\n";

		$replace .= '<style type="text/css">'."\n";
		$replace .= '   @import url(\''.$this->settings['zendesk_css'].'\');'."\n";
		if ($this->settings['zendesk_param_tab_position'] == 'right')
			$replace .= '   a#zenbox_tab { left: auto; right: 0px; }'."\n";
		$replace .= '</style>'."\n";

		$replace .= '<script type="text/javascript" src="'.$this->settings['zendesk_js'].'"></script>'."\n";
		$replace .= '<script type="text/javascript">'."\n";
		$replace .= '   $("document").ready(function() {'."\n";
		$replace .= '      Zenbox.init();'."\n";
		$replace .= '      $("#zenbox_tab").css({"background-image": "url('.constant('PATH_CP_GBL_IMG').'zendesk_tabs/'.$this->settings['zendesk_param_tab_position'].'/tab_'.$this->settings['zendesk_param_tab_id'].'_front.png)",'."\n";
		$replace .= '                            "z-index": "10",'."\n";
		$replace .= '                            "'.$this->settings['zendesk_param_tab_position'].'": "-16px"});'."\n";
		$replace .= '      $("#zenbox_tab").hover('."\n";
		$replace .= '         function() { $(this).css({"background-color": "'.$this->settings['zendesk_param_tab_hover'].'"}); $(this).animate({"'.$this->settings['zendesk_param_tab_position'].'": "0px",}, 300); },'."\n";
		$replace .= '         function() { $(this).css({"background-color": "'.$this->settings['zendesk_param_tab_color'].'"}); $(this).animate({"'.$this->settings['zendesk_param_tab_position'].'": "-16px",}, 300); }'."\n";
		$replace .= '      );'."\n";
		$replace .= '   });'."\n";
		$replace .= '</script>'."\n";

		$replace .= $find ."\n";
		// ---------------------------------------------

		// Inject the necessary HTML, JS and CSS code
		$html = str_replace($find, $replace, $html);

		// Return new output and call display
		$this->CI->output->set_output($html);
		$this->CI->output->_display();
	}

	// --------------------------------------------------------------------

}
// END CLASS Cp_zendesk

/* End of file ext.cp_zendesk.php */
/* Location: ./system/expressionengine/third_party/zendesk/ext.cp_zendesk.php */