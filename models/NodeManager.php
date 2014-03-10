<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_NodeManager
{

    /**
     * get all storage node
     * 
     * @return array contains Core_Model_ValueObject_Node
     */
    public function getNodes()
    {
        return Core_Model_DiFactory::getNodeManager()
                ->getNodesByService(MazelabStorage_Model_StorageManager::MODULE_NAME);
    }
    
    /**
     * get all storage nodes as array
     * 
     * @return array
     */
    public function getNodesAsArray()
    {
        return Core_Model_DiFactory::getNodeManager()
                ->getNodesByServiceAsArray(MazelabStorage_Model_StorageManager::MODULE_NAME);
    }

    /**
     * get all nodes by client
     *
     * @param $clientId
     * @return array contains Core_Model_ValueObject_Node
     */
    public function getNodesByClient($clientId)
    {
        $storagesByClient = MazelabStorage_Model_DiFactory::getStorageManager()->getStoragesByClient($clientId);
        $nodes = array();

        foreach($storagesByClient as $storageId => $storage) {
            $nodeId = $storage->getData('nodeId');
            if($nodeId && !array_key_exists($nodeId, $nodes) && ($node = $storage->getNode())) {
                $nodes[$nodeId] = $node;
            }
        }

        return $nodes;
    }

}