<?php
/**
* 
*/
class Model_User extends My_Model_Abstract
{
	/**
     * Get User by their id
     * 
     * @param  int $id
     * @return null|Resource_User_Item 
     */
    public function getUserById($id)
    {
        $id = (int) $id;
        return $this->getResource('User')->getUserById($id);
    }

    /**
     * Get User by their email address
     *
     * @param  string $email The email address to search for
     * @param  Resource_User_Item $ignoreUser User to ignore from the search
     * @return null|Resource_User_Item 
     */
    public function getUserByEmail($email, $ignoreUser=null)
    {
        return $this->getResource('User')->getUserByEmail($email, $ignoreUser);
    }
    
	
	public function getUserByUsername($username = null)
	{
		if ($username === null) {
			return false;
		}
		
		return $this->getResource('User')->getUserByUsername($username);
	}
	
    /**
     * Get all Users
     * 
     * @param  boolean $paged Return paginator?
     * @param  array   $order The order fields
     * @return Zend_Db_Table_Rowset
     */
    public function getUsers($options = array())
    {
        return $this->getResource('User')->getUsers($options);
    }
	
    /**
     * Get all Users
     * 
     * @param  boolean $paged Return paginator?
     * @param  array   $order The order fields
     * @return Zend_Db_Table_Rowset
     */
    public function getUserData($page, $recordPerPage)
    {
        return $this->getResource('User')->getUserData($page, $recordPerPage);
    }
	
	/**
     * Register a new user
     * 
     * @param array $post
     * @return false|int 
     */
    public function registerUser($post)
    {
        if (!$this->checkAcl('register')) {
            throw new SF_Acl_Exception("Insufficient rights");
        }
        
        $form = $this->getForm('userRegister');
        return $this->save($form, $post, array('role' => 'Customer'));
    }

    /**
     * Update a user
     * 
     * @param  array  $post The data
     * @param  string $validator Which validation chain to use
     * @return false|int
     */
    public function saveUser($data = null)
    {
		if ($data === null) {
			return false;
		}
		
		$user = array_key_exists('id', $data) ? $this->getUserById($data['id']) : null;
	
		if ($user === null) {
			unset($data['id']);
		}
		// $this->getCached()->getCache()->clean();
		return $this->getResource('User')->saveUser($data, $user);
    }
    
    /**
     * Save the data to db
     *
     * @param  Zend_Form $form The Validator
     * @param  array     $info The data
     * @param  array     $defaults Default values
     * @return false|int 
     */
    protected function save($form, $info, $defaults=array())
    {
        if (!$form->isValid($info)) {
            return false;
        }

        // get filtered values
        $data = $form->getValues();

        // password hashing
        if (array_key_exists('passwd', $data) && '' != $data['passwd']) {
            $data['salt'] = md5($this->createSalt());
            $data['passwd'] = sha1($data['passwd'] . $data['salt']);
        } else {
            unset($data['passwd']);
        }

        // apply any defaults
        foreach ($defaults as $col => $value) {
            $data[$col] = $value;
        }

        $user = array_key_exists('userId', $data) ?
            $this->getResource('User')->getUserById($data['userId']) : null;

        return $this->getResource('User')->saveRow($data, $user);
    }

    /**
     * Delete a user
     *
     * @param int|Resource_User_Item_Interface $user
     * @return boolean
     */
    public function deleteUser($user)
    {
		
        if ($user instanceof Resource_User_Item) {
            $userId = (int) $user->id;
        } else {
            $userId = (int) $user;
        }
        
        $user = $this->getUserById($userId);

        if (null === $user) {
            return false;
        }

		return $this->getResource('User')->deleteUser($user);
    }

    /**
     * Implement the Zend_Acl_Resource_Interface, make this model
     * an acl resource
     * 
     * @return string The resource id 
     */
    public function getResourceId()
    {
        return 'User';
    }

    /**
     * Injector for the acl, the acl can be injected either directly
     * via this method or by passing the 'acl' option to the models
     * construct.
     *
     * We add all the access rule for this resource here, so we
     * add $this as the resource, plus its rules.
     * 
     * @param SF_Acl_Interface $acl
     * @return SF_Model_Abstract
     */
    public function setAcl(SF_Acl_Interface $acl)
    {
        if (!$acl->has($this->getResourceId())) {
            $acl->add($this)
                ->allow('Guest', $this, array('register'))
                ->allow('Customer', $this, array('saveUser'))
                ->allow('Admin', $this);
        }
        $this->_acl = $acl;
        return $this;
    }

    /**
     * Get the acl and automatically instantiate the default acl if one
     * has not been injected.
     * 
     * @return Zend_Acl
     */
    public function getAcl()
    {
        if (null === $this->_acl) {
            $this->setAcl(new Model_Acl_Storefront());
        }
        return $this->_acl;
    }

    public function createPassword() 
	{
		$password = '';

		   for ($x = 1; $x <= 8; $x++) {
		      switch ( rand(1, 3) ) {

		      //  Add a random digit, 0-9
		      case 1:
		      $password .= rand(0, 9);
		      break;

		      //  Add a random upper-case letter
		      case 2:
		      $password .= chr( rand(65, 90) );
		      break;

		      //  Add a random lower-case letter
		      case 3:
		      $password  .= chr( rand(97, 122) );
		      break;
		      }
		   }

		   return $password;
	}

    /**
     * Create the salt string
     * 
     * @return string 
     */
    private function createSalt()
    {
        $salt = '';
        for ($i = 0; $i < 50; $i++) {
            $salt .= chr(rand(33, 126));
        }
        return $salt;
    }

	public function updateLogin($user)
	{
		if (!$user instanceof Resource_User_Item) {
			
			return false;
		}
		
		return $this->getResource('User')->updateLogin($user);
	}
}
