<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_ReportManager
{

    /**
     * message for checked imported storages
     */
    CONST MESSAGE_CHECKED_IMPORTED_STORAGES = 'All imported storages where checked for the moment';

    /**
     * message for unchecked imported storages
     */
    CONST MESSAGE_UNCHECKED_IMPORTED_STORAGES = 'There are imported storages. Please check the client assignment';

    /**
     * action for checked imported storages
     */
    CONST ACTION_CHECKED_IMPORTED_STORAGES = ' checked imported storages';

    /**
     * action for unchecked imported storages
     */
    CONST ACTION_UNCHECKED_IMPORTED_STORAGES = ' unchecked imported storages';

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

        $values = $form->getValues();
        $values['imported'] = true;

        $storageManager = MazelabStorage_Model_DiFactory::getStorageManager();
        if(!($storageId = $storageManager->addStorage($values, false)) ||
                !($storage = $storageManager->getStorage($storageId))) {
            return false;
        }
        
        $storage->evalReport($data);
        $storage->removeCommands();
        
        return true;
    }

    /**
     * writes import log entry to display storages which needs to be checked
     *
     * @param Core_Model_ValueObject_Node $node node instance
     * @return boolean
     */
    protected function _writeUncheckedImportLog(Core_Model_ValueObject_Node $node)
    {
        if(!MazelabStorage_Model_DiFactory::getStorageManager()->countStoragesWithImportedFlag() === 0) {
            return false;
        }

        return Core_Model_DiFactory::getLogger()->setType(Core_Model_Logger::TYPE_CONFLICT)
            ->setMessage(self::MESSAGE_UNCHECKED_IMPORTED_STORAGES)
            ->setAction(self::ACTION_UNCHECKED_IMPORTED_STORAGES)
            ->setModuleRef(MazelabStorage_Model_StorageManager::MODULE_NAME)
            ->setRoute(array(), 'mazelab-storage_imports')
            ->setNodeRef($node->getId())
            ->saveByContext(self::ACTION_UNCHECKED_IMPORTED_STORAGES);
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
            $this->_writeUncheckedImportLog($node);
        }
        
        return true;
    }

    /**
     * writes import log entry to change import storage conflicts
     *
     * @return boolean
     */
    public function writeCheckedImportLog()
    {
        if(MazelabStorage_Model_DiFactory::getStorageManager()->countStoragesWithImportedFlag() > 0) {
            return false;
        }

        return Core_Model_DiFactory::getLogger()->setType(Core_Model_Logger::TYPE_NOTIFICATION)
            ->setMessage(self::MESSAGE_CHECKED_IMPORTED_STORAGES)
            ->setAction(self::ACTION_CHECKED_IMPORTED_STORAGES)
            ->saveByContext(self::ACTION_UNCHECKED_IMPORTED_STORAGES, Core_Model_Logger::TYPE_CONFLICT,
                self::ACTION_UNCHECKED_IMPORTED_STORAGES);
    }
    
}
