<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Form_Add extends Zend_Form
{
    
    protected $_elementDecorators = array(
        'ViewHelper',
        'TwitterBootstrapError'
    );

    public function init()
    {
        $this->addPrefixPath('MazeLib_Form_Decorator_', 'MazeLib/Form/Decorator/', 'decorator');
        
        $this->addElement('text', 'name', array(
            'label' => 'Name',
            'required' => true,
            'validators' => array(
                new MazelabStorage_Form_Validate_StorageName(),
                array('regex', false, array(
                    'pattern' => '/^[a-z_0-9].*$/',
                    'messages' => array(
                        'regexNotMatch' => 'Only lowercase letters, digits and _ are allowed'
                    )
                ))
            )
        ));
        
        $this->addElement('text', 'quota', array(
            'label' => 'Quota in MB',
            'required' => true,
            'validators' => array(
                'digits',
                array('greaterThan', false, array(0))
            ),
        ));
        
        $this->addElement('select', 'nodeId', array(
            "label" => "On node",
            "multiOptions" => array(
                "" => "unassigned"
            ),
            "value" => array("")
        ));
        
        $this->addElement('select', 'clientId', array(
            "label" => "Of client",
            "multiOptions" => array(
                "" => "unassigned"
            ),
            "value" => array("")
        ));
        
        $this->addElement('select', 'status', array(
            "label" => "The storage is",
            "multiOptions" => array(
                "0" => "deactivated",
                "1" => "activated"
            ),
            "value" => array(1)
        ));
        
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
        
        $this->setElementDecorators($this->_elementDecorators);
        
        $this->initClientSelect();
        $this->initNodeSelect();
    }
    
    /**
     * add available nodes to node select element
     */
    public function initNodeSelect()
    {
        foreach(MazelabStorage_Model_DiFactory::getNodeManager()->getNodes() as $id => $node) { 
            $this->getElement('nodeId')->addMultiOption($id, $node->getName());
        }
        
        return $this;
    }
    
    /**
     * add available clients to client select element
     */
    public function initClientSelect()
    {
        foreach(MazelabStorage_Model_DiFactory::getClientManager()->getClients() as $id => $client) { 
            $this->getElement('clientId')->addMultiOption($id, $client->getLabel());
        }
        
        return $this;
    }
    
}
