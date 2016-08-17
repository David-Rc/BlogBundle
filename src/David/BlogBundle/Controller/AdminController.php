<?php

namespace David\BlogBundle\Controller;
use David\BlogBundle\Entity\Comment;
use David\BlogBundle\Entity\Articles;
use David\BlogBundle\Entity\Article;
use David\BlogBundle\Entity\Image;
use David\BlogBundle\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;


class AdminController extends Controller
{


    public function indexAction(Request $request) //ADMIN
    {

        /**
         * @Route("/blog/admin", name='blog_admin_index')
         */



        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            return $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        }

        $admin = $this->getUser();

        //////////////////////////////////////Requete d'affichage des articles/////////////////////////////
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Article');

        $articles = $repository->findAll();

        ////////////////////////////////////////////Formulaire de Creation d'article//////////////////////////////////////////



        ///////////////////////////////////////////////////////////////////////////////////////////////////////////

        return $this->render('DavidBlogBundle:Admin:index.html.twig', array(
            'articles' => $articles,
            'name'=>$admin->getUsername(),
        ));

    }

    public function articleAction(Request $request, $id)//ADMIN
    {
        /**
         * @route("/blog/admin/article/{id}" name="blog_admin_article")
         */

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Article');

        $article = $repository->find($id);

        $repository2 = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Comment');

        $comments = $repository2->findBy(array('article'=>$article));

        $image = $article->getImage()->getUrl();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $article = $form->getData();
            if($form->getData()->getImage()->getUrl())
            {
                $img = $article->getImage()->getUrl();
                $imgName = md5(uniqid()).'.'.$img->guessExtension();
                $img->move(
                    $this->getParameter('img_directory'),
                    $imgName
                );
                $article->getImage()->setUrl($imgName);
            } else
            {
                $article->getImage()->setUrl($image);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->redirect('blog/admin/article/'.$id);

        }

        return $this->render('DavidBlogBundle:Admin:article.html.twig', array(
            'form'=>$form->createView(),
            'article' => $article,
            'comments'=>$comments
        ));
    }

    public function deleteArticleAction($id)
    {
        /**
         * @route('/blog/admin/delete/article/{id}' name='blog_admin_delete_article')
         */

        $em = $this->getDoctrine()
            ->getEntityManager();

        $article = $em->getRepository('DavidBlogBundle:Article')->findOneBy(array('id'=>$id));

        $comments = $em->getRepository('DavidBlogBundle:Comment')->findBy(array('article'=>$article));


        $img = $article->getImage()->getId();
        $image = $em->getRepository('DavidBlogBundle:Image')->findOneBy(array('id'=>$img));

        foreach($comments as $comment)
        {
            $em->remove($comment);
        }

        $em->remove($image);

        $em->remove($article);

        $em->flush();

        return $this->redirect('/blog/admin');
    }

    public function deleteCommentAction($id)
    {
        /**
         * @route('/blog/admin/delete/comment/{id}' name='blog_admin_delete_comment')
         */

        $em = $this->getDoctrine()
            ->getEntityManager();

        $comment = $em->getRepository('DavidBlogBundle:Comment')->findOneBy(array('id'=>$id));

        $article = $comment->getArticle()->getId();

        $em->remove($comment);

        $em->flush();

        return $this->redirect('/blog/admin/article/'.$article);
    }

    public function addArticleAction(Request $request)
    {
        $article = new Article();
        $article->setDate(new \DateTime());
        $article->setAuthor($this->getUser()->getUsername());

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $article = $form->getData();
            $img = $article->getImage()->getUrl();
            $imgName = md5(uniqid()).'.'.$img->guessExtension();
            $img->move(
                $this->getParameter('img_directory'),
                $imgName
            );
            $article->getImage()->setUrl($imgName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return $this->redirect('/blog/admin');
        }

        return $this->render('DavidBlogBundle:Admin:addArticle.html.twig', array(
            'form' => $form->createView(),
    ));
    }

}