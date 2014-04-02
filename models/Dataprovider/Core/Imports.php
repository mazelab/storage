<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Dataprovider_Core_Imports
    extends MazelabStorage_Model_Dataprovider_Core_Data
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
        $result = array();
        $query = array(
            self::KEY_IMPORTED => true,
            self::KEY_DELETE => array(
                '$ne' => true
            )
        );

        if ($searchTerm) {
            $query[self::KEY_NAME] = new MongoRegex("/$searchTerm/i");
        }
        
        $sort = array(
            self::KEY_NAME => -1
        );
        
        $mongoCursor = $this->_getStorageCollection()->find($query);
        $result['total'] = $total = $mongoCursor->count();
        if($total > $limit) {
            $rest = ($total / $limit) - floor($total / $limit);
            if($rest != 0) {
                $limit = bcmul($rest, $limit);
            }
        }
        
        foreach($mongoCursor->sort($sort)->limit($limit) as $storageId => $storage) {
            $storage[self::KEY_ID] = $storageId;
            $result['data'][$storageId] = $storage;
        }
        
        $result['data'] = array_reverse($result['data']);
        
        return $result;
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
        $result = array();
        $query = array(
            self::KEY_IMPORTED => true,
            self::KEY_DELETE => array(
                '$ne' => true
            )
        );
        
        if ($searchTerm) {
            $query[self::KEY_NAME] = new MongoRegex("/$searchTerm/i");
        }
        
        $sort = array(
            self::KEY_NAME => 1
        );
        
        $mongoCursor = $this->_getStorageCollection()->find($query);
        $result['total'] = $mongoCursor->count();
        
        $skip = ($limit * $page) - $limit;
        foreach($mongoCursor->sort($sort)->skip($skip)->limit($limit) as $storageId => $storage) {
            $storage[self::KEY_ID] = $storageId;
            $result['data'][$storageId] = $storage;
        }
        
        return $result;
    }
    
}

