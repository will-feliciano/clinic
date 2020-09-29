<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use App\Repository\MedicoRepository;
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

    /**
     * @var MedicoRepository
     */
    private $medicoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        MedicoFactory $medicoFactory,
        MedicoRepository $medicoRepository
    ) {
        $this->entityManager = $entityManager;
        $this->medicoFactory = $medicoFactory;
        $this->medicoRepository = $medicoRepository;
    }

    /**
     * @Route ("/medicos", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {        
        $medico = $this->medicoFactory->createMedico($request->getContent());        
        
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

        $medicoAtual = $this->medicoRepository->find($id);

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
     * @Route("/medicos/{id}", methods={"DELETE"})
     */
    public function remove(int $id)
    {
        $medico = $this->medicoRepository->find($id);
        $this->entityManager->remove($medico);

        $this->entityManager->flush();

        return new JsonResponse("", Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route ("/medicos", methods={"GET"})
     */
    public function getAll(): Response
    {
        $medicos = $this->medicoRepository->findAll();
        
        return new JsonResponse($medicos);
    }

    /**
     * @Route ("/medicos/{id}", methods={"GET"})
     */
    public function getById($id): Response
    {
        $medico = $this->medicoRepository->find($id);

        return new JsonResponse(
            $medico,
            is_null($medico) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK
        );
    }

    /**
     * @Route ("/especialidades/{especialidadeId}/medicos", methods={"GET"})
     */
    public function getBySpecialty(int $especialidadeId): Response
    {
        $medicos = $this->medicoRepository->findBy([
            "especialidade" => $especialidadeId
        ]);

        return new JsonResponse($medicos);
    }
    
}