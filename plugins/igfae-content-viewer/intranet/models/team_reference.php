<?php
class IntranetTeamReference
{
    public $_teamReferenceId                = NULL;
    public $_name                   = NULL;

    public function __construct($teamReferenceId = NULL, $name = NULL)
    {
        $this->_teamReferenceId                         = $teamReferenceId;
        $this->_name                                     = $name;
    }


    /**
     * Get the value of _teamReferenceId
     */
    public function getTeamReferenceId()
    {
        return $this->_teamReferenceId;
    }

    /**
     * Get the value of _name
     */
    public function getName()
    {
        return $this->_name;
    }
}
