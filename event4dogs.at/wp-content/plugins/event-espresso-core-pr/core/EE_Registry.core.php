<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * 
 * Event Espresso
 *
 * Event Registration and Ticketing Management Plugin for WordPress
 *
 * @ package			Event Espresso
 * @ author			Seth Shoultes
 * @ copyright		(c) 2008-2011 Event Espresso  All Rights Reserved.
 * @ license			http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link					http://www.eventespresso.com
 * @ version		 	$VID:$
 *
 * ------------------------------------------------------------------------
 *
 * EE_Registry Class
 *
 * Centralized Application Data Storage and Management
 *
 * @package				Event Espresso
 * @subpackage		core
 * @author					Brent Christensen
 *
 * ------------------------------------------------------------------------
 */
final class EE_Registry {

	/**
	* 	EE_Registry Object
	* 	@var EE_Registry $_instance
	* 	@access 	private 	
	*/
	private static $_instance = NULL;


	/**
	* 	EE_Cart Object
	* 	@access 	public
	*	@var 	EE_Cart $CART
	*/
	public $CART = NULL;

	/**
	* 	EE_Config Object
	* 	@access 	public
	*	@var 	EE_Config $CFG
	*/
	public $CFG = NULL;



	/**
	 * EE_Network_Config Object
	 * @access public
	 * @var EE_Network_Config $NET_CFG
	 */
	public $NET_CFG = NULL;



	/**
	* 	array for storing library classes in
	* 	@public LIB
	*/
	public $LIB = NULL;

	/**
	 * 	EE_Request_Handler Object
	 * 	@access 	public
	 *	@var 	EE_Request_Handler	$REQ
	 */
	public $REQ = NULL;

	/**
	* 	EE_Session Object
	* 	@access 	public
	* 	@var 	EE_Session	 $SSN
	*/
	public $SSN = NULL;

	/**
	 * 	$shortcodes
	 * 	@access 	public
	 *	@var 	array	$shortcodes
	 */
	public $shortcodes = array();

	/**
	 * 	$modules
	 * 	@access 	public
	 *	@var 	array	$modules
	 */
	public $modules = array();

	/**
	 * 	$widgets
	 * 	@access 	public
	 *	@var 	array	$widgets
	 */
	public $widgets = array();

	/**
	 * 	$models
	 * 	@access 	public
	 *	@var 	array	$models keys are 'short names' (eg Event), values ar eclassnames (eg 'EEM_Event')
	 */
	public $models = array();




	/**
	 * $non_abstract_db_models
	 * @access public
	 * @var array this is an array of all implemented model names (i.e. not the parent abstract models, or models
	 * which don't actually fetch items from the DB in the normal way (ie, aren't children of EEM_Base))
	 */
	public $non_abstract_db_models = array();




	/**
	* 	$i18n_js_strings - internationalization for JS strings
	*  	usage:   EE_Registry::i18n_js_strings['string_key'] = __( 'string to translate.', 'event_espresso' );
	*  	in js file:  var translatedString = eei18n.string_key;
	* 	
	* 	@access 	public
	*	@var 	array	
	*/
	public static $i18n_js_strings = array();

	/**
	* 	$main_file - path to espresso.php
	* 	
	* 	@access 	public
	*	@var 	array	
	*/
	public $main_file;
	
	/**
	 *
	 * @var type 
	 */
	public $addons;





	/**
	 *@singleton method used to instantiate class object
	 *@access public
	 *@return EE_Registry instance
	 */	
	public static function instance() {
		// check if class object is instantiated
		if ( self::$_instance === NULL  or ! is_object( self::$_instance ) or ! ( self::$_instance instanceof EE_Registry )) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}	



	/**
	 *private constructor to prevent direct creation
	 *@Constructor
	 *@access private
	 *@return void
	 */	
	private function __construct() {
		$this->load_core( 'Base' );
		// class library
		$this->LIB = new StdClass();
		add_action( 'AHEE__EE_System__set_hooks_for_core', array( $this, 'init' ));
	}



