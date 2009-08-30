<?php
class User extends AppModel {

	var $name = 'User';

	/**
	 * Validation 
	 */
	var $validate = array(
	    'username' => array(
	        'loginRule-1' => array(
	            'rule' => 'alphaNumeric',  
	            'message' => 'Only alphabets and numbers allowed',
	            'last' => true ),
	        'loginRule-2' => array(
	            'rule' => array('minLength', 4),  
	            'message' => 'Minimum length of 4 characters' ),  
	        'loginRule-3' => array(
	        	'rule' => 'isUnique',
	        	'message' => 'Please use a unique username' )
	    ),
	    'password' => array(
	    	'rule' => array('minLength', 6),
	    	'message' => 'Password must be at least 6 characters long'),
	    'email' => array(
	    	'emailRule-1' => array(
	        	'rule' => array('email', true),
	        	'message' => 'Please supply a valid email address.'),
	    	'emailRule-2' => array(
	    		'rule' => 'isUnique',
	    		'message' => 'An account with this email address already exists')
	    )    
	);
	
	/**
	 * Creates an activeation hash for the current user
	 * 
	 * @param VOID
	 * @return String activation hash
	 */
	function getActivationHash() {
		if( !isset($this->id)) {
			return false;
		}
		
		return substr(Security::hash(Configure::read('Security.salt') . $this->field('created')), 0, 8);
	}


}
?>