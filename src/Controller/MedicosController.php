<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use App\Controller\BaseController;
use App\Repository\MedicoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends BaseController
{
    
    public function __construct(
        EntityManagerInterface $entityManager,
        MedicoFactory $medicoFactory,
        MedicoRepository $medicoRepository
    ) {
        parent::__construct($medicoRepository, $entityManager, $medicoFactory);                
    }    

    /**
     * @Route ("/especialidades/{especialidadeId}/medicos", methods={"GET"})
     */
    public function getBySpecialty(int $especialidadeId): Response
    {
        $medicos = $this->repository->findBy([
            "especialidade" => $especialidadeId
        ]);

        return new JsonResponse($medicos);
    }
    
    public function updateEntity($oldEntity, $newEntity)
    {
        $oldEntity
            ->setCrm($newEntity->getCrm())
            ->setNome($newEntity->getNome())
            ->setEspecialidade($newEntity->getEspecialidade());
    }
}