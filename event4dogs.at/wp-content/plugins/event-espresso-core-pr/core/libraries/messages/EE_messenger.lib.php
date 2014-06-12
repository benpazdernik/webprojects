<?php 
if (!defined('EVENT_ESPRESSO_VERSION') )
	exit('NO direct script access allowed');

/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package			Event Espresso
 * @ author				Seth Shoultes
 * @ copyright		(c) 2008-2011 Event Espresso  All Rights Reserved.
 * @ license			http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link				http://www.eventespresso.com
 * @ version		 	4.0
 *
 * ------------------------------------------------------------------------
 *
 * EE_messenger class
 *
 * Abstract class for setting up messengers. 
 * Different messengers (i.e. email, sms) can be setup by extending this class and adding them to the /includes/core/messages/messengers' directory. View examples there.
 *
 * @package			Event Espresso
 * @subpackage		includes/core/messages
 * @author			Darren Ethier, Brent Christensen
 *
 * ------------------------------------------------------------------------
 */
abstract class EE_messenger extends EE_Messages_Base {



	/**
	 * This property holds the default message types associated with this messenger when it is activated. The values of the array must match a valid message type.
	 * This property gets set by the _set_default_message_types() method.
	 * 
	 * @var array
	 */
	protected $_default_message_types = array();



	/**
	 * Holds the configuration for the EE_Messages_Validator class to know how to validated the different fields. Note that the Validator will match each field here with the allowed shortcodes set in the "valid_shortcodes" array for the matched message type context.  So message types don't need to set a $_validator_config property.
	 *
	 * Remember, ALL fields must be declared in this array.  However, an empty value for the field means that the field will accept all valid shortcodes set for the given context in the message type (by default). 
	 * 
	 * Array should be in this format:
	 *
	 * array(
	 * 	'field_name(i.e.to)' => array(
	 * 		'shortcodes' => array('email'), //an array of shortcode groups (correspond to EE_Shortcodes library class) that are allowed in the field. Typically you can just include $this->_valid_shortcodes['field_name'] as the value here (because they will match).
	 * 		'specific_shortcodes' => array( array('[EVENT_AUTHOR_EMAIL]' => __('Admin Email', 'event_espresso')), //if this index is present you can further restrict the field to ONLY specific shortcodes if an entire group isn't sufficient. Specific shortcodes need to be listed as an array with the index the shortcode and the value = the label.
	 * 		'type' => 'email' //this is the field type and should match one of the validator types (see EE_Messages_Validator::validator() for all the possible types).  If not required you can just leave empty.,
	 * 		'required' => array'[SHORTCODE]') //this is used to indicate the shortcodes that MUST be in the assembled array of shortcodes by the validator in order for this field to be included in validation.  Otherwise the validator will always assign shortcodes for this field (regardless of whether the field settings for the given messenger/message_type/context use the field or not.).. please note, this does NOT mean that the shortcodes listed here MUST be in the given field.
	 * 	)
	 * )
	 * 
	 * @var array
	 */
	protected $_validator_config = array();



	/**
	 * This will hold the EEM_message_templates model for interacting with the database and retrieving active templates for the messenger
	 * @var object
	 */
	protected $_EEM_data;

	

	/**
	 * this property just holds an array of the various template refs.
	 * @var array
	 */
	protected $_template_fields = array();



	
	/**
	 * This holds an array of the arguments used in parsing a template for the sender.
	 * @var array
	 */
	protected $_template_args = array();






	/**
	 * This property will hold the configuration for any test settings fields that are required for the "test" button that is used to trigger an actual test of this messenger
	 *
	 * @protected
	 * @var array
	 */
	protected $_test_settings_fields = array();





	/**
	 * holds all the active templates saved in the database
	 * @access public
	 * @var array
	 */
	public $active_templates = array();






	public function __construct() {
		$this->_EEM_data = EEM_Message_Template_Group::instance();
		$this->_messages_item_type = 'messenger';
		
		parent::__construct();

		$this->_set_test_settings_fields();
		$this->_set_templates();	
		$this->_set_template_fields();
		$this->_set_default_message_types();
		$this->_set_validator_config();
	}





	/**
	 * _set_template_fields
	 * This sets up the fields that a messenger requires for the message to go out.
	 * 
	 * @abstract
	 * @access  protected
	 * @return void
	 */
	abstract protected function _set_template_fields();








	
	/**
	 * This method sets the _default_message_type property (see definition in docs attached to property)
	 *
	 * @abstract
	 * @access protected
	 * @return void
	 */
	abstract protected function _set_default_message_types();











