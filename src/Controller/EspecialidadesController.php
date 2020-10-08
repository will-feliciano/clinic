<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Repository\EspecialidadeRepository;
use App\Controller\BaseController;
use App\Helper\EspecialidadeFactory;
use App\Helper\ExtractorDataRequest;
use Doctrine\ORM\EntityManagerInterface;

class EspecialidadesController extends BaseController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $repository,
        EspecialidadeFactory $factory,
        ExtractorDataRequest $extractor
    ) {
        parent::__construct($repository, $entityManager, $factory, $extractor);
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
}
