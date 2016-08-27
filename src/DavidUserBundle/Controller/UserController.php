<?php

namespace DavidUserBundle\Controller;

use DavidUserBundle\Entity\User;
use DavidUserBundle\Form\AdminType;
use DavidUserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function indexAction()
    {
        return $this->render('DavidUserBundle:User:profil.html.twig');
    }

    public function postedCommentAction(User $author)
    {
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Comment');

        $comments = $repository->findBy(array('author'=>$author));

        return $this->render('DavidUserBundle:User:postedComment.html.twig', array(
            'comments'=>$comments
        ));
    }

    public function deleteCommentAction($id)
    {

        $user = $this->getUser();

        $em = $this->getDoctrine()
            ->getEntityManager();

        $comment = $em->getRepository('DavidBlogBundle:Comment')
                      ->findOneBy(array('id' => $id));

        echo $comment->getContent();

        $em->remove($comment);

        $em->flush();

        return $this->redirect('/posted_comment/'.$user->getId());
    }

    public function modifyProfilAction(Request $request)
    {
            $user = $this->getUser();
            $avatar = $user->getAvatar();
            $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

//            if(!$user->getUsername())
//            {
//                $user->setUsername($username);
//            }
//
//            if(!$user->getAvatar())
//            {
//                $user->setAvatar($avatar);
//            }
//
//            if(!$user->getPassword())
//            {
//                $user->setPassworrd($pass);
//            }

            $hash = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $ava = $user->getAvatar();
            if($form->getData()->getAvatar())
            {
                $avatarName = md5(uniqid()).'.'.$ava->guessExtension();
                $ava->move(
                    $this->getParameter('avatar_directory'),
                    $avatarName
                );
                $user->setAvatar($avatarName);
            } else {
                $user->setAvatar($avatar);
            }



            $em = $this->getDoctrine()->getManager();

            $em->persist($user);

            $em->flush();

            return $this->redirect('/profil');

        }

        return $this->render('DavidUserBundle:User:modifyProfil.html.twig', array(
            'form'=>$form->createView()
        ));
    }

    public function deleteProfilAction()
    {

        $user = $this->getUser();

        $em = $this->getDoctrine()
            ->getEntityManager();


        $comments = $em->getRepository('DavidBlogBundle:Comment')->findBy(array('author'=>$user));
        if($comments)
        {
            foreach($comments as $comment)
            {
                $em->remove($comment);
            }
        }

        if($user->getAvatar()){
            $img = $user->getAvatar();
            $image = $em->getRepository('DavidBlogBundle:Image')->findOneBy(array('id'=>$img));
            $em->remove($image);
        }

        $em->remove($user);

        $em->flush();

        return $this->redirect('/login');
    }
}