	/**
	 * Child classes must declare the $_validator_config property using this method.
	 * See comments for $_validator_config for details on what it is used for.
	 *
	 * NOTE:  messengers should set an array of valid shortcodes for ALL scenarios.  The corresponding validator class (validators/{messenger}) can be used to restrict only certain shortcodes per template so users cannot add certain shortcodes.
	 *
	 * @access protected
	 * @return void
	 */
	abstract protected function _set_validator_config();




	/**
	 * messengers must define the location of the inline css template to use in final assembled templates.
	 *
	 * This method is also used in the admin backend to set the css for the tinymce editor.
	 * 
	 * @access public
	 * @param bool $url if true we return the url to the css, if false, we return the path.
	 * @return string the location of the css file to use for inline css.
	 */
	abstract public function get_inline_css_template( $url = FALSE );






	/**
	 * We just deliver the messages don't kill us!!  This method will need to be modified by child classes for whatever action is taken to actually send a message.  
	 * @return void
	 * @todo  at some point we may want to return success or fail so we know whether a message has gone off okay and we can assemble reporting.
	 */
	abstract protected function _send_message();




	/**
	 * We give you pretty previews of the messages!
	 * @return string html body for message content.
	 */
	abstract protected function _preview();









	/**
	 * This just returns the default message types associated with this messenger when it is first activated.
	 *
	 * @access public
	 * @return array
	 */
	public function get_default_message_types() {
		return $this->_default_message_types;
	}





	/**
	 * this is just used by the custom validators (EE_Messages_Validator classes) to modify the _validator_config for certain message_type/messenger combos where a context may only use certain shortcodes etc.
	 *
	 * @access public
	 * @param array $new_config Whatever is put in here will reset the _validator_config property
	 */
	public function set_validator_config( $new_config ) {
		$this->_validator_config = $new_config;
	}




	/**
	 * This returns the _validator_config property
	 *
	 * @access public
	 * @return array
	 */
	public function get_validator_config() {
		$class = get_class($this);

		$config = apply_filters( 'FHEE__' . $class . '__get_validator_config', $this->_validator_config, $this );
		$config = apply_filters( 'FHEE__EE_messenger__get_validator_config', $config, $this );
		return $config;
	}




	/**
	 * this public method accepts a page slug (for an EE_admin page) and will return the response from the child class callback function if that page is registered via the `_admin_registered_page` property set by the child class.
	 * 
	 * @param string $page the slug of the EE admin page
	 * @param array $message_types an array of active message type objects 
	 * @param string $action the page action (to allow for more specific handling - i.e. edit vs. add pages)
	 * @param array $extra  This is just an extra argument that can be used to pass additional data for setting up page content.
	 * @access public
	 * @return string content for page
	 */
	public function get_messenger_admin_page_content( $page, $action = null, $extra = array(), $message_types = array() ) {
		return $this->_get_admin_page_content( $page, $action, $extra, $message_types );
	}







