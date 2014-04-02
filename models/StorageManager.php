<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_StorageManager
{

    /**
     * message when storage was successfully created
     */
    CONST MESSAGE_STORAGE_CREATED = 'Storage %1$s was created';

    /**
     * message when storage was disabled
     */
    CONST MESSAGE_STORAGE_DISABLED = 'Storage %1$s was disabled';

    /**
     * message when storage was deleted
     */
    CONST MESSAGE_STORAGE_DELETED = 'Storage %1$s was deleted';

    /**
     * message when storage should be removed from nodes and then to be deleted
     */
    CONST MESSAGE_STORAGE_DELETE_MARKED = 'Storage %1$s has been marked to be deleted';

    /**
     * message when storage was enabled
     */
    CONST MESSAGE_STORAGE_ENABLED = 'Storage %1$s was enabled';

    /**
     * message when storage was assigned to a node
     */
    CONST MESSAGE_STORAGE_NODE_ASSIGNED = 'Storage %1$s was assigned to node %2$s';

    /**
     * message when node was unassigned
     */
    CONST MESSAGE_STORAGE_NODE_UNASSIGNED = 'Storage %1$s was unassigned from node %2$s';

    /**
     * message when storage was updated
     */
    CONST MESSAGE_STORAGE_UPDATE = 'Storage %1$s was updated';

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
     * get Logger instance
     *
     * @return Core_Model_Logger
     */
    protected function _getLogger()
    {
        return Core_Model_DiFactory::getLogger();
    }

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
     * sets standard storage log params and saves it
     *
     * other log params should be set to _getLogger() before calling this method
     *
     * @param MazelabStorage_Model_ValueObject_Storage $storage storage instance
     * @param string $type log type definition
     * @param boolean $client message can also be viewed by the client
     * @return string|null returns log id on success
     */
    protected function _logStorage(MazelabStorage_Model_ValueObject_Storage $storage, $type = Core_Model_Logger::TYPE_NOTIFICATION, $client = false)
    {
        if($client) {
            $this->_getLogger()->setClientRef($storage->getData('clientId'));
        }

        $this->_getLogger()->setType($type)->setNodeRef($storage->getData('nodeId'))
            ->setModuleRef(self::MODULE_NAME);

        return $this->_getLogger()->save();
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

        $this->_getLogger()->setMessage(self::MESSAGE_STORAGE_NODE_ASSIGNED)
            ->setMessageVars($storage->getName(), $storage->getData('nodeName'));
        $this->_logStorage($storage, Core_Model_Logger::TYPE_WARNING);

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
        if(!$storage->setLoaded(true)->setData($data, $encryptPassword)->setFlags()->save()) {
            return false;
        }
        
        $storage->apply();

        $this->_getLogger()->setMessage(self::MESSAGE_STORAGE_CREATED)->setData($storage->getData())
            ->setMessageVars($storage->getName());
        $this->_logStorage($storage);

        MazelabStorage_Model_DiFactory::getIndexManager()->setStorage($storage->getId());

        return $storage->getId();
    }

    /**
     * count all storages with imported flag
     *
     * @return int
     */
    public function countStoragesWithImportedFlag()
    {
        if(!($count = $this->_getProvider()->countStoragesWithImportedFlag()) || !is_int($count)) {
            return 0;
        }

        return $count;
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

        if(!$this->_getProvider()->deleteStorage($storageId)) {

        }

        if(array_key_exists($storageId, $this->_storages)) {
            unset($this->_storages[$storageId]);
        }

        $this->_getLogger()->setMessage(self::MESSAGE_STORAGE_DELETED)->setMessageVars($storage->getName());
        $this->_logStorage($storage);

        MazelabStorage_Model_DiFactory::getIndexManager()->unsetStorage($storageId);

        return true;
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

        if(!$storage->disable()) {
            return false;
        }

        $this->_getLogger()->setMessage(self::MESSAGE_STORAGE_DISABLED)->setMessageVars($storage->getName());
        $this->_logStorage($storage);
        
        return true;
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

        if(!$storage->enable()) {
            return false;
        }

        $this->_getLogger()->setMessage(self::MESSAGE_STORAGE_ENABLED)->setMessageVars($storage->getName());
        $this->_logStorage($storage);

        return true;
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

        if(!$storage->setDeleteFlag()) {
            return false;
        }

        $this->_getLogger()->setMessage(self::MESSAGE_STORAGE_DELETE_MARKED)->setMessageVars($storage->getName());
        $this->_logStorage($storage);

        if($storage->getData('imported')) {
            MazelabStorage_Model_DiFactory::getReportManager()->writeCheckedImportLog();
        }

        return true;
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
     * get all storage instances
     *
     * @return array contains MazelabStorage_Model_ValueObject_Storage
     */
    public function getStorages()
    {
        foreach($this->_getProvider()->getStorages() as $storageId => $storage) {
            if(!array_key_exists($storageId, $this->_storages)) {
                $this->registerStorage($storageId, $storage);
            }
        }

        return $this->_storages;
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
            if(!array_key_exists($storageId, $this->_storages) && !$this->registerStorage($storageId, $storage)) {
                continue;
            }
            $storages[$storageId] = $this->_storages[$storageId];
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
    public function getStorageByName($name)
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
            if(!array_key_exists($storageId, $this->_storages) && !$this->registerStorage($storageId, $storage)) {
                continue;
            }
            $storages[$storageId] = $this->_storages[$storageId];
        }
        
        return $storages;
    }

    /**
     * imports a certain storage with the given client assignment
     *
     * @param string $storageId
     * @param string $clientId
     * @return bool
     */
    public function importStorage($storageId, $clientId)
    {
        if(!($storage = $this->getStorage($storageId))) {
            return false;
        }

        $storage->setData(array('clientId' => $clientId))->apply();
        if(!$storage->unsetProperty('imported')->setFlags()->save()) {
            return false;
        }

        MazelabStorage_Model_DiFactory::getReportManager()->writeCheckedImportLog();

        return true;
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
     * resolve a certain conflicted storage with given data
     *
     * @param string $storageId
     * @param array $data
     * @return bool
     */
    public function resolveStorage($storageId, array $data)
    {
        if(!($storage = $this->getStorage($storageId))) {
            return false;
        }

        if(!$storage->setRemoteData($data)->setData($data)->setFlags()->save()) {
            return false;
        }

        $storage->apply();

        return $storage->resolveDiff();
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

        $oldNodeName = $storage->getData('nodeName');
        if(!$storage->unsetProperty('nodeId')->unsetProperty('nodeName')->save()) {
            return false;
        }

        $this->_getLogger()->setMessage(self::MESSAGE_STORAGE_NODE_UNASSIGNED)->setMessageVars($storage->getName(), $oldNodeName);
        $this->_logStorage($storage, Core_Model_Logger::TYPE_WARNING);

        return true;
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

        $this->_getLogger()->setMessage(self::MESSAGE_STORAGE_UPDATE)->setMessageVars($storage->getName())->setData($data);
        $this->_logStorage($storage);
        
        return true;
    }

}