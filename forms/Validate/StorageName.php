<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Form_Validate_StorageName extends Zend_Validate_Abstract
{
    
    const EXISTS  = 'exists';
 
    protected $_messageTemplates = array(
        self::EXISTS  => "Storage '%value%' allready exists"
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
        
        if(($storage = MazelabStorage_Model_DiFactory::getStorageManager()->getStoragesByName($value))) {
            $this->_error(self::EXISTS, $value);
            return false;
        }
        
        return true;
    }
    
}
