<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_ReportManager
{
    
    /**
     * import given storage data from report
     * 
     * @param array $data contains storage attributes
     * @return boolean
     */
    protected function _importStorage($data)
    {
        $form = new MazelabStorage_Form_Import();
        $form->initNodeSelect();
        
        if (array_key_exists('status', $data) && $data['status'] === 'enabled') {
            $data['status'] = true;
        } elseif (array_key_exists('status', $data)) {
            $data['status'] = false;
        }
        
        if(!$form->isValid($data)) {
            return false;
        }

        $storageManager = MazelabStorage_Model_DiFactory::getStorageManager();
        if(!($storageId = $storageManager->addStorage($form->getValues(), false)) ||
                !($storage = $storageManager->getStorage($storageId))) {
            return false;
        }
        
        $storage->evalReport($data);
        $storage->removeCommands();
        
        return true;
    }
    
    /**
     * process report of a certain node
     * 
     * @param string $nodeId
     * @param string $report
     * @return boolean
     */
    public function reportNode($nodeId, $report)
    {
        if(!($node = Core_Model_DiFactory::getNodeManager()->getNode($nodeId)) ||
                !$node->hasService(MazelabStorage_Model_StorageManager::MODULE_NAME)) {
            return false;
        }
        
        if(!($reportedStorages = json_decode($report, true))) {
            $reportedStorages = array();
        }
        
        $unknownStorages = $reportedStorages;
        foreach(MazelabStorage_Model_DiFactory::getStorageManager()->getStoragesByNode($nodeId) as $storage) {
            $storageName = $storage->getName();
            if(array_key_exists($storageName, $reportedStorages)) {
                unset($unknownStorages[$storageName]);
            } else {
                $reportedStorages[$storageName] = array();
            }
            
            $storage->evalReport($reportedStorages[$storageName]);
        }
        
        if($unknownStorages) {
            foreach($unknownStorages as $storageName => $storage) {
                $storage['nodeId'] = $nodeId;
                $storage['name'] = $storageName;
                $this->_importStorage($storage);
            }
        }
        
        return true;
    }
    
}
