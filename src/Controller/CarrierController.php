<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Carrier;

/**
 * @Route("/api")
 */
class CarrierController extends AbstractController
{
    /**
     * @Route("/carrier", name="carrier")
     */
    public function index()
    {
        return $this->render('carrier/index.html.twig', [
            'controller_name' => 'CarrierController',
        ]);
    }

    /**
     * @param Request $request
     * @Route("/carrier/add", name="add_carrier", methods="PUT")
     * 
     * @return JsonResponse
     */
    public function addCarrier(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $name = $request->get('name');
        $description = $request->get('description');
        $immatriculation = $request->get('immatriculation');
        $licenseplate = $request->get('licenseplate');
        $vin = $request->get('vin');

        if (empty($name) || empty($immatriculation) || empty($licenseplate) || empty($vin)) {
            return $this->json(['success' => false, 'error' => 'INVALID_DATA'], Response::HTTP_BAD_REQUEST);
        }
        // TODO controllare formato targa, vin e n immatricolazione
        $carrier = $em->getRepository('App:BaseUser')->findOneBy(['licenseplate' => $licenseplate]);


        if ($carrier !== null) {
            return $this->json(['success' => false, 'error' => 'CARRIER_ALREADY_EXISTS'], Response::HTTP_CONFLICT);
        }

        $carrier = new Carrier();

        if ($description !== null)
            $carrier->setDescription($description);
        $carrier->setName($name);
        $carrier->setImmatriculation($immatriculation);
        $carrier->setLicensePlate($licenseplate);
        $carrier->setVin($vin);

        $em->flush();
    }

    /**
     * @param Request $request
     * @Route("/carrier/edit", name="edit_carrier", methods="POST")
     * 
     * @return JsonResponse
     */
    public function editCarrier(Request $request)
    {

        //TODO: check token if user == role admin isinuse == false

        $em = $this->getDoctrine()->getManager();

        if ($request->get('id') === null) {
            return $this->json(['success' => false, 'error' => 'NO_CARRIER_ID_PROVIDED'], Response::HTTP_BAD_REQUEST);
        }
        /** @var DealerUser $retailProduct */
        $carrier = $em->getRepository('App:Carrier')->find($request->get('id'));


        if ($carrier === null) {
            return $this->json(['success' => false, 'error' => 'ITEM_NOT_FOUND'], 404);
        }

        if ($request->get('name') !== null) {
            $retailProduct->setName($request->get('name'));
        }

        if ($request->get('licenseplate') !== null) {
            $retailProduct->setLicenseplate($request->get('licenseplate'));
        }

        if ($request->get('vin') !== null) {
            $retailProduct->setVin($request->get('vin'));
        }

        if ($request->get('description') !== null) {
            $retailProduct->setDescription($request->get('description'));
        }

        if ($request->get('immatriculation') !== null) {
            $retailProduct->setImmatriculation($request->get('immatriculation'));
        }

        if ($request->get('is_in_use') !== null) {
            $retailProduct->setIsInUse($request->get('is_in_use'));
        }

        if ($request->get('is_active') !== null) {
            $token = substr($request->headers->get('Authorization'), 7);
            $userId = $em->getRepository('App:UserAuthToken')->findOneBy(['authToken' => $token]);
            $user = $em->getRepository('App:BaseUser')->findOneBy(['id' => $userId]);
            if ($user === null) {
                return $this->json(['success' => false, 'error' => 'TAMPERED_OR_INVALID_API_TOKEN'], Response::HTTP_NOT_FOUND);
            }

            $roles = $user->getRoles();
            if (array_search('ROLE_ADMIN', $roles) !== false) {
                $retailProduct->setIsActive($request->get('is_active'));
            }
        }

        $em->persist($carrier);
        $em->flush();

        return $this->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @Route("/carrier/get", name="get_carrier", methods="GET")
     * 
     * @return JsonResponse
     */
    public function getCarrier(Request $request)
    {

        $carriers = $this->getDoctrine()->getManager()->getRepository('App:Carrier')->findAll();
        //echo serialize($carriers);
        $out = array();

        foreach ($carriers as $car) {
            if ($car->getIsActive() == true && $car->getIsInUse() == false) {
                array_push($out, $car->toArray());
            }
        }
        return $this->json(['results' => $out]);
    }
}
