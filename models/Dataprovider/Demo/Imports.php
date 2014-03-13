<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Dataprovider_Demo_Imports
    extends MazelabStorage_Model_Dataprovider_Demo_Data
    implements Core_Model_Dataprovider_Interface_Search
{

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

