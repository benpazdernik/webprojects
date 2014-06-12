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
 * EE_message_type class
 *
 * Abstract class for message types.
 * Different types can be setup by extending this class and adding them to the /includes/core/messages/types' directory. View examples there.
 *
 * @package			Event Espresso
 * @subpackage		includes/core/messages
 * @author			Darren Ethier, Brent Christensen
 *
 * ------------------------------------------------------------------------
 */
abstract class EE_message_type extends EE_Messages_Base {



	/**
	 * message type child classes will set what contexts are associated with the message type via this array.
	 * format:
	 * array(
	 * 'context' => array(
	 * 		'label' => __('Context Label', 'event_espresso'),
	 * 		'description' => __('Context description (for help popups)', 'event_espresso')
	 * 	));
	 * @var array
	 */
	protected $_contexts = array();



	/**
	 * This property is used to define what the display label will be for contexts (eg. "Recipients", "Themes" etc.)
	 * Format:
	 * array( 'label' => 'something', 'plural' => 'somethings', 'description' => 'something' );
	 * @var array
	 */
	protected $_context_label;


	/** MESSAGE ASSEMBLING PROPERTIES **/

	/**
	 * This parameter simply holds all the message objects for retrieval by the controller and sending to the messenger.
	 * @var array of message objects.
	 */
	public $messages = array();

	/**
	 * The following holds the templates that will be used to assemble the message object for the messenger.
	 * @var array
	 */
	protected $_templates;


	/** OTHER INFO PROPERTIES **/
	/**
	 * This will hold the count of the message objects in the messages array. This could be used for determining if batching/queueing is needed.
	 * @var int
	 */
	public $count = 0;

	/**
	 * This will hold the active messenger object that is passed to the type so the message_type knows what template files to process.  IT is possible that the active_messenger sent along actually doesn't HAVE a template (or maybe turned off) for the given message_type.
	 * @var object
	 */
	protected $_active_messenger;

	/**
	 * This will hold the shortcode_replace instance for handling replacement of shortcodes in the various templates
	 * @var object
	 */
	protected $_shortcode_replace;



	/**
	 * The purpose for this property is to simply allow message types to indicate if the message generated is intended for only single context.  Child message types should redefine this variable (if necessary) in the _set_data_Handler() method.
	 * @var boolean
	 */
	protected $_single_message = FALSE;



	/**
	 * This holds the data passed to this class from the controller
	 * @var object
	 */
	protected $_data;




	/**
	 * this is just a flag indicating whether we're in preview mode or not.
	 * @var bool
	 */
	protected $_preview = FALSE;




	/**
	 * This just holds defaults for addressee data that children merge with their data array setup
	 * @var array
	 */
	protected $_default_addressee_data;



	/**
	 * Child classes declare through this property what handler they want to use for the incoming data and this string is used to instantiate the EE_Messages_incoming_data child class for that handler.
	 * @var string
	 */
	protected $_data_handler;



	/**
	 * This holds any specific fields for holding any settings related to a message type (if any needed)
	 * @var array
	 */
	protected $_admin_settings_fields = array();

	/**
	 * this property will hold any existing setting that may have been set in the admin.
	 * @var array
	 */
	protected $_existing_admin_settings = array();




	/**
	 * This holds the addressees in an array indexed by context for later retrieval when assembling the message objects.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_addressees = array();




	public function __construct() {
		$this->_messages_item_type = 'message_type';
		$this->_set_contexts();
		parent::__construct();
	}

	/** METHODS **/

