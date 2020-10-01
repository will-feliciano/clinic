<?php

namespace App\Helper;

use App\Entity\Especialidade;

class EspecialidadeFactory implements EntityFactory
{
    public function createEntity(string $json): Especialidade
    {
        $dado = json_decode($json);

        $especialidade = new Especialidade();
        $especialidade->setDescricao($dado->descricao);

        return $especialidade;
    }
}