	/**
	 * 	init
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function init() {
		// Get current page protocol
		$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
		// Output admin-ajax.php URL with same protocol as current page
		self::$i18n_js_strings['ajax_url'] = admin_url( 'admin-ajax.php', $protocol );
		self::$i18n_js_strings['wp_debug'] = WP_DEBUG;
	}




	/**
	 *	loads core classes - must be singletons
	 * 
	 * 	@access 	public
	 *	@param string $class_name - simple class name ie: session
	 *	@return instantiated class object
	 */	
	public function load_core ( $class_name, $arguments = array() ) {
		$paths = array(
			EE_CORE,
			EE_ADMIN,
			EE_CPTS
		);
		// retreive instantiated class
		return $this->_load( $paths, 'EE_' , $class_name, 'core', $arguments );
	}




	/**
	 *	loads data_migration_scripts
	 * 
	 * 	@access 	public
	 *	@param string $class_name - class name for the DMS ie: EE_DMS_Core_4_2_0
	 *	@return EE_Data_Migration_Script_Base
	 */	
	public function load_dms ( $class_name, $arguments = array() ) {
		// retreive instantiated class
		return $this->_load( EE_CORE . 'data_migration_scripts' . DS, 'EE_DMS_' , $class_name, 'dms', $arguments, FALSE, FALSE, FALSE );
	}





	/**
	 *	loads object creating classes - must be singletons
	 * 
	 *	@param string $class_name - simple class name ie: attendee
	 *	@param array  $arguments - an array of arguments to pass to the class
	 *	@param bool   $from_db    - some classes are instantiated from the db and thus call a different method to instantiate
	 *	@param bool   $cache      if you dont' want the class to be stored in the internal cache (non-persistent) then set this to FALSE (ie. when instantiating model objects from client in a loop)
	 *	@param bool   $load_only      whether or not to just load the file and NOT instantiate, or load AND instantiate (default)
	 *	@return instantiated class object
	 */
	public function load_class ( $class_name, $arguments = array(), $from_db = FALSE, $cache = TRUE, $load_only = FALSE ) {
		// retreive instantiated class
		return $this->_load( EE_CLASSES, 'EE_' , $class_name, 'class', $arguments, $from_db, $cache, $load_only );
	}





	/**
	 *	generic class loader
	 * 
	 *	@param string $path_to_file - directory path to file location, not including filename
	 *	@param string $class_name - full class name  ie:  My_Class
	 *	@param string $type - file type - core? class? helper? model?
	 *	@return instantiated class object
	 */	
	public function load_file ( $path_to_file, $class_name, $type = 'class', $arguments = array(), $load_only = TRUE ) {
		// set path to class file
		$path_to_file = rtrim( $path_to_file, '/\\' ) . DS;
		$type = trim( $type, '. ' );
		// retreive instantiated class
		return $this->_load( $path_to_file, 'file', $class_name, $type, $arguments, FALSE, TRUE, $load_only );
	}





	/**
	 * 	loads helper classes - must be singletons
	 * 
	 *	@param string $class_name - simple class name ie: price
	 *	@return instantiated class object
	 */	
	public function load_helper ( $class_name, $arguments = array(), $load_only = TRUE ) {
		// retreive instantiated class
		return $this->_load( EE_HELPERS, 'EEH_', $class_name, 'helper', $arguments, FALSE, TRUE, $load_only );
	}




	/**
	 *	loads core classes - must be singletons
	 * 
	 * 	@access 	public
	 *	@param string $class_name - simple class name ie: session
	 *	@return instantiated class object
	 */	
	public function load_lib ( $class_name, $arguments = array(), $load_only = FALSE ) {
		$paths = array(
			EE_LIBRARIES,
			EE_LIBRARIES . 'messages' . DS,
			EE_LIBRARIES . 'shortcodes' . DS,
			EE_LIBRARIES . 'qtips' . DS
		);
		// retreive instantiated class
		return $this->_load( $paths, 'EE_' , $class_name, 'lib', $arguments, FALSE, TRUE, $load_only );
	}




