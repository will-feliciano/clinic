<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use App\Controller\BaseController;
use App\Helper\ExtractorDataRequest;
use App\Repository\MedicoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends BaseController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        MedicoFactory $medicoFactory,
        MedicoRepository $medicoRepository,
        ExtractorDataRequest $extractor,
        CacheItemPoolInterface $cache
    ) {
        parent::__construct($medicoRepository, $entityManager, $medicoFactory, $extractor, $cache);
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

    public function updateEntity(int $id, $newEntity)
    {
        $oldEntity = $this->repository->find($id);

        if(is_null($oldEntity)) {
            throw new \InvalidArgumentException();
        }

        $oldEntity
            ->setCrm($newEntity->getCrm())
            ->setNome($newEntity->getNome())
            ->setEspecialidade($newEntity->getEspecialidade());

        return $oldEntity;
    }

    public function cachePrefix(): string
    {
        return 'medico_';
    }
}