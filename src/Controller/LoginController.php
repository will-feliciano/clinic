<?php

namespace App\Controller;

use App\Helper\ResponseFactory;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(
        UserRepository $repository,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->repository = $repository;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/login", name="login")
     */
    public function index(Request $request)
    {
        $data = json_decode($request->getContent());
        
        if(is_null($data->usuario) || is_null($data->senha)) {
            
            $responseFactory = new ResponseFactory(
                false,
                [
                    'erro' => 'Favor enviar Usuário e Senha'
                ],
                Response::HTTP_BAD_REQUEST
            );

            return $responseFactory->getResponse();
        }

        $user = $this->repository->findOneBy([
            'username' => $data->usuario
        ]);

        if(!$this->encoder->isPasswordValid($user, $data->senha)) {

            $responseFactory = new ResponseFactory(
                false,
                [
                    'erro' => 'Usuário ou Senha inválidos'
                ],
                Response::HTTP_UNAUTHORIZED
            );

            return $responseFactory->getResponse();
        }

        $responseFactory = new ResponseFactory(
            false,
            [
                'access_token' => JWT::encode(['username' => $user->getUsername()], 'chave')
            ],
            Response::HTTP_OK
        );

        return $responseFactory->getResponse();
    }
}
