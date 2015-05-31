<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PageController
 *
 * @package AppBundle\Controller
 */
class PageController extends Controller
{
    /**
     * Locale action.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function localeAction(Request $request)
    {
        $request->setLocale($request->getPreferredLanguage(array('en', 'de')));

        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * Home action.
     *
     * @Route("/", name="homepage")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction()
    {
        return $this->render('page/home.html.twig');
    }
}
