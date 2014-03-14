<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
class MazelabStorage_Model_IndexManager
{

    /**
     * search category string for storage
     */
    CONST SEARCH_CATEGORY_STORAGE = 'Storage';
    
    /**
     * Zend_View_Helper_Url
     */
    protected  $_urlHelper;
    
    /**
     * get search index core model
     * 
     * @return Core_Model_Search_Index
     */
    public function _getSearchIndex()
    {
        return Core_Model_DiFactory::getSearchIndex();
    }
    
    /**
     * get zend url helper
     * 
     * @return Zend_View_Helper_Url
     */
    public function _getUrlHelper()
    {
        if(!$this->_urlHelper) {
            $this->_urlHelper = new Zend_View_Helper_Url();
        }
        
        return $this->_urlHelper;
    }
    
    /**
     * builds and save core search index of a certain storage
     *
     * @param string $storageId
     * @return boolean
     */
    public function setStorage($storageId)
    {
        if (!($storage = MazelabStorage_Model_DiFactory::getStorageManager()->getStorage($storageId))) {
            return false;
        }
        
        $data['id'] = $storageId;
        $data['search'] = $storage->getName();
        $data['headline'] = $storage->getName();
        $data['link'] = $this->_getUrlHelper()->url(array($storageId), 'mazelab-storage_edit');
        $data['categoryLink'] = $this->_getUrlHelper()->url(array(), 'mazelab-storage_index');
        
        return $this->_getSearchIndex()->setSearchIndex(self::SEARCH_CATEGORY_STORAGE, $storageId, $data);
    }

    /**
     * builds and save core search index of all storage
     * 
     * @return boolean
     */
    public function setStorages()
    {
        foreach(array_keys(MazelabStorage_Model_DiFactory::getStorageManager()->getStorages()) as $storageId) {
            $this->setStorage($storageId);
        }
    }
    
    /**
     * unsets a certain storage in core search index
     * 
     * @param string $storageId
     * @return boolean
     */
    public function unsetStorage($storageId)
    {
        return $this->_getSearchIndex()->deleteIndex(self::SEARCH_CATEGORY_STORAGE, $storageId);
    }
    
}