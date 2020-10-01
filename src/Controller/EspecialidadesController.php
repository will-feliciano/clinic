<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Repository\EspecialidadeRepository;
use App\Controller\BaseController;
use App\Helper\EspecialidadeFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadesController extends BaseController
{   

    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $repository,
        EspecialidadeFactory $factory
    ) {
        parent::__construct($repository, $entityManager, $factory);        
    }
     

    /**
     * @Route ("/especialidades/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $request = $request->getContent();
        $json = json_decode($request);

        $especialidade = $this->repository->find($id);
        $especialidade->setDescricao($json->descricao);

        $this->entityManager->flush();

        return new JsonResponse($especialidade);
    }    
}
