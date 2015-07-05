<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class PostController
 *
 * @Route("/post")
 *
 * @package AppBundle\Controller
 */
class PostController extends Controller
{
    /**
     * Server side Post datatable.
     *
     * @Route("/", name="post")
     * @Method("GET")
     * @Template(":post:index.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        $datatable = $this->get("app.datatable.server_side.post");
        $datatable->buildDatatable();

        return array(
            "datatable" => $datatable,
        );
    }

    /**
     * Get all Post entities.
     *
     * @Route("/results", name="post_results")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexResultsAction()
    {
        $datatable = $this->get("app.datatable.server_side.post");
        $datatable->buildDatatable();

        $query = $this->get("sg_datatables.query")->getQueryFrom($datatable);

        // Callback example
        $function = function($qb)
        {
            $qb->andWhere("post.visible = true");
        };

        // Add as WhereAll callback
        //$query->addWhereAll($function);

        // Or add as WhereResult
        //$query->addWhereResult($function);

        // Or to the query
        //$query->buildQuery();
        //$qb = $query->getQuery();
        //$qb->andWhere("post.visible = true");
        //$query->setQuery($qb);
        //return $query->getResponse(false);

        return $query->getResponse();
    }

    /**
     * Client side Post datatable.
     *
     * @Route("/cs", name="cs_post")
     * @Method("GET")
     * @Template(":post:index.html.twig")
     *
     * @return array
     */
    public function clientSideIndexAction()
    {
        $repository = $this->getDoctrine()->getRepository("AppBundle:Post");

        $query = $repository->createQueryBuilder("p")
            ->select("p, c")
            ->join("p.comments", "c")
            ->getQuery();

        $results = $query->getArrayResult();

        foreach ($results as $key => $value) {
            $results[$key]["owner"] = "test";
        }

        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $datatable = $this->get("app.datatable.client_side.post");
        $datatable->buildDatatable();
        $datatable->setData($serializer->serialize($results, "json"));

        return array(
            "datatable" => $datatable,
        );
    }

    /**
     * Creates a new Post entity.
     *
     * @param Request $request
     *
     * @Route("/", name="post_create")
     * @Method("POST")
     * @Template(":post:new.html.twig")
     * @Security("has_role('ROLE_USER')")
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $entity = new Post();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('post_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Post entity.
     *
     * @param Post $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Post $entity)
    {
        $form = $this->createForm(new PostType(), $entity, array(
            'action' => $this->generateUrl('post_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Post entity.
     *
     * @Route("/new", name="post_new")
     * @Method("GET")
     * @Template(":post:new.html.twig")
     * @Security("has_role('ROLE_USER')")
     *
     * @return array
     */
    public function newAction()
    {
        $entity = new Post();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Post entity.
     *
     * @param integer $id
     * @param Request $request
     *
     * @Method("GET")
     * @Template(":post:show.html.twig")
     *
     * @return array
     */
    public function showAction($id, Request $request)
    {
        $request->setLocale($request->getPreferredLanguage(array('en', 'de')));

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @param integer $id
     * @param Request $request
     *
     * @Method("GET")
     * @Template(":post:edit.html.twig")
     * @Security("has_role('ROLE_USER')")
     *
     * @return array
     */
    public function editAction($id, Request $request)
    {
        $request->setLocale($request->getPreferredLanguage(array('en', 'de')));

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Post entity.
    *
    * @param Post $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Post $entity)
    {
        $form = $this->createForm(new PostType(), $entity, array(
            'action' => $this->generateUrl('post_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Post entity.
     *
     * @param Request $request
     * @param int     $id
     *
     * @Route("/{id}", name="post_update")
     * @Method("PUT")
     * @Template(":post:edit.html.twig")
     * @Security("has_role('ROLE_USER')")
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('post_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Post entity.
     *
     * @param Request $request
     * @param int     $id
     *
     * @Route("/{id}", name="post_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Post')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Post entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('post'));
    }

    /**
     * Creates a form to delete a Post entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
