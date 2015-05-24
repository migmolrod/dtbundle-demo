<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class BulkActionsController
 *
 * @Route("/post")
 *
 * @package AppBundle\Controller
 */
class BulkActionsController extends Controller
{
    /**
     * Delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="post_bulk_delete")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return Response
     */
    public function bulkDeleteAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get("data");

            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository("AppBundle:Post");

            foreach ($choices as $choice) {
                $entity = $repository->find($choice["value"]);
                $em->remove($entity);
            }

            $em->flush();

            return new Response("Success", 200);
        }

        return new Response("Bad Request", 400);
    }

    /**
     * Invisible action.
     *
     * @param Request $request
     *
     * @Route("/bulk/invisible", name="post_bulk_invisible")
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkInvisibleAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get("data");

            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository("AppBundle:Post");

            foreach ($choices as $choice) {
                $entity = $repository->find($choice["value"]);
                $entity->setVisible(false);
                $em->persist($entity);
            }

            $em->flush();

            return new Response("Success", 200);
        }

        return new Response("Bad Request", 400);
    }
}
