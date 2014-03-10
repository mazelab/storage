<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_ClientManager
{

    /**
     * message when deleting storage failed
     */
    CONST MESSAGE_STORAGE_DELETE_FAILED = 'Failed to delete storage %1$s';

    /**
     * get all storage clients
     * 
     * @return array contains Core_Model_ValueObject_Client
     */
    public function getClients()
    {
        return Core_Model_DiFactory::getClientManager()
                ->getClientsByService(MazelabStorage_Model_StorageManager::MODULE_NAME);
    }
    
    /**
     * get all storage clients as array
     * 
     * @return array
     */
    public function getClientsAsArray()
    {
        return Core_Model_DiFactory::getClientManager()
                ->getClientsByServiceAsArray(MazelabStorage_Model_StorageManager::MODULE_NAME);
    }

    /**
     * get all clients of a certain node
     *
     * @param $nodeId
     * @return array contains Core_Model_ValueObject_Client
     */
    public function getClientsByNode($nodeId)
    {
        $storagesByNode = MazelabStorage_Model_DiFactory::getStorageManager()->getStoragesByNode($nodeId);
        $clients = array();

        foreach($storagesByNode as $storageId => $storage) {
            $clientId = $storage->getData('clientId');
            if($clientId && !array_key_exists($clientId, $clients) && ($client = $storage->getClient())) {
                $clients[$clientId] = $client;
            }
        }

        return $clients;
    }

    /**
     * remove client dependencies of storage
     *
     * @param $clientId
     * @return bool
     */
    public function removeClient($clientId)
    {
        $storageManager = MazelabStorage_Model_DiFactory::getStorageManager();

        foreach($storageManager->getStoragesByClient($clientId) as $storageId => $storage) {
            if(!$storageManager->deleteStorage($storageId)) {
                Core_Model_DiFactory::getMessageManager()
                    ->addError(self::MESSAGE_STORAGE_DELETE_FAILED, $storage->getName());
                return false;
            }
        }

        return true;
    }

}