	/**
	 * 	loads model classes - must be singletons
	 * 
	 *	@param string $class_name - simple class name ie: price
	 *	@return EEM_Base
	 */	
	public function load_model ( $class_name, $arguments = array(), $load_only = FALSE ) {
		// retreive instantiated class
		return $this->_load( EE_MODELS, 'EEM_' , $class_name, 'model', $arguments, FALSE, TRUE, $load_only );
	}



	/**
	 * 	loads model classes - must be singletons
	 * 
	 *	@param string $class_name - simple class name ie: price
	 *	@return instantiated class object
	 */	
	public function load_model_class ( $class_name, $arguments = array(), $load_only = TRUE ) {
		$paths = array(
			EE_MODELS . 'fields' . DS,
			EE_MODELS . 'helpers' . DS,
			EE_MODELS . 'relations' . DS,
			EE_MODELS . 'strategies' . DS
		);
		// retreive instantiated class
		return $this->_load( $paths, 'EE_' , $class_name, '', $arguments, FALSE, TRUE, $load_only );
	}





	/**
	 * Determines if $model_name is the name of an actual EE model.
	 * @param string $model_name like Event, Attendee, Question_Group_Question, etc.
	 * @return boolean
	 */
	public function is_model_name( $model_name ){
		return isset( $this->models[ $model_name ] ) ? TRUE : FALSE;
	}





