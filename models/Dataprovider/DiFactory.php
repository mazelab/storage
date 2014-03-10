<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Dataprovider_DiFactory
{
    
    /**
     * default adapter for class building
     */
    CONST DEFAULT_ADAPTER = 'Core';
    
    /**
     * class prefix for provider class building
     */
    CONST PROVIDER_CLASS_PATH_PRE = 'MazelabStorage_Model_Dataprovider_';
    
    /**
     * current adapter for data provider
     * 
     * @var string
     */
    static protected $_adapter;
    
    /**
     * current instance of storage provider
     *
     * @var MazelabStorage_Model_Dataprovider_Interface_Storage
     */
    static protected $_storage;
    
    /**
     * returns the current adapter
     * 
     * @return string
     */
    static public function getAdapter()
    {
        if (is_null(self::$_adapter)) {
            self::setAdapter(self::DEFAULT_ADAPTER);
        }

        return self::$_adapter;
    }
    
    /**
     * get storage instance
     * 
     * @return MazelabStorage_Model_Dataprovider_Interface_Storage
     */
    static public function getStorage()
    {
        if (!self::$_storage instanceof MazelabStorage_Model_Dataprovider_Interface_Storage) {
            self::$_storage = self::newStorage();
        }

        return self::$_storage;
    }
    
    /**
     * returns new search instance
     * 
     * @param string $clientId
     * @return Core_Model_Dataprovider_Interface_Search
     * @throws Core_Model_DataProvider_Exception
     */
    static public function newSearch($clientId = null)
    {
        $currentAdapter = self::getAdapter();
        $className = self::PROVIDER_CLASS_PATH_PRE . $currentAdapter . '_Search';

        $newOne = new $className($clientId);
        if ($newOne instanceof Core_Model_Dataprovider_Interface_Search) {
            return $newOne;
        }
        
        throw new Core_Model_DataProvider_Exception(
            'The data provider: ' . $currentAdapter . ' doesn\'t have a valid search implementation.'
        );
    }    
    
    /**
     * create storage instance
     * 
     * @return MazelabStorage_Model_Dataprovider_Interface_Storage
     * @throws MazelabStorage_Model_Dataprovider_Exception
     */
    static public function newStorage()
    {
        $currentAdapter = self::getAdapter();
        $className = self::PROVIDER_CLASS_PATH_PRE . $currentAdapter . '_Storage';

        $newOne = new $className();
        if ($newOne instanceof MazelabStorage_Model_Dataprovider_Interface_Storage) {
            return $newOne;
        }

        throw new MazelabStorage_Model_Dataprovider_Exception(
            'The data provider: ' . $currentAdapter . ' doesn\'t have a valid storage implementation.'
        );
    }
    
    /**
     * resets instance
     */
    static public function reset()
    {
        self::setStorage();
    }
    
    /**
     * sets adapter for the dataprovider
     * 
     * if no adapter is given it will reset the current adapter to default
     * 
     * @param string $adapter
     */
    static public function setAdapter($adapter = null)
    {
        if (self::$_adapter) {
            self::reset();
            self::$_adapter = self::DEFAULT_ADAPTER;
        }

        self::$_adapter = $adapter;
    }
    
    /**
     * set storage instance
     * 
     * @param MazelabStorage_Model_Dataprovider_Interface_Storage $storage
     */
    static public function setStorage(MazelabStorage_Model_Dataprovider_Interface_Storage $storage = null)
    {
        self::$_storage = $storage;
    }
    
}
