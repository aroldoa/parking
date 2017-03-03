<?php 
/**
* 
*/
class My_Decorator_Submit extends Zend_Form_Decorator_Abstract
{
	public function buildInput()
    {
        $element = $this->getElement();
        $helper  = $element->helper;
		unset($element->helper);
        return $element->getView()->$helper(
            $element->getName(),
            $element->getLabel(),
            $element->getAttribs(),
            $element->options
        );
    }

	public function render($content)
	{
		$input = $this->buildInput();
		$output = '<div class="form-submit">'
				. $input
				. '</div>';
				
		return $output;
	}
}
