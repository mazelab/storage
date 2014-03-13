<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Form_EditImport extends Zend_Form
{

    protected $_elementDecorators = array(
        'ViewHelper'
    );

    public function init()
    {
        $this->setElementDecorators($this->_elementDecorators);

        $this->addElement('text', 'storageId', array(
            'required' => true
        ));
        
        $this->addElement('select', 'clientId', array(
            "label" => "Client",
            "multiOptions" => array(
                "" => "unassigned"
            ),
            "value" => array("")
        ));

        $this->initClientSelect();
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