	/**
	 * This method simply takes care of setting up message objects and returning them in an array.
	 *
	 * @access public
	 * @param  array|object $data       Data to be parsed for messenger/message_type
	 * @param  string $active_messenger The active messenger being used
	 * @param  mixed (bool|string) $context if present then this is a preview being generated, so we'll make sure we ONLY do the preview for the given context and set up the message
	 * @return void
	 */
	public function set_messages($data, $active_messenger, $context = FALSE ) {

		$this->_active_messenger = $active_messenger;
		$this->_data = $data;

		//this is a special method that allows child message types to trigger an exit from generating messages early (in cases where there may be a delay on send).
		$exit = $this->_trigger_exit();
		if ( $exit && !$context ) return FALSE;

		//todo: need to move require into registration hook but for now we'll require here.
		EE_Registry::instance()->load_helper( 'Parse_Shortcodes' );
		//get shortcode_replace instance- set when _get_messages is called in child...
		$this->_shortcode_replace = new EEH_Parse_Shortcodes();


		//if there is a context available then we're going to reset the datahandler to the Preview_incoming_data handler
		$this->_set_data_handler();

		$this->_data_handler = !$context ? $this->_data_handler : 'Preview';
		$this->_set_contexts;

		//if there is an incoming context then this is a preview so let's ONLY show the given context!
		if ( $context ) {
			$this->_preview = TRUE;
			$cntxt = $this->_contexts[$context];
			$this->_contexts = array();
			$this->_contexts[$context] = $cntxt;
		}

		$exit = $this->_init_data();

		//final check for if we exit or not cause child objects may have run conditionals that cleared out data so no addresees generated.
		if ( $exit ) return FALSE;

		$this->_get_templates(); //get the templates that have been set with this type and for the given messenger that have been saved in the database.
		$this->_assemble_messages();
		$this->count = count($this->messages);
	}








	/**
	 * This sets the data handler for the message type.  It must be used to define the _data_handler property.  It is called when messages are setup.
	 *
	 * @abstract
	 * @access protected
	 * @return void
	 */
	abstract protected function _set_data_handler();





	/**
	 * _set_contexts
	 * This sets up the contexts associated with the message_type
	 *
	 * @abstract
	 * @access  protected
	 * @return  void
	 */
	abstract protected function _set_contexts();








	/**
	 * this public method accepts a page slug (for an EE_admin page) and will return the response from the child class callback function if that page is registered via the `_admin_registered_page` property set by the child class.
	 *
	 * *
	 * @param string $page the slug of the EE admin page
	 * @param array  $messengers an array of active messenger objects
	 * @param string $action the page action (to allow for more specific handling - i.e. edit vs. add pages)
	 * @param array $extra  This is just an extra argument that can be used to pass additional data for setting up page content.
	 * @access public
	 * @return void
	 */
	public function get_message_type_admin_page_content($page, $action = NULL, $extra = array(), $messengers = array() ) {
		//we can also further refine the context by action (if present).
		return $this->_get_admin_page_content( $page, $action, $extra, $messengers );
	}





	/**
	 * This method can be overridden by child classes to do any special conditionals that might triger an exit from generating messages (that might happen with delays etc).
	 * @return bool   TRUE will trigger an exit, FALSE will continue the code execution.
	 */
	protected function _trigger_exit() {
		return FALSE;
	}





	public function get_contexts() {
		return $this->_contexts;
	}


	/**
	 * This just returns the context label for a given context (as set in $_context_label property)
	 *
	 * @access public
	 * @return string
	 */
	public function get_context_label() {
		return $this->_context_label;
	}




	/**
	 * The main purpose of this function is to setup the various parameters within the message_type.  $this->addressees, $this->_templates, $this->count, and any extra stuff to the data object that can come from the message_type template options.
	 * Child classes might overwrite this if they aren't expecting EE_Session as the incoming data object.
	 *
	 * @return void
	 * @access protected
	 */
	protected function _init_data() {

		/**
		 * first let's make sure that incoming data isn't empty!
		 */
		if ( is_array($this->_data) && empty($this->_data) && !$this->_preview ) {
			$msg = sprintf( __( '"%s" message type incoming data is empty.  There is nothing to work with so why are you bugging me?', 'event_espresso'), $this->label['singular'] );
			throw new EE_Error( $msg );
		}

		if ( empty( $this->_data_handler) ) {
			$msg = sprintf( __('Hey %s hasn\'t declared a handler for the incoming data, so I\'m stuck', 'event_espresso'), __CLASS__ );
			throw new EE_Error( $msg );
		}


		//setup class name for the data handler
		$classname = 'EE_Messages_' . $this->_data_handler . '_incoming_data';

		//check that the class exists
		if ( !class_exists( $classname ) ) {

			$msg[] = __('uhoh, Something went wrong and no data handler is found', 'event_espresso');
			$msg[] = sprintf( __('The %s class has set the "$_data_handler" property but the string included (%s) does not match any existing "EE_Messages_incoming_data" classes (found in "/includes/core/messages/data_class"', 'event_espresso'), __CLASS__, $this->_data_handler );
			throw new EE_error( implode('||', $msg) );
		}

		//k lets get the prepared data object and replace existing data property with it.
		$a = new ReflectionClass( $classname );
		$this->_data = $a->newInstance( $this->_data );

		$this->_set_default_addressee_data();
		return $this->_process_data();
	}



