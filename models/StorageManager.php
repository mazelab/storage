<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_StorageManager
{
    
    /**
     * @var string name of this module
     */
    CONST MODULE_NAME = 'storage';
    
    /**
     * contains initialized storages
     * 
     * @var array|null
     */
    protected $_storages = array();
    
    /**
     * get storage provider
     * 
     * @return MazelabStorage_Model_Dataprovider_Interface_Storage
     */
    protected function _getProvider()
    {
        return MazelabStorage_Model_Dataprovider_DiFactory::getStorage();
    }
    
    /**
     * loads and registers a certain storage instance
     * 
     * @param string $storageId id of storage
     * @param string $clientId only of this client
     * 
     * @return boolean
     */
    protected function _loadStorage($storageId, $clientId = null)
    {
        if(!($data = $this->_getProvider()->getStorage($storageId, $clientId))) {
            return false;
        }
        
        return $this->registerStorage($storageId, $data);
    }

    /**
     * assign certain storage to a certain node
     * 
     * @param string $storageId
     * @param string $nodeId
     * @return boolean
     */
    public function assignToNode($storageId, $nodeId = null)
    {
        if(!($storage = $this->getStorage($storageId))) {
            return false;
        }

        if(!$nodeId) {
            return $this->unassignNode($storageId);
        }
        
        if($nodeId && (!($node = Core_Model_DiFactory::getNodeManager()->getNode($nodeId)) ||
                !$node->hasService(self::MODULE_NAME))) {
            return false;
        }
        
        if(($actualNode = $storage->getNode()) 
                && $actualNode->getId() == $nodeId) {
            return true;
        }

        if($actualNode && !$this->unassignNode($storageId) ||
                !$storage->setData(array('nodeId' => $nodeId))->save()) {
            return false;
        }
        
        $storage->apply();
        
        return true;
    }
    
    /**
     * adds new storage with given data
     * 
     * @param array $data
     * @param boolean $encryptPassword
     * @return string|false storage id
     */
    public function addStorage(array $data, $encryptPassword = true)
    {
        if(array_key_exists('nodeId', $data) && $data['nodeId'] &&
                !Core_Model_DiFactory::getNodeManager()->getNode($data['nodeId'])) {
            return false;
        }
        if(array_key_exists('clientId', $data) && $data['clientId'] &&
                !Core_Model_DiFactory::getClientManager()->getClient($data['clientId'])){
            return false;
        }
        
        $storage = MazelabStorage_Model_DiFactory::newStorage();
        if(!$storage->setLoaded(true)->setData($data, $encryptPassword)->save()) {
            return false;
        }
        
        $storage->apply();

        return $storage->getId();
    }
    
    /**
     * deletes storage in maze
     * 
     * @param string $storageId
     * @return boolean
     */
    public function deleteStorage($storageId)
    {
        if(!($storage = $this->getStorage($storageId))) {
            return false;
        }
        
        return $this->_getProvider()->deleteStorage($storageId);
    }
    
    /**
     * disables given storage
     * 
     * @param string $storageId
     * @return boolean
     */
    public function disableStorage($storageId)
    {
        if(!($storage = $this->getStorage($storageId))) {
            return false;
        }
        
        return $storage->disable();
    }
    
    /**
     * enables given storage
     * 
     * @param string $storageId
     * @return boolean
     */
    public function enableStorage($storageId)
    {
        if(!($storage = $this->getStorage($storageId))) {
            return false;
        }
        
        return $storage->enable();
    }
    
    /**
     * flags storage to be deleted
     * 
     * if neseccary wait for node response first
     * 
     * @param string $storageId
     * @return boolean
     */
    public function flagDelete($storageId)
    {
        if(!($storage = $this->getStorage($storageId))) {
            return false;
        }
        
        if(!$storage->getNode()) { 
            return $this->deleteStorage($storageId);
        }
        
        return $storage->setDeleteFlag();
    }
    
    /**
     * gets a certain storage
     * 
     * @param string $storageId id of the storage
     * @param string $clientId only of this client
     * 
     * @return MazelabStorage_Model_ValueObject_Storage|null
     */
    public function getStorage($storageId, $clientId = null)
    {
        if(!$storageId) {
            return null;
        }
        
        if(isset($this->_storages[$storageId])) {
            if($clientId && $this->_storages[$storageId]
                    ->getData('clientId') === $clientId) {
                return null;
            }
            
            return $this->_storages[$storageId];
        }
        
        if(!$this->_loadStorage($storageId, $clientId) ||
                !isset($this->_storages[$storageId])) {
            return null;
        }
        
        return $this->_storages[$storageId];
    }
    
    /**
     * gets a certain storage
     * 
     * @param string $storageId id of the storage
     * @param string $clientId ownly of this client
     * 
     * @return array
     */
    public function getStorageAsArray($storageId, $clientId = null)
    {
        if(!($storage = $this->getStorage($storageId, $clientId))) {
            return array();
        }
        
        return $storage->getData();
    }    
    
    /**
     * gets all storages of a certain client
     * 
     * @param string $clientId
     * 
     * @return array contains MazelabStorage_Model_ValueObject_Storage
     */
    public function getStoragesByClient($clientId)
    {
        if(!$clientId) {
            return null;
        }
        
        $storages = array();
        foreach($this->_getProvider()->getStoragesByClient($clientId) as $storageId => $storage) {
            if(!array_key_exists($storageId, $this->_storages) &&
                    $this->registerStorage($storageId, $storage)) {
                $storages[$storageId] = $this->_storages[$storageId];
            }
        }
        
        return $storages;
    }
    
    /**
     * gets all storages of a certain client as array
     * 
     * @param string $clientId
     * 
     * @return array
     */
    public function getStoragesByClientAsArray($clientId)
    {
        $result = array();
        foreach($this->getStoragesByClient($clientId) as $id => $storage) {
            $result[$id] = $storage->getData();
        }
        
        return $result;
    }
    
    /**
     * get storage instance by name
     * 
     * @param string $name name of the storage
     * @return MazelabStorage_Model_ValueObject_Storage|null
     */
    public function getStoragesByName($name)
    {
        if(!$name || !($storage = $this->_getProvider()->getStorageByName($name))) {
            return null;
        }
        if(array_key_exists('_id', $storage) && !($id = $storage['_id'])) {
            return null;
        }
        
        $this->registerStorage($id, $storage);
        return $this->getStorage($id);
    }
    
    /**
     * gets all storages of a certain node
     * 
     * @param string $nodeId
     * 
     * @return array contains MazelabStorage_Model_ValueObject_Storage
     */
    public function getStoragesByNode($nodeId)
    {
        if(!$nodeId) {
            return null;
        }
        
        $storages = array();
        foreach($this->_getProvider()->getStoragesByNode($nodeId) as $storageId => $storage) {
            if(!array_key_exists($storageId, $this->_storages) &&
                    $this->registerStorage($storageId, $storage)) {
                $storages[$storageId] = $this->_storages[$storageId];
            }
        }
        
        return $storages;
    }
    
    /**
     * registers a storage instance
     * 
     * overwrites existing instances
     * 
     * @param string $storageId
     * @param array|MazelabStorage_Model_ValueObject_Storage $context
     * @param boolean $setLoadedFlag only when $context is array states if
     * loading flag will be set to avoid double loading
     * @return boolean
     */
    public function registerStorage($storageId, $context, $setLoadedFlag = true)
    {
        $storage = null;
        
        if($context instanceof MazelabStorage_Model_ValueObject_Storage) {
            $storage = $context;
        } elseif (is_array($context)) {
            $storage = MazelabStorage_Model_DiFactory::newStorage($storageId);
            
            if($setLoadedFlag) {
                $storage->setLoaded(true);
            }
            
            $storage->getBean()->setBean($context);
        }
        
        if(!$storage) {
            return false;
        }
        
        $this->_storages[$storageId] = $storage;
        
        return true;
    }

    /**
     * unassign node of a certain storage
     * 
     * @param string $storageId
     * @return boolean
     */
    public function unassignNode($storageId)
    {
        if(!($storage = $this->getStorage($storageId))) {
            return false;
        }
        
        if(!$storage->getData('nodeId')) {
            return true;
        }

        return $storage->unsetProperty('nodeId')->unsetProperty('nodeName')->save();
    }
    
    /**
     * updates a certain stroage with the given data
     * 
     * @param string $storageId
     * @param array $data
     * @return boolean
     */
    public function updateStorage($storageId, array $data)
    {
        if (!($storage = $this->getStorage($storageId))) {
            return false;
        }
        
        if(array_key_exists('confirmPassword', $data)) {
            unset($data['confirmPassword']);
        }
        
        if(array_key_exists('nodeId', $data)) {
            if(!$this->assignToNode($storageId, $data['nodeId'])) {
                return false;
            }
            unset($data['nodeId']);
        }
        
        if(!$storage->setData($data)->save()) {
            return false;
        }
        
        $storage->apply(true);
        
        return true;
    }

}