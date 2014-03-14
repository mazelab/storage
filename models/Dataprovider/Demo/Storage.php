<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Dataprovider_Demo_Storage 
    extends MazelabStorage_Model_Dataprovider_Demo_Data
    implements MazelabStorage_Model_Dataprovider_Interface_Storage
{
    
    CONST COLLECTION_NAME = 'storages';
    
    /**
     * gets all storages
     * 
     * @return array
     */
    protected function _getStorages()
    {
        return $this->_getCollection(self::COLLECTION_NAME);
    }

    /**
     * counts storages with imported flag
     *
     * @return int
     */
    public function countStoragesWithImportedFlag()
    {
        $result = array();

        foreach($this->_getCollection(self::COLLECTION_NAME) as $storageId => $storage) {
            if(array_key_exists('imported', $storage) && $storage['imported'] === true) {
                $result[$storageId] = $storage;
            }
        }

        return count($result);
    }

    /**
     * deletes a certain storage
     * 
     * @param string $storageId
     * @return boolean
     */
    public function deleteStorage($storageId)
    {
        $storages = $this->_getStorages();
        if(!array_key_exists($storageId, $storages)) {
            return true;
        }
        
        unset($storages[$storageId]);
        $this->_setCollection(self::COLLECTION_NAME, $storages);
        
        return true;
    }

    /**
     * drops the storage collection
     *
     * @return boolean
     */
    public function drop()
    {
        $this->_setCollection(self::COLLECTION_NAME, array());
    }
    
    /**
     * get a certain storage by id and optional clientId
     * 
     * @param string $id
     * @param string $clientId only of this client
     * 
     * @return array|null
     */
    public function getStorage($id, $clientId = null)
    {
        if(($storages = $this->_getCollection(self::COLLECTION_NAME)) && array_key_exists($id, $storages)) {
            $storage = $storages[$id];
            if(array_key_exists('clientId', $storage) &&
                    $storage['clientId'] === $clientId) {
                return $storage;
            }
        }
        
        return array();
    }

    /**
     * gets storage by name
     *
     * @param string $name
     *
     * @return array
     */
    public function getStorageByName($name)
    {
        foreach($this->_getCollection(self::COLLECTION_NAME) as $storage) {
            if(array_key_exists('name', $storage) && $storage['name'] == $name) {
                return $storage;
            }
        }

        return array();
    }

    /**
     * gets all storages
     *
     * @return array
     */
    public function getStorages()
    {
        return $this->_getCollection(self::COLLECTION_NAME);
    }

    /**
     * gets all storages of a certain client
     * 
     * @param string $clientId
     * 
     * @return array
     */
    public function getStoragesByClient($clientId)
    {
        $result = array();
        
        foreach($this->_getCollection(self::COLLECTION_NAME) as $storageId => $storage) {
            if(array_key_exists('clientId', $storage) && $storage['clientId'] == $clientId) {
                $result[$storageId] = $storage;
            }
        }
        
        return $result;
    }
    
    /**
     * gets all storages of a certain node
     * 
     * @param string $nodeId
     * 
     * @return array
     */
    public function getStoragesByNode($nodeId)
    {
        $result = array();
        
        foreach($this->_getCollection(self::COLLECTION_NAME) as $storageId => $storage) {
            if(array_key_exists('nodeId', $storage) && $storage['nodeId'] == $nodeId) {
                $result[$storageId] = $storage;
            }
        }
        
        return $result;
    }

    /**
     * sets storage with given data
     * 
     * @param array $data
     * @param string $id
     * @return string storage id
     */
    public function setStorage(array $data, $id = null)
    {
        $storages = $this->_getCollection(self::COLLECTION_NAME);
        if(($storage = $this->getStorage($id))) {
            $data = array_merge($storage, $data);
        }

        if(!$id) {
            while (!$id || array_key_exists($id, $storages)) {
                $id = $this->_generateId();
            }
        }
        
        $data['_id'] = $id;
        $storages[$id] = $data;
        
        $this->_setCollection(self::COLLECTION_NAME, $storages);
        
        return $id;
    }
    
}