	/**
	 * processes the data object so we get
	 * @return void
	 */
	protected function _process_data() {
		if ( empty( $this->_data->events ) )
			return TRUE;  //EXIT!

		//process addressees for each context.  Child classes will have to have methods for each context defined to handle the processing of the data object within them
		foreach ( $this->_contexts as $context => $details ) {
			$xpctd_method = '_' . $context . '_addressees';
			if ( !method_exists( $this, $xpctd_method ) )
				throw new EE_Error( sprintf( __('The data for %1$s message type cannot be prepared because there is no set method for doing so.  The expected method name is "%2$s" please doublecheck the %1$s message type class and make sure that method is present', 'event_espresso'), $this->label['singular'], $xpctd_method) );
			 $this->_addressees[$context] = call_user_func( array( $this, $xpctd_method ) );
		}
		return FALSE; //DON'T EXIT
	}




	/**
	 * sets the default_addressee_data property,
	 *
	 * @access private
	 * @return void
	 */
	private function _set_default_addressee_data() {
		$this->_default_addressee_data = array(
			'billing' => $this->_data->billing,
			'taxes' => $this->_data->taxes,
			'txn' => $this->_data->txn,
			'payment' => isset($this->_data->payment) ? $this->_data->payment : NULL,
			'reg_objs' => $this->_data->reg_objs,
			'registrations' => $this->_data->registrations,
			'datetimes' => $this->_data->datetimes,
			'tickets' => $this->_data->tickets,
			'questions' => $this->_data->questions,
			'answers' => $this->_data->answers,
			'txn_status' => $this->_data->txn_status,
			'total_ticket_count' => $this->_data->total_ticket_count
			);

		if ( is_array( $this->_data->primary_attendee_data ) ) {
			$this->_default_addressee_data = array_merge( $this->_default_addressee_data, $this->_data->primary_attendee_data );
			$this->_default_addressee_data['primary_att_obj'] = $this->_data->primary_attendee_data['att_obj'];
			$this->_default_addressee_data['primary_reg_obj'] = $this->_data->primary_attendee_data['reg_obj'];
		}
	}



	/********************
	 * setup default shared addressee object/contexts
	 * These can be utilized simply by defining the context in the child message type.  They can also be overridden if a specific message type needs to do something different for that context.
	 ****************/


	/**
	 * see abstract declaration in parent class for details, children message types can override these valid shortcodes if desired (we include all for all contexts by default).
	 */
	protected function _set_valid_shortcodes() {
		$all_shortcodes = array( 'attendee_list', 'attendee', 'datetime_list', 'datetime', 'event_list', 'event_meta', 'event', 'organization', 'recipient_details', 'recipient_list', 'ticket_list', 'ticket', 'transaction', 'venue', 'primary_registration_details', 'primary_registration_list', 'event_author', 'email' );
		$contexts = $this->get_contexts();
		foreach ( $contexts as $context => $details ) {
			$this->_valid_shortcodes[$context] = $all_shortcodes;

			//make sure non admin context does not include the event_author shortcodes
			if ( $context != 'admin' ) {
				if( ($key = array_search('event_author', $this->_valid_shortcodes[$context] ) ) !== false) {
				    unset($this->_valid_shortcodes[$context][$key]);
				}
			}
		}

		//make sure admin context does not include the recipient_details shortcodes
		if( ($key = array_search('recipient_details', $this->_valid_shortcodes['admin'] ) ) !== false) {
			    unset($this->_valid_shortcodes['admin'][$key]);
			}
		//make sure admin context does not include the recipient_details shortcodes
		if( ($key = array_search('recipient_list', $this->_valid_shortcodes['admin'] ) ) !== false) {
			    unset($this->_valid_shortcodes['admin'][$key]);
			}
	}



