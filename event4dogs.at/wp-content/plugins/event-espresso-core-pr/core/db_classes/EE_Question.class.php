<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
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
 * EE_Answer class
 *
 * @package			Event Espresso
 * @subpackage		includes/classes/EE_Answer.class.php
 * @author				Mike Nelson
 *
 * ------------------------------------------------------------------------
 */
require_once ( EE_CLASSES . 'EE_Soft_Delete_Base_Class.class.php' );
class EE_Question extends EE_Soft_Delete_Base_Class{


	/**
	 * question's id
	 * @access protected
	 * @var int
	 */
	protected $_QST_ID=FALSE;


	/**
	 * how the question is displayed.eg, "What is your name?"
	 * @access protected
	 * @var string
	 */
	protected $_QST_display_text=NULL;


	/**
	 * An administrative label to help differentiate between two questions that have the same display text
	 * @access protected
	 * @var string
	 */
	protected $_QST_admin_label=NULL;


	/**
	 * If it's a system name, the column of the attendee column to which this question corresponds
	 * @access protected
	 * @var string
	 */
	protected $_QST_system=NULL;


	/**
	 * Whether the question's textfield, radio button list, etc.
	 * valid values are: TEXT, TEXTAREA, SINGLE, DROPDOWN, MULTIPLE, DATE
	 * @access protected
	 * @var string
	 */
	protected $_QST_type=NULL;


	/**
	 * Indictes whether the question must be answered if presented in a form
	 * @access protected
	 * @var boolean
	 */
	protected $_QST_required=NULL;


	/**
	 *Text to show when the field isn't entered in a form when it's required
	 * @access protected
	 * @var string
	 */
	protected $_QST_required_text=NULL;


	/**
	 * Number to indicate where this question ought to appear in the order of questions
	 * @access protected
	 * @var int
	 */
	protected $_QST_order=NULL;


	/**
	 * Indicates whether this question is for administrators only
	 * @access protected
	 * @var boolena
	 */
	protected $_QST_admin_only=NULL;

	/**
	 *
	 * ID of the WP USEr who created this question
	 * @access protected
	 * @var int
	 */
	protected $_QST_wp_user=NULL;

	/**
	 * Boolean to indicate whether this question
	 * has been deleted or not
	 * @access private
	 * @var boolean
	 */
	protected $_QST_deleted=NULL;

	/**
	 * realted answers, lazy-loaded
	 * @var EE_Answer[]
	 */
	protected $_Answer;

	/**
	 * related question groups, lazy-loaded
	 * @var EE_Question_Group[]
	 */
	protected $_Question_Group;

	/**
	 * related question options, lazy-loaded
	 * @var EE_Question_Option[]
	 */
	protected $_Question_Option;


	protected $_Question_Group_Question; //for QST_order relation





	public static function new_instance( $props_n_values = array() ) {
		$classname = __CLASS__;
		$has_object = parent::_check_for_object( $props_n_values, $classname );
		return $has_object ? $has_object : new self( $props_n_values );
	}




	public static function new_instance_from_db ( $props_n_values = array() ) {
		return new self( $props_n_values, TRUE );
	}




	/**
	*		Set	Question display text
	*
	* 		@access		public
	*		@param		int		$QST_display_text
	*/
	public function set_display_text( $QST_display_text = FALSE ) {
		return $this->set('QST_display_text',$QST_display_text);
	}



	/**
	*		Set	Question admin text
	*
	* 		@access		public
	*		@param		int		$QST_admin_label
	*/
	public function set_admin_label( $QST_admin_label = FALSE ) {
		return $this->set('QST_admin_label',$QST_admin_label);
	}



	/**
	*		Set	system name
	*
	* 		@access		public
	*		@param		int		$QST_system
	*/
	public function set_system_ID( $QST_system = NULL ) {
		return $this->set('QST_system',$QST_system);
	}

	/**
	*		Set	question's type
	*
	* 		@access		public
	*		@param		int		$QST_type
	*/
	public function set_question_type( $QST_type = FALSE ) {
		return $this->set('QST_type',$QST_type);
	}

	/**
	 * Retrieves the list of allowedquestion types from the model.
	 * @return string[]
	 */
	private function _allowed_question_types(){
		$questionModel=$this->get_model();
		/* @var $questionModel EEM_Question*/
		return $questionModel->allowed_question_types();
	}

	/**
	*		Sets whether this question must be answered when presented in a form
	*
	* 		@access		public
	*		@param		int		$QST_required
	*/
	public function set_required( $QST_required = FALSE ) {
		return $this->set('QST_required',$QST_required);
	}

	/**
	*		Set	Question display text
	*
	* 		@access		public
	*		@param		int		$QST_required_text
	*/
	public function set_required_text( $QST_required_text = FALSE ) {
		return $this->set('QST_required_text',$QST_required_text);
	}



	/**
	*		Sets the order of this question when placed in a sequence of questions
	*
	* 		@access		public
	*		@param		int		$QST_order
	*/
	public function set_order( $QST_order = FALSE ) {
		return $this->set('QST_order',$QST_order);
	}



	/**
	*		Sets whether the question is admin-only
	*
	* 		@access		public
	*		@param		int		$QST_admin_only
	*/
	public function set_admin_only( $QST_admin_only = FALSE ) {
		return $this->set('QST_admin_only',$QST_admin_only);
	}



	/**
	*		Sets the wordpress user ID on the question
	*
	* 		@access		public
	*		@param		int		$QST_wp_user
	*/
	public function set_wp_user( $QST_wp_user = FALSE ) {
		return $this->set('QST_wp_user',$QST_wp_user);
	}

