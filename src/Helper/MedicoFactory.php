<?php

namespace App\Helper;

use App\Entity\Medico;

class MedicoFactory
{
    public function createMedico(string $json): Medico
    {
        $dado = json_decode($json);

        $medico = new Medico();
        $medico->crm = $dado->crm;
        $medico->nome = $dado->nome;

        return $medico;
    }
}