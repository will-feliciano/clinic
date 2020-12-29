<?php

namespace App\Controller;

use App\Helper\EntityFactory;
use App\Helper\ExtractorDataRequest;
use App\Helper\ResponseFactory;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\Log\LoggerInterface;
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
    /**
     * @var ExtractorDataRequest
     */
    protected $extractor;
    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ObjectRepository $repository,
        EntityManagerInterface $entityManager,
        EntityFactory $factory,
        ExtractorDataRequest $extractor,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
        $this->extractor = $extractor;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function create(Request $request): Response
    {
        $content = $request->getContent();
        $entity = $this->factory->createEntity($content);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $cacheItem = $this->cache->getItem(
            $this->cachePrefix(). $entity->getId()
        );
        $cacheItem->set($entity);
        $this->cache->save($cacheItem);

        $this->logger->notice(
            'Novo registro de {entidade} adicionado com id: {id}.',
            [
                'entidade' => get_class($entity),
                'id' => $entity->getId()
            ]
        );

        $responseFactory = new ResponseFactory(
            true,
            $entity,
            Response::HTTP_CREATED
        );

        return $responseFactory->getResponse();
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $content = $request->getContent();
        $newEntity = $this->factory->createEntity($content);

        try{

            $oldEntity = $this->updateEntity($id, $newEntity);
            $this->entityManager->flush();

            $cacheItem = $this->cache->getItem($this->cachePrefix() . $id);
            $cacheItem->set($newEntity);
            $this->cache->save($cacheItem);

            $responseFactory = new ResponseFactory(
                true,
                $oldEntity,
                Response::HTTP_CREATED
            );

            return $responseFactory->getResponse();
        } catch (\InvalidArgumentException $e) {

            $responseFactory = new ResponseFactory(
                false,
                'Recurso não encontrado',
                Response::HTTP_NOT_FOUND
            );

            return $responseFactory->getResponse(); 
        }
    }

    public function getAll(Request $request): Response
    {
        $order = $this->extractor->getDataOrder($request);
        $filter = $this->extractor->getDataFilter($request);
        [$page, $itens] = $this->extractor->getDataPages($request);

        $entities = $this->repository->findBy(
            $filter,
            $order,
            $itens,
            ($page - 1) * $itens
        );

        $responseFactory = new ResponseFactory(
            true,
            $entities,
            Response::HTTP_OK,
            $page,
            $itens
        );

        return $responseFactory->getResponse();
    }

    public function getById($id): Response
    {
        $entity = $this->cache->hasItem($this->cachePrefix() . $id)
            ? $this->cache->getItem($this->cachePrefix() . $id)->get()
            : $this->repository->find($id);

        $responseFactory = new ResponseFactory(
            true,
            $entity,
            is_null($entity) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK
        );

        return $responseFactory->getResponse();
    }

    public function remove(int $id)
    {
        $entity = $this->repository->find($id);
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->cache->deleteItem($this->cachePrefix() . $id);

        $responseFactory = new ResponseFactory(
            true,
            "Item Removido",
            Response::HTTP_NO_CONTENT
        );

        return $responseFactory->getResponse();
    }

    abstract public function updateEntity(
        int $id,
        $newEntity
    );
}