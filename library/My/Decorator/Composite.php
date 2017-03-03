<?php 
class My_Decorator_Composite extends Zend_Form_Decorator_Abstract
{
	public function __construct($options = null)
	{
		if (isset($options['wrapperClass'])) {
			$this->wrapperClass = $options['wrapperClass'];
		} else {
			$this->wrapperClass = 'form-row';
		}
		
	}
    public function buildLabel()
    {
        $element = $this->getElement();
        $label = $element->getLabel();
		$attribs = array();

        if ($translator = $element->getTranslator()) {
            $label = $translator->translate($label);
        }
        if ($element->isRequired()) {
            $attribs['class'] = 'required';
        }
        // $label .= ' :';
        return $element->getView()
                       ->formLabel($element->getName(), $label, $attribs);
    }

    public function buildInput()
    {
        $element = $this->getElement();
        $helper  = $element->helper;
		unset($element->helper);
        return $element->getView()->$helper(
            $element->getName(),
            $element->getValue(),
            $element->getAttribs(),
            $element->options
        );
    }

    public function buildErrors()
    {
        $element  = $this->getElement();
        $messages = $element->getMessages();
        if (empty($messages)) {
            return '';
        }
        return $element->getView()->formErrors($messages);
    }

    public function buildDescription()
    {
        $element = $this->getElement();
        $desc    = $element->getDescription();
        if (empty($desc)) {
            return '';
        }
        return '<div class="description">' . $desc . '</div>';
    }

    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }
        if (null === $element->getView()) {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $label     = $this->buildLabel();
        $input     = $this->buildInput();
        $errors    = $this->buildErrors();
        $desc      = $this->buildDescription();

        $output = '<div class="' . $this->wrapperClass . '">'
                . $label
				// . '<br />'
                . $input
                . $errors
                . $desc
                . '</div>';

        switch ($placement) {
            case (self::PREPEND):
                return $output . $separator . $content;
            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }
    }
}
