<?php

namespace App\Helper;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;
use App\Traits\CheckIfPropertyExists;

class MedicoFactory implements EntityFactory
{
    /**
     * @var EspecialidadeRepository
     */
    private $especialidadeRepository;

    public function __construct(EspecialidadeRepository $especialidadeRepository)
    {
        $this->especialidadeRepository = $especialidadeRepository;
    }

    public function createEntity($json): Medico
    {
        $dado =  json_decode($json);                

        $especialidadeId = CheckIfPropertyExists::checkProperty(
            $dado,
            'especialidadeId',
            'Médico'
        );
        
        $especialidade = $this->especialidadeRepository->find($especialidadeId);        

        $medico = new Medico();
        $medico
            ->setCrm(
                CheckIfPropertyExists::checkProperty(
                    $dado,
                    'crm',
                    'Médico'
                )
            )
            ->setNome(
                CheckIfPropertyExists::checkProperty(
                    $dado,
                    'nome',
                    'Médico'
                )
            )
            ->setEspecialidade($especialidade);

        return $medico;
    }
}