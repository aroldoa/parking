<?php
/**
* 
*/
class My_Form_Abstract extends Zend_Form
{
	protected $_model;
	
	public function init()
	{
		$this->setDecorators(array(
			'Description',
	        'FormElements',
	        array('Form', array('class' => 'echo-form'))
	    ));
	
		// for all elements:
		$this->addElementPrefixPath(
			'My_Decorator',
			'My/Decorator/',
			'decorator'
		);
		
		// add path to custom validators & filters
        $this->addElementPrefixPath(
            'Form_Validate',
            APPLICATION_PATH . '/forms/validate/',
            'validate'
        );

		$this->addElementPrefixPath(
            'Form_Filter',
            APPLICATION_PATH . '/forms/filter/',
            'filter'
        );
	}
	
	public function setCustomDecorators()
	{
		foreach ($this->getElements() as $element) {
			if ('Zend_Form_Element_Submit' == $element->getType()) {
				
				$element->setDecorators(array('Submit'));
				
			} else if ('Zend_Form_Element_Hidden' == $element->getType()){
				
				$element->setDecorators(array('ViewHelper'));
				
			} else {
				
				$element->setDecorators(array('Composite'));
				
			}
		}
	}
	
	public function setCustomDisplayGroupDecorators($special = null)
	{
		if ('button' == $special) {
			return array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'class' => 'button-group')));
		}
		
		return array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'class' => 'form-group')));
	}

    /**
     * Model setter
     * 
     * @param My_Model_Interface $model 
     */
    public function setModel(My_Model_Interface $model)
    {
        $this->_model = $model;
    }

    /**
     * Model Getter
     * 
     * @return My_Model_Interface 
     */
    public function getModel()
    {
        return $this->_model;
    }
}
