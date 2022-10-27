<?php

namespace App\Controller;

use App\Entity\Assessment;
use App\Entity\UserAuthToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api")
 */
class AssessmentController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/assessment/add", name="add_assessment", methods="PUT")
     * 
     * @return JsonResponse
     */
    public function addAssessment(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $token = $em->getRepository(UserAuthToken::class)->findOneBy(['authToken' => substr($request->headers->get("Authorization"), 7)]);
        $user = $em->getRepository(BaseUser::class)->findOneBy(['id' => $token->getUser()]);
        if ($user === null) {
            return $this->json(['success' => false, 'error' => 'TAMPERED_OR_INVALID_API_TOKEN'], Response::HTTP_NOT_FOUND);
        }

        $assessment = new Assessment();
        $assessment->setUser($user);

        $damages = $request->get('damages');

        if ($damages !== null) {
            foreach ($damages as $dam) {
                $assessment->addDamage($dam);
            }
        }
    }

    /**
     * @param Request $request
     * @Route("/assessment/get", name="get_assessment", methods="GET")
     * 
     * @return JsonResponse
     */
    public function getAssessment(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $limit = 10;
        if ($request->get('limit') !== null && (int) $request->get('limit') !== 0) {
            $limit = min((int) $request->get('limit'), 100);
        }

        $offset = 0;
        if ($request->get('page') !== null && (int) $request->get('page') !==0) {
            $offset = ((int) $request->get('page') - 1) * $limit;
        }

        /*$assessments = $em->getRepository('App:Assessment')
                    ->createQueryBuilder('a')
                    //->orderBy('p.' . $keys[$key], $order)
                    ->setFirstResult($offset)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();
        */
        $assessments = $em->getRepository('App:Assessment')->findBy(array(), ['createdAt' => 'DESC'], $limit, $offset);
        
        $count = $em->getRepository('App:Assessment')
                    ->createQueryBuilder('a')
                    ->select('count(a.id)')
                    ->getQuery()
                    ->getResult();
        //echo serialize($carriers);
        $out = array();
        foreach ($assessments as $as) {
            array_push($out, $as->toArray());
        }
        
        return $this->json(['success' => true, 'results' => $out, 'total' => (int)$count[0][1]]);
    }

    /**
     * @param Request $request
     * @Route("/assessment/getById", name="get_assessment_by_id", methods="GET")
     * 
     * @return JsonResponse
     */
    public function getAssessmentById(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        if ($id !== null) {
            $assessments = $em->getRepository('App:Assessment')->findBy(['carrier' => $id]);
            $count = $em->getRepository('App:Assessment')
                    ->createQueryBuilder('a')
                    ->select('count(a.id)')
                    ->where('a.carrier = ?1')
                    ->setParameter(1, $id)
                    ->getQuery()
                    ->getResult();
            //echo serialize($carriers);
            $out = array();
            foreach ($assessments as $as) {
                array_push($out, $as->toArray());
            }
            return $this->json(['success' => true, 'results' => $out, 'total' => (int)$count[0][1]]);
        } else {
            return $this->json(['success' => false, 'results' => []]);
        }
    }
}
