<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Form_Validate_StorageName extends Zend_Validate_Abstract
{
    
    CONST EXISTS  = 'exists';
 
    protected $_messageTemplates = array(
        self::EXISTS  => "Storage '%value%' already exists"
    );
 
    public function isValid($value)
    {
        if (!$this->validate($value)){
            return false;
        }
 
        return true;
    }
    
    /**
     * validates domain availibility
     * 
     * @param string $value
     * @return boolean
     */
    protected function validate($value)
    {
        if(!$value) {
            return true;
        }
        
        if(($storage = MazelabStorage_Model_DiFactory::getStorageManager()->getStorageByName($value))) {
            $this->_error(self::EXISTS, $value);
            return false;
        }
        
        return true;
    }
    
}
