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

    public function modifyProfilAction()
    {
     $user = $this->getUser();
        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            $form = $this->createForm(AdminType::class, $user);
        } else {
            $form = $this->createForm(UserType::class, $user);
        }


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

            return $this->redirect('/modify/profil/'.$user->getId());

        }

        return $this->render('DavidUserBundle:User:modifyProfil.html.twig', array(
            'form'=>$form->createView()
        ));
    }
}

