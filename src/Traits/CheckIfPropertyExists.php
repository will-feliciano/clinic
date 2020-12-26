<?php

namespace App\Traits;

use App\Helper\EntityFactoryException;
//use Symfony\Bridge\Doctrine\DependencyInjection\Security\UserProvider\EntityFactory;

trait CheckIfPropertyExists
{
    public static function checkProperty(object $data, string $field, string $entity){
        if (!property_exists($data, $field)) {
            throw new EntityFactoryException("$entity precisa de $field");
        }
        return $data->$field;
    }
}