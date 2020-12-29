<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Repository\EspecialidadeRepository;
use App\Controller\BaseController;
use App\Helper\EspecialidadeFactory;
use App\Helper\ExtractorDataRequest;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

class EspecialidadesController extends BaseController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $repository,
        EspecialidadeFactory $factory,
        ExtractorDataRequest $extractor,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        parent::__construct($repository, $entityManager, $factory, $extractor, $cache, $logger);
    }

    public function updateEntity(int $id, $newEntity)
    {
        $oldEntity = $this->repository->find($id);

        if(is_null($oldEntity)) {
            throw new \InvalidArgumentException();
        }

        $oldEntity->setDescricao($newEntity->getDescricao());

        return $oldEntity;
    }

    public function cachePrefix(): string
    {
        return 'especialidade_';
    }
}
