<?php

namespace Family\TreeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FamilyTreeBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
