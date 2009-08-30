<?php
/* SVN FILE: $Id: app_controller.php 7945 2008-12-19 02:16:01Z gwoo $ */
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision: 7945 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2008-12-18 18:16:01 -0800 (Thu, 18 Dec 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 */
class AppController extends Controller {
	
	var $components = array('Auth');	
	
	function beforeFilter() {		
		//-- Various settings
		$this->Auth->loginError = 'No username and password was found with that combination.';
		$this->Auth->logoutRedirect = '/';	
		$this->Auth->allow('display');
		$this->Auth->authorize = 'controller';		
	}
	
	function beforeRender() {
    	$this->set('user', $this->Auth->user());
	}
	
	function isAuthorized() {
		// Check if the action is an admin function, and that the
		// user is an admin, if not return false. 
		if (!(strpos($this->action, "admin_") === false))
	    {
	        if ($this->Auth->user('admin') == '0')
	        {
	                return false;
	        }
	    }
	    return true;
    }
	
	
}
?>