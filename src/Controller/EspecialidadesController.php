<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadesController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EspecialidadeRepository
     */
    private $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $repository
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Route("/especialidades", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $content = $request->getContent();
        $json = json_decode($content);

        $especialidade = new Especialidade();
        $especialidade->setDescricao($json->descricao);

        $this->entityManager->persist($especialidade);
        $this->entityManager->flush();

        return new JsonResponse($especialidade);
    }

    /**
     * @Route("/especialidades", methods={"GET"})
     */
    public function getAll(): Response
    {
        $especialidades = $this->repository->findAll();

        return new JsonResponse($especialidades);
    }

    /**
     * @Route("/especialidades/{id}", methods={"GET"})
     */
    public function getById($id): Response
    {
        return new JsonResponse($this->repository->find($id));
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

    /**
     * @Route("/especialidades/{id}", methods={"DELETE"})
     */
    public function remove(int $id)
    {
        $especialidade = $this->repository->find($id);
        $this->entityManager->remove($especialidade);

        $this->entityManager->flush();

        return new JsonResponse("", Response::HTTP_NO_CONTENT);
    }
}