<?php
/**
 * Required  by EEM_Event_Question_Group in case someone queries for all its model objects
 */
class EE_Event_Venue extends EE_Base_Class{
	protected $_EVV_ID = null;
	protected $_EVT_ID = null;
	protected $_VNU_ID = null;
	protected $_EVV_primary = null;
	protected $_EQG_primary = null;
	protected $_Event;
	protected $_Venue;


	public static function new_instance( $props_n_values = array() ) {
		$classname = __CLASS__;
		$has_object = parent::_check_for_object( $props_n_values, $classname );
		return $has_object ? $has_object : new self( $props_n_values);
	}


	public static function new_instance_from_db ( $props_n_values = array() ) {
		return new self( $props_n_values, TRUE );
	}
	
}