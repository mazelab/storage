<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Api_Core extends Core_Model_Module_Api_Abstract
{

    /**
     * removes the module collection
     *
     * @return boolean
     */
    public function deinstall()
    {
        return MazelabStorage_Model_Dataprovider_DiFactory::getStorage()->drop();
    }

    /**
     * returns all nodes
     *
     * @return array contains Core_Model_ValueObject_Node
     */
    public function getNodes()
    {
        return MazelabStorage_Model_DiFactory::getNodeManager()->getNodes();
    }

    /**
     * returns all nodes of a certain client which are set in a particular module
     *
     * @param string $clientId
     * @return array contains Core_Model_ValueObject_Node
     */
    public function getNodesByClient($clientId)
    {
        return MazelabStorage_Model_DiFactory::getNodeManager()->getNodesByClient($clientId);
    }

    /**
     * returns all clients of a certain node
     *
     * @param string $nodeId
     * @return array contains Core_Model_ValueObject_Client
     */
    public function getClientsByNode($nodeId)
    {
        return MazelabStorage_Model_DiFactory::getClientManager()->getClientsByNode($nodeId);
    }

    /**
     * deletes a certain client
     *
     * @param  string $clientId
     * @return boolean
     */
    public function removeClient($clientId)
    {
        return MazelabStorage_Model_DiFactory::getClientManager()->removeClient($clientId);
    }

    /**
     * process report of a certain node
     *
     * if false will be returned then the report will be abort
     *
     * @param string $nodeId
     * @param string $report
     * @return boolean
     */
    public function reportNode($nodeId, $report)
    {
        return MazelabStorage_Model_DiFactory::getReportManager()->reportNode($nodeId, $report);
    }

}

