<?php

namespace App\Helper;

interface EntityFactory
{
    public function createEntity(string $json);    
}