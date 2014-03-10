<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Form_Import extends Zend_Form
{
    
    public function init()
    {
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
        
        $this->addElement('select', 'status', array(
            "label" => "The storage is",
            "multiOptions" => array(
                "0" => "enabled",
                "1" => "disabled"
            ),
            "value" => array(1)
        ));
        
        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'required' => true
        ));
        
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
    
}
