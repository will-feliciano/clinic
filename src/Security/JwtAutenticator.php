<?php

namespace App\Security;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtAutenticator extends AbstractGuardAuthenticator
{
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function start(
        Request $request, 
        ?AuthenticationException $authException = null
    ) {  }

    public function supports(Request $request)
    {
        return $request->getPathInfo() !== '/login';
    }

    public function getCredentials(Request $request)
    {
        $token = str_replace(
            'Bearer ',
            '',
            $request->headers->get('Token')
        );

        try{
            return JWT::decode($token, 'chave', ['HS256']);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getUser(
        $credentials, 
        UserProviderInterface $userProvider
    ) {
        if(!is_object($credentials) || !property_exists($credentials, 'username')){
            return null;
        }

        return $this->repository->findOneBy(['username' => $credentials->username]);
    }

    public function checkCredentials(
        $credentials, 
        UserInterface $user
    ) {
        return is_object($credentials) && property_exists($credentials, 'username');
    }
    
    public function onAuthenticationFailure(
        Request $request, 
        AuthenticationException $exception
    ) {
      return new JsonResponse([
          'erro' => 'Falha na autenticação'
      ], Response::HTTP_UNAUTHORIZED);  
    }
    
    public function onAuthenticationSuccess(
        Request $request, 
        TokenInterface $token, 
        string $providerKey
    ) {
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
