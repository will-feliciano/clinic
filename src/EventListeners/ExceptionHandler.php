<?php

namespace App\EventListeners;

use App\Helper\EntityFactoryException;
use App\Helper\ResponseFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionHandler implements EventSubscriberInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['handlerEntityException', 1],
                ['handler404Exception', 0],
                ['handlerGenericException', -1]
            ]
        ];
    }

    public function handler404Exception(ExceptionEvent $event)
    {        
        if($event->getThrowable() instanceof NotFoundHttpException) {
            $exception = $event->getThrowable();
            $responseFactory = new ResponseFactory(
                false,
                [
                    'mensagem' => $exception->getMessage()
                ],
                $exception->getStatusCode()
            );

            $event->setResponse($responseFactory->getResponse());
        }
    }

    public function handlerEntityException(ExceptionEvent $event)
    {
        if($event->getThrowable() instanceof EntityFactoryException) {
            $exception = $event->getThrowable();
            $responseFactory = new ResponseFactory(
                false,
                [
                    'mensagem' => $exception->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );

            $event->setResponse($responseFactory->getResponse());
        }
    }

    public function handlerGenericException(ExceptionEvent $event)
    {

        $this->logger->critical('Uma exceção ocorreu. {stack}', [
            'stack' => $event->getThrowable()->getTraceAsString()
        ]);

        $exception = $event->getThrowable();
        $responseFactory = new ResponseFactory(
            false,
            [
                'mensagem' => $exception->getMessage()
            ],
            Response::HTTP_BAD_REQUEST
        );

        $event->setResponse($responseFactory->getResponse());

    }
}