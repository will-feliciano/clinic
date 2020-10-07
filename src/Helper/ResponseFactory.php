<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseFactory
{
    /**
     * @var bool
     */
    private $success;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $itens;

    private $content;

    public function __construct(
        bool $success,
        $content,
        int $responseStatus = Response::HTTP_OK,
        int $page = null,
        int $itens = null
    ) {
        $this->success = $success;
        $this->content = $content;
        $this->responseStatus = $page == null ? $responseStatus : Response::HTTP_PARTIAL_CONTENT;
        $this->page = $page;
        $this->itens = $itens;
    }

    public function getResponse(): JsonResponse
    {
        $content = [
            'success' => $this->success,
            'page' => $this->page,
            'itens' => $this->itens,
            'content' => $this->content
        ];

        if(is_null($this->page)){
            unset($content['page']);
            unset($content['itens']);
        }

        return new JsonResponse($content, $this->responseStatus);
    }
}