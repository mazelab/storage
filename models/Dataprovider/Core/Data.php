<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Dataprovider_Core_Data
{
    
    /**
     * name of the used collection
     */
    CONST COLLECTION = 'storages';

    /**
     * name of the client id key
     */
    CONST KEY_CLIENTID = 'clientId';
    
    /**
     * name of the id key
     */
    CONST KEY_ID = '_id';
    
    /**
     * name of the storage name key
     */
    CONST KEY_NAME = 'name';
    
    /**
     * name of the node id key
     */
    CONST KEY_NODEID = 'nodeId';

    /**
     * name of the imported flag
     */
    CONST KEY_IMPORTED = 'imported';

    /**
     * @var MongoDb_Mongo
     */
    protected $_mongoDb;

    /**
     * @var MongoCollection
     */
    protected $_storageCollection;
    
    /**
     * gets mongo db
     * 
     * @return MongoDb_Mongo
     */
    protected function _getDatabase()
    {
        if(!$this->_mongoDb instanceof MongoDB) {
            $this->_mongoDb = Core_Model_DiFactory::getMongoDb();
        }
        
        return $this->_mongoDb;
    }

    /**
     * get storage collection
     * 
     * @return MongoCollection
     */
    protected function _getStorageCollection()
    {
        if (!$this->_storageCollection instanceof MongoCollection) {
            $this->_storageCollection = $this->_getDatabase()->getCollection(self::COLLECTION);

            $this->_storageCollection->ensureIndex(array(
                self::KEY_CLIENTID => 1
            ));
        }

        return $this->_storageCollection;
    }

}
