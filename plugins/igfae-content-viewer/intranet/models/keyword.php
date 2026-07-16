<?php
class IntranetKeyword
{
    protected $_name                    = NULL;

    public function __construct($name = NULL)
    {
        $this->_name                         = $name;
    }

    public function getName()
    {
        return $this->_name;
    }   
}
