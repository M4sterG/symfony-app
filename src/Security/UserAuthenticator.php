<?php

namespace App\Security;

use App\Entity\BaseUser;
use App\Entity\UserAuthToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;


class UserAuthenticator extends AbstractGuardAuthenticator
{
    /** @var EntityManagerInterface $em */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function supports(Request $request)
    {
        return $request->headers->has('Authorization');
    }

    public function getCredentials(Request $request)
    {
        $apiToken = $request->headers->get('Authorization');
        return substr($apiToken, 7);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) 
        {
            return null;
        }
        $token = $this->em->getRepository(UserAuthToken::class)->findOneBy(['authToken' => $credentials]);;
        if($token !== null) {
            return $this->em->getRepository(BaseUser::class)->findOneBy(['id' => $token->getUser()]);
        }
        return null;   
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'success' => false,
            'message' => 'AUTHENTICATION_FAILED'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