	/**
	 * Used by Validators to modify the valid shortcodes.
	 * @param  array  $new_config array of valid shortcodes (by context)
	 * @return void               sets valid_shortcodes property
	 */
	public function reset_valid_shortcodes_config( $new_config ) {
		foreach ( $new_config as $context => $shortcodes ) {
			$this->_valid_shortcodes[$context] = $shortcodes;
		}
	}



	/**
	 * returns an array of addressee objects for event_admins
	 *
	 * @access protected
	 * @return array array of EE_Messages_Addressee objects
	 */
	protected function _admin_addressees() {
		$admin_ids = array();
		$admin_events = array();
		$admin_attendees = array();
		$addressees = array();

		//first we need to get the event admin user id for all the events and setup an addressee object for each unique admin user.
		foreach ( $this->_data->events as $line_ref => $event ) {
			$admin_id = $this->_get_event_admin_id($event['ID']);
			//get the user_id for the event
			$admin_ids[] = $admin_id;
			//make sure we are just including the events that belong to this admin!
			$admin_events[$admin_id][$line_ref] = $event;
		}

		//make sure we've got unique event_admins!
		$admin_ids = array_unique($admin_ids);

		//k now we can loop through the event_admins and setup the addressee data.
		foreach ( $admin_ids as $event_admin ) {
			$aee = array(
				'user_id' => $event_admin,
				'events' => $admin_events[$event_admin],
				'attendees' => $this->_data->attendees
				);
			$aee = array_merge( $this->_default_addressee_data, $aee );
			$addressees[] = new EE_Messages_Addressee( $aee );
		}

		return $addressees;
	}



	/**
	 * Takes care of setting up the addressee object(s) for the primary attendee.
	 *
	 * @access protected
	 * @return array of EE_Addressee objects
	 */
	protected function _primary_attendee_addressees() {
		$aee = $this->_default_addressee_data;
		$aee['events'] = $this->_data->events;
		$aee['attendees'] = $this->_data->attendees;

		//great now we can instantiate the $addressee object and return (as an array);
		$add[] = new EE_Messages_Addressee( $aee );
		return $add;
	}




	/**
	 * Takes care of setting up the addresee object(s) for the registered attendees
	 *
	 * @access protected
	 * @return array of EE_Addressee objects
	 */
	protected function _attendee_addressees() {
		$add = array();
		//we just have to loop through the attendees.  We'll also set the attached events for each attendee.
		//use to verify unique attendee emails... we don't want to sent multiple copies to the same attendee do we?
		$already_processed = array();
		foreach ( $this->_data->attendees as $att_id => $details ) {
			//set the attendee array to blank on each loop;
			$aee = array();

			if ( isset( $this->_data->reg_obj ) && ( $this->_data->reg_obj->attendee_ID() != $att_id ) && $this->_single_message ) continue;

			if ( in_array( $details['attendee_email'], $already_processed ) )
				continue;

			$already_processed[] = $details['attendee_email'];

			foreach ( $details as $item => $value ) {
				$aee[$item] = $value;
				if ( $item == 'line_ref' ) {
					foreach ( $value as $event_id ) {
						$aee['events'][$event_id] = $this->_data->events[$event_id];
					}
				}

				if ( $item == 'attendee_email' ) {
					$aee['attendee_email'] = $value;
				}

				/*if ( $item == 'registration_id' ) {
					$aee['attendee_registration_id'] = $value;
				}/**/
			}

			//note the FIRST reg object in this array is the one we'll use for this attendee as the primary registration for this attendee.
			$aee['reg_obj'] = array_shift($this->_data->attendees[$att_id]['reg_obj']);

			$aee['attendees'] = $this->_data->attendees;

			//merge in the primary attendee data
			$aee = array_merge( $this->_default_addressee_data, $aee );
			$add[] = new EE_Messages_Addressee( $aee );
		}

		return $add;
	}




