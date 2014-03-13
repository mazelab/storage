<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_DiFactory
{

    /**
     * @var MazelabStorage_Model_Apply_Storage
     */
    static protected $_applyStorage;
    
    /**
     * @var MazelabStorage_Model_ClientManager
     */
    static protected $_clientManager;

    /**
     * @var Core_Model_SearchManager
     */
    static protected $_imports;

    /**
     * @var MazelabStorage_Model_NodeManager
     */
    static protected $_nodeManager;
    
    /**
     * @var MazelabStorage_Model_StorageManager
     */
    static protected $_storageManager;
    
    /**
     * @var MazelabStorage_Model_ReportManager
     */
    static protected $_reportManager;
    
    /**
     * get actual instance of MazelabStorage_Model_Apply_Storage
     * 
     * @return MazelabStorage_Model_Apply_Storage
     */
    static public function getApplyStorage()
    {
        if (!self::$_applyStorage instanceof MazelabStorage_Model_Apply_Storage) {
            self::$_applyStorage = self::newApplyStorage();
        }

        return self::$_applyStorage;
    }
    
    /**
     * get actual instance of MazelabStorage_Model_ClientManager
     * 
     * @return MazelabStorage_Model_ClientManager
     */
    static public function getClientManager()
    {
        if (!self::$_clientManager instanceof MazelabStorage_Model_ClientManager) {
            self::$_clientManager = self::newClientManager();
        }

        return self::$_clientManager;
    }

    /**
     * get actual instance of Core_Model_SearchManager with imports dataprovider
     *
     * @return Core_Model_SearchManager
     */
    static public function getImports()
    {
        if (!self::$_imports instanceof Core_Model_SearchManager) {
            self::$_imports = self::newImportSearch();
        }

        return self::$_imports;
    }

    /**
     * get actual instance of MazelabStorage_Model_NodeManager
     * 
     * @return MazelabStorage_Model_NodeManager
     */
    static public function getNodeManager()
    {
        if (!self::$_nodeManager instanceof MazelabStorage_Model_NodeManager) {
            self::$_nodeManager = self::newNodeManager();
        }

        return self::$_nodeManager;
    }
    
    /**
     * get actual instance of MazelabStorage_Model_StorageManager
     * 
     * @return MazelabStorage_Model_StorageManager
     */
    static public function getStorageManager()
    {
        if (!self::$_storageManager instanceof MazelabStorage_Model_StorageManager) {
            self::$_storageManager = self::newStorageManager();
        }

        return self::$_storageManager;
    }
    
    /**
     * get actual instance of MazelabStorage_Model_ReportManager
     * 
     * @return MazelabStorage_Model_ReportManager
     */
    static public function getReportManager()
    {
        if (!self::$_reportManager instanceof MazelabStorage_Model_ReportManager) {
            self::$_reportManager = self::newReportManager();
        }

        return self::$_reportManager;
    }
    
    /**
     * get new instance of MazelabStorage_Model_Apply_Storage
     * 
     * @return MazelabStorage_Model_Apply_Storage
     */
    static public function newApplyStorage()
    {
        return new MazelabStorage_Model_Apply_Storage();
    }
    
    /**
     * get new instance of MazelabStorage_Model_ClientManager
     * 
     * @return MazelabStorage_Model_ClientManager
     */
    static public function newClientManager()
    {
        return new MazelabStorage_Model_ClientManager();
    }

    /**
     * returns new instance of Core_Model_SearchManager with the
     * imports dataprovider
     *
     * @return Core_Model_SearchManager
     */
    static public function newImportSearch()
    {
        $searchManager = new Core_Model_SearchManager();
        $searchManager->setProvider(MazelabStorage_Model_Dataprovider_DiFactory::getImports());

        return $searchManager;
    }

    /**
     * get new instance of MazelabStorage_Model_NodeManager
     * 
     * @return MazelabStorage_Model_NodeManager
     */
    static public function newNodeManager()
    {
        return new MazelabStorage_Model_NodeManager();
    }
    
    /**
     * get new instance of MazelabStorage_Model_ValueObject_Storage
     * 
     * @param string $storageId
     * @return MazelabStorage_Model_ValueObject_Storage
     */
    static public function newStorage($storageId = null)
    {
        return new MazelabStorage_Model_ValueObject_Storage($storageId);
    }
    
    /**
     * get new instance of MazelabStorage_Model_StorageManager
     * 
     * @return MazelabStorage_Model_StorageManager
     */
    static public function newStorageManager()
    {
        return new MazelabStorage_Model_StorageManager();
    }
    
    /**
     * returns new instance of Core_Model_SearchManager with the
     * search dataprovider
     * 
     * @param string $clientId
     * @return Core_Model_SearchManager
     */
    static public function newStorageSearch($clientId = null)
    {
        $searchManager = new Core_Model_SearchManager();
        $searchManager->setProvider(MazelabStorage_Model_Dataprovider_DiFactory::newSearch($clientId));
        
        return $searchManager;
    }
    
    /**
     * get new instance of MazelabStorage_Model_ReportManager
     * 
     * @return MazelabStorage_Model_ReportManager
     */
    static public function newReportManager()
    {
        return new MazelabStorage_Model_ReportManager();
    }
    
    /**
     * sets instance of MazelabStorage_Model_Apply_Storage
     * 
     * @param $applyStorage MazelabStorage_Model_Apply_Storage|null
     */
    static public function setApplyStorage(MazelabStorage_Model_Apply_Storage $applyStorage = null)
    {
        self::$_applyStorage = $applyStorage;
    }
    
    /**
     * sets instance of MazelabStorage_Model_ClientManager
     * 
     * @param $clientManager MazelabStorage_Model_ClientManager|null
     */
    static public function setClientManager(MazelabStorage_Model_ClientManager $clientManager = null)
    {
        self::$_clientManager = $clientManager;
    }
    
    /**
     * sets instance of MazelabStorage_Model_NodeManager
     * 
     * @param $nodeManager MazelabStorage_Model_NodeManager|null
     */
    static public function setNodeManager(MazelabStorage_Model_NodeManager $nodeManager = null)
    {
        self::$_nodeManager = $nodeManager;
    }
    
    /**
     * sets instance of MazelabStorage_Model_StorageManager
     * 
     * @param $storageManager MazelabStorage_Model_StorageManager|null
     */
    static public function setStorageManager(MazelabStorage_Model_StorageManager $storageManager = null)
    {
        self::$_storageManager = $storageManager;
    }
    
    /**
     * sets instance of MazelabStorage_Model_ReportManager
     * 
     * @param $reportManager MazelabStorage_Model_ReportManager|null
     */
    static public function setReportManager(MazelabStorage_Model_ReportManager $reportManager = null)
    {
        self::$_reportManager = $reportManager;
    }
    
}
