<?php
namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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
        $form->add('submit',SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            //check for uniqueness
            if (!preg_match('/^[-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6}$/', $form->get('email')->getData())){
                return [
                    'message' => 'Некорректный email'
                ];
            }

            $email = $this->getDoctrine()->getRepository('VinilShopBundle:User')->findBy(['email' => $form->get('username')->getData()]);

            if($email){
                return [
                    'message' => ' Пользователь с таким email уже зарегистрирован'
                ];
            }
            $login = $this
                ->getDoctrine()
                ->getRepository('VinilShopBundle:User')
                ->findBy(['username' => $form->get('username')->getData()]);
            if($login){
                return [
                    'form' => $form->createView(),
                    'message' => 'Пользователь с таким логином уже зарегистрирован'
                ];
            }
            //end check

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $user->setIsActive(true);
            $user->setRole('ROLE_USER');


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home_page');

        }

        return [
            'form' => $form->createView(),
            'message' => $message
        ];

    }
}