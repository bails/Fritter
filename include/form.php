<?php
/**
 * Form.php
 *
 * the Form class is meant to simplify the task of keeping
 * track of errors in user submitted forms and the form
 * field valyes that were entered correctly
 *
 */

class Form
{
	var $values = array();		//holds submitted form field values
	var $errors = array();		//holds submitted form error messages
	var $num_errors;		//the number of errors in the submitted form

	//class constructor
	function Form(){
		/**
		 * get form value and error arrays, used when there
		 * is an error with a user-submitted form.
		 */
		if(isset($_SESSION['value_array']) && isset($_SESSION['error_array'])){
			$this->values = $_SESSION['value_array'];
			$this->errors = $_SESSION['error_array'];
			$this->num_errors  = count($this->errors);
				
			unset($_SESSION['value_array']);
			unset($_SESSION['error_array']);
		}
		else{
			$this->num_errors = 0;
		}
	}

	/**
	 * setValue - records the value typed into the given
	 * form field by the user
	 */
	function setValue($field, $value){
		$this->values[$field] = $value;
	}

	/**
	 * setError - records new form error given the form
	 * field name and the error messge attached to it
	 */
	function setError($field, $errmsg){
		$this->errors[$field] = $errmsg;
		$this->num_errors = count($this->errors);
	}

	/**
	 * value - returns the value attached to the given
	 * field, if none exists, the empty string is returned
	 */
	function value($field){
		if(array_key_exists($field, $this->values)){
			return htmlspecialchars(stripslashes($this->values[$field]));
		}else{
			return "";
		}
	}

	/**
	 * error - returns the error message attached to the
	 * given field, if none exists, the empty string is returned.
	 */
	function error($field){
		if(array_key_exists($field, $this->errors)){
			return "<font size=\"2\" color=\"#ff0000\">".$this->errors[$field]."</font>";
		}else{
			return "";
		}
	}

	// getErrorArray - returns the array of error messages
	function getErrorArray(){
		return $this->errors;
	}

};

?>