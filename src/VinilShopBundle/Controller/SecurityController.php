<?php
namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use VinilShopBundle\Entity\User;
use VinilShopBundle\Form\UserType;

class SecurityController extends Controller
{

    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $user = $this->getUser();
        if($user){
            return $this->redirectToRoute('home_page');
        }

        $authUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return [
            'last_username' => $lastUsername,
            'error'         => $error,
        ];

    }

    /**
     * @Route("/logout", name="logout")
     *
     */
    public function logoutAction()
    {
        $this->get('security.token_storage')->setToken(NULL);

        return $this->redirectToRoute('home_page');
    }

    /**
     * @Route("/register", name="register")
     * @Template()
     */
    public function registerAction(Request $request)
    {
        $passwordEncoder = $this->get('security.password_encoder');
        $message = '';

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('submit',SubmitType::class, [
                                                'label' => 'Регистрация',
                                                ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('login');
        }
        return [
            'form' => $form->createView(),
            'message' => $message
        ];

    }
}