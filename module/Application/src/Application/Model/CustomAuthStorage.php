<?php

namespace Application\Model;

use Zend\Authentication\Storage;

class CustomAuthStorage extends Storage\Session
{
    public function forgetMe()
    {
        $this->session->getManager()->forgetMe();
    }
}