<?php

 namespace DavidUserBundle\Controller;

use DavidUserBundle\Entity\User;
use DavidUserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function registerAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            return $this->redirectToRoute('blog_page_index');
        }

        $user = new User();
        $user->setRoles('ROLE_USER');
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $hash = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $avatar = $user->getAvatar();
            $avatarName = md5(uniqid()).'.'.$avatar->guessExtension();
            $avatar->move(
                $this->getParameter('avatar_directory'),
                $avatarName
            );
            $user->setAvatar($avatarName);

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);

            $em->flush();

            return $this->redirect('/login');

        }

        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('DavidUserBundle:Security:register.html.twig', array(
            'last_username'=>$authenticationUtils->getLastUsername(),
            'error'=>$authenticationUtils->getLastAuthenticationError(),
            'form'=>$form->createView(),
        ));
    }

    public function loginAction(Request $request)
    {

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'DavidUserBundle:Security:login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );

    }
}