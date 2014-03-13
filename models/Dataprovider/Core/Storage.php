<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Dataprovider_Core_Storage
    extends MazelabStorage_Model_Dataprovider_Core_Data
    implements MazelabStorage_Model_Dataprovider_Interface_Storage
{
    
    CONST COLLECTION_NAME = 'storages';

    /**
     * gets all storages with the imported flag
     *
     * @return int
     */
    public function countStoragesWithImportedFlag()
    {
        $result = array();
        $query = array(
            self::KEY_IMPORTED => true
        );

        return $this->_getStorageCollection()->find($query)->count();
    }

    /**
     * deletes a certain storage
     * 
     * @param string $storageId
     * @return boolean
     */
    public function deleteStorage($storageId)
    {
        $mongoId = new MongoId($storageId);

        $query = array(
            self::KEY_ID => $mongoId
        );

        $options = array(
            "j" => true
        );

        $result = $this->_getStorageCollection()->remove($query, $options);

        if(!array_key_exists('ok', $result) || $result['ok'] != true) {
            return false;
        }

        return true;
    }

    /**
     * drops the storage collection
     *
     * @return boolean
     */
    public function drop()
    {
        if (($result = $this->_getStorageCollection()->drop()) && $result["ok"] == 1) {
            return true;
        }

        return false;
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
        $query = array(
            self::KEY_ID => new MongoId($id)
        );
        
        if($clientId) {
            $query[self::KEY_CLIENTID] = $clientId;
        }
        
        if(($storage = $this->_getStorageCollection()->findOne($query))) {
            $storage[self::KEY_ID] = (string) $storage[self::KEY_ID];
        }
        
        return $storage;
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
        $query = array(
            self::KEY_NAME => $name
        );
        
        if(($storage = $this->_getStorageCollection()->findOne($query))) {
            $storage[self::KEY_ID] = (string) $storage[self::KEY_ID];
        }
        
        return $storage;
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
        $query = array(
            self::KEY_CLIENTID => $clientId
        );
        
        foreach($this->_getStorageCollection()->find($query) as $storageId => $storage) {
            $storage[self::KEY_ID] = $storageId;
            $result[$storageId] = $storage;
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
        $query = array(
            self::KEY_NODEID => $nodeId
        );
        
        foreach($this->_getStorageCollection()->find($query) as $storageId => $storage) {
            $storage[self::KEY_ID] = $storageId;
            $result[$storageId] = $storage;
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
        $mongoId = new MongoId($id);
        $data[self::KEY_ID] = $mongoId;
        
        $options = array(
            "j" => true
        );
        
        if(!($result = $this->_getStorageCollection()->save($data, $options))) {
            return false;
        }
        
        return (string) $mongoId;
    }
    
}