	/**
	*		Sets whether teh question has been deleted
	*		(we use this boolean isntead of actually
	*		deleting it because when users delete this question
	*		they really want to remove the question from future
	*		forms, BUT keep their old answers which depend
	*		on this record actually existing.
	*
	* 		@access		public
	*		@param		int		$QST_wp_user
	*/
	public function set_deleted( $QST_deleted = FALSE ) {
		return $this->set('QST_deleted',$QST_deleted);
	}


	/**
	 * returns the text for displaying the question to users
	 * @access public
	 * @return string
	 */
	public function display_text(){
		return $this->get('QST_display_text');
	}


	/**
	 * returns the text for the administrative label
	 * @access public
	 * @return string
	 */
	public function admin_label(){
		return $this->get('QST_admin_label');
	}

	/**
	 * returns the attendee column name for this question
	 * @access public
	 * @return string
	 */
	public function system_ID(){
		return $this->get('QST_system');
	}

	/**
	 * returns either a string of 'text', 'textfield', etc.
	 * @access public
	 * @return boolean
	 */
	public function required(){
		return $this->get('QST_required');
	}

	/**
	 * returns the text which should be displayed when a user
	 * doesn't answer this question in a form
	 * @access public
	 * @return string
	 */
	public function required_text(){
		return $this->get('QST_required_text');
	}
	/**
	 * returns the type of this question
	 * @access public
	 * @return string
	 */
	public function type(){
		return $this->get('QST_type');
	}

	/**
	 * returns an integer showing where this questino should
	 * be placed in a sequence of questions
	 * @access public
	 * @return int
	 */
	public function order(){
		return $this->get('QST_order');
	}

	/**
	 * returns whether this question should only appears to admins,
	 * or to everyone
	 * @access public
	 * @return boolean
	 */
	public function admin_only(){
		return $this->get('QST_admin_only');
	}

	/**
	 * returns the id the wordpress user who created this question
	 * @access public
	 * @return int
	 */
	public function wp_user(){
		return $this->get('QST_wp_user');
	}

	/**
	 * returns whether this question has been marked as 'deleted'
	 * @access public
	 * @return boolean
	 */
	public function deleted(){
		return $this->get('QST_deleted');
	}

	/**
	 * Gets an array of related EE_Answer  to this EE_Question
	 * @return EE_Answer[]
	 */
	public function answers(){
		return $this->get_many_related('Answer');
	}


	/**
	 * Boolean check for if there are answers on this question in th db
	 * @return boolean true = has answers, false = no answers.
	 */
	public function has_answers() {
		return $this->count_related('Answer') > 0 ? TRUE : FALSE;
	}


	/**
	 * gets an array of EE_Question_Group which relate to this question
	 * @return EE_Question_Group[]
	 */
	public function question_groups(){
		return $this->get_many_related('Question_Group');
	}

	/**
	 * Returns all the options for this question. By default, it returns only the not-yet-deleted ones.
	 * @param boolean $notDeletedOptionsOnly 1
	 * whehter to return ALL options, or only the ones which have not yet been deleleted
	 * @param string|array $selected_value_to_always_include, when retrieving options to an ANSWERED question,
	 * we want to usually only show non-deleted options AND the value that was selected for the answer,
	 * whether it was trashed or not.
	 * @return EE_Question_Option[]
	 */
	public function options($notDeletedOptionsOnly=true,$selected_value_to_always_include = NULL){
		if ( empty( $this->_QST_ID ) )
			return false;
		$query_params = array();
		if($selected_value_to_always_include){
			if(is_array($selected_value_to_always_include)){
				$query_params[0]['OR*options-query']['QSO_value']=array('IN',$selected_value_to_always_include);
			}else{
				$query_params[0]['OR*options-query']['QSO_value'] = $selected_value_to_always_include;
			}
		}
		if($notDeletedOptionsOnly){
			$query_params[0]['OR*options-query']['QSO_deleted'] = false;
		}
		return  $this->get_many_related('Question_Option', $query_params);
	}
	/**
	 * returns an array of EE_Question_Options which relate to this question
	 * @param EE_Question_Option $option
	 * @return boolean success
	 */
	public function temp_options(){
		return $this->_Question_Option;
	}
	/**
	 * Adds an option for this question. Note: if the option were previously associted with a different
	 * Question, that relationship will be overwritten.
	 * @param EE_Question_Option $option
	 * @return boolean success
	 */
	public function add_option(EE_Question_Option $option){
		return $this->_add_relation_to($option, 'Question_Option');
	}
	/**
	 * Adds an option directly to this question without saving to the db
	 * @param EE_Question_Option $option
	 * @return boolean success
	 */
	public function add_temp_option( EE_Question_Option $option ){
		return $this->_Question_Option[] = $option;
	}
	/**
	 * Marks the option as deleted.
	 * @param EE_Question_Option $option
	 * @return boolean success
	 */
	public function remove_option(EE_Question_Option $option){
		return $this->_remove_relation_to($option, 'Question_Option');
	}


	public function is_system_question() {
		$system_ID = $this->get('QST_system');
		return !empty( $system_ID ) ? TRUE : FALSE;
	}


	/**
	 * The purpose of this method is set the question order this question order to be the max out of all questions
	 *
	 * @access public
	 * @return void
	 */
	public function set_order_to_latest() {
		$latest_order = $this->get_model()->get_latest_question_order();
		$latest_order++;
		$this->set('QST_order', $latest_order );
	}


}
