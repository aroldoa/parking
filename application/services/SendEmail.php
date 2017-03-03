<?php
/**
* 
*/
class Service_SendEmail
{
	protected $_mail;
	protected $_config;
	protected $_view;
	protected $_toAdmin = false;
	protected $_template;
	
	public function __construct()
	{
		$this->_mail = new Zend_Mail();
		$this->_config = Zend_Registry::get('config');
		$this->_view = new Zend_View();
		$this->_view->addScriptPath(APPLICATION_PATH . '/views/scripts/email-templates/');
	}
	
	public function process($data, $template) 
	{
		//create smtp config
		/*$config = array('auth' => 'login',
				'ssl' => 'tls',
                'username' => 'mario@missiongastro.com',
                'password' => 'Primomotif2!',
                'port'     => 587);*/
		
		$config = array('auth' => 'login',
								'ssl' => 'tls',
                'username' => 'orders@lighthouseparking.org',
                'password' => 'Primomotif2!',
                'port'     => 587);

		$transport = new Zend_Mail_Transport_Smtp('7wvr-fzqk.accessdomain.com', $config);

		// $template = $this->getTemplatePath($template);
		
		// assign data to the view
		$this->_view->data = $data;
		
		// get message body from template
		$body = $this->_getBody($template);
		
		// seperate template into sub/body
		list($subject, $body) = preg_split('/\r|\n/', $body, 2);
		
		// default is to send email to site admin, 
		// otherwise to user from admin
		if ($this->_toAdmin) {
			// get to info from config
			$this->_mail->addTo($this->_config['site']['contact']['email'], 
				$this->_config['site']['contact']['name']);

	        $this->_mail->setFrom($data['user']->email, $data['user']->fullname);
		} else {
			// set to user, from admin
			$this->_mail->addTo($data['user']->email, $data['user']->fullname);
			// $this->_mail->addBcc($this->_config['site']['contact']['email'], 
				// $this->_config['site']['contact']['name']);
			$this->_mail->setFrom($this->_config['site']['contact']['email'], 
				$this->_config['site']['contact']['name']);
		}
		

        // set the subject and body and send the mail
        $this->_mail->setSubject(trim($subject));
        $this->_mail->setBodyHtml(trim($body));
		$this->_mail->setBodyText(trim(strip_tags($body)));

        $this->_mail->send($transport);

		return true;
	}
	
	protected function _getBody($template) 
	{
		return $this->_view->render($template);
	}
	
	public function setToAdmin()
	{
		$this->_toAdmin = true;
		return $this;
	}
	
	public function getTemplatePath($template)
	{
		if (!$this->_template) {
			$this->_template = $this->_config['site']['email']['templateDirectory'] . $template;
		}
		
		return $this->_template;
	}
}
