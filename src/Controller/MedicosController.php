<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MedicoFactory
     */
    private $medicoFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        MedicoFactory $medicoFactory
    ) {
        $this->entityManager = $entityManager;
        $this->medicoFactory = $medicoFactory;
    }

    /**
     * @Route ("/medicos", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $content = $request->getContent();
        $medico = $this->medicoFactory->createMedico($content);
        
        $this->entityManager->persist($medico);
        $this->entityManager->flush();

        return new JsonResponse($medico, Response::HTTP_CREATED);
    }

    /**
     * @Route ("/medicos/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $content = $request->getContent();
        $medico = $this->medicoFactory->createMedico($content);

        $medicoAtual = $this->getMedico($id);

        if(is_null($medicoAtual)) {
            return new JsonResponse("", Response::HTTP_NOT_FOUND);    
        }

        $medicoAtual->crm = $medico->crm;
        $medicoAtual->nome = $medico->nome;
        
        $this->entityManager->flush();

        return new JsonResponse($medicoAtual, Response::HTTP_CREATED);
    }

    /**
     * @Route("/medicos/{id}", methods={"DELETE"})
     */
    public function remove(int $id)
    {
        $medico = $this->getMedico($id);
        $this->entityManager->remove($medico);

        $this->entityManager->flush();

        return new JsonResponse("", Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route ("/medicos", methods={"GET"})
     */
    public function getAll(): Response
    {
        $medicos = $this->entityManager
            ->getRepository(Medico::class)
            ->findAll();
        
        return new JsonResponse($medicos);
    }

    /**
     * @Route ("/medicos/{id}", methods={"GET"})
     */
    public function getById($id): Response
    {
        $medico = $this->getMedico($id);

        return new JsonResponse(
            $medico,
            is_null($medico) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK
        );
    }

    /**
     * @param int $id
     * @return object|null
     */
    public function getMedico($id)
    {
        //return $this->entityManager->getReference(Medico::class, $id);
        return $this->entityManager
            ->getRepository(Medico::class)
            ->find($id);
    }
}