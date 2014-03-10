<?php
/**
 * storage
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabStorage_Model_Bean_Storage extends MazeLib_Bean
{
    
    protected $mapping = array(
        'status' => MazeLib_Bean::STATUS_MANUALLY,
        'quota' => MazeLib_Bean::STATUS_MANUALLY,
        'password' => MazeLib_Bean::STATUS_PRIO_MAZE
    );
    
}

