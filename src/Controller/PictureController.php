<?php

namespace App\Controller;

use App\Entity\Picture;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api")
 */
class PictureController extends AbstractController
{

    const relativePath = '/groeco/public/';
    /**
     * @param Request $request
     *
     * @Route("/picture/add", name="add_picture", methods="POST")
     *
     * @return JsonResponse
     */
    public function addPicture(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $token = $em->getRepository('App:UserAuthToken')->findOneBy(['authToken' => substr($request->headers->get("Authorization"), 7)]);
        $user = $em->getRepository('App:BaseUser')->findOneBy(['id' => $token->getUser()]);
        if ($user === null) {
            return $this->json(['success' => false, 'error' => 'TAMPERED_OR_INVALID_API_TOKEN'], Response::HTTP_NOT_FOUND);
        }

        $file = $request->files->get('image');
        if ($file === null) {
            return $this->json(['success' => false, 'error' => 'FILE_NOT_PRESENT'], Response::HTTP_NOT_FOUND);
        }

        switch ($request->get('type')) {
            case 'DAMAGE':
                $damage = $em->getRepository('App:Damage')->findOneBy(['id' => $request->get('id')]);
                if ($damage === null) {
                    return $this->json(['success' => false, 'error' => 'DAMAGE_NOT_FOUND'], Response::HTTP_NOT_FOUND);
                }

                $key = 'pictures/damages/' . $damage->getId() .  '.' .  $file->getClientOriginalExtension();

                if (move_uploaded_file($file, $_SERVER['DOCUMENT_ROOT'] . self::relativePath . $key)) {

                    $picture = new Picture();
                    $picture->setPath($key);
                    $picture->setTakenAt(new DateTime());
                    $damage->setPicture($picture);

                    $em->persist($picture);
                    $em->persist($damage);
                    $em->flush();

                    return $this->json(['success' => true, 'object_key' => $key]);
                } else {
                    return $this->json(['success' => false, 'error' => 'FILE_UPLOAD_FAILED']);
                }
                break;
            case 'USER':
                $user = $em->getRepository('App:BaseUser')->findOneBy(['id' => $request->get('id')]);
                if ($user === null) {
                    return $this->json(['success' => false, 'error' => 'USER_NOT_FOUND'], Response::HTTP_NOT_FOUND);
                }

                $key = 'pictures/profile/' . $user->getId() .  '.' .  $file->getClientOriginalExtension();

                if (move_uploaded_file($file, $_SERVER['DOCUMENT_ROOT'] . self::relativePath . $key)) {

                    $picture = new Picture();
                    $user->setPicture($picture);
                    $picture->setPath($key);
                    $picture->setTakenAt(new DateTime());

                    $em->persist($picture);
                    $em->persist($user);
                    $em->flush();

                    return $this->json(['success' => true, 'object_key' => $key]);
                } else {
                    return $this->json(['success' => false, 'error' => 'FILE_UPLOAD_FAILED']);
                }
                break;

            case 'SIGNATURE':
                $assessment = $em->getRepository('App:Assessment')->findOneBy(['id' => $request->get('id')]);
                if ($assessment === null) {
                    return $this->json(['success' => false, 'error' => 'DAMAGE_NOT_FOUND'], Response::HTTP_NOT_FOUND);
                }

                $key = 'pictures/signatures/' . $user->getName() . $user->getSurname() . $assessment->getId() .  '.' .  $file->getClientOriginalExtension();

                if (move_uploaded_file($file, $_SERVER['DOCUMENT_ROOT'] . self::relativePath . $key)) {

                    $picture = new Picture();
                    $picture->setPath($key);
                    $picture->setTakenAt(new DateTime());
                    $assessment->setSignature();

                    $em->persist($picture);
                    $em->persist($assessment);
                    $em->flush();

                    return $this->json(['success' => true, 'object_key' => $key]);
                } else {
                    return $this->json(['success' => false, 'error' => 'FILE_UPLOAD_FAIED']);
                }
            break;
            case 'CARRIER':
                $carrier = $em->getRepository('App:Carrier')->findOneBy(['id' => $request->get('id')]);
                if ($carrier === null) {
                    return $this->json(['success' => false, 'error' => 'DAMAGE_NOT_FOUND'], Response::HTTP_NOT_FOUND);
                }

                $key = 'pictures/carrier/' . $user->getName() . $user->getSurname() . $carrier->getId() .  '.' .  $file->getClientOriginalExtension();

                if (move_uploaded_file($file, $_SERVER['DOCUMENT_ROOT'] . self::relativePath . $key)) {

                    $picture = new Picture();
                    $picture->setPath($key);
                    $picture->setTakenAt(new DateTime());
                    $carrier->addPicture($picture);

                    $em->persist($picture);
                    $em->persist($carrier);
                    $em->flush();

                    return $this->json(['success' => true, 'object_key' => $key]);
                } else {
                    return $this->json(['success' => false, 'error' => 'FILE_UPLOAD_FAIED']);
                }
            break;

            case 'UPLOAD':
                $key = 'pictures/img.' .  $file->getClientOriginalExtension();
                move_uploaded_file($file, $_SERVER['DOCUMENT_ROOT'] . self::relativePath . $key);
                return $this->json(['success' => true, 'file' => $key]);
                break;

            default:
                return $this->json(['success' => false, 'error' => 'FILE_TYPE_ERROR']);
        }
    }
}
