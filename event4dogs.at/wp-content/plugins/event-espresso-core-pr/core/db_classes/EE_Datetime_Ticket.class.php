<?php if (!defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
do_action( 'AHEE_log', __FILE__, __FUNCTION__, '' );
/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package			Event Espresso
 * @ author				Seth Shoultes
 * @ copyright		(c) 2008-2011 Event Espresso  All Rights Reserved.
 * @ license			{@link http://eventespresso.com/support/terms-conditions/}   * see Plugin Licensing *
 * @ link					{@link http://www.eventespresso.com}
 * @ since		 		4.0
 *
 * ------------------------------------------------------------------------
 *
 * EE_Datetime_Ticket class
 *
 * @package			Event Espresso
 * @subpackage		includes/classes/EE_Datetime_Ticket.class.php
 * @author			Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EE_Datetime_Ticket extends EE_Base_Class{

	/**
	 * Primary Key
	 * @var int
	 */
	protected $_DTK_ID;




	/**
	 * Foreign Key to Datetime
	 * @var int
	 */
	protected $_DTT_ID;




	/**
	 * Foreign Key to Ticket
	 * @var int
	 */
	protected $_TKT_ID;




	//cached related objects
	

	/**
	 * Ticket object
	 * @var EE_Ticket
	 */
	protected $_Ticket;
	



	/**
	 * Datetime object
	 * @var EE_Datetime
	 */
	protected $_Datetime;






	public static function new_instance( $props_n_values = array(), $timezone = NULL ) {
		$classname = __CLASS__;
		$has_object = parent::_check_for_object( $props_n_values, $classname, $timezone );
		return $has_object ? $has_object : new self( $props_n_values, FALSE, $timezone );
	}


	public static function new_instance_from_db ( $props_n_values = array(), $timezone = NULL ) {
		return new self( $props_n_values, TRUE, $timezone );
	}

	
} //end EE_Datetime_Ticket class