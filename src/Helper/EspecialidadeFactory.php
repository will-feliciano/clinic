<?php

namespace App\Helper;

use App\Entity\Especialidade;
use App\Traits\CheckIfPropertyExists;

class EspecialidadeFactory implements EntityFactory
{
    public function createEntity(string $json): Especialidade
    {
        $dado = json_decode($json);
        
        $especialidade = new Especialidade();
        $especialidade->setDescricao(
            CheckIfPropertyExists::checkProperty(
                $dado,
                'descricao',
                'Especialidade'
            )
        );

        return $especialidade;
    }
}