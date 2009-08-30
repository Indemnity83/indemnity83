<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $components = array('Auth', 'Email');	
	
	function isAuthorized() {
		if (!parent::isAuthorized()) {
			return false;
		}
		if (in_array($this->action, array('register', 'login'))) {
			$this->Auth->authError = 'You are already logged in.';
			return false;
		}
		return true;
	}


	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('activate', 'register', 'registered');
		$this->Auth->autoRedirect = false;
	}
	
	//--------------------
	//-- Standard Views --
	//--------------------
		
	function login() {
		// Check for incomming login request
		if($this->data) {
			// Use the AuthComponent's login action
			if($this->Auth->login($this->data)) {
				// Retrieve user data
				$results = $this->User->find(array('User.username' => $this->data['User']['username']), array('User.active'), null, false);
				if($results['User']['active'] == 0) {
					$this->Session->setFlash('Your account has not been activated yet!');
					$this->Auth->logout();
					$this->redirect(array('action' => 'activate'));
				} else {
					$this->redirect('/');
				}
			}
		}
	}
	
	function logout(){
		$this->Session->setFlash('Logout');
		$this->redirect($this->Auth->logout());
	}	
	
	function register() {
		if (!empty($this->data)) {
			if ($this->data['User']['password'] == $this->Auth->password($this->data['User']['password_confirm'])) {
				$this->User->create();
				if($this->User->save($this->data)) {
					$this->__sendActivationEmail($this->User->getLastInsertID());
					$this->redirect(array('action' => 'registered'));	
				} else {
					$this->data['User']['password'] = null;
				}
			}
		}
	}
	
	function registered() {
		
	}
	
	function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}
	
	function view($id = null) {
		if (strtolower($id) == 'me') {
			$id = $this->Auth->user('id');
		}
		if (!$id) {
			$this->Session->setFlash(__('Invalid User.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}
	
	function activate($user_id = null, $in_hash = null) {
		$this->User->id = $user_id;
		if($this->User->exists() && ($in_hash == $this->User->getActivationHash())) {
			$this->User->saveField('active', 1);
			
			$this->Session->setFlash('Your account has been activated, please log in blow');
			$this->redirect(array('action' => 'login'));
		}
		
		
	}
	
	//-----------------
	//-- Admin Views --
	//-----------------
	
	function admin_login() {
		$this->login();
	}
	
		
	function admin_index() {
		$this->index();
	}
	
	function admin_view($id = null) {
		$this->view();
	}	

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid User', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The User has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
	}
	
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for User', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->del($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function admin_add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The User has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
			}
		}
	}
	
	//-----------------------
	//-- Private Functions --
	//-----------------------
	
	/**
	 * Send out an activation email to the user.id 
	 * 
	 * @param $user_id User to send activation email to
	 * @return boolean indicates success
	 */
	function __sendActivationEmail($user_id) {
		$user = $this->User->find(array('User.id' => $user_id));
		if($user === false) {
			debug(__METHOD__." failed to retrieve User data for user.id: {$user_id}");
			return false;
		}
		
		$this->set('activate_url', 'http://'.env('SERVER_NAME').'/users/activate/'.$user['User']['id'].'/'.$this->User->getActivationHash());
		$this->set('username', $this->data['User']['username']);
		
		debug('http://'.env('SERVER_NAME').'/users/activate/'.$user['User']['id'].'/'.$this->User->getActivationHash());
		
		$this->Email->to = $user['User']['email_address'];
		$this->Email->subject = 'Account confirmation for '.env('SERVER_NAME');
		$this->Email->from = 'noreply@'.env('SERVER_NAME');
		$this->Email->template = 'user_confirm';
		$this->Email->sendAs = 'text';
		
		return $this->Email->send();
	}
}
?>