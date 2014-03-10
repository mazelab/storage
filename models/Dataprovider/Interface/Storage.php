<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

interface MazelabStorage_Model_Dataprovider_Interface_Storage
{
 
    /**
     * deletes a certain storage
     * 
     * @param string $storageId
     * @return boolean
     */
    public function deleteStorage($storageId);

    /**
     * drops the storage collection
     *
     * @return boolean
     */
    public function drop();

    /**
     * get a certain storage by id and optional clientId
     * 
     * @param string $id
     * @param string $clientId only of this client
     * 
     * @return array|null
     */
    public function getStorage($id, $clientId = null);
    
    /**
     * gets all storages of a certain client
     * 
     * @param string $clientId
     * 
     * @return array
     */
    public function getStoragesByClient($clientId);
    
    /**
     * get storage by name
     * 
     * @param string $name
     * 
     * @return array
     */
    public function getStorageByName($name);
    
    /**
     * gets all storages of a certain node
     * 
     * @param string $nodeId
     * 
     * @return array
     */
    public function getStoragesByNode($nodeId);
    
    /**
     * sets storage with given data
     * 
     * @param array $data
     * @param string $id
     * @return string storage id
     */
    public function setStorage(array $data, $id = null);
    
}

