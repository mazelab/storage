<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Dataprovider_Demo_Search
    extends MazelabStorage_Model_Dataprovider_Demo_Data
    implements Core_Model_Dataprovider_Interface_Search
{

    /**
     * @var string
     */
    protected $_clientId;
    
    /**
     * @param string $clientId
     */
    public function __construct($clientId)
    {
        parent::__construct();
        
        $this->_clientId = $clientId;
    }
    
    /**
     * gets last data set with limit
     * 
     * return should be build like:
     * array(
     *  'data' => array(),
     *  'total' => '55'
     * )
     * 
     * @param int $limit
     * @param string $searchTerm
     * @return array
     */
    public function last($limit, $searchTerm = null)
    {
        return array();
    }

    /**
     * gets a certain page
     * 
     * return should be build like:
     * array(
     *  'data' => array(),
     *  'total' => '55'
     * )
     * 
     * @param int $limit
     * @param int $page
     * @param string $searchTerm
     * @return array
     */
    public function page($limit, $page, $searchTerm = null)
    {
        return array();
    }
    
}

