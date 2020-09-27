<?php

namespace App\Helper;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;

class MedicoFactory
{
    /**
     * @var EspecialidadeRepository
     */
    private $especialidadeRepository;

    public function __construct(EspecialidadeRepository $especialidadeRepository)
    {
        $this->especialidadeRepository = $especialidadeRepository;
    }

    public function createMedico($json): Medico
    {
        $dado =  json_decode($json);                

        $especialidadeId = $dado->especialidadeId;
        
        $especialidade = $this->especialidadeRepository->find($especialidadeId);        

        $medico = new Medico();
        $medico
            ->setCrm($dado->crm)
            ->setNome($dado->nome)
            ->setEspecialidade($especialidade);

        return $medico;
    }
}