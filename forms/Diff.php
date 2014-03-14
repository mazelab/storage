<?php

class MazelabStorage_Form_Diff extends Zend_Form
{
    
    protected $_elementDecorators = array(
        'ViewHelper',
        'TwitterBootstrapError'
    );

    public function init()
    {
        $formLocal = new Zend_Form();
        $formLocal->addPrefixPath('MazeLib_Form_Decorator_', 'MazeLib/Form/Decorator/', 'decorator');
        $formLocal->setElementDecorators($this->_elementDecorators);

        $formRemote = new Zend_Form();
        $formRemote->addPrefixPath('MazeLib_Form_Decorator_', 'MazeLib/Form/Decorator/', 'decorator');
        $formRemote->setElementDecorators($this->_elementDecorators);

        $optionsStatus = array(
            "label" => "The storage is",
            "multiOptions" => array(
                "" => "disabled",
                "1" => "enabled"
            )
        );
        $optionsQuota = array(
            'label' => 'Quota in MB',
            'required' => true,
            'validators' => array(
                'digits',
                array('greaterThan', false, array(0))
            ),
        );

        $formLocal->addElement('select', 'status', $optionsStatus)->addElement('text', 'quota', $optionsQuota);
        $formLocal->setElementsBelongTo('local');
        $this->addSubForm($formLocal, 'local');

        $formRemote->addElement('select', 'status', $optionsStatus)->addElement('text', 'quota', $optionsQuota);
        $formRemote->setElementsBelongTo('remote');
        $this->addSubForm($formRemote, 'remote');
    }
    
}
