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
     * @Route ("/medicos/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $content = $request->getContent();
        $medico = $this->factory->createEntity($content);

        $medicoAtual = $this->repository->find($id);

        if(is_null($medicoAtual)) {
            return new JsonResponse("", Response::HTTP_NOT_FOUND);    
        }

        $medicoAtual
            ->setCrm($medico->getCrm())
            ->setNome($medico->getNome());
        
        $this->entityManager->flush();

        return new JsonResponse($medicoAtual, Response::HTTP_CREATED);
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
}