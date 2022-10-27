<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\BaseUser;
use App\Entity\UserAuthToken;
//use \Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @Route("/user/login", name="login", methods="POST")
     *
     * @return JsonResponse
     */
    public function loginUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $username = $request->get('username');
        $password = $request->get('password');
        $uuid = $request->get('uuid');

        if (empty($username) || empty($password) || empty($uuid)) {
            return $this->json(['success' => false, 'error' => 'INVALID_DATA'], Response::HTTP_BAD_REQUEST);
        }

        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1) {
            return $this->json(['success' => false, 'error' => 'INVALID_UUID_FORMAT'], Response::HTTP_NOT_FOUND);
        }

        /** @var BaseUser $user */
        $user = $em->getRepository('App:BaseUser')->findOneBy(['username' => $username]);
        if ($user === null) {
            return $this->json(['success' => false, 'error' => 'USERNAME_NOT_FOUND'], Response::HTTP_NOT_FOUND);
        }

        //echo $this->json(["data" => $user->toArray()]);
        if ($passwordEncoder->isPasswordValid($user, $password)) {

            $token = $em->getRepository('App:UserAuthToken')->findOneBy(['uuid' => $uuid]);
            if ($token === null) {
                $token = new UserAuthToken();
                $token->setUser($user);
                $token->setUuid($uuid);

                $em->persist($token);
                $em->flush();
            }

            /* $payload = array(
                "iat" => time(),
                "exp" => time() + (12 * 3600),
                "sub" => $user->toArray()['id']
            );*/
            //$jwt = JWT::encode($payload, $_ENV['APP_SECRET']);
            return $this->json(['success' => true, 'user' => $user->toArray(), 'token' => $token->getAuthToken()/*, 'jwt' => "Bearer " . $jwt*/]);
        } else {
            return $this->json(['success' => false, 'error' => 'WRONG_CREDENTIALS'], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @Route("/user/add", name="register", methods="POST")
     *
     * @return JsonResponse
     */
    public function registerUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $username = $request->get('username');
        $email = $request->get('email');
        $password = $request->get('password');
        $name = $request->get('name');
        $surname = $request->get('surname');
        $cf = $request->get('cf');
        $license = $request->get('license');

        if (empty($email) || empty($password) || empty($username) || empty($name) || empty($surname) || empty($cf) || empty($license)) {
            return $this->json(['success' => false, 'error' => 'INVALID_DATA'], Response::HTTP_BAD_REQUEST);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['success' => false, 'error' => 'INVALID_EMAIL_FORMAT'], Response::HTTP_PARTIAL_CONTENT);
        }

        $user = $em->getRepository('App:BaseUser')->findOneBy(['username' => $username]);

        if ($user !== null) {
            return $this->json(['success' => false, 'error' => 'USER_ALREADY_EXISTS'], Response::HTTP_CONFLICT);
        }

        $mail = $em->getRepository('App:BaseUser')->findOneBy(['email' => $email]);

        if ($mail !== null) {
            return $this->json(['success' => false, 'error' => 'EMAIL_ALREADY_EXISTS'], Response::HTTP_CONFLICT);
        }

        $user = new BaseUser();

        $encodedPassword = $passwordEncoder->encodePassword($user, $password);

        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword($encodedPassword);
        $user->setName($name);
        $user->setSurname($surname);
        $user->setCf($cf);
        $user->setLicense($license);

        $em->persist($user);
        $em->flush();

        return $this->json(['success' => true, 'user' => $user->toArray()]);
    }

    private function genstr(int $len)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        if ($len > 0 && $len < 65)
            for ($i = 0; $i < $len; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

        return $randomString;
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @Route("/user/edit", name="edit_user", methods="PATCH")
     *
     * @return JsonResponse
     */
    public function editUser(Request $request)
    {
        /** @var BaseUser $user */
        $em = $this->getDoctrine()->getManager();
        $caller = $this->getUser();

        $user = $em->getRepository('App:BaseUser')->find($request->get('id'));

        if ($user === null) {
            return $this->json(['success' => false], 404);
        }

        if(!$caller->isAdmin() || $caller->getId() !== $user->getId()) {
            return $this->json(['success' => false], Response::HTTP_UNAUTHORIZED);
        }

        if ($request->get('name') !== null) {
            $user->setName($request->get('name'));
        }

        if ($request->get('surname') !== null) {
            $user->setSurname($request->get('surname'));
        }

        if ($request->get('cf') !== null) {
            $user->setCf($request->get('cf'));
        }

        if ($request->get('cf') !== null) {
            $user->setCf($request->get('cf'));
        }
        $em->persist($user);
        $em->flush();

        return $this->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @Route("/user/logout", name="edit_user", methods="GET")
     *
     * @return JsonResponse
     */
    public function logOutUser(Request $request)
    {
        $uuid = $request->get('uuid');
        $em = $this->getDoctrine()->getManager();

        if (empty($uuid)) {
            return $this->json(['success' => false, 'error' => 'INVALID_DATA'], Response::HTTP_BAD_REQUEST);
        }

        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1) {
            return $this->json(['success' => false, 'error' => 'INVALID_UUID_FORMAT'], Response::HTTP_PARTIAL_CONTENT);
        }

        $token = $em->getRepository('App:UserAuthToken')->findOneBy(['uuid' => $uuid]);

        if (!empty($token)) {
            $em->remove($token);
            $em->flush();
        }

        return $this->json(['success', true]);
    }

    /**
     * @param Request $request
     * @Route("/user/get", name="get_user", methods="GET")
     * 
     * @return JsonResponse
     */
    public function getBaseUser(Request $request)
    {
        $users = $this->getDoctrine()->getManager()->getRepository('App:BaseUser')->findAll();
        //echo serialize($users); 
        $out = array();

        foreach ($users as $user) {
            array_push($out, $user->toArray());
        }
        return $this->json(['success' => true,'results' => $out, 'total' => count($users)]);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @Route("/user/delete", name="disable_user", methods="POST")
     *
     * @return JsonResponse
     */
    public function disableUser(Request $request)
    {
        /** @var BaseUser $user */
        $em = $this->getDoctrine()->getManager();
        $caller = $this->getUser();

        $user = $em->getRepository('App:BaseUser')->find($request->get('id'));

        if ($user === null) {
            return $this->json(['success' => false], Response::HTTP_NOT_FOUND);
        }

        if(!$caller->isAdmin() || ($caller->getId() !== $user->getId()) && $user->isAdmin()) {
            return $this->json(['success' => false], Response::HTTP_UNAUTHORIZED);
        }

        $user->setIsActive(false);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @Route("/user/restore", name="restore_user", methods="POST")
     *
     * @return JsonResponse
     */
    public function enableUser(Request $request)
    {
        /** @var BaseUser $user */
        $em = $this->getDoctrine()->getManager();
        $caller = $this->getUser();

        $user = $em->getRepository('App:BaseUser')->find($request->get('id'));

        if ($user === null) {
            return $this->json(['success' => false], Response::HTTP_NOT_FOUND);
        }

        if(!$caller->isAdmin() || ($caller->getId() !== $user->getId()) && $user->isAdmin()) {
            return $this->json(['success' => false], Response::HTTP_UNAUTHORIZED);
        }

        $user->setIsActive(true);
    }
}
