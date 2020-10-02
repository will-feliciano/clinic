<?php

namespace App\Controller;

use App\Helper\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /**
     * @var ObjectRepository
     */
    protected $repository;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var EntityFactory
     */
    protected $factory;

    public function __construct(
        ObjectRepository $repository,
        EntityManagerInterface $entityManager,
        EntityFactory $factory
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
    }

    public function create(Request $request): Response
    {
        $content = $request->getContent();
        $entity = $this->factory->createEntity($content);
        
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return new JsonResponse($entity, Response::HTTP_CREATED);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $content = $request->getContent();
        $newEntity = $this->factory->createEntity($content);

        $oldEntity = $this->repository->find($id);

        if(is_null($oldEntity)) {
            return new JsonResponse("", Response::HTTP_NOT_FOUND);    
        }

        $this->updateEntity($oldEntity, $newEntity);
                
        $this->entityManager->flush();

        return new JsonResponse($oldEntity, Response::HTTP_CREATED);
    }

    public function getAll(): Response
    {
        $entities = $this->repository->findAll();
        
        return new JsonResponse($entities);
    }

    public function getById($id): Response
    {
        $entity = $this->repository->find($id);
        
        return new JsonResponse(
            $entity,
            is_null($entity) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK
        );
    }

    public function remove(int $id)
    {
        $entity = $this->repository->find($id);
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return new JsonResponse("", Response::HTTP_NO_CONTENT);
    }

    abstract public function updateEntity(
        $oldEntity, 
        $newEntity
    );
}