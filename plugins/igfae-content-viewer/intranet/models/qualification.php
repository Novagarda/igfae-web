<?php
class IntranetQualification
{
    protected $_qualificationId     = NULL;
    protected $_name                = NULL;

    public function __construct($qualification = NULL, $name = NULL)
    {
        $this->_qualificationId             = $qualification;
        $this->_name                        = $name;
    }

    public function getQualificationId()
    {
        return $this->_qualificationId;
    }
    public function getName()
    {
        return $this->_name;
    }
}
