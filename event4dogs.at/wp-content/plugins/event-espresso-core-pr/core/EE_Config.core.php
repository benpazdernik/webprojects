<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package			Event Espresso
 * @ author			Seth Shoultes
 * @ copyright		(c) 2008-2011 Event Espresso  All Rights Reserved.
 * @ license			http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link					http://www.eventespresso.com
 * @ version		 	4.0
 *
 * ------------------------------------------------------------------------
 *
 * EE_Config
 *
 * @package			Event Espresso
 * @subpackage	core/
 * @author				Brent Christensen
 *
 * ------------------------------------------------------------------------
 */
final class EE_Config {


	/**
	 * 	instance of the EE_Config object
	 *	@var 	$_instance
	 * 	@access 	private
	 */
	private static $_instance = NULL;

	/**
	 *
	 * @var EE_Config_Base[]
	 */
	public $addons;

	/**
	 *
	 * @var EE_Admin_Config
	 */
	public $admin;

	/**
	 *
	 * @var EE_Core_Config
	 */
	public $core;

	/**
	 *
	 * @var EE_Currency_Config
	 */
	public $currency;

	/**
	 *
	 * @var EE_Gateway_Config
	 */
	public $gateway;

	/**
	 *
	 * @var EE_Organization_Config
	 */
	public $organization;

	/**
	 *
	 * @var EE_Registration_Config
	 */
	public $registration;

	/**
	 *
	 * @var EE_Template_Config
	 */
	public $template_settings;

	/**
	 * 	_module_route_map
	 *	@var 	array	$_module_route_map
	 * 	@access 	private
	 */
	private static $_module_route_map = array();

	/**
	 * 	_module_forward_map
	 *	@var 	array	$_module_forward_map
	 * 	@access 	private
	 */
	private static $_module_forward_map = array();

	/**
	 * 	_module_view_map
	 *	@var 	array	$_module_view_map
	 * 	@access 	private
	 */
	private static $_module_view_map = array();



	/**
	 *		@singleton method used to instantiate class object
	 *		@access public
	 *		@return EE_Config instance
	 */
	public static function instance() {
		// check if class object is instantiated, and instantiated properly
		if ( self::$_instance === NULL  or ! is_object( self::$_instance ) or ! ( self::$_instance instanceof EE_Config )) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}




	/**
	 * 	class constructor
	 *
	 *  @access 	private
	 *  @return 	void
	 */
	private function __construct() {
		do_action( 'AHEE__EE_Config__construct__begin',$this );
		//set defaults
		$this->core = new EE_Core_Config();
		$this->organization = new EE_Organization_Config();
		$this->currency = new EE_Currency_Config();
		$this->registration = new EE_Registration_Config();
		$this->admin = new EE_Admin_Config();
		$this->template_settings = new EE_Template_Config();
		$this->map_settings = new EE_Map_Config();
		$this->gateway = new EE_Gateway_Config();
		$this->addons = array();
		// set _module_route_map
		EE_Config::$_module_route_map = array();
		// set _module_forward_map
		EE_Config::$_module_forward_map = array();
		// set _module_view_map
		EE_Config::$_module_view_map = array();
		// load existing EE site settings
		$this->_load_config();
		//  register shortcodes and modules
		add_action( 'AHEE__EE_System__register_shortcodes_modules_and_addons', array( $this, 'register_shortcodes_and_modules' ), 999 );
		//  initialize shortcodes and modules
		add_action( 'AHEE__EE_System__core_loaded_and_ready', array( $this, 'initialize_shortcodes_and_modules' ));
		// register widgets
		add_action( 'widgets_init', array( $this, 'widgets_init' ), 10 );
		// construct__end hook
		do_action( 'AHEE__EE_Config__construct__end',$this );
		$this->template_settings->current_espresso_theme = 'Espresso_Arabica_2014';
	}




	/**
	 * use to get the current theme if needed from static context
	 * @return string current theme set.
	 */
	public static function get_current_theme() {
		return isset( self::$_instance->template_settings->current_espresso_theme ) ? self::$_instance->template_settings->current_espresso_theme : 'Espresso_Arabica_2014';
	}




	/**
	 * 		load EE organization options
	 *
	 * 		@access private
	 * 		@return void
	 */
	private function _load_config() {
		$espresso_config = $this->get_espresso_config();
		foreach ( $espresso_config as $config => $settings ) {
			$config_class = is_object( $settings ) && is_object( $this->$config ) ? get_class( $this->$config ) : FALSE;
			if ( ! empty( $settings ) && ( ! $config_class || ( $settings instanceof $config_class ))) {
				$this->$config = $settings;
			}
		}
	}




	/**
	 * 	get_espresso_config
	 *
	 *  @access 	public
	 *  @return 	array of espresso config stuff
	 */
	public function get_espresso_config() {
		// grab espresso configuration
		$CFG = get_option( 'ee_config', array() );
		$CFG = apply_filters( 'FHEE__Config__get_espresso_config__CFG', $CFG );
		return $CFG;
	}



