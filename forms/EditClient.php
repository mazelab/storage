<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Form_EditClient extends Zend_Form
{
    
    protected $_elementDecorators = array(
        'ViewHelper'
    );

    public function init()
    {
        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'required' => true,
            'validators' => array(
                array('StringLength', NULL, array(4)),
                array('identical', true, array('confirmPassword'))
            )
        ));
        $this->addElement('password', 'confirmPassword', array(
            'label' => 'Confirm password',
            'required' => true,
            'ignore' => true,
            'validators' => array(
                array('identical', true, array('password'))
            )
        ));
    }
    
}
