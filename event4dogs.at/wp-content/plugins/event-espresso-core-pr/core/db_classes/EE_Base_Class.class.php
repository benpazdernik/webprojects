<?php if (!defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
do_action( 'AHEE_log', __FILE__, ' FILE LOADED', '' );
/**
 *
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
 * EE_Base_Class class
 *
 * @package				Event Espresso
 * @subpackage			includes/classes/EE_Base_Class.class.php
 * @author				Michael Nelson
 *
 * ------------------------------------------------------------------------
 */

abstract class EE_Base_Class{

	/**
	 * 	system registry
	 *	@var 	EE_Registry		$EE
	 * 	@access 	protected
	 */
	//protected $EE;

	/**
	 * This is an array of the original properties and values provided during construction
	 * of this model object. (keys are model field names, values are their values).
	 * This list is important to remember so that when we are merging data from the db, we know
	 * which values to override and which to not override.
	 * @var array
	 */
	private $_props_n_values_provided_in_constructor = null;

	/**
	 * Timezone
	 * This gets set by the "set_timezone()" method so that we know what timezone incoming strings|timestamps are in.  This can also be used before a get to set what timezone you want strings coming out of the object to be in.  NOT all EE_Base_Class child classes use this property but any that use a EE_Datetime_Field data type will have access to it.
	 * @var string
	 */
	protected $_timezone = NULL;





	/**
    *	date format
	*
    *	pattern or format for displaying dates
	*
	*	@access	protected
    *	@var string
    */
	protected $_dt_frmt;



    /**
    *	time format
	*
    *	pattern or format for displaying time
	*
	*	@access	protected
    *	@var string
    */
	protected $_tm_frmt;




	/**
	 * This property is for holding a cached array of object properties indexed by property name as the key.
	 * The purpose of this is for setting a cache on properties that may have calculated values after a prepare_for_get.  That way the cache can be checked first and the calculated property returned instead of having to recalculate.
	 *
	 * Used by _set_cached_property() and _get_cached_property() methods.
	 * @access protected
	 * @var array
	 */
	protected $_cached_properties = array();

	/**
	 * Everything is related to extra meta... except extra meta, but it doesn't hurt
	 * to have this in that case
	 * $var EE_Extra_Meta[]
	 */
	protected $_Extra_Meta = NULL;


	/**
	 * basic constructor for Event Espresso classes, performs any necessary initialization,
	 * and verifies it's children play nice
	 * @param array $fieldValues where each key is a field (ie, array key in the 2nd layer of the model's _fields array, (eg, EVT_ID, TXN_amount, QST_name, etc) and valuse are their values
	 * @param boolean $bydb a flag for setting if the class is instantiated by the corresponding db model or not.
	 * @param string $timezone indicate what timezone you want any datetime fields to be in when instantiating a EE_Base_Class object.
	 *
	 */
	protected function __construct($fieldValues=null, $bydb = FALSE, $timezone = NULL ){
		//
		$className=get_class($this);
		//set default formats for date and time
		$this->_dt_frmt = EE_Base_Class::fix_date_format_for_use_with_strtotime( get_option( 'date_format' ));
		$this->_tm_frmt = get_option( 'time_format' );


		do_action("AHEE__{$className}__construct",$this,$fieldValues);
		$model=$this->get_model();
		$model_fields = $model->field_settings( FALSE );
		// ensure $fieldValues is an array
		$fieldValues = is_array( $fieldValues ) ? $fieldValues : array( $fieldValues );
		// printr( $fieldValues, '$fieldValues  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
		// verify client code hasnt passed any invalid field names
		foreach($fieldValues as $field_name=> $field_value){
			if( ! array_key_exists($field_name,$model_fields)){
				throw new EE_Error(sprintf(__("Invalid field (%s) passed to constructor of %s. Allowed fields are :%s", "event_espresso"),$field_name,get_class($this),implode(", ",array_keys($model_fields))));
			}
		}
		// printr( $model_fields, '$model_fields  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );

		//if db model is instantiatiating
		if( $bydb ){
			//client code has indicated these field values are from teh database
			foreach($fieldValues as $field_name => $field_value_from_db){
				$this->set_from_db($field_name,$field_value_from_db);
			}
		}else{
			//we're constructing a brand
			//new instance of the model object. Generally, this means we'll need to do more field validation
			foreach($model_fields as $fieldName => $field_obj){
				$this->set($fieldName,isset($fieldValues[$fieldName]) ? $fieldValues[$fieldName] : null ,true);
			}
		}

		try {

			//verify we have all the attributes required in teh model
			foreach($model->field_settings() as $fieldName=>$field_obj){
				if( ! $field_obj->is_db_only_field() && ! EEH_Class_Tools::has_property( $this, $this->_get_private_attribute_name( $fieldName ))){
					throw new EE_Error(
						sprintf(
							__('You have added an attribute titled \'%s\' to your model %s, but have not set a corresponding attribute on %s. Please add $%s to %s','event_espresso'),
							$fieldName,
							get_class($model),
							get_class($this),
							$this->_get_private_attribute_name($fieldName),
							get_class($this))
					);
				}
			}
			// verify we have all the model relations, except on extra metas because they
			//are meant to be related to everything
			if(get_class($this) !== 'EE_Extra_Meta'){
				foreach($model->relation_settings() as $relationName=>$relationSettings){
					if( ! EEH_Class_Tools::has_property( $this, $this->_get_private_attribute_name( $relationName ))) {
						throw new EE_Error(
							sprintf(
								__('You have added a relation titled \'%s\' to your model %s, but have not set a corresponding attribute on %s. Please add protected $%s to %s','event_espresso'),
								$relationName,
								get_class($model),
								get_class($this),
								$this->_get_private_attribute_name($relationName),
								get_class($this)
							)
						);
					}
				}
			}

		} catch ( EE_Error $e ) {
			$e->get_error();
			echo EE_Error::get_notices();
		}



		$this->_timezone = $timezone;
		//remember what values were passed to this constructor
		$this->_props_n_values_provided_in_constructor = $fieldValues;
		//remember in entity mapper
		if($model->has_primary_key_field() && $this->ID()){
			$model->add_to_entity_map($this);
	}
	}




	/**
	 * Overrides parent because parent expects old models.
	 * This also doesn't do any validation, and won't work for serialized arrays
	 * @param type $field_name
	 * @param type $field_value
	 * @param type $use_default
	 */
	public function set($field_name,$field_value,$use_default= false){
		$privateAttributeName=$this->_get_private_attribute_name($field_name);
		$field_obj = $this->get_model()->field_settings_for($field_name);
		if ( method_exists( $field_obj, 'set_timezone' ) )
			$field_obj->set_timezone( $this->_timezone );
		 $holder_of_value = $field_obj->prepare_for_set($field_value);
		 if( ($field_value === NULL || $holder_of_value === NULL || $holder_of_value ==='') && $use_default){
			 $this->$privateAttributeName = $field_obj->get_default_value();
		 }else{
			$this->$privateAttributeName = $holder_of_value;
		 }

		 //if we're not in the constructor...
		 //now check if what we set was a primary key
		 if($this->_props_n_values_provided_in_constructor && //note: props_n_values_provided_in_constructor is only set at the END of the constructor
				 $field_name == $this->_get_primary_key_name(get_class($this)) &&
				 $field_value){
			//if so, we want all this object's fields to be filled either with
			 //what we've explictly set on this model
			 //or what we have in the db
			// echo "setting primary key!";
			 $fields_on_model = $this->_get_model(get_class($this))->field_settings();

			 $obj_in_db = $this->_get_model(get_class($this))->get_one_by_ID($field_value);
			 foreach($fields_on_model as $field_obj){
				 if( ! array_key_exists($field_obj->get_name(), $this->_props_n_values_provided_in_constructor)
						&& $field_obj->get_name() != $field_name ){

					$this->set($field_obj->get_name(),$obj_in_db->get($field_obj->get_name()));
				 }
			 }
			 //oh this model object has an ID? well make sure its in the entity mapper
			 $this->get_model()->add_to_entity_map($this);
		 }
		 //let's unset any cache for this field_name from the $_cached_properties property.
		 $this->_clear_cached_property( $privateAttributeName );
	}



	/**
	 * See $_timezone property for description of what the timezone property is for.  This SETS the timezone internally for being able to refernece what timezone we are running conversions on when converting TO the internal timezone (UTC Unix Timestamp) for the object OR when converting FROM the internal timezone (UTC Unix Timestamp).
	 *  This is available to all child classes that may be using the EE_Datetime_Field for a field data type.
	 *
	 * @access public
	 * @param string $timezone A valid timezone string as described by @link http://www.php.net/manual/en/timezones.php
	 * @return void
	 */
	public function set_timezone( $timezone ) {
		$timezone = empty( $timezone ) ? get_option( 'timezone_string' ) : $timezone;

		//if timezone is STILL empty then let's get the GMT offset and then set the timezone_string using our converter
		if ( empty( $timezone ) ) {
			//let's get a the WordPress UTC offset
			$offset = get_option('gmt_offset');
			$timezone = EE_Datetime_Field::timezone_convert_to_string_from_offset( $offset );
		}

		EE_Datetime_Field::validate_timezone( $timezone ); //just running validation on the timezone.
		$this->_timezone = $timezone;
		//make sure we clear all cached properties because they won't be relevant now
		$this->_clear_cached_properties();
	}




	/**
	 * This just returns whatever is set for the current timezone.
	 *
	 * @access public
	 * @return string timezone string
	 */
	public function get_timezone() {
		return $this->_timezone;
	}



	/**
	 * fix_date_format_for_use_with_strtotime
	 *
	 * From the PHP strtotime() function documentation:
	 * "Dates in the m/d/y or d-m-y formats are disambiguated by looking at the separator between the various components: if the separator is a slash (/), then the American m/d/y is assumed; whereas if the separator is a dash (-) or a dot (.), then the European d-m-y format is assumed."
	 *
	 * @access public
	 * @param string $dt_frmt
	 * @return string
	 */
	public static function fix_date_format_for_use_with_strtotime( $dt_frmt ) {
		// if the date format is d/m/y
		if ( strpos( $dt_frmt, 'd/' )=== 0 ) {
			// change it to d-m-y, or else strtotime() will think it is m/d/y
			$dt_frmt = str_replace( '/', '-', $dt_frmt );
		}
		return $dt_frmt;
	}





	/**
	 * cache
	 * stores the passed model object on the current model object.
	 * In certain circumstances, we can use this cached model object instead of querying for another one entirely.
	 *
	 * @param string $relationName 	one of the keys in the _model_relations array on the model. Eg 'Registration' assocaited with this model object
	 * @param EE_Base_Class $object_to_cache 	that has a relation to this model object. (Eg, if this is a Transaction, that could be a payment or a registration)
	 * @param mixed int|string $cache_id 	a string or number that will be used as the key for any Belongs_To_Many items which will be stored in an array on this object
	 * @return mixed 	index into cache, or just TRUE if the relation is of type Belongs_To (because there's only one related thing, no array)
	 */
	public function cache( $relationName = '', $object_to_cache = NULL, $cache_id = NULL ){
		// its entirely possible that there IS no related object yet in which case there is nothing to cache.
		if ( ! $object_to_cache instanceof EE_Base_Class ) {
			return;
		}
		// get the class name (minus the prefix) for the related object
		$relationNameClassAttribute = $this->_get_private_attribute_name( $relationName );
		// also get "how" the object is related, or throw an error
		if( ! $relationship_to_model = $this->get_model()->related_settings_for( $relationName )) {
			throw new EE_Error( sprintf( __( 'There is no relationship to %s on a %s. Cannot cache it', 'event_espresso' ), $relationName, get_class( $this )));
		}
		// how many things are related ?
		if( $relationship_to_model instanceof EE_Belongs_To_Relation ){
			// if it's a "belongs to" relationship, then there's only one related model object  eg, if this is a registration, there's only 1 attendee for it
			// so for these model objects just set it to be cached
			$this->{$relationNameClassAttribute} = $object_to_cache;
			$return = TRUE;
		} else {
			// otherwise, this is the "many" side of a one to many relationship, so we'll add the object to the array of related objects for that type.
			// eg: if this is an event, there are many registrations for that event, so we cache the registrations in an array
			if( ! is_array( $this->{$relationNameClassAttribute} )) {
				// if for some reason, the cached item is a model object, then stick that in the array, otherwise start with an empty array
				$this->{$relationNameClassAttribute} = $this->{$relationNameClassAttribute} instanceof EE_Base_Class ? array( $this->{$relationNameClassAttribute} ) : array();
			}
			// first check for a cache_id which is normally empty
			if ( ! empty( $cache_id )) {
				// if the cache_id exists, then it means we are purposely trying to cache this with a known key that can then be used to retrieve the object later on
				$this->{$relationNameClassAttribute}[ $cache_id ] = $object_to_cache;
				$return = $cache_id;
			} elseif ( $object_to_cache->ID() ) {
				// OR the cached object originally came from the db, so let's just use it's PK for an ID
				$this->{$relationNameClassAttribute}[ $object_to_cache->ID() ] = $object_to_cache;
				$return = $object_to_cache->ID();
			} else {
				// OR it's a new object with no ID, so just throw it in the array with an autoincremented ID
				$this->{$relationNameClassAttribute}[] = $object_to_cache;
				  // move the internal pointer to the end of the array
				end( $this->{$relationNameClassAttribute} );
				// and grab the key so that we can return it
				$return = key( $this->{$relationNameClassAttribute} );
			}

		}
		return $return;
	}




	/**
	 * For adding an item to the cached_properties property.
	 *
	 * @access protected
	 * @param string $propertyname the property item the corresponding value is for.
	 * @param mixed  $value        The value we are caching.
	 * @return void
	 */
	protected function _set_cached_property( $propertyname, $value, $cache_type = NULL ) {
		//first make sure this property exists
		if ( !EEH_Class_Tools::has_property( $this, $propertyname ) )
			throw new EE_Error( sprintf( __('Trying to cache a non-existent property (%s).  Doublecheck the spelling please', 'event_espresso'), $propertyname ) );

		$cache_type = empty( $cache_type ) ? 'standard' : $cache_type;
		$this->_cached_properties[$propertyname][$cache_type] = $value;
	}





	/**
	 * This returns the value cached property if it exists OR the actual property value if the cache doesn't exist.
	 * This also SETS the cache if we return the actual property!
	 * @param  string $propertyname the name of the property we're trying to retrieve
	 * @param string         $extra_cache_ref This allows the user to specify an extra cache ref for the given property (in cases where the same property may be used for different outputs - i.e. datetime, money etc.)
	 *		It can also accept certain pre-defined "schema" strings to define how to output the property. see the field's prepare_for_pretty_echoing for what strings can be used
	 * @return mixed                whatever the value for the property is we're retrieving
	 */
	protected function _get_cached_property( $propertyname, $pretty = FALSE, $extra_cache_ref = NULL ) {

		//first make sure this property exists
		if ( !EEH_Class_Tools::has_property( $this, $propertyname )) {
			throw new EE_Error( sprintf( __('Trying to retrieve a non-existent property (%s).  Doublecheck the spelling please', 'event_espresso'), $propertyname ) );
		}

		$cache_type = $pretty ? 'pretty' : 'standard';
		$cache_type .= !empty( $extra_cache_ref ) ? '_' . $extra_cache_ref : '';

		if ( isset( $this->_cached_properties[$propertyname][$cache_type] ) ) {
			return $this->_cached_properties[$propertyname][$cache_type];
		}

		//otherwise let's return the property
		$field_name = ltrim( $propertyname, '_' );
		$field_obj = $this->get_model()->field_settings_for($field_name);
		$value = $pretty ? $field_obj->prepare_for_pretty_echoing($this->$propertyname, $extra_cache_ref) : $field_obj->prepare_for_get($this->$propertyname );
		$this->_set_cached_property( $propertyname, $value, $cache_type );
		return $value;
	}




	/**
	 * This just takes care of clearing out the cached_properties
	 * @return void
	 */
	protected function _clear_cached_properties() {
		$this->_cached_properties = array();
	}





	/**
	 * This just clears out ONE property if it exists in the cache
	 * @param  string $propertyname the property to remove if it exists (from the _cached_properties array)
	 * @return void
	 */
	protected function _clear_cached_property( $propertyname ) {
		if ( isset( $this->_cached_properties[$propertyname] ) )
			unset( $this->_cached_properties[$propertyname] );
	}



	/**
	 * Ensures that this related thing is a model object.
	 * @param mixed $object_or_id EE_base_Class/int/string either a rellate dmodel object, or its ID
	 * @param string $model_name name of the related thing, eg 'Attendee',
	 * @return EE_Base_Class
	 */
	protected function ensure_related_thing_is_model_obj($object_or_id,$model_name){
		$other_model_instance = self::_get_model_instance_with_name(self::_get_model_classname($model_name), $this->_timezone);
		$model_obj = $other_model_instance->ensure_is_obj($object_or_id);
		return $model_obj;
	}

	/**
	 * Forgets the cached model of the given relation Name. So the next time we request it,
	 * we will fetch it again from teh database. (Handy if you know it's changed somehow).
	 * If a specific object is supplied, and the relationship to it is either a HasMany or HABTM,
	 * then only remove that one object from our cached array. Otherwise, clear the entire list.
	 * @param string $relationName one of the keys in the _model_relations array on the model. Eg 'Registration'
	 * @param mixed $object_to_remove_or_index_into_array or an index into the array of cached things
	 * @param bool          $clear_all This flags clearing the entire cache relation property if this is HasMany or HABTM.
	 * @return EE_Base_Class from which was cleared from teh cache, or true if we requested to remove a relation from all
	 */
	public function clear_cache($relationName, $object_to_remove_or_index_into_array = null, $clear_all = FALSE){
		$relationship_to_model = $this->get_model()->related_settings_for($relationName);
		if( ! $relationship_to_model){
			throw new EE_Error(sprintf(__("There is no relationship to %s on a %s. Cannot clear that cache",'event_espresso'),$relationName,get_class($this)));
		}
		$relationNameClassAttribute = $this->_get_private_attribute_name($relationName);
		if($clear_all){
			$obj_removed = true;
			$this->$relationNameClassAttribute  = null;
		}elseif($relationship_to_model instanceof EE_Belongs_To_Relation){
			$obj_removed = $this->$relationNameClassAttribute;
			$this->$relationNameClassAttribute  = null;
		}else{
			if($object_to_remove_or_index_into_array instanceof EE_Base_Class && $object_to_remove_or_index_into_array->ID()){
				$index_in_cache = $object_to_remove_or_index_into_array->ID();
				if( is_array($this->{$relationNameClassAttribute}) && ! isset($this->{$relationNameClassAttribute}[$index_in_cache])){
					//find this object in teh array even though it has a different key
					foreach($this->$relationNameClassAttribute as $index=>$obj){
						if($obj == $object_to_remove_or_index_into_array || $obj->ID() == $object_to_remove_or_index_into_array->ID()){
							$index_in_cache = $index;
							break;
						}
					}
				}
			}elseif($object_to_remove_or_index_into_array instanceof EE_Base_Class){
				//so they provided a model object, but it's not yet saved to the DB... so let's go hunting for it!
				foreach($this->get_all_from_cache($relationName) as $index => $potentially_obj_we_want){
					if($potentially_obj_we_want == $object_to_remove_or_index_into_array){
						$index_in_cache = $index;
					}
				}
			}else{
				$index_in_cache = $object_to_remove_or_index_into_array;
			}
			$obj_removed = $this->{$relationNameClassAttribute}[$index_in_cache];
			unset($this->{$relationNameClassAttribute}[$index_in_cache]);
		}
		return $obj_removed;
	}



	/**
	 * update_cache_after_object_save
	 * Allows a cached item to have it's cache ID (within the array of cached items) reset using the new ID it has obtained after being saved to the db
	 *
	 * @param string $relationName - the type of object that is cached
	 * @param EE_Base_Class $newly_saved_object - the newly saved object to be recached
	 * @param string $current_cache_id - the ID that was used when originally caching the object
	 * @return boolean TRUE on success, FALSE on fail
	 */
	public function update_cache_after_object_save( $relationName, EE_Base_Class $newly_saved_object, $current_cache_id = '' ){
//		echo '<h4>$relationName : ' . $relationName . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
//		echo '<h4>$current_cache_id : ' . $current_cache_id . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
		// get the correct relation name for the related item
		$relationNameClassAttribute = $this->_get_private_attribute_name( $relationName );
//		echo '<h4>$relationNameClassAttribute : ' . $relationNameClassAttribute . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
//		printr( $this->{$relationNameClassAttribute}, '$this->{$relationNameClassAttribute}  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
		// verify that incoming object is of the correct type
		$obj_class = 'EE' . $relationNameClassAttribute;
		if ( $newly_saved_object instanceof $obj_class ) {
			// now get the type of relation
			$relationship_to_model = $this->get_model()->related_settings_for( $relationName );
//			printr( $relationship_to_model, '$relationship_to_model  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
			// if this is a 1:1 realtionship
			if( $relationship_to_model instanceof EE_Belongs_To_Relation ) {
				// then just replace the cached object with the newly saved object
				$this->{$relationNameClassAttribute} = $newly_saved_object;
				return TRUE;
			// or if it's some kind of sordid feral polyamorous relationship...
			} elseif ( is_array( $this->{$relationNameClassAttribute} ) && isset( $this->{$relationNameClassAttribute}[ $current_cache_id ] )) {
				// then remove the current cached item
				unset( $this->{$relationNameClassAttribute}[ $current_cache_id ] );
				// and cache the newly saved object using it's new ID
				$this->{$relationNameClassAttribute}[ $newly_saved_object->ID() ] = $newly_saved_object;
				return TRUE;
			}
		}
//		printr( $newly_saved_object, '$newly_saved_object  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
		return FALSE;
	}



	/**
	 * Fetches a single EE_Base_Class on that relation. (If the relation is of type
	 * BelongsTo, it will only ever have 1 object. However, other relations could have an array of objects)
	 *
	 * @param string $relationName
	 * @return EE_Base_Class
	 */
	public function get_one_from_cache($relationName){
		$relationNameClassAttribute = $this->_get_private_attribute_name($relationName);
		$cached_array_or_object =  $this->$relationNameClassAttribute;
		if(is_array($cached_array_or_object)){
			return array_shift($cached_array_or_object);
		}else{
			return $cached_array_or_object;
		}
	}



	/**
	 * Fetches a single EE_Base_Class on that relation. (If the relation is of type
	 * BelongsTo, it will only ever have 1 object. However, other relations could have an array of objects)
	 * @param string $relationName
	 * @return EE_Base_Class[]
	 */
	public function get_all_from_cache($relationName){
		$relationNameClassAttribute = $this->_get_private_attribute_name($relationName);
		$cached_array_or_object =  $this->$relationNameClassAttribute;
		if(is_array($cached_array_or_object)){
			return $cached_array_or_object;
		}elseif($cached_array_or_object){//if the result isnt an array, but exists, make it an array
			return array($cached_array_or_object);
		}else{//if nothing was found, return an empty array
			return array();
		}
	}


	/**
	 * Overrides parent because parent expects old models.
	 * This also doesn't do any validation, and won't work for serialized arrays
	 * @param type $field_name
	 * @param type $field_value_from_db
	 * @param type $use_default
	 */
	public function set_from_db($field_name,$field_value_from_db){
		$privateAttributeName=$this->_get_private_attribute_name($field_name);
		$field_obj = $this->get_model()->field_settings_for($field_name);
		//you would think the DB hass no NULLs for non-nullabel fields right? wrong!
		//eg, a CPT model object could have an entry in the posts table, but no
		//entry in the meta table. Meaning that all its columsn in the meta table
		//are null! yikes! so when we find one like that, use defaults for its meta columns
		if($field_value_from_db === NULL && ! $field_obj->is_nullable()){
			$field_value = $field_obj->get_default_value();
		}else{
			$field_value = $field_value_from_db;
		}
		$this->$privateAttributeName = $field_obj->prepare_for_set_from_db($field_value);
		//echo '<h4>' . $privateAttributeName . ' : ' . $this->$privateAttributeName . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
	}




	/**
	 * verifies that the specified field is of the correct type
	 * @param mixed $value the value to check if it's of the correct type
	 * @param EE_Model_Field $fieldSettings settings for a specific field.
	 * @param string         $extra_cache_ref This allows the user to specify an extra cache ref for the given property (in cases where the same property may be used for different outputs - i.e. datetime, money etc.)
	 * @return boolean
	 * @throws EE_Error if fieldSettings is misconfigured
	 */
	public function get($field_name, $extra_cache_ref = NULL ){
		$privateAttributeName=$this->_get_private_attribute_name($field_name);
		return $this->_get_cached_property( $privateAttributeName, FALSE, $extra_cache_ref );
	}


	/**
	 * This method simply returns the RAW unprocessed value for the given property in this class
	 * @param  string $field_name A valid fieldname
	 * @return mixed              Whatever the raw value stored on the property is.
	 * @throws EE_Error if fieldSettings is misconfigured or the field doesn't exist.
	 */
	public function get_raw($field_name) {
		$privateAttributeName = $this->_get_private_attribute_name($field_name);
		$this->_property_exists( $privateAttributeName );
		return $this->$privateAttributeName;
	}



	/**
	 * To be used in template to immediately echo out the value, and format it for output.
	 * Eg, shoudl call stripslashes and whatnought before echoing
	 * @param string $field_name the name of the field as it appears in teh DB
	 * @param string         $extra_cache_ref This allows the user to specify an extra cache ref for the given property (in cases where the same property may be used for different outputs - i.e. datetime, money etc.)
	 * @return void
	 */
	public function e($field_name, $extra_cache_ref = NULL){
		echo $this->get_pretty($field_name, $extra_cache_ref);
	}
	/**
	 * Exactly like e(), echoes out the field, but sets its schema to 'form_input', so that it
	 * can be easily used as the value of form input.
	 * @param string $field_name
	 * @return void
	 */
	public function f($field_name){
		$this->e($field_name,'form_input');
	}

	/**
	 *
	 * @param string $field_name
	 * @param string         $extra_cache_ref This allows the user to specify an extra cache ref for the given property (in cases where the same property may be used for different outputs - i.e. datetime, money etc.)
	 * @return mixed
	 */
	public function get_pretty($field_name, $extra_cache_ref = NULL){
		$privateAttributeName = $this->_get_private_attribute_name($field_name);
		return  $this->_get_cached_property( $privateAttributeName, TRUE, $extra_cache_ref );
	}



	/**
	 * This simply returns the datetime for the given field name
	 * Note: this protected function is called by the wrapper get_date or get_time or get_datetime functions (and the equivalent e_date, e_time, e_datetime).
	 *
	 * @access protected
	 * @param  string                $field_name  Field on the instantiated EE_Base_Class child object
	 * @param  mixed(null|string) $date_format valid datetime format used for date (if '' then we just use the default on the field, if NULL we use the last-used format)
	 * @param  mixed(null|string) $time_format Same as above except this is for time format
	 * @param string $date_or_time if NULL then both are returned, otherwise "D" = only date and "T" = only time.
	 * @param  boolean $echo        Whether the dtt is echoing using pretty echoing or just returned using vanilla get
	 * @return mixed               string on success, FALSE on fail, or EE_Error Exception is thrown if field is not a valid dtt field
	 */
	protected function _get_datetime( $field_name, $dt_frmt = NULL, $tm_frmt = NULL, $date_or_time = NULL, $echo = FALSE ) {

		$in_dt_frmt = empty($dt_frmt) ? $this->_dt_frmt : EE_Base_Class::fix_date_format_for_use_with_strtotime( $dt_frmt );
		$in_tm_frmt = empty($tm_frmt) ? $this->_tm_frmt : $tm_frmt;


		//validate field for datetime and returns field settings if valid.
		$field = $this->_get_dtt_field_settings( $field_name );
		$var_name = $this->_get_private_attribute_name( $field_name );

		if ( $dt_frmt !== NULL ) {
			$this->_clear_cached_property( $var_name, $date_or_time );
		}
		if ( $echo )
			$field->set_pretty_date_format( $in_dt_frmt );
		else
			$field->set_date_format( $in_dt_frmt );

		if ( $tm_frmt !== NULL ) {
			$this->_clear_cached_property( $var_name, $date_or_time );
		}
		if ( $echo )
			$field->set_pretty_time_format( $in_tm_frmt );
		else
			$field->set_time_format( $in_tm_frmt );

		//set timezone in field object
		$field->set_timezone( $this->_timezone );

		//set the output returned
		switch ( $date_or_time ) {

			case 'D' :
				$field->set_date_time_output('date');
				break;

			case 'T' :
				$field->set_date_time_output('time');
				break;

			default :
				$field->set_date_time_output();
		}


		if ( $echo ) {
			$this->e( ltrim( $var_name, '_' ), $date_or_time );
		 } else
			return $this->get( ltrim( $var_name, '_' ), $date_or_time );
	}


	/**
	 * below are wrapper functions for the various datetime outputs that can be obtained for JUST returning the date portion of a datetime value. (note the only difference between get_ and e_ is one returns the value and the other echoes the pretty value for dtt)
	 * @param  string $field_name name of model object datetime field holding the value
	 * @param  string $format     format for the date returned (if NULL we use default in dt_frmt property)
	 * @return string            datetime value formatted
	 */
	public function get_date( $field_name, $format = NULL ) {
		return $this->_get_datetime( $field_name, $format, NULL, 'D' );
	}
	public function e_date( $field_name, $format = NULL ) {
		$this->_get_datetime( $field_name, $format, NULL, 'D', TRUE );
	}


	/**
	 * below are wrapper functions for the various datetime outputs that can be obtained for JUST returning the time portion of a datetime value. (note the only difference between get_ and e_ is one returns the value and the other echoes the pretty value for dtt)
	 * @param  string $field_name name of model object datetime field holding the value
	 * @param  string $format     format for the time returned ( if NULL we use default in tm_frmt property)
	 * @return string             datetime value formatted
	 */
	public function get_time( $field_name, $format = NULL ) {
		return $this->_get_datetime( $field_name, NULL, $format, 'T' );
	}
	public function e_time( $field_name, $format = NULL ) {
		$this->_get_datetime( $field_name, NULL, $format, 'T', TRUE );
	}




	/**
	 * below are wrapper functions for the various datetime outputs that can be obtained for returning the date AND time portion of a datetime value. (note the only difference between get_ and e_ is one returns the value and the other echoes the pretty value for dtt)
	 * @param  string $field_name name of model object datetime field holding the value
	 * @param  string $dt_frmt    format for the date returned (if NULL we use default in dt_frmt property)
	 * @param  string $tm_frmt    format for the time returned (if NULL we use default in tm_frmt property)
	 * @return string             datetime value formatted
	 */
	public function get_datetime( $field_name, $dt_frmt = NULL, $tm_frmt = NULL ) {
		return $this->_get_datetime( $field_name, $dt_frmt, $tm_frmt );
	}
	public function e_datetime( $field_name, $dt_frmt = NULL, $tm_frmt = NULL ) {
		$this->_get_datetime( $field_name, $dt_frmt, $tm_frmt, NULL, TRUE);
	}




	/**
	 * This method validates whether the given field name is a valid field on the model object as well as it is of a type EE_Datetime_Field.  On success there will be returned the field settings.  On fail an EE_Error exception is thrown.
	 * @param  string $field_name The field name being checked
	 * @return EE_Datetime_Field
	 */
	protected function _get_dtt_field_settings( $field_name ) {
		$field = $this->get_model()->field_settings_for($field_name);

		//check if field is dtt
		if ( $field instanceof EE_Datetime_Field ) {
			return $field;
		} else {
			throw new EE_Error( sprintf( __('The field name "%s" has been requested for the EE_Base_Class datetime functions and it is not a valid EE_Datetime_Field.  Please check the spelling of the field and make sure it has been setup as a EE_Datetime_Field in the %s model constructor', 'event_espresso'), $field_name, self::_get_model_classname( get_class($this) ) ) );
		}
	}




	/**
	 * NOTE ABOUT BELOW:
	 * These convenience date and time setters are for setting date and time independently.  In other words you might want to change the time on a datetime_field but leave the date the same (or vice versa).
	 *
	 * IF on the other hand you want to set both date and time at the same time, you can just use the models default set($fieldname,$value) method and make sure you send the entire datetime value for setting.
	 */

	/**
	 * sets the time on a datetime property
	 *
	 * @access protected
	 * @param string $time      a valid time string for php datetime functions
	 * @param string $fieldname the name of the field the time is being set on (must match a EE_Datetime_Field)
	 */
	protected function _set_time_for( $time, $fieldname ) {
		$this->_set_date_time( 'T', $time, $fieldname );
	}





	/**
	 * sets the date on a datetime property
	 *
	 * @access protected
	 * @param string $date      a valid date string for php datetime functions
	 * @param string $fieldname the name of the field the date is being set on (must match a EE_Datetime_Field)
	 */
	protected function _set_date_for( $date, $fieldname ) {
		$this->_set_date_time( 'D', $date, $fieldname );
	}






	/**
	 * This takes care of setting a date or time independently on a given model object property. This method also verifies that the given fieldname matches a model object property and is for a EE_Datetime_Field field
	 *
	 * @access private
	 * @param string $what          "T" for time, 'B' for both, 'D' for Date.
	 * @param string $datetime_value A valid Date or Time string
	 * @param string $fieldname     the name of the field the date OR time is being set on (must match a EE_Datetime_Field property)
	 */
	private function _set_date_time( $what = 'T', $datetime_value, $fieldname ) {
		$field = $this->_get_dtt_field_settings( $fieldname );
		$attribute_field_name = $this->_get_private_attribute_name($fieldname);
		$field->set_timezone( $this->_timezone );

		switch ( $what ) {
			case 'T' :
				$this->$attribute_field_name = $field->prepare_for_set_with_new_time( $datetime_value, $this->$attribute_field_name );
				break;
			case 'D' :
				$this->$attribute_field_name = $field->prepare_for_set_with_new_date( $datetime_value, $this->$attribute_field_name );
				break;
			case 'B' :
				$this->$attribute_field_name = $field->prepare_for_set( $datetime_value );
				break;
		}

		$this->_clear_cached_property($attribute_field_name);
	}






	/**
	 * This will return a timestamp for the website timezone but ONLY when the current website timezone is different than the timezone set for the website.
	 *
	 * NOTE, this currently only works well with methods that return values.  If you use it with methods that echo values the $_timestamp property may not get reset to its original value and that could lead to some unexpected results!
	 *
	 * @access public
	 * @param string $field_name This is the name of the field on the object that contains the date/time value being returned.
	 * @param string $callback must match a valid method in this class (defaults to get_datetime)
	 * @param mixed (array|string) $args This is the arguments that will be passed to the callback.
	 * @param string $prepend You can include something to prepend on the timestamp
	 * @param string $append You can include somethign to append on the timestamp
	 * @return string timestamp
	 */
	public function display_in_my_timezone( $field_name, $callback = 'get_datetime', $args = NULL, $prepend = '', $append = '' ) {
		EE_Registry::instance()->load_helper('DTT_Helper');
		$timezone = EEH_DTT_Helper::get_timezone();

		if ( $timezone == $this->_timezone )
			return '';

		$original_timezone = $this->_timezone;
		$this->set_timezone( $timezone );

		$fn = (array) $field_name;
		$args = array_merge( $fn, (array) $args );

		if ( !method_exists( $this, $callback ) )
			throw EE_Error(sprintf( __('The method named "%s" given as the callback param in "display_in_my_timezone" does not exist.  Please check your spelling', 'event_espresso'), $callback ) );
		$args = (array) $args;
		$return =  $prepend . call_user_func_array( array( $this, $callback ), $args ) . $append;

		$this->set_timezone( $original_timezone );
		return $return;
	}




	/**
	 * Deletes this model object. That may mean just 'soft deleting' it though.
	 * @return boolean success
	 */
	public function delete(){
		$model=$this->get_model();
		$result=$model->delete_by_ID($this->ID());
		if($result){
			return true;
		}else{
			return false;
		}
	}



	/**
	 * Deletes this model object permanently from db (but keep in mind related models my block the delete and return an error)
	 * @return boolean success
	 */
	public function delete_permanently(){
		$model=$this->get_model();
		$result=$model->delete_permanently_by_ID($this->ID());
		return $result ? true : false;
	}



	/**
	*		Saves this object to teh database. An array may be supplied to set some values on this
	 * object just before saving.
	*
	* 		@access		public
	* 		@param		array		$set_cols_n_values
	*		@return int, 1 on a successful update, the ID of
	*					the new entry on insert; 0 on failure
	*/
	public function save($set_cols_n_values=array()) {
		//set attributes as provided in $set_cols_n_values
		foreach($set_cols_n_values as $column=>$value){
			$this->set($column,$value);
		}
		//now get current attribute values
		$save_cols_n_values = array();
		foreach($this->get_model()->field_settings(false) as $fieldName=>$field_obj){
			$attributeName=$this->_get_private_attribute_name($fieldName);
			$save_cols_n_values[$fieldName] = $this->$attributeName;

		}
		//if the object already has an ID, update it. Otherwise, insert it
		//also: change the assumption about values passed to the model NOT being prepare dby the model obejct. They have been
		$old_assumption_concerning_value_preparation = $this->get_model()->get_assumption_concerning_values_already_prepared_by_model_object();
		$this->get_model()->assume_values_already_prepared_by_model_object(true);

		if ( !empty( $save_cols_n_values[self::_get_primary_key_name( get_class($this) )] ) ){
			$results = $this->get_model()->update ( $save_cols_n_values, array(array(self::_get_primary_key_name(get_class($this))=>$this->ID()),'default_where_conditions'=>'other_models_only') );
		} else {
			unset($save_cols_n_values[self::_get_primary_key_name( get_class( $this) )]);
			$results = $this->get_model()->insert( $save_cols_n_values, true);
			if($results){
				//if successful, set the primary key
				//but don't use the normal SET method, because it will check if
				//an item with the same ID exists in the mapper & db, then
				//will find it in the db (because we just added it) and THAT object
				//will get added to teh mapper before we can add this one!
				//but if we just avoid using the SET method, all that headache can be avoided
				$pk_attribute = $this->_get_private_attribute_name(self::_get_primary_key_name( get_class($this)));
				$this->$pk_attribute = $results;
				$this->_clear_cached_property($pk_attribute);
			}
			$this->get_model()->add_to_entity_map($this);
		}
		//restore the old assumption about values being prepared by the model obejct
		$this->get_model()->assume_values_already_prepared_by_model_object($old_assumption_concerning_value_preparation);

		return $results;
	}

	/**
	 * Saves this model object and its NEW cached relations to the database.
	 * (Meaning, for now, IT DOES NOT WORK if the cached items already exist in the DB.
	 * In order for that to work, we would need to mark model objects as dirty/clean...
	 * because otherwise, there's a potential for infinite looping of saving
	 * Saves the cached related model objects, and ensures the relation between them
	 * and this object and properly setup
	 * @return int ID of new model object on save; 0 on failure+
	 */
	public function save_new_cached_related_model_objs(){
		//make sure this has been saved
		if( ! $this->ID()){
			$id = $this->save();
		}else{
			$id = $this->ID();
		}
		//now save all the NEW cached model objects  (ie they don't exist in the DB)
		foreach($this->get_model()->relation_settings() as $relationName => $relationObj){

			$property_name = $this->_get_private_attribute_name($relationName);

			if($this->$property_name){
				//is this a relation where we should expect just ONE related object (ie, EE_Belongs_To_relation)
				//or MANY related objects (ie, EE_HABTM_Relation or EE_Has_Many_Relation)?
				if($relationObj instanceof EE_Belongs_To_Relation){
					//add a relation to that relation type (which saves the appropriate thing in the process)
					//but ONLY if it DOESNT exist in the DB
					/* @var $related_model_obj EE_Base_Class */
					$related_model_obj = $this->$property_name;
//					if( ! $related_model_obj->ID()){
						$this->_add_relation_to($related_model_obj, $relationName);
						$related_model_obj->save_new_cached_related_model_objs();
//					}
				}else{
					foreach($this->$property_name as $related_model_obj){
						//add a relation to that relation type (which saves the appropriate thing in the process)
						//but ONLY if it DOESNT exist in the DB
//						if( ! $related_model_obj->ID()){
							$this->_add_relation_to($related_model_obj, $relationName);
							$related_model_obj->save_new_cached_related_model_objs();
//						}
					}
				}
			}
		}

		return $id;
	}


	/**
	 * converts a field name to the private attribute's name on teh class.
	 * Eg, converts "ANS_ID" to "_ANS_ID", which can be used like so $attr="_ANS_ID"; $this->$attr;
	 * @param string $fieldName
	 * @return string
	 */
	protected function _get_private_attribute_name($fieldName){
		return "_".$fieldName;
	}


	/**
	 * for getting a model while instantiated.
	 * @return EEM_Base model object
	 */
	public function get_model() {
		$modelName = self::_get_model_classname( get_class($this) );
		return self::_get_model_instance_with_name($modelName, $this->_timezone );
	}

	protected static function _get_object_from_entity_mapper($props_n_values, $classname){
		//TODO: will not work for TErm_RElationships because they ahve no PK!
		$primary_id_ref = self::_get_primary_key_name( $classname );
		if ( array_key_exists( $primary_id_ref, $props_n_values ) && !empty( $props_n_values[$primary_id_ref] ) ) {
			$id = $props_n_values[$primary_id_ref];
			return self::_get_model($classname)->get_from_entity_map($id);
		}
		return false;
	}

	/**
	 * This is called by child static "new_instance" method and we'll check to see if there is an existing db entry for the primary key (if present in incoming values).
	 * If there is a key in the incoming array that matches the primary key for the model AND it is not null, then we check the db. If there's a an object we return it.  If not we return false.
	 * @param  array  $props_n_values incoming array of properties and their values
	 * @param  string $classname      the classname of the child class
	 * @return mixed (EE_Base_Class|bool)
	 */
	protected static function _check_for_object( $props_n_values, $classname, $timezone = NULL ) {
		$primary_id_ref = self::_get_primary_key_name( $classname );

		if ( array_key_exists( $primary_id_ref, $props_n_values ) && !empty( $props_n_values[$primary_id_ref] ) ) {
			$existing = self::_get_model( $classname, $timezone )->get_one_by_ID( $props_n_values[$primary_id_ref] );
			if ( $existing ) {
				foreach ( $props_n_values as $property => $field_value ) {
					$existing->set( $property, $field_value );
				}
				return $existing;
			} else {
				return FALSE;
			}
		}
		return FALSE;
	}






	/**
	 * Gets the EEM_*_Model for this class
	 * @access public now, as this is more convenient
	 * @return EEM_Base
	 */
	protected static function  _get_model( $classname, $timezone = NULL ){
		//find model for this class
		if( ! $classname ){
			throw new EE_Error(sprintf(__("What were you thinking calling _get_model(%s)?? You need to specify the class name", "event_espresso"),$classname));
		}
		$modelName=self::_get_model_classname($classname);
		return self::_get_model_instance_with_name($modelName, $timezone );
	}



	/**
	 * Gets the model instance (eg instance of EEM_Attendee) given its classname (eg EE_Attendee)
	 * @param string $model_classname
	 * @return EEM_Base
	 */
	protected static function _get_model_instance_with_name($model_classname, $timezone = NULL){
		$model_classname = str_replace( 'EEM_', '', $model_classname );
		$model = EE_Registry::instance()->load_model( $model_classname );
		$model->set_timezone( $timezone );
		return $model;
	}


	/**
	 * If a model name is provided (eg Registration), gets the model classname for that model.
	 * Also works if a model class's classname is provided (eg EE_Registration).
	 * @return string like EEM_Attendee
	 */
	private static function _get_model_classname( $model_name = null){
		if(strpos($model_name,"EE_")===0){
			$model_classname=str_replace("EE_","EEM_",$model_name);
		}else{
			$model_classname = "EEM_".$model_name;
		}
		return $model_classname;
	}


	/**
	 * returns the name of the primary key attribute
	 * @return string
	 */
	protected static function _get_primary_key_name( $classname = NULL ){
		if( ! $classname){
			throw new EE_Error(sprintf(__("What were you thinking calling _get_primary_key_name(%s)", "event_espresso"),$classname));
		}
		return self::_get_model( $classname )->get_primary_key_field()->get_name();
	}
	/**
	 * Gets the value of the primary key.
	 * @return mixed, if the primary key is of type INT it'll be an int. Otherwise it could be a string
	 */
	public function ID(){
		//get the name of teh primary key for this class' model, then find what php class attribute's name
		$pk_field_parameter = $this->_get_private_attribute_name(self::_get_primary_key_name( get_class($this) ));
		//now that we know the name of the variable, use a variable variable to get its value and return its
		return $this->$pk_field_parameter;
	}

	/**
	 * Adds a relationship to the specified EE_Base_Class object, given the relationship's name. Eg, if the curren tmodel is related
	 * to a group of events, the $relationName should be 'Event', and should be a key in the EE Model's $_model_relations array.
	 * If this model object doensn't exist in teh DB, just caches the related thing
	 * @param mixed $otherObjectModelObjectOrID EE_Base_Class or the ID of the other object
	 * @param string $relationName eg 'Events','Question',etc.
	 * an attendee to a group, you also want to specify which role they will have in that group. So you would use this parameter to specificy array('role-column-name'=>'role-id')
	 * @param array  $where_query You can optionally include an array of key=>value pairs that allow you to further constrict the relation to being added.  However, keep in mind that the colums (keys) given must match a column on the JOIN table and currently only the HABTM models accept these additional conditions.  Also remember that if an exact match isn't found for these extra cols/val pairs, then a NEW row is created in the join table.
	 * @return EE_Base_Class the object the relation was added to
	 */
	public function _add_relation_to( $otherObjectModelObjectOrID,$relationName, $where_query = array(), $cache_id = NULL ){
		//if this thing exists in the DB, save the relation to the DB
		if( $this->ID() ){
			$otherObject = $this->get_model()->add_relationship_to( $this, $otherObjectModelObjectOrID, $relationName, $where_query );
			//clear cache so future get_many_related and get_first_related() return new results.
			$this->clear_cache( $relationName, $otherObject, TRUE );
		} else {
			//this thing doesn't exist in the DB,  so just cache it
			if( ! $otherObjectModelObjectOrID instanceof EE_Base_Class){
				throw new EE_Error( sprintf(
					__( 'Before a model object is saved to the database, calls to _add_relation_to must be passed an actual object, not just an ID. You provideed %s as the model object to a %s', 'event_espresso' ),
					$otherObjectModelObjectOrID,
					get_class( $this )
				));
			} else {
				$otherObject = $otherObjectModelObjectOrID;
			}
			$this->cache( $relationName, $otherObjectModelObjectOrID, $cache_id );
		}


		return $otherObject;
	}



	/**
	 * Removes a relationship to the psecified EE_Base_Class object, given the relationships' name. Eg, if the currentmodel is related
	 * to a group of events, the $relationName should be 'Events', and should be a key in the EE Model's $_model_relations array.
	 * If this model object doesn't exist in the DB, just removes teh related thing from the cache
	 * @param mixed $otherObjectModelObjectOrID EE_Base_Class or the ID of the other object, OR an array key into the cache if this isn't saved to teh DB yet
	 * @param string $relationName
	 * @param array  $where_query You can optionally include an array of key=>value pairs that allow you to further constrict the relation to being added.  However, keep in mind that the colums (keys) given must match a column on the JOIN table and currently only the HABTM models accept these additional conditions.  Also remember that if an exact match isn't found for these extra cols/val pairs, then a NEW row is created in the join table.
	 * @return EE_Base_Class the relation was removed from
	 */
	public function _remove_relation_to($otherObjectModelObjectOrID,$relationName, $where_query = array() ){
		if($this->ID()){//if this exists in the DB, save the relation change to the DB too
			$otherObject = $this->get_model()->remove_relationship_to($this, $otherObjectModelObjectOrID, $relationName, $where_query );
			$this->clear_cache($relationName, $otherObject);
		}else{//this doesn't exist in teh DB, just remove it from the cache
			$otherObject = $this->clear_cache($relationName,$otherObjectModelObjectOrID);
		}
		return $otherObject;
	}

	/**
	 * Removes ALL the related things for teh $relationName.
	 * @param string $relationName
	 * @param array $where_query_params like EEM_Base::get_all's $query_params[0] (where conditions)
	 * @return EE_Base_Class
	 */
	public function _remove_relations($relationName,$where_query_params = array()){
		if($this->ID()){//if this exists in the DB, save the relation change to the DB too
			$otherObjects = $this->get_model()->remove_relations($this, $relationName, $where_query_params );
			$this->clear_cache($relationName,null,true);
		}else{//this doesn't exist in teh DB, just remove it from the cache
			$otherObjects = $this->clear_cache($relationName,null,true);
		}
		return $otherObjects;
	}
	/**
	 * Gets all the related model objects of the specified type. Eg, if the current class if
	 * EE_Event, you could call $this->get_many_related('Registration') to get an array of all the
	 * EE_Registration objects which related to this event. Note: by default, we remove the "default query params"
	 * because we want to get even deleted items etc.
	 * @param string $relationName key in the model's _model_relations array
	 * @param array $query_paramslike EEM_Base::get_all
	 * @return EE_Base_Class[]
	 */
	public function get_many_related($relationName,$query_params = array()){
		if($this->ID()){//this exists in teh DB, so get the related things from either the cache or the DB
			//if there are query parameters, forget about caching the related model objects.
			if( $query_params ){
				$related_model_objects = $this->get_model()->get_all_related($this, $relationName, $query_params);
			}else{
				//did we already cache the result of this query?
				$cached_results = $this->get_all_from_cache($relationName);
				if ( ! $cached_results ){
					$related_model_objects = $this->get_model()->get_all_related($this, $relationName, $query_params);
					//if no query parameters were passed, then we got all the related model objects
					//for that relation. We can cache them then.
					foreach($related_model_objects as $related_model_object){
						$this->cache($relationName, $related_model_object);
					}
				}else{
					$related_model_objects = $cached_results;
				}
			}
		}else{//this doesn't exist itn eh DB, so just get the related things from the cache
			$related_model_objects = $this->get_all_from_cache($relationName);
		}
		return $related_model_objects;
	}

	/**
	 * Insetad of getting the related model objects, simply counts them. Ignores default_where_conditions by default,
	 * unless otherwise specified in the $query_params
	 * @param string $model_name like 'Event', or 'Registration'
	 * @param array $query_params like EEM_Base::get_all's
	 * @param string $field_to_count name of field to count by. By default, uses primary key
	 * @param bool 	 $distinct if we want to only count the distinct values for the column then you can trigger that by the setting $distinct to TRUE;
	 * @return int
	 */
	public function count_related($relation_name, $query_params =array(),$field_to_count = NULL, $distinct = FALSE){
		return $this->get_model()->count_related($this,$relation_name,$query_params,$field_to_count,$distinct);
	}
	/**
	 * Insetad of getting the related model objects, simply sums up the values of the specified field.
	 * Note: ignores default_where_conditions by default, unless otherwise specified i nthe $query_params
	 * @param string $model_name like 'Event', or 'Registration'
	 * @param array $query_params like EEM_Base::get_all's
	 * @param string $field_to_sum name of field to count by. By default, uses primary key (which doesn't make much sense,
	 * so you should probably change it)
	 * @return int
	 */
	public function sum_related($relation_name, $query_params = array(), $field_to_sum = null){
		return $this->get_model()->sum_related($this, $relation_name, $query_params, $field_to_sum);
	}

	/**
	 * Gets the first (ie, one) related model object of the specified type.
	 * @param string $relationName key in the model's _model_relations array
	 * @param array $query_paramslike EEM_Base::get_all
	 * @return EE_Base_Class (not an array, a single object)
	 */
	public function get_first_related($relationName,$query_params = array()){
		if($this->ID()){//this exists in the DB, get from the cache OR the DB

			//if they've provided some query parameters, don't bother trying to cache teh result
			//also make sure we're not caching the result of get_first_related
			//on a relation which should have an array of objects (because the cache might have an array of objects)
			if ($query_params || ! $this->get_model()->related_settings_for($relationName) instanceof EE_Belongs_To_Relation){
				$related_model_object =  $this->get_model()->get_first_related($this, $relationName, $query_params);
			}else{
				//first, check if we've already cached the result of this query
				$cached_result = $this->get_one_from_cache($relationName);
				if ( ! $cached_result ){

					$related_model_object = $this->get_model()->get_first_related($this, $relationName, $query_params);
					$this->cache($relationName,$related_model_object);
				}else{
					$related_model_object = $cached_result;
				}
			}
		}else{
			//this doesn't existin teh Db, but maybe the relation is of type belongsto, and so the related thing might
			if( $this->get_model()->related_settings_for($relationName) instanceof EE_Belongs_To_Relation){
				$related_model_object =  $this->get_model()->get_first_related($this, $relationName, $query_params);
			}else{
				$related_model_object = null;
			}
			//this doesn't exist in the DB and apparently teh thing it belogns to doesnt either, just get what's cached on this object
			if( ! $related_model_object){
				$related_model_object = $this->get_one_from_cache($relationName);
			}

		}
		return $related_model_object;
	}


	/**
	 * Does a delete on all related objects of type $relationName and removes
	 * the current model object's relation to them. If they can't be deleted (because
	 * of blocking related model objects) does nothing. If the related model obejcts are
	 * soft-deletable, they will be soft-deleted regardless of related blocking model objects.
	 * If this model object doesn't exist yet in the DB, just removes its related things
	 * @param string $relationName
	 * @param array $query_params like EEM_Base::get_all's
	 * @return int how many deleted
	 */
	public function delete_related($relationName,$query_params = array()){
		if($this->ID()){
			$count =  $this->get_model()->delete_related($this, $relationName, $query_params);
		}else{
			$count = count($this->get_all_from_cache($relationName));
			$this->clear_cache($relationName);
		}
		return $count;
	}

	/**
	 * Does a hard delete (ie, removes teh DB row) on all related objects of type $relationName and removes
	 * the current model object's relation to them. If they can't be deleted (because
	 * of blocking related model objects) just does a soft delete on it instead, if possible.
	 * If the related thing isn't a soft-deletable model object, this function is identical
	 * to delete_related(). If this model object doesn't exist in the DB, just remove its related things
	 * @param string $relationName
	 * @param array $query_params like EEM_Base::get_all's
	 * @return int how many deleted (includign those soft deleted)
	 */
	public function delete_related_permanently($relationName,$query_params = array()){
		if($this->ID()){
			$count =  $this->get_model()->delete_related_permanently($this, $relationName, $query_params);
		}else{
			$count = count($this->get_all_from_cache($relationName));
		}
		$this->clear_cache($relationName);
		return $count;
	}





	/**
	 * is_set
	 * Just a simple utility function children can use for checking if property exists
	 *
	 * @access  public
	 * @param  string $field_name property to check
	 * @return bool            				  TRUE if existing,FALSE if not.
	 */
	public function is_set( $field_name ) {
		$privateAttributeName = $this->_get_private_attribute_name( $field_name );
		return EEH_Class_Tools::has_property( $this, $privateAttributeName ) ? TRUE : FALSE;
	}



	/**
	 * Just a simple utility function children can use for checking if property (or properties) exists and thworing an EE_Error exception if they don't
	 * @param  mixed (string|array) $properties properties to check
	 * @return bool            				  TRUE if existing, throw EE_Error if not.
	 */
	protected function _property_exists( $properties ) {

		foreach ( (array) $properties as $propertyname ) {
			//first make sure this property exists
			if ( !EEH_Class_Tools::has_property( $this, $propertyname ) )
				throw new EE_Error( sprintf( __('Trying to retrieve a non-existent property (%s).  Doublecheck the spelling please', 'event_espresso'), $propertyname ) );
		}

		return TRUE;
	}




	/**
	 * This simply returns an array of model fields for this object
	 * @return array
	 */
	public function model_field_array() {
		$fields = $this->get_model()->field_settings(FALSE);
		$properties = array();
		//remove prepended underscore
		foreach ( $fields as $field_name => $settings ) {
			$properties[$field_name] = $this->get($field_name);
		}
		return $properties;
	}




	/**
	 * Very handy general function to allow for plugins to extend any child of EE_Base_Class.
	 * If a method is called on a child of EE_Base_Class that doesn't exist, this function is called (http://www.garfieldtech.com/blog/php-magic-call)
	 * and passed the method's name and arguments.
	 * Instead of requiring a plugin to extend the EE_Base_Class (which works fine is there's only 1 plugin, but when will that happen?)
	 * they can add a hook onto 'filters_hook_espresso__{className}__{methodName}' (eg, filters_hook_espresso__EE_Answer__my_great_function)
	 * and accepts 2 arguments: the object on which teh function was called, and an array of the original arguments passed to the function. Whatever their callbackfunction returns will be returned by this function.
	 * Example: in functions.php (or in a plugin):
	 * add_filter('FHEE__EE_Answer__my_callback','my_callback',10,3);
	 * function my_callback($previousReturnValue,EE_Base_Class $object,$argsArray){
			$returnString= "you called my_callback! and passed args:".implode(",",$argsArray);
	 *		return $previousReturnValue.$returnString;
	 * }
	 * require('EE_Answer.class.php');
	 * $answer= EE_Answer::new_instance(array('REG_ID' => 2,'QST_ID' => 3,'ANS_value' => The answer is 42'));
	 * echo $answer->my_callback('monkeys',100);
	 * //will output "you called my_callback! and passed args:monkeys,100"
	 * @param string $methodName name of method which was called on a child of EE_Base_Class, but which
	 * @param array $args array of original arguments passed to the function
	 * @return mixed whatever the plugin which calls add_filter decides
	 */
	public function __call($methodName,$args){
		$className=get_class($this);
		$tagName="FHEE__{$className}__{$methodName}";
		if(!has_filter($tagName)){
			throw new EE_Error(sprintf(__("Method %s on class %s does not exist! You can create one with the following code in functions.php or in a plugin: add_filter('%s','my_callback',10,3);function my_callback(\$previousReturnValue,EE_Base_Class \$object, \$argsArray){/*function body*/return \$whatever;}","event_espresso"),
										$methodName,$className,$tagName));
		}
		return apply_filters($tagName,null,$this,$args);
	}




	/**
	 * Similar to insert_post_meta, adds a record in teh Extra_Meta model's table with the given key and value.
	 * A $previous_value can be specified in case there are many meta rows with teh same key
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param string $previous_value
	 * @return int records updated (or BOOLEAN if we actually ended up inserting the extra meta row)
	 * NOTE: if the values havent changed, returns 0
	 */
	public function update_extra_meta($meta_key,$meta_value,$previous_value = NULL){
		$query_params  = array(array(
			'EXM_key'=>$meta_key,
			'OBJ_ID'=>$this->ID(),
			'EXM_type'=>$this->get_model()->get_this_model_name()));
		if($previous_value !== NULL){
			$query_params[0]['EXM_value'] = $meta_value;
		}
		$existing_rows_like_that = EEM_Extra_Meta::instance()->get_all($query_params);
		if( ! $existing_rows_like_that){
			return $this->add_extra_meta($meta_key, $meta_value);
		}else{
			return EEM_Extra_Meta::instance()->update(array('EXM_value'=>$meta_value), $query_params);;
		}
	}

	/**
	 * Adds a new extra meta record. If $unique is set to TRUE, we'll first double-check
	 * no other extra meta for this model object have teh same key. Returns TRUE if the
	 * extra meta row was entered, false if not
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param boolean $unique
	 * @return boolean
	 */
	public function add_extra_meta($meta_key,$meta_value,$unique = false){
		if($unique){
			$existing_extra_meta = EEM_Extra_Meta::instance()->get_one(array(array('EXM_key'=>$meta_key,'OBJ_ID'=>$this->ID(),'EXM_type'=>$this->_get_model_classname(get_class($this)))));
			if($existing_extra_meta){
				return false;
			}
		}
		$new_extra_meta = EE_Extra_Meta::new_instance(array(
			'EXM_key'=>$meta_key,
			'EXM_value'=>$meta_value,
			'OBJ_ID'=>$this->ID(),
			'EXM_type'=>$this->get_model()->get_this_model_name()));
		$new_extra_meta->save();
		return true;
	}

	/**
	 * Deletes all the extra meta rows for this record as specified by key. If $meta_value
	 * is specified, only deletes extra meta records with that value.
	 * @param string $meta_key
	 * @param string $meta_value
	 * @return int number of extra meta rows deleted
	 */
	public function delete_extra_meta($meta_key,$meta_value = NULL){
		$query_params  = array(array(
			'EXM_key'=>$meta_key,
			'OBJ_ID'=>$this->ID(),
			'EXM_type'=>$this->get_model()->get_this_model_name()));
		if($meta_value !== NULL){
			$query_params[0]['EXM_value'] = $meta_value;
		}
		$count_deleted = EEM_Extra_Meta::instance()->delete($query_params);
		return $count_deleted;
	}

	/**
	 * Gets the extra meta with the given meta key. If you specify "single" we just return 1, otherwise
	 * an array of everything found. Requires that this model actually have a relation of type EE_Has_Many_Any_Relation.
	 * You can specify $default is case you haven't found the extra meta
	 * @param string $meta_key
	 * @param boolean $single
	 * @param mixed $default if we don't find anything, what should we return?
	 * @return mixed single value if $single; array if ! $single
	 */
	public function get_extra_meta($meta_key,$single = FALSE,$default = NULL){
		if($single){
			$result = $this->get_first_related('Extra_Meta',array(array('EXM_key'=>$meta_key)));
			if($result){
				return $result->value();
			}else{
				return $default;
			}
		}else{
			$results =  $this->get_many_related('Extra_Meta',array(array('EXM_key'=>$meta_key)));
			if($results){
				$values = array();
				foreach($results as $result){
					$values[$result->ID()] = $result->value();
				}
				return $values;
			}else{
				return $default;
			}
		}

	}






}


interface EEI_Has_Address {
	public function address();
	public function address2();
	public function city();
	public function state_ID();
	public function state_obj();
	public function country_ID();
	public function country_obj();
	public function zip();
}