	/**
	 * get and set the templates for the type and messenger from the database
	 * @return void
	 * @access protected
	 */
	protected function _get_templates() {
		$current_templates = $this->_active_messenger->active_templates;
		$has_event_template = false;
		$event_id = null;
		$global_templates = $event_templates = $global_override = array();

		//in vanilla EE we're assuming there's only one event.  However, if there are multiple events then we'll just do global.
		if ( count($this->_data->events) === 1 ) {
			foreach ( $this->_data->events as $event ) {
				$event_id = $event['ID'];
			}
		}


		if ( isset($current_templates) ) {

			foreach ( $current_templates as $group_object ) {
				if ( $this->name == $group_object->message_type() ) {
					$templates = $group_object->context_templates();

					if ( $group_object->get('MTP_is_override') )
						$global_override[$context] = TRUE;

					foreach ( $templates as $context => $template_fields ) {
						foreach ( $template_fields as $template_field => $template_obj ) {
								if ( $group_object->is_global() ) {
									$global_templates[$template_field][$context] = $template_obj->get('MTP_content');
								}

								if ( $group_object->event() == $event_id && !empty( $event_id )) {
									$event_templates[$template_field][$context] = $template_obj->get('MTP_content');
								}
						}
					}
				}
			}

			//k we now have $global and (possibly) $event_templates.  So let's decide who makes it to the finals.
			//first if there are no event templates, global wins
			if ( empty( $event_templates) ) {
				$this->_templates = $global_templates;

			//next if there are event templates and no global overrides set, event wins.
			} elseif ( !empty( $event_templates) && empty( $global_override ) ) {
				$this->_templates = $event_templates;


			//hmph looks like we have event templates and global overrides present.  So its down to the wire, lets take a snapshot and see who wins.
			} else {
				foreach ( $event_templates as $field => $contexts ) {
					foreach ( $contexts as $context ) {
						$this->_templates[$field][$context] = isset( $global_override[$context] ) ? $global_templates[$field][$context] : $event_templates[$field][$context];
					}
				}
			}

			//hang on, not done yet.  If this is a PREVIEW being generated (and there is no evt_id in the request) then we want to make sure global always wins (even if the generated events happen to have a custom template) because peopel need to see what the global template looks like.
			if ( $this->_preview && empty($_REQUEST['evt_id'] ) ) {
				$this->_templates = $global_templates;
			}
		}
	}

	/**
	 * This function assembles the $messages array which will contain the message objects.
	 * @return void
	 * @access protected
	 */
	protected function _assemble_messages() {
		foreach ( $this->_addressees as $context => $addressees ) {
			foreach ( $addressees as $addressee ) {
				$message = $this->_setup_message_object($context, $addressee);
				//only assign message if everything went okay
				if ( $message )
					$this->messages[] = $message;
			}
		}
	}

	/**
	 * This function setups up and returns the message object
	 *
	 * @return void
	 * @access protected
	 *
	 */
	protected function _setup_message_object($context, $addressee) {
		$message = new stdClass();

		//get what shortcodes are supposed to be used
		$mt_shortcodes = $this->get_valid_shortcodes();
		$m_shortcodes = $this->_active_messenger->get_valid_shortcodes();

		//if the 'to' field is empty (messages will ALWAYS have a "to" field, then we get out because this context is turned off) EXCEPT if we're previewing
		if ( empty( $this->_templates['to'][$context] ) && !$this->_preview )
			return false;

		foreach ( $this->_templates as $field => $ctxt ) {
			//let's setup the valid shortcodes for the incoming context.
			$valid_shortcodes = $mt_shortcodes[$context];
			//merge in valid shortcodes for the field.
			$shortcodes = isset($m_shortcodes[$field]) ? $m_shortcodes[$field] : $valid_shortcodes;
			if ( isset( $this->_templates[$field][$context] ) ) {
				$message->$field = $this->_shortcode_replace->parse_message_template($this->_templates[$field][$context], $addressee, $shortcodes);
			}
		}
		return $message;
	}


	protected function _get_event_admin_id($event_id) {
		$event = EEM_Event::instance()->get_one_by_ID($event_id);
		return $event->wp_user();
	}


}
//end EE_message_type class
