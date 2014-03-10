<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Dataprovider_Demo_Data
{

    /**
     * session name storage
     */
    CONST SESSION_NAMESPACE = 'moduleStorageTest';

    /**
     * @var array
     */
    protected $_database;
    
    /**
     * @var array
     */
    protected $_initial = array(
        'storages' => array(
            '0000001' => array(
                '_id' => '0000001',
                'status' => true,
                'name' => 'sample_1',
                'clientLabel' => 'Max Mustermann'
            ),
            '0000002' => array(
                '_id' => '0000002',
                'status' => true,
                'name' => 'sample_2'
            ),
            '0000003' => array(
                '_id' => '0000003',
                'status' => true,
                'name' => 'sample_3',
                'clientLabel' => 'Ex Ample'
            ),
            '0000004' => array(
                '_id' => '0000004',
                'status' => false,
                'name' => 'sample_4'
            ),
            '0000005' => array(
                '_id' => '0000005',
                'status' => false,
                'name' => 'sample_5',
                'clientLabel' => 'Another One',
            ),
            '0000006' => array(
                '_id' => '0000006',
                'status' => false,
                'name' => 'sample_6'
            ),
            '0000007' => array(
                '_id' => '0000007',
                'status' => true,
                'name' => 'sample_7'
            ),
        )
    );    
    
    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;
    
    /**
     * inits demo data
     */
    public function __construct()
    {
        if (!$this->_getSession()->database) {
            $this->_getSession()->database = $this->_initial;
        }
    }
    
    /**
     * generate rnd id
     * 
     * @return string
     */
    protected function _generateId()
    {
        return md5(time());
    }
    
    /**
     * get data of a certain dummy collection
     * 
     * @param string $collectionName
     * @return array
     */
    protected function _getCollection($collectionName)
    {
        if(!array_key_exists($collectionName, $this->_getSession()->database)) {
            return array();
        }
        
        return $this->_getSession()->database[$collectionName];
    }

    /**
     * get dummy database
     * 
     * @return array
     */
    protected function _getDatabase()
    {
        return $this->_getSession()->database;
    }
    
    /**
     * gets demo session
     * 
     * @return Zend_Session_Namespace
     */
    protected function _getSession()
    {
        if(!$this->_session) {
            $this->_session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
        }
        
        return $this->_session;
    }
    
    /**
     * sets dataset of a certain dummy collection
     * 
     * @param string $collection
     * @param array $collectionData
     */
    protected function _setCollection($collection, array $collectionData)
    {
        $this->_getSession()->database[$collection] = $collectionData;
    }

}
