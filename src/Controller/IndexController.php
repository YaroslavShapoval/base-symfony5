<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/set-locale", name="set_locale")
     */
    public function set_locale(Request $request)
    {
        $newLocale = $request->query->get('lang');
        $availableLocales = $this->getParameter('app.supported_locales');

        if (in_array($newLocale, $availableLocales)) {
            $session = $request->getSession();
            $session->set('_locale', $newLocale);

            $user = $this->getUser();
            if ($user) {
                /** @var UserRepository $userRepository */
                $userRepository = $this->getDoctrine()->getRepository(User::class);
                $userRepository->setLocale($user, $newLocale);
            }
        }

        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        } else {
            return $this->redirectToRoute('index');
        }
    }
}
