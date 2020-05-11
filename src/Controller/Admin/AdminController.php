<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Admin;
use App\Repository\AdminRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminController extends BaseAdminController
{
    /**
     * @Route("/set-locale", name="admin_set_locale")
     */
    public function set_locale(Request $request)
    {
        $newLocale = $request->query->get('lang');
        $availableLocales = $this->getParameter('app.admin_supported_locales');

        if (in_array($newLocale, $availableLocales)) {
            $session = $request->getSession();
            $session->set(\App\EventSubscriber\AdminUserLocaleSubscriber::LOCALE_SESSION_PARAM, $newLocale);

            $user = $this->getUser();
            if ($user) {
                /** @var AdminRepository $userRepository */
                $userRepository = $this->getDoctrine()->getRepository(Admin::class);
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

    protected function persistUserEntity(User $user)
    {
        $encodedPassword = $this->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($encodedPassword);

        parent::persistEntity($user);
    }

    protected function updateUserEntity(User $user)
    {
        $encodedPassword = $this->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($encodedPassword);

        parent::updateEntity($user);
    }

    protected function persistAdminEntity(Admin $admin)
    {
        $encodedPassword = $this->encodePassword($admin, $admin->getPlainPassword());
        $admin->setPassword($encodedPassword);

        parent::persistEntity($admin);
    }

    protected function updateAdminEntity(Admin $admin)
    {
        $encodedPassword = $this->encodePassword($admin, $admin->getPlainPassword());
        $admin->setPassword($encodedPassword);

        parent::updateEntity($admin);
    }

    private function encodePassword(UserInterface $user, $plainPassword)
    {
        $encoder = $this->get('security.encoder_factory')->getEncoder(get_class($user));
        return $encoder->encodePassword($plainPassword);
    }
}