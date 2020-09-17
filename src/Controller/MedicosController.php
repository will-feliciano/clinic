<?php

namespace App\Controller;

use App\Entity\Medico;
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

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route ("/medicos", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $content = $request->getContent();
        $array = json_decode($content);

        $medico = new Medico();
        $medico->crm = $array->crm;
        $medico->nome = $array->nome;

        $this->entityManager->persist($medico);
        $this->entityManager->flush();

        return new JsonResponse($medico, Response::HTTP_CREATED);
    }

    /**
     * @Route ("/medicos", methods={"GET"})
     */
    public function getAll(): Response
    {
        $em = $this->getDoctrine()->getRepository(Medico::class);
        $medicos = $em->findAll();

        return new JsonResponse($medicos);
    }

    /**
     * @Route ("/medicos/{medicoId}", methods={"GET"})
     */
    public function getById($medicoId): Response
    {
        $em = $this->getDoctrine()->getRepository(Medico::class);
        $medicos = $em->find($medicoId);

        return new JsonResponse(
            $medicos,
            is_null($medicos) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK
        );
    }
}