	/**
	 *	loads and tracks classes
	 * 
	 *	@param string $file_path - file path including file name
	 *	@param string $class_prefix - EE  or EEM or... ???
	 *	@param string $class_name - $class name
	 *	@param string $type - file type - core? class? helper? model?
	 *	@param boolean $arguments - an array of arguments to pass to the class upon instantiation 
	 *	@param bool   $from_db    - some classes are instantiated from the db and thus call a different method to instantiate
	 *	@return instantiated class object
	 */	
	private function _load ( $file_paths = array(), $class_prefix = 'EE_', $class_name = FALSE, $type = 'class', $arguments = array(), $from_db = FALSE, $cache = TRUE, $load_only = FALSE ) {

		if ( ! empty( $class_prefix ) && $class_prefix != 'file' ) {
			// make sure $class_prefix is uppercase
			$class_prefix = strtoupper( trim( $class_prefix ));
			// add class prefix ONCE!!!
			$class_name = $class_prefix . str_replace( $class_prefix, '', trim( $class_name ));
		}

		$class_abbreviations = array(
			'EE_Cart' => 'CART',
			'EE_Config' => 'CFG',
			'EE_Network_Config' => 'NET_CFG',
			'EE_Request_Handler' => 'REQ',
			'EE_Session' => 'SSN'
		);

		// check if class has already been loaded, and return it if it has been
		if ( isset( $class_abbreviations[ $class_name ] ) && ! is_null( $this->$class_abbreviations[ $class_name ] )) {
			return $this->$class_abbreviations[ $class_name ];
		} else if ( isset ( $this->{$class_name} )) {
			return $this->{$class_name};
		} else if ( isset ( $this->LIB->$class_name )) {
			return $this->LIB->$class_name;
		}
		
		// assume all paths lead nowhere
		$path = FALSE;
		// make sure $file_paths is an array
		$file_paths = is_array( $file_paths ) ? $file_paths : array( $file_paths );
		// cycle thru paths 
		foreach ( $file_paths as $key => $file_path ) {
			// convert all separators to proper DS, if no filepth, then use EE_CLASSES
			$file_path = $file_path ? str_replace( array( '/', '\\' ), DS, $file_path ) : EE_CLASSES;
			// prep file type
			$type = ! empty( $type ) ? trim( $type, '.' ) . '.' : '';
			// build full file path
			$file_paths[ $key ] = $class_prefix == 'file' ? rtrim( $file_path, DS ) . '.' . $type . 'php' : rtrim( $file_path, DS ) . DS . $class_name . '.' . $type . 'php';
			//does the file exist and can be read ?
			if ( is_readable( $file_paths[ $key ] )) {
				$path = $file_paths[ $key ];
				break;
			}
		}

		// don't give up! you gotta...
		try {
			//does the file exist and can it be read ?
			if ( ! $path ) {
				// so sorry, can't find the file
				throw new EE_Error (
					sprintf (
						__('The %s file %s could not be located or is not readable due to file permissions. Please ensure that the following filepath(s) is correct: %s','event_espresso'),
						trim( $type, '.' ),
						$class_name,
						implode( ', ', $file_paths )
					)
				);
			}
			// get the file
			require_once( $path );
			// if the class isn't already declared somewhere
			if ( class_exists( $class_name, FALSE ) === FALSE ) {
				// so sorry, not a class
				throw new EE_Error(
					sprintf(
						__('The %s file %s does not appear to contain the %s Class.','event_espresso'),
						$type, 
						$class_name, 
						$class_name 
					)
				);
			}

		} catch ( EE_Error $e ) {
			$e->get_error();
		}

		
		// don't give up! you gotta...
		try {
			// create reflection
			$reflector = new ReflectionClass( $class_name );
			// instantiate the class and add to the LIB array for tracking
			// EE_Base_Classes are instantiated via new_instance by default (models call them via new_instance_from_db)
			if ( $reflector->getConstructor() === NULL || $load_only ) {
				$instantiation_mode = 0;
				// no constructor = static methods only... nothing to instantiate, loading file was enough
			} else if ( $from_db && method_exists( $class_name, 'new_instance_from_db' ) ) {
				$instantiation_mode = 1;
				$class_obj =  call_user_func_array( array( $class_name, 'new_instance_from_db' ), $arguments );
			} else if ( method_exists( $class_name, 'new_instance' ) ) {
				$instantiation_mode = 2;
				$class_obj =  call_user_func_array( array( $class_name, 'new_instance' ), $arguments );
			} else if ( method_exists( $class_name, 'instance' )) {
				$instantiation_mode = 3;
				$class_obj =  call_user_func_array( array( $class_name, 'instance' ), $arguments );
			} else if ( $reflector->isInstantiable() ) {
				$instantiation_mode = 4;
				$class_obj =  $reflector->newInstance( $arguments );			
			} else {
				// heh ? something's not right !
				$instantiation_mode = 5;
			}
			
		} catch ( EE_Error $e ) {
			$e->get_error();
		}

//	echo '<h4>$class_name : ' . $class_name . '  <br /><span style="font-size:10px;font-weight:normal;">$instantiation_mode : ' . $instantiation_mode . '<br/>' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';			
//	echo '<h4>$from_db : ' . $from_db . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
//	echo '<h4>$cache : ' . $cache . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
//	echo '<h4>$load_only : ' . $load_only . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
//	printr( $arguments, '$arguments  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
//	printr( $class_obj, '$class_obj  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );

		
		if ( isset( $class_obj )) {			
			// return newly instantiated class
			if ( isset( $class_abbreviations[ $class_name ] )) {		
				$this->$class_abbreviations[ $class_name ] = $class_obj;
			} else if ( EEH_Class_Tools::has_property( $this, $class_name )) {
				$this->{$class_name} = $class_obj;
			} else if ( !$from_db && $cache  ) {
				$this->LIB->$class_name = $class_obj;
			}
			return $class_obj;
		}
			
	}




	/**
	 *		@ override magic methods
	 *		@ return void
	 */
	final function __destruct() {}
	final function __call($a,$b) {}
	final function __get($a) {}
	final function __set($a,$b) {}
	final function __isset($a) {}
	final function __unset($a) {}
	final function __sleep() {
		return array();
	}
	final function __wakeup() {}
	final function __toString() {}
	final function __invoke() {}
	final function __set_state() {}
	final function __clone() {}
	final static function __callStatic($a,$b) {}

	public function addons(){
		foreach($this->modules as $module){
			
		}
	}
 
}
// End of file EE_Registry.core.php
// Location: ./core/EE_Registry.core.php