	/**
	 * 	update_espresso_config'
	 *
	 *  @access 	public
	 *  @return 	boolean success
	 */
	public function update_espresso_config( $add_success = FALSE, $add_error = TRUE ) {
		do_action( 'AHEE__EE_Config__update_espresso_config__begin',$this );
		// compare existing settings with what's already saved'
		$saved_config = $this->get_espresso_config();
		// update
		$saved = $saved_config == $this ? TRUE : update_option( 'ee_config', $this );
		do_action( 'AHEE__EE_Config__update_espresso_config__end', $this, $saved );
		// if config remains the same or was updated successfully
		if ( $saved ) {
			if ( $add_success ) {
				EE_Error::add_success( __( 'The Event Espresso Configuration Settings have been successfully updated.', 'event_espresso' ), __FILE__, __FUNCTION__, __LINE__ );
			}
			return TRUE;
		} else {
			if ( $add_error ) {
				EE_Error::add_error( __( 'The Event Espresso Configuration Settings were not updated.', 'event_espresso' ), __FILE__, __FUNCTION__, __LINE__ );
			}
			return FALSE;
		}
	}



	/**
	 * 	update_post_shortcodes
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function update_post_shortcodes() {
		do_action( 'AHEE__EE_Config__update_post_shortcodes',$this->core->post_shortcodes );
		$this->core->post_shortcodes = isset( $this->core->post_shortcodes ) && is_array( $this->core->post_shortcodes ) ? $this->core->post_shortcodes : array();
		// cycle thru post_shortcodes
		foreach( $this->core->post_shortcodes as $post_name => $shortcodes ){
			// skip the posts page, because we want all shortcodes registered for it
			if ( $post_name != 'posts' ) {
				foreach( $shortcodes as $shortcode => $post_id ){
					// make sure post still exists
					if ( $post = get_post( $post_id )) {
						// check that the post name matches what we have saved
						if ( $post->post_name == $post_name ) {
							// if so, then break before hitting the unset below
							break;
						}
					}
					// we don't like missing posts around here >:(
					unset( $this->core->post_shortcodes[ $post_name ] );
				}
			}
		}
		//only show errors
		$this->update_espresso_config();
	}



	/**
	 * 	register_shortcodes_and_modules.
	 *
	 * 	At this point, it's too early to tell if we're maintenance mode or not.
	 * 	In fact, this is where we give modules a chance to let core know they exist
	 * 	so they can help trigger maintenance mode if it's needed
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function register_shortcodes_and_modules() {
		// allow shortcodes to register with WP and to set hooks for the rest of the system
		EE_Registry::instance()->shortcodes =$this->_register_shortcodes();
		// allow modules to set hooks for the rest of the system
		EE_Registry::instance()->modules = $this->_register_modules();
	}


	/**
	 * 	initialize_shortcodes_and_modules
	 * 	meaning they can start adding their hooks to get stuff done
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function initialize_shortcodes_and_modules() {
		// allow shortcodes to set hooks for the rest of the system
		$this->_initialize_shortcodes();
		// allow modules to set hooks for the rest of the system
		$this->_initialize_modules();
	}




	/**
	 * 	widgets_init
	 *
	 * 	@access private
	 * 	@return void
	 */
	public function widgets_init() {
		//only init widgets on admin pages when not in complete maintenance, and
		//on frontend when not in any maintenance mode
		if( (is_admin() && EE_Maintenance_Mode::instance()->level() != EE_Maintenance_Mode::level_2_complete_maintenance)  ||
				! EE_Maintenance_Mode::instance()->level()){
			// grab list of installed widgets
			$widgets_to_register = glob( EE_WIDGETS . '*', GLOB_ONLYDIR );
			// filter list of modules to register
			$widgets_to_register = apply_filters( 'FHEE__EE_Config__register_widgets__widgets_to_register', $widgets_to_register );
			// cycle thru widget folders
			foreach ( $widgets_to_register as $widget_path ) {
				// add to list of installed widget modules
				EE_Config::register_ee_widget( $widget_path );
			}
			// filter list of installed modules
			EE_Registry::instance()->widgets = apply_filters( 'FHEE__EE_Config__register_widgets__installed_widgets', EE_Registry::instance()->widgets );
		}
	}



	/**
	 * 	register_ee_widget - makes core aware of this widget
	 *
	 *  @access 	public
	 *  @param 	string 	$widget_path - full path up to and including widget folder
	 *  @return 	void
	 */
	public static function register_ee_widget( $widget_path = NULL ) {
		do_action( 'AHEE__EE_Config__register_widget__begin', $widget_path );
		$widget_ext = '.widget.php';
		// make all separators match
		$widget_path = rtrim( str_replace( '/\\', DS, $widget_path ), DS );
		// grab and sanitize widget directory name
		$widget_dir = sanitize_key( basename( $widget_path ));
		// create classname from widget directory name
		$widget = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $widget_dir )));
		// add class prefix
		$widget_class = 'EEW_' . $widget;
		// does the widget exist ?
		if ( ! is_readable( $widget_path . DS . $widget_class . $widget_ext )) {
			$msg = sprintf( __( 'The requested %s widget file could not be found or is not readable due to file permissions.', 'event_espresso' ), $widget_class );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		// load the widget class file
		require_once( $widget_path . DS . $widget_class . $widget_ext );
		// verfiy that class exists
		if ( ! class_exists( $widget_class )) {
			$msg = sprintf( __( 'The requested %s widget class does not exist.', 'event_espresso' ), $widget_class );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		if ( register_widget( $widget_class )) {
			// add to array of registered widgets
			EE_Registry::instance()->widgets[ $widget_class ] = $widget_path . DS . $widget_class . $widget_ext;
			return TRUE;
		} else {
			return FALSE;
		}
	}



	/**
	 * 		_register_shortcodes
	 *
	 * 		@access private
	 * 		@return void
	 */
	private function _register_shortcodes() {
		// load base class
		require_once( EE_SHORTCODES . 'EES_Shortcode.shortcode.php' );
		// grab list of installed shortcodes
		$shortcodes_to_register = glob( EE_SHORTCODES . '*', GLOB_ONLYDIR );
		// filter list of modules to register
		$shortcodes_to_register = apply_filters( 'FHEE__EE_Config__register_shortcodes__shortcodes_to_register', $shortcodes_to_register );
		// cycle thru shortcode folders
		foreach ( $shortcodes_to_register as $shortcode_path ) {
			// add to list of installed shortcode modules
			EE_Config::register_shortcode( $shortcode_path );
		}
		// filter list of installed modules
		return apply_filters( 'FHEE__EE_Config___register_shortcodes__installed_shortcodes', EE_Registry::instance()->shortcodes );
	}



	/**
	 * 	register_shortcode - makes core aware of this shortcode
	 *
	 *  @access 	public
	 *  @param 	string 		$shortcode_path - full path up to and including shortcode folder
	 *  @return 	void
	 */
	public static function register_shortcode( $shortcode_path = NULL ) {
		do_action( 'AHEE__EE_Config__register_shortcode__begin',$shortcode_path );
		$shortcode_ext = '.shortcode.php';
		// make all separators match
		$shortcode_path = rtrim( str_replace( '/\\', DS, $shortcode_path ), DS );
		// grab and sanitize shortcode directory name
		$shortcode_dir = sanitize_key( basename( $shortcode_path ));
		// create classname from shortcode directory name
		$shortcode = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $shortcode_dir )));
		// add class prefix
		$shortcode_class = 'EES_' . $shortcode;
		// does the shortcode exist ?
		if ( ! is_readable( $shortcode_path . DS . $shortcode_class . $shortcode_ext )) {
			$msg = sprintf( __( 'The requested %s shortcode file could not be found or is not readable due to file permissions. It should be in %s', 'event_espresso' ), $shortcode_class,$shortcode_path . DS . $shortcode_class . $shortcode_ext );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		// load the shortcode class file
		require_once( $shortcode_path . DS . $shortcode_class . $shortcode_ext );
		// verfiy that class exists
		if ( ! class_exists( $shortcode_class )) {
			$msg = sprintf( __( 'The requested %s shortcode class does not exist.', 'event_espresso' ), $shortcode_class );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		// add to array of registered shortcodes
		EE_Registry::instance()->shortcodes[ strtoupper( $shortcode ) ] = $shortcode_path . DS . $shortcode_class . $shortcode_ext;
		return TRUE;
	}




	/**
	 * 		_register_modules
	 *
	 * 		@access private
	 * 		@return void
	 */
	private function _register_modules() {
		// load base class
		require_once( EE_MODULES . 'EED_Module.module.php' );
		// grab list of installed modules
		$modules_to_register = glob( EE_MODULES . '*', GLOB_ONLYDIR );
		// filter list of modules to register
		$modules_to_register = apply_filters( 'FHEE__EE_Config__register_modules__modules_to_register', $modules_to_register );
		// loop through folders
		foreach ( $modules_to_register as $module_path ) {
			/**TEMPORARILY EXCLUDE gateways from modules for time being**/
			if ( $module_path != EE_MODULES . 'zzz-copy-this-module-template' && $module_path != EE_MODULES . 'gateways' ) {
				// add to list of installed modules
				EE_Config::register_module( $module_path );
			}
		}
		// filter list of installed modules
		return apply_filters( 'FHEE__EE_Config___register_modules__installed_modules', EE_Registry::instance()->modules );
	}



	/**
	 * 	register_module - makes core aware of this module
	 *
	 *  @access 	public
	 *  @param 	string 		$module_path - full path up to and including module folder
	 *  @return 	void
	 */
	public static function register_module( $module_path = NULL ) {
		do_action( 'AHEE__EE_Config__register_module__begin', $module_path );
		$module_ext = '.module.php';
		// make all separators match
		$module_path = rtrim( str_replace( '/\\', DS, $module_path ), DS );
		// grab and sanitize module name
		$module_dir = basename( $module_path );
		// create classname from module directory name
		$module = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $module_dir )));
		// add class prefix
		$module_class = 'EED_' . $module;
		// does the module exist ?
		if ( ! is_readable( $module_path . DS . $module_class . $module_ext )) {
			$msg = sprintf( __( 'The requested %s module file could not be found or is not readable due to file permissions.', 'event_espresso' ), $module );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		if ( WP_DEBUG === TRUE ) { EEH_Debug_Tools::instance()->start_timer(); }
		// load the module class file
		require_once( $module_path . DS . $module_class . $module_ext );
		if ( WP_DEBUG === TRUE ) { EEH_Debug_Tools::instance()->stop_timer("Requiring module $module_class"); }
		// verfiy that class exists
		if ( ! class_exists( $module_class )) {
			$msg = sprintf( __( 'The requested %s module class does not exist.', 'event_espresso' ), $module_class );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		// add to array of registered modules
		EE_Registry::instance()->modules[ $module ] = $module_path . DS . $module_class . $module_ext;
		do_action( 'AHEE__EE_Config__register_module__complete', $module, EE_Registry::instance()->modules[ $module ] );
		return TRUE;
	}



	/**
	 * 	_initialize_shortcodes
	 * 	allow shortcodes to set hooks for the rest of the system
	 *
	 * 	@access private
	 * 	@return void
	 */
	private function _initialize_shortcodes() {
		// cycle thru shortcode folders
		foreach ( EE_Registry::instance()->shortcodes as $shortcode => $shortcode_path ) {
			//echo '<h4>' . $shortcode . ' : ' . $shortcode_path . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
			// add class prefix
			$shortcode_class = 'EES_' . $shortcode;
			// fire the shortcode class's set_hooks methods in case it needs to hook into other parts of the system
			// which set hooks ?
			if ( is_admin() ) {
				// fire immediately
				call_user_func( array( $shortcode_class, 'set_hooks_admin' ));
			} else {
				// delay until other systems are online
				add_action( 'AHEE__EE_System__set_hooks_for_shortcodes_modules_and_addons', array( $shortcode_class,'set_hooks' ));
				// convert classname to UPPERCASE and create WP shortcode.
				// NOTE: this shortcode declaration will get overridden if the shortcode is successfully detected in the post content in EE_Front_Controller->_initialize_shortcodes()
				add_shortcode( strtoupper( $shortcode ), array( $shortcode_class, 'fallback_shortcode_processor' ));
			}
		}
	}



	/**
	 * 	_initialize_modules
	 * 	allow modules to set hooks for the rest of the system
	 *
	 * 	@access private
	 * 	@return void
	 */
	private function _initialize_modules() {
		//printr( EE_Registry::instance()->modules, 'EE_Registry::instance()->modules  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
		// cycle thru shortcode folders
		foreach ( EE_Registry::instance()->modules as $module => $module_path ) {
			// add class prefix
			$module_class = 'EED_' . $module;
			// fire the shortcode class's set_hooks methods in case it needs to hook into other parts of the system
			// which set hooks ?
			if ( is_admin() ) {
				// fire immediately
				call_user_func( array( $module_class, 'set_hooks_admin' ));
			} else {
				// delay until other systems are online
				add_action( 'AHEE__EE_System__set_hooks_for_shortcodes_modules_and_addons', array( $module_class,'set_hooks' ));
			}
		}
	}




	/**
	 * 	register_route - adds module method routes to route_map
	 *
	 *  @access 	public
	 *  @param 	string 		$route - "pretty" public alias for module method
	 *  @param 	string 		$module - module name (classname without EED_ prefix)
	 *  @param 	string 		$method_name - the actual module method to be routed to
	 *  @return 	void
	 */
	public static function register_route( $route = NULL, $module = NULL, $method_name = NULL ) {
		do_action( 'AHEE__EE_Config__register_route__begin',$route,$module,$method_name );
		$module = str_replace( 'EED_', '', $module );
		if ( ! isset( EE_Registry::instance()->modules[ $module ] )) {
			$msg = sprintf( __( 'The module %s has not been registered.', 'event_espresso' ), $module );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		if ( empty( $route )) {
			$msg = sprintf( __( 'No route has been supplied.', 'event_espresso' ), $route );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		if ( ! method_exists ( 'EED_' . $module, $method_name )) {
			$msg = sprintf( __( 'A valid class method for the %s route has not been supplied.', 'event_espresso' ), $route );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		EE_Config::$_module_route_map[ $route ] = array( 'EED_' . $module, $method_name );
	}



	/**
	 *    get_route - get module method route
	 *
	 * @access 	public
	 * @param 	string $route - "pretty" public alias for module method
	 * @return  mixed string | NULL
	 */
	public static function get_route( $route = NULL ) {
		do_action( 'AHEE__EE_Config__get_route__begin',$route );
		$route = apply_filters( 'FHEE__EE_Config__get_route',$route );
		if ( isset( EE_Config::$_module_route_map[ $route ] )) {
			return EE_Config::$_module_route_map[ $route ];
		}
		return NULL;
	}



	/**
	 *    register_forward - allows modules to forward request to another module for further processing
	 *
	 * @access    public
	 * @param    string  $route  - "pretty" public alias for module method
	 * @param    integer $status - integer value corresponding  to status constant strings set in module parent class, allows different forwards to be served based on status
	 * @param null       $forward
	 * @internal  param array|string $mixed $forward - function name or array( class, method )
	 * @return    mixed string | bool
	 */
	public static function register_forward( $route = NULL, $status = 0, $forward = NULL ) {
		do_action( 'AHEE__EE_Config__register_forward',$route,$status,$forward );
		if ( ! isset( EE_Config::$_module_route_map[ $route ] ) ||  empty( $route )) {
			$msg = sprintf( __( 'The module route %s for this forward has not been registered.', 'event_espresso' ), $route );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		if ( empty( $forward )) {
			$msg = sprintf( __( 'No forwarding route has been supplied.', 'event_espresso' ), $route );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		if ( is_array( $forward )) {
			if ( ! isset( $forward[1] )) {
				$msg = sprintf( __( 'A class method for the %s forwarding route has not been supplied.', 'event_espresso' ), $route );
				EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
				return FALSE;
			}
			if ( ! method_exists( $forward[0], $forward[1] )) {
				$msg = sprintf( __( 'The class method %s for the %s forwarding route is in invalid.', 'event_espresso' ), $forward[1], $route );
				EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
				return FALSE;
			}
		} else if ( ! function_exists( $forward )) {
			$msg = sprintf( __( 'The function %s for the %s forwarding route is in invalid.', 'event_espresso' ), $forward, $route );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		EE_Config::$_module_forward_map[ $route ][ absint( $status ) ] = $forward;
//		printr( EE_Config::$_module_forward_map, 'EE_Config::$_module_forward_map  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
	}



	/**
	 * 	get_forward - get forwarding route
	 *
	 *  @access 	public
	 *  @param 	string 		$route - "pretty" public alias for module method
	 *  @param 	integer	$status - integer value corresponding  to status constant strings set in module parent class, allows different forwards to be served based on status
	 *  @return 	mixed string | NULL
	 */
	public static function get_forward( $route = NULL, $status = 0 ) {
		do_action( 'AHEE__EE_Config__get_forward__begin',$route,$status );
		if ( isset( EE_Config::$_module_forward_map[ $route ][ $status ] )) {
			return apply_filters( 'FHEE__EE_Config__get_forward',EE_Config::$_module_forward_map[ $route ][ $status ],$route,$status );
		}
		return NULL;
	}



	/**
	 *    register_forward - allows modules to specify different view templates for different method routes and status results
	 *
	 * @access    	public
	 * @param    	string  $route  - "pretty" public alias for module method
	 * @param    	integer $status - integer value corresponding  to status constant strings set in module parent class, allows different views to be served based on status
	 * @param 		string       $view
	 * @internal  	param array|string $mixed $forward - function name or array( class, method )
	 * @return    	mixed string | bool
	 */
	public static function register_view( $route = NULL, $status = 0, $view = NULL ) {
		do_action( 'AHEE__EE_Config__register_view__begin',$route,$status,$view );
		if ( ! isset( EE_Config::$_module_route_map[ $route ] ) ||  empty( $route )) {
			$msg = sprintf( __( 'The module route %s for this view has not been registered.', 'event_espresso' ), $route );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		if ( ! is_readable( $view )) {
			$msg = sprintf( __( 'The %s view file could not be found or is not readable due to file permissions.', 'event_espresso' ), $view );
			EE_Error::add_error( $msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__ );
			return FALSE;
		}
		EE_Config::$_module_view_map[ $route ][ absint( $status ) ] = $view;
	}





	/**
	 * 	get_view - get view for route and status
	 *
	 *  @access 	public
	 *  @param 	string 		$route - "pretty" public alias for module method
	 *  @param 	integer	$status - integer value corresponding  to status constant strings set in module parent class, allows different views to be served based on status
	 *  @return 	mixed string | NULL
	 */
	public static function get_view( $route = NULL, $status = 0 ) {
		do_action( 'AHEE__EE_Config__get_view__begin',$route,$status );
		if ( isset( EE_Config::$_module_view_map[ $route ][ $status ] )) {
			return apply_filters( 'FHEE__EE_Config__get_view',EE_Config::$_module_view_map[ $route ][ $status ],$route,$status );
		}
		return NULL;
	}




	/**
	 * 	__sleep
	 *
	 *  @access 	public
	 *  @return 	array
	 */
	public function __sleep() {
		return apply_filters( 'FHEE__EE_Config__sleep',array(
			'core',
			'organization',
			'currency',
			'registration',
			'admin',
			'template_settings',
			'map_settings',
			'gateway',
			'addons'
		) );
	}



}





/**
 * Base class used for config classes. These classes should generally not have
 * magic functions in use, except we'll allow them to magically set and get stuff...
 * basically, they should just be well-defined stdClasses
 */
class EE_Config_Base{
	/**
	 *		@ override magic methods
	 *		@ return void
	 */
//	public function __get($a) { return apply_filters('FHEE__'.get_class($this).'__get__'.$a,$this->$a); }
//	public function __set($a,$b) { return apply_filters('FHEE__'.get_class($this).'__set__'.$a, $this->$a = $b ); }
	public function __isset($a) { return FALSE; }
	public function __unset($a) { return FALSE; }
	public function __clone() { return FALSE; }
	public function __wakeup() { return FALSE; }
	public function __destruct() { return FALSE; }
}




/**
 * Class for defining what's in the EE_Config relating to registration settings
 */
class EE_Core_Config extends EE_Config_Base {

	public $current_blog_id;
	public $ee_ueip_optin;
	public $ee_ueip_has_notified;
	/**
	 * Not to be confused with the 4 critical page variables (See
	 * get_critical_pages_array()), this is just an array of wp posts that have EE
	 * shortcodes in them. Keys are slugs, values are arrays with only 1 element: where the key is the shortcode
	 * in the page, and the value is the page's ID. The key 'posts' is basially a duplicate of this same array.
	 * @var array
	 */
	public $post_shortcodes;
	public $module_route_map;
	public $module_forward_map;
	public $module_view_map;
	/**
	 * The next 4 vars are the IDs of critical EE pages.
	 * @var int
	 */
	public $reg_page_id;
	public $txn_page_id;
	public $thank_you_page_id;
	public $cancel_page_id;

	/**
	 * 	class constructor
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function __construct() {
		$current_network_main_site = is_multisite() ? get_current_site() : NULL;
		$current_main_site_id = !empty( $current_network_main_site ) ? $current_network_main_site->blog_id : 1;
		// set default organization settings
		$this->current_blog_id = get_current_blog_id();
		$this->current_blog_id = $this->current_blog_id === NULL ? 1 : $this->current_blog_id;
		$this->ee_ueip_optin = is_main_site() ? get_option( 'ee_ueip_optin', TRUE ) : get_blog_option( $current_main_site_id, 'ee_ueip_optin', TRUE );
		$this->ee_ueip_has_notified = is_main_site() ? get_option( 'ee_ueip_has_notified', FALSE ) : TRUE;
		$this->post_shortcodes = array();
		$this->module_route_map = array();
		$this->module_forward_map = array();
		$this->module_view_map = array();
		$this->reg_page_id = FALSE;
		$this->txn_page_id = FALSE;
		$this->thank_you_page_id = FALSE;
		$this->cancel_page_id = FALSE;
	}

	public function get_critical_pages_array() {
		return array(
			$this->reg_page_id,
			$this->txn_page_id,
			$this->thank_you_page_id,
			$this->cancel_page_id
		);
	}

}



/**
 * Config class for storing info on the Organization
 */
class EE_Organization_Config extends EE_Config_Base {

	/**
	* @var string  $name
	* eg EE4.1
	*/
	public $name;

	/**
	* @var string $address_1
	* eg 123 Onna Road
	*/
	public $address_1;

	/**
	* @var string $address_2
	* eg PO Box 123
	*/
	public $address_2;

	/**
	* @var string $city
	* eg Inna City
	*/
	public $city;

	/**
	* @var int $STA_ID
	* eg 4
	*/
	public $STA_ID;

	/**
	* @var string  $CNT_ISO
	* eg US
	*/
	public $CNT_ISO;

	/**
	* @var string $zip
	* eg 12345  or V1A 2B3
	*/
	public $zip;

	/**
	* @var string  $email
	* eg support@eventespresso.com
	*/
	public $email;



	/**
	 * @var string $phone
	 * eg. 111-111-1111
	 */
	public $phone;


	/**
	 * @var string $vat
	 * VAT/Tax Number
	 */
	public $vat;

	/**
	* @var string  $logo_url
	* eg http://www.somedomain.com/wp-content/uploads/kittehs.jpg
	*/
	public $logo_url;


	/**
	 * The below are all various properties for holding links to organization social network profiles
	 * @var string
	 */

	/**
	 * facebook (facebook.com/profile.name)
	 * @var string
	 */
	public $facebook;


	/**
	 * twitter (twitter.com/twitterhandle)
	 * @var string
	 */
	public $twitter;



	/**
	 * linkedin (linkedin.com/in/profilename)
	 * @var string
	 */
	public $linkedin;



	/**
	 * pinterest (www.pinterest.com/profilename)
	 * @var string
	 */
	public $pinterest;



	/**
	 * google+ (google.com/+profileName)
	 * @var string
	 */
	public $google;



	/**
	 * instragram (instagram.com/handle)
	 * @var string
	 */
	public $instagram;

	/**
	 * 	class constructor
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function __construct() {
		// set default organization settings
		$this->name = get_bloginfo('name');
		$this->address_1 = '123 Onna Road';
		$this->address_2 = 'PO Box 123';
		$this->city = 'Inna City';
		$this->STA_ID = 4;
		$this->CNT_ISO = 'US';
		$this->zip = '12345';
		$this->email = get_bloginfo('admin_email');
		$this->phone = '';
		$this->vat = '123456789';
		$this->logo_url = '';
		$this->facebook = '';
		$this->twitter = '';
		$this->linkedin = '';
		$this->pinterest = '';
		$this->google = '';
		$this->instagram = '';
	}

}




/**
 * Class for defining what's in the EE_Config relating to currency
 */
class EE_Currency_Config extends EE_Config_Base {

	/**
	* @var string  $code
	* eg 'US'
	*/
	public $code;

	/**
	* @var string $name
	* eg 'Dollar'
	*/
	public $name;

	/**
	* plural name
	* @var string $plural
	* eg 'Dollars'
	*/
	public $plural;

	/**
	* currency sign
	* @var string  $sign
	* eg '$'
	*/
	public $sign;

	/**
	* Whether the currency sign shoudl come before the number or not
	* @var boolean $sign_b4
	*/
	public $sign_b4;

	/**
	* How many digits should come after the decimal place
	* @var int $dec_plc
	*/
	public $dec_plc;

	/**
	* Symbol to use for decimal mark
	* @var string $dec_mrk
	* eg '.'
	*/
	public $dec_mrk;

	/**
	* Symbol to use for thousands
	* @var string $thsnds
	* eg ','
	*/
	public $thsnds;


	/**
	 * 	class constructor
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function __construct( $CNT_ISO = NULL ) {
		// get country code from organization settings or use default
		$ORG_CNT = isset( EE_Registry::instance()->CFG->organization ) && EE_Registry::instance()->CFG->organization instanceof EE_Organization_Config ? EE_Registry::instance()->CFG->organization->CNT_ISO : 'US';
		// but override if requested
		$CNT_ISO = ! empty( $CNT_ISO ) ? $CNT_ISO : $ORG_CNT;
		// so if we have a country code and EE core is ready to go...
		if ( ! empty( $CNT_ISO ) && EE_Maintenance_Mode::instance()->models_can_query() && ! get_option( 'ee_espresso_activation' )) {
			// retreive the country settings from the db, just in case they have been customized
			if ( $country = EE_Registry::instance()->load_model( 'Country' )->get_one_by_ID( $CNT_ISO )) {
				if ( $country instanceof EE_Country ) {
					$this->code = $country->currency_code(); 	// currency code: USD, CAD, EUR
					$this->name = $country->currency_name_single();	// Dollar
					$this->plural = $country->currency_name_plural(); 	// Dollars
					$this->sign =  $country->currency_sign(); 			// currency sign: $
					$this->sign_b4 = $country->currency_sign_before(); 		// currency sign before or after: $TRUE  or  FALSE$
					$this->dec_plc = $country->currency_decimal_places();	// decimal places: 2 = 0.00  3 = 0.000
					$this->dec_mrk = $country->currency_decimal_mark();	// decimal mark: (comma) ',' = 0,01   or (decimal) '.' = 0.01
					$this->thsnds = $country->currency_thousands_separator();	// thousands separator: (comma) ',' = 1,000   or (decimal) '.' = 1.000
				}
			}
		}
		// fallback to hardcoded defaults, in case the above failed
		if ( empty( $this->code )) {
			// set default currency settings
			$this->code = 'USD'; 	// currency code: USD, CAD, EUR
			$this->name = __( 'Dollar', 'event_espresso' ); 	// Dollar
			$this->plural = __( 'Dollars', 'event_espresso' ); 	// Dollars
			$this->sign =  '$'; 	// currency sign: $
			$this->sign_b4 = TRUE; 	// currency sign before or after: $TRUE  or  FALSE$
			$this->dec_plc = 2; 	// decimal places: 2 = 0.00  3 = 0.000
			$this->dec_mrk = '.'; 	// decimal mark: (comma) ',' = 0,01   or (decimal) '.' = 0.01
			$this->thsnds = ','; 	// thousands separator: (comma) ',' = 1,000   or (decimal) '.' = 1.000
		}
	}
}




/**
 * Class for defining what's in the EE_Config relating to registration settings
 */
class EE_Registration_Config extends EE_Config_Base {

	/**
	 * Default registration status
	 * @var string $default_STS_ID
	 * eg 'RPP'
	 */
	public $default_STS_ID;

	/**
	 * 	whether or not to show alternate payment options during the reg process if payment status is pending
	 * @var boolean $show_pending_payment_options
	 */
      public $show_pending_payment_options;

	/**
	 * Whether to skip the registration confirmation page
	 * @var boolean $skip_reg_confirmation
	 */
      public $skip_reg_confirmation;

	/**
	 * Whether registration confirmation should be the last page of SPCO
	 * @var boolean $reg_confirmation_last
	 */
      public $reg_confirmation_last;

	  /**
	   * Whether or not to use ReCaptcha
	   * @var boolean $use_captcha
	   */
      public $use_captcha;

	  /**
	   * ReCaptcha Theme
	   * @var string $recaptcha_theme
	   * eg 'clean', 'red'
	   */
      public $recaptcha_theme;

	  /**
	   * ReCaptcha language
	   * @var string $recaptcha_language
	   * eg 'en'
	   */
      public $recaptcha_language;

	  /**
	   * ReCaptcha width
	   * @var int $recaptcha_width
	   */
      public $recaptcha_width;

	  /**
	   * ReCaptcha public key
	   * @var string $recaptcha_publickey
	   */
      public $recaptcha_publickey;

	  /**
	   * ReCaptcha private key
	   * @var string $recaptcha_privatekey
	   */
      public $recaptcha_privatekey;


	/**
	 * 	class constructor
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function __construct() {
		// set default registration settings
		$this->default_STS_ID = EEM_Registration::status_id_pending_payment;
		$this->show_pending_payment_options = FALSE;
		$this->skip_reg_confirmation = FALSE;
		$this->reg_confirmation_last = FALSE;
		$this->use_captcha = FALSE;
		$this->recaptcha_theme = 'clean';
		$this->recaptcha_language = 'en';
		$this->recaptcha_width = 500;
		$this->recaptcha_publickey = NULL;
		$this->recaptcha_privatekey = NULL;
	}

}



/**
 * Class for defining what's in the EE_Config relating to admin settings
 */
class EE_Admin_Config extends EE_Config_Base {

	/**
	* @var boolean $use_personnel_manager
	*/
	public $use_personnel_manager;

	/**
	* @var boolean $use_dashboard_widget
	*/
	public $use_dashboard_widget;

	/**
	* @var int $events_in_dasboard
	*/
	public $events_in_dasboard;

	/**
	* @var boolean $use_event_timezones
	*/
	public $use_event_timezones;

	/**
	* @var boolean $use_full_logging
	*/
	public $use_full_logging;

	/**
	* @var boolean $use_remote_logging
	*/
	public $use_remote_logging;

	/**
	* @var string $remote_logging_url
	*/
	public $remote_logging_url;

	/**
	* @var boolean $show_reg_footer
	*/
	public $show_reg_footer;

	/**
	* @var string $affiliate_id
	*/
	public $affiliate_id;


	/**
	 * help tours on or off (global setting)
	 * @var boolean
	 */
	public $help_tour_activation;


	/**
	 * 	class constructor
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function __construct() {
		// set default general admin settings
		$this->use_personnel_manager = TRUE;
		$this->use_dashboard_widget = TRUE;
		$this->events_in_dasboard = 30;
		$this->use_event_timezones = FALSE;
		$this->use_full_logging = FALSE;
		$this->use_remote_logging = FALSE;
		$this->remote_logging_url = NULL;
		$this->show_reg_footer = TRUE;
		$this->affiliate_id = NULL;
		$this->help_tour_activation = TRUE;
	}

}



/**
 * Class for defining what's in the EE_Config relating to template settings
 */
class EE_Template_Config extends EE_Config_Base {

	/**
	* @var boolean $enable_default_style
	*/
	public $enable_default_style;

	/**
	* @var boolean $display_address_in_regform
	*/
	public $display_address_in_regform;

	/**
	* @var int $display_description_on_multi_reg_page
	*/
	public $display_description_on_multi_reg_page;

	/**
	* @var boolean $use_custom_templates
	*/
	public $use_custom_templates;

	/**
	* @var string $current_espresso_theme
	*/
	public $current_espresso_theme;


	/**
	 * 	class constructor
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function __construct() {
		// set default template settings
		$this->enable_default_style = TRUE;
		$this->display_address_in_regform = TRUE;
		$this->display_description_on_multi_reg_page = FALSE;
		$this->use_custom_templates = FALSE;
		$this->current_espresso_theme = 'Espresso_Arabica_2014';
	}

}



/**
 * Class for defining what's in the EE_Config relating to map settings
 */
class EE_Map_Config extends EE_Config_Base {

	/**
	* @var boolean $use_google_maps
	*/
	public $use_google_maps;

	/**
	* @var int $event_details_map_width
	*/
	public $event_details_map_width;

	/**
	* @var int $event_details_map_height
	*/
	public $event_details_map_height;

	/**
	* @var int $event_details_map_zoom
	*/
	public $event_details_map_zoom;

	/**
	* @var boolean $event_details_display_nav
	*/
	public $event_details_display_nav;

	/**
	* @var boolean $event_details_nav_size
	*/
	public $event_details_nav_size;

	/**
	* @var string $event_details_control_type
	*/
	public $event_details_control_type;

	/**
	* @var string $event_details_map_align
	*/
	public $event_details_map_align;

	/**
	* @var int $event_list_map_width
	*/
	public $event_list_map_width;

	/**
	* @var int $event_list_map_height
	*/
	public $event_list_map_height;

	/**
	* @var int $event_list_map_zoom
	*/
	public $event_list_map_zoom;

	/**
	* @var boolean $event_list_display_nav
	*/
	public $event_list_display_nav;

	/**
	* @var boolean $event_list_nav_size
	*/
	public $event_list_nav_size;

	/**
	* @var string $event_list_control_type
	*/
	public $event_list_control_type;

	/**
	* @var string $event_list_map_align
	*/
	public $event_list_map_align;



	/**
	 * 	class constructor
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function __construct() {
		// set default map settings
		$this->use_google_maps = TRUE;
		// for event details pages (reg page)
		$this->event_details_map_width = 585; 			// ee_map_width_single
		$this->event_details_map_height = 362; 			// ee_map_height_single
		$this->event_details_map_zoom = 14; 			// ee_map_zoom_single
		$this->event_details_display_nav = TRUE; 			// ee_map_nav_display_single
		$this->event_details_nav_size = FALSE; 			// ee_map_nav_size_single
		$this->event_details_control_type = 'default'; 		// ee_map_type_control_single
		$this->event_details_map_align = 'center'; 			// ee_map_align_single
		// for event list pages
		$this->event_list_map_width = 300; 			// ee_map_width
		$this->event_list_map_height = 185; 		// ee_map_height
		$this->event_list_map_zoom = 12; 			// ee_map_zoom
		$this->event_list_display_nav = FALSE; 		// ee_map_nav_display
		$this->event_list_nav_size = TRUE; 			// ee_map_nav_size
		$this->event_list_control_type = 'dropdown'; 		// ee_map_type_control
		$this->event_list_map_align = 'center'; 			// ee_map_align
	}

}

/**
 * stores payment gateway info
 */
class EE_Gateway_Config extends EE_Config_Base{
	/**
	 * Array with keys that are payment gateways slugs, and values are arrays
	 * with any config info the gateway wants to store
	 * @var array
	 */
	public $payment_settings;
	/**
	 * Where keys are gateway slugs, and values are booleans indicating whether or not
	 * the gateway is stored in the uploads directory
	 * @var array
	 */
	public $active_gateways;

	public function __construct(){
		$this->payment_settings = array();
		$this->active_gateways = array('Invoice'=>false);
	}
}




/**
 * stores Events_Archive settings
 */
class EE_Events_Archive_Config extends EE_Config_Base{

	public $display_status_banner;
	public $display_description;
	public $display_ticket_selector;
	public $display_datetimes;
	public $display_venue;
	public $display_expired_events;

	public function __construct(){
		$this->display_status_banner = 0;
		$this->display_description = 1;
		$this->display_ticket_selector = 0;
		$this->display_datetimes = 1;
		$this->display_venue = 0;
		$this->display_expired_events = 0;
	}
}



/**
 * Stores Event_Single_Config settings
 */
class EE_Event_Single_Config extends EE_Config_Base{
	public $display_status_banner_single;
	public $display_venue;

	public function __construct() {
		$this->display_status_banner_single = 0;
		$this->display_venue = 1;
	}
}


// End of file EE_Config.core.php
// Location: /core/EE_Config.core.php