	protected function _get_admin_content_events_edit( $message_types, $extra ) {
		//we don't need message types here so we're just going to ignore. we do, however, expect the event id here. The event id is needed to provide a link to setup a custom template for this event.
		$event_id = isset($extra['event']) ? $extra['event'] : null;
		$event_template_set = array();
		$event_template_trashed = array();
		$event_group_id = array();
		$new_event = empty($event_id) ? true : false;

		//active message types
		$EE_MSG = new EE_messages();
		$installed_message_types = $EE_MSG->get_installed_message_types();

		//todo: this should be replaced by EE_MSG_ADMIN_URL constant when we have access to it.
		$ee_msg_admin_url = defined('EE_MSG_ADMIN_URL') ? EE_MSG_ADMIN_URL : admin_url('admin.php?page=espresso_messages');


		//is there a template for this event (and each message type)?  If so, then we need to indicate that it's been selected and provide the option to switch back to global (which trashes the event template). $this->active_templates ONLY includes non-trashed templates.	
		if ( count($this->active_templates) > 1 && !empty($event_id) ) {
			foreach ( $this->active_templates as $template ) {
				if ( $event_id == $template->event() ) {
					$event_template_set[$template->message_type()] = true;
					$event_group_id[$template->message_type()] = $template->GRP_ID();
				}
			}
		}


		//now we need to see if there are any untrashed event templates for this event
		$trashed_evt_templates = $this->_EEM_data->get_all_trashed_message_templates_by_event($event_id);
		
		if ( count($trashed_evt_templates) > 0 && $trashed_evt_templates ) {
			foreach ( $trashed_evt_templates as $trashed ) {
				$event_template_set[$trashed->message_type()] = true;
				$event_group_id[$trashed->message_type()] = $trashed->GRP_ID();
				$event_template_trashed[$trashed->message_type()] = true;
			}
		}
		
		
		$content = '<div id="message-templates-' . $this->name . '" class="message-templates-container">' . "\n\t";
		
		foreach ( $this->active_templates as $template ) {
			//if this is a template for a different event then get out because we're only showing templates related to THIS event (NOT other events)
			$this_template_event_id = $template->event();
			if ( !empty($event_id) && !empty( $this_template_event_id ) && $event_id != $template->event() )
				continue;

			$et_set = isset($event_template_set[$template->message_type()]) ? true : false;
			$et_trashed = isset($event_template_trashed[$template->message_type()]) ? true : false;
			$et_group_id = isset($event_group_id[$template->message_type()]) ? $event_group_id[$template->message_type()] : false;


			//need to get the label for the message type.
			$mt_label = $installed_message_types[$template->message_type()]->label['singular'];
			
			//check for existence of Event Template and if present AND the current template in the loop is the event template (or the current template in the loop is a DIFFERENT event template) let's skip (we'll delay until we get to global)
			if ( $et_set && !$template->is_global() ) continue;

			//if this is a new event then we ONLY want to show ONE option.
			
			$template_type = $template->message_type();
			if ( ( isset($old_template_type) && $old_template_type == $template_type ) ) continue;


			//setup current button
			$button_text = $et_set && !$et_trashed ? __('Custom Templates', 'event_espresso') : __('Global Templates', 'event_espresso');

			//setup query_args for button link
			if ( $et_set && !$et_trashed ) {
				$button_query_args = array(
					'action' => 'edit_message_template',
					'id' => $et_group_id,
					'evt_id' => $event_id,
					'edit_message_template_nonce' => wp_create_nonce( 'edit_message_template_nonce')
					);
			} else {
				$button_query_args = array(
					'action' => 'edit_message_template',
					'id' => $template->GRP_ID(),
					'edit_message_template_nonce' => wp_create_nonce( 'edit_message_template_nonce' )
					);
			}

			$button_link = add_query_arg( $button_query_args, $ee_msg_admin_url);

			//setup switch button
			$switch_b_text = ($et_set && $et_trashed) || !$et_set ? __('Switch to Custom Templates', 'event_espresso') : __('Switch to Global Templates', 'event_espresso');
			$switch_b_text = empty($event_id) ? false : $switch_b_text;

			//setup query_args for switcher button
			if ( $et_set && $et_trashed ) {
				$switch_query_args = array(
					'action' => 'restore_message_template',
					'message_type' => $template->message_type(),
					'id' => $et_group_id,
					'template_switch' => TRUE,
					'evt_id' => $event_id,
					'restore_message_template_nonce' => wp_create_nonce( 'restore_message_template_nonce' )
					);
			} else if ( !$et_set && !empty($event_id) ) {
				$switch_query_args = array(
					'action' => 'add_new_message_template',
					'evt_id' => $event_id,
					'messenger' => $this->name,
					'message_type' => $template->message_type(),
					'add_new_message_template_nonce' => wp_create_nonce('add_new_message_template_nonce')
					);
			} else {
				$switch_query_args = array(
					'action' => 'trash_message_template',
					'id' => $et_group_id,
					'template_switch' => TRUE,
					'evt_id' => $event_id,
					'trash_message_template_nonce' => wp_create_nonce('trash_message_template_nonce')
				);
			}

			$switch_b_link = add_query_arg( $switch_query_args, $ee_msg_admin_url );

			$main_button = '<a class="button-primary template_picker" href="' . $button_link . '" title="' . __('Click to Edit', 'event_espresso') . '">' . $button_text . '</a>';
			$switch_button = $switch_b_text ? sprintf( __('You can %s if you want', 'event_espresso'),'<span class="switch-template-button"><a class="button-secondary template_picker" href="' . $switch_b_link . '">' . $switch_b_text . '</a></span>') : '<span class="switch-template-button">' . __('You can\'t create custom templates (for this event) until you\'ve saved this event', 'event_espresso') . '</span>';

			$content .= '<div class="message-template-message-type-container">' . "\n\t";
			$content .= '<p>';
			$content .= sprintf( __('This event will use %s for <span class="message-type-text">%s %s</span> messages. %s.', 'event_espresso'), $main_button, $mt_label, $this->label['singular'], $switch_button);
			$content .= '</p>' . "\n" . '</div>';

			$old_template_type = $template_type;
		}

		$content .= '</div>';
		return $content;
	}

	




	/**
	 * get_template_fields
	 * 
	 * @access public
	 * @return array $this->_template_fields
	 */
	public function get_template_fields() {
		$template_fields = apply_filters( 'FHEE__' . get_class($this) . '__get_template_fields', $this->_template_fields, $this );
		$template_fields = apply_filters(' FHEE__EE_messenger__get_template_fields', $template_fields, $this );
		return $template_fields;
	}






	/**
	 * This sets the active template groups for the messenger.  
	 * @access protected
	 */
	protected function _set_templates() {
		$this->active_templates = $this->_EEM_data->get_all_active_message_templates_by_messenger($this->name);
	}

	/** SETUP METHODS **/

	/**
	 * The following method doesn't NEED to be used by child classes but might be modified by the specific messenger
	 */
	protected function _set_template_value($item, $value) {
		if ( array_key_exists($item, $this->_template_fields) ) {
			$prop = '_' . $item;
			$this->$prop= $value;
		}
	}

	/**
	 * Sets up the message for sending.
	 * @param  EE_message_type $message the message object that contains details about the message.
	 */
	public function send_message( $message ) {
		$this->_validate_and_setup( $message );
		$this->_send_message();
	}



	/**
	 * Sets up and returns message preview
	 * @param  object $message incoming message object
	 * @param  bool   $send    true we will actually use the _send method (for test sends). FALSE we just return preview
	 * @return string          return the message html content
	 */
	public function get_preview( $message, $send = FALSE ) {
		$this->_validate_and_setup( $message );

		if ( $send ) {
			//are we overriding any existing template fields?
			$settings = $this->get_existing_test_settings();
			if ( !empty( $settings ) ) {
				foreach( $settings as $field => $value ) {
					$this->_set_template_value( $field, $value );
				}
			}
		}

		return $send ? $this->_send_message() : $this->_preview();
	}




	/**
	 * simply validates the incoming message object and then sets up the properties for the messenger
	 * @param  object $message message object
	 * @return void          
	 */
	protected function _validate_and_setup( $message ) {
		if ( !is_object( $message ) )
			throw new EE_Error( __('Incoming "$message" must be an object', 'event_espresso' ) );

		$template_fields = $this->get_template_fields();

		foreach ( $template_fields as $template => $value ) {
			if ( $template !== 'extra' )
				$this->_set_template_value($template, $message->$template);
		}
	}




	/**
	 * Utility method for child classes to get the contents of a template file and return
	 *
	 * We're assuming the child messenger class has already setup template args!
	 * @param  string  	$template url for template
	 * @param  bool 	$preview if true we use the preview wrapper otherwise we use main wrapper.
	 * @return string
	 */
	protected function _get_main_template( $preview = FALSE ) {

		//first get inline css (will be empty if the messenger doesn't use it)
		$this->_template_args['inline_style'] = file_get_contents( $this->get_inline_css_template(FALSE, $preview), TRUE );
		$base_path = EE_LIBRARIES . 'messages/messenger/assets/' . $this->name . '/';

		//figure out main template path
		$wrapper_template = !$preview ? $base_path . $this->name . '-messenger-main-wrapper.template.php' : $base_path . $this->name . '-messenger-preview-wrapper.template.php';
		//check file exists and is readable
		if ( !is_readable( $wrapper_template ) )
			throw new EE_Error( sprintf( __('Unable to access the template file for the %s messenger main content wrapper.  The location being attempted is %s.', 'event_espresso' ), ucwords($this->label['singular'])), $wrapper_template );

		//require template helper
		EE_Registry::instance()->load_helper( 'Template' );
		return EEH_Template::display_template( $wrapper_template, $this->_template_args, TRUE );
	}



	/**
	 * set the _test_settings_fields property
	 *
	 * @access protected
	 * @return void 
	 */
	protected function _set_test_settings_fields() {
		$this->_test_settings_fields = array();
	}



	/**
	 * return the _test_settings_fields property
	 * @return array
	 */
	public function get_test_settings_fields() {
		return $this->_test_settings_fields;
	}




	/**
	 * This just returns any existing test settings that might be saved in the database
	 *
	 * @access public
	 * @return array
	 */
	public function get_existing_test_settings() {
		$settings = get_option('ee_active_messengers', true);
		return isset( $settings[$this->name]['test_settings'] ) ? $settings[$this->name]['test_settings'] : array();
	}



	/**
	 * All this does is set the existing test settings (in the db) for the messenger
	 *
	 * @access public
	 * @return bool 	success/fail
	 */
	public function set_existing_test_settings( $settings ) {
		$existing = get_option('ee_active_messengers', true);
		$existing[$this->name]['test_settings'] = $settings;
		return update_option('ee_active_messengers', $existing);
	}


	

} 
// end EE_messenger class