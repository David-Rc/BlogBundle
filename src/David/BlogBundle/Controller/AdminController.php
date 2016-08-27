<?php

namespace David\BlogBundle\Controller;
use David\BlogBundle\Entity\Article;
use David\BlogBundle\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


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

        //////////////////////////////////////SELECT ALL ARTICLES/////////////////////////////

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Article');

        $articles = $repository->findAll();

        /*
         *  Variables qui permettent de gérer les options de la page index de l'administrateur : Voir articles publiées, non publiées.
         */

        $action = false;
        $publish = true;
        $noPublish = false;
        $all = true;

        return $this->render('DavidBlogBundle:Admin:index.html.twig', array(
            'articles' => $articles,
            'name'=>$admin->getUsername(),
            'action'=>$action,
            'publish'=>$publish,
            'noPublish'=>$noPublish,
            'all'=>$all,
        ));

    }

    public function articleAction(Request $request, $id)//ADMIN
    {
        /**
         * @route("/blog/admin/article/{id}" name="blog_admin_article")
         */


        /*
         * Récupere l'article selectionné et ses commentaires à partir de son id.
         */

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Article');

        $article = $repository->find($id);

        $repository2 = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Comment');

        $comments = $repository2->findBy(array('article'=>$article));



        return $this->render('DavidBlogBundle:Admin:article.html.twig', array(
            'article' => $article,
            'comments'=>$comments
        ));
    }

    public function deleteArticleAction($id)
    {
        /**
         * @route('/blog/admin/delete/article/{id}' name='blog_admin_delete_article')
         */

        /*
         * Récupere l'article à partir de son id ainsi que ses commentaires et son image et les supprimes.
         */

        $em = $this->getDoctrine()
            ->getEntityManager();

        $article = $em->getRepository('DavidBlogBundle:Article')->findOneBy(array('id'=>$id));

        $comments = $em->getRepository('DavidBlogBundle:Comment')->findBy(array('article'=>$article));

        if($article->getImage() != null)
        {
            $img = $article->getImage()->getId();
            $image = $em->getRepository('DavidBlogBundle:Image')->findOneBy(array('id'=>$img));
            $em->remove($image);
        }


        foreach($comments as $comment)
        {
            $em->remove($comment);
        }

        $em->remove($article);

        $em->flush();

        return $this->redirect('/blog/admin');
    }

    public function deleteCommentAction($id)
    {
        /**
         * @route('/blog/admin/delete/comment/{id}' name='blog_admin_delete_comment')
         */
        /*
         * Recupere le commentaire à partir de son id et le supprime
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

        /*
         * Affiche le formulaire d'ajout d'article. Avec un ajout d'image non obligatoire.
         */

        $article = new Article();
        $article->setDate(new \DateTime());
        $article->setAuthor($this->getUser()->getUsername());
        $articleView = new Article();
        $articleView->setDate(new \DateTime());
        $articleView->setAuthor($this->getUser()->getUsername());

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted())
        {

                $article = $form->getData();
                $articleView = $form->getData();

                if($article->getImage() != null)
                {

                    $img = $article->getImage()->getUrl();
                    $imgName = md5(uniqid()).'.'.$img->guessExtension();
                    $img->move(
                        $this->getParameter('img_directory'),
                        $imgName
                    );
                    $article->getImage()->setUrl($imgName);
                }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            unset($article);
            unset($form);
            $article = new Article();
            $form = $this->createForm(ArticleType::class, $article);
        }

        return $this->render('DavidBlogBundle:Admin:addArticle.html.twig', array(
            'form' => $form->createView(),
            'article'=>$articleView,
    ));
    }

    public function modifyArticleAction(Request $request, $id)
    {
        /*
         * Recupere l'article à partir de son id et afffiche un formulaire de création d'article avec les données de l'article sélectionné
         */
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Article');

        $article = $repository->find($id);

        $image = $article->getImage()->getURL();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $article = $form->getData();

            if($form->getData()->getImage()->getUrl() != null)
            {
                $img = $article->getImage()->getUrl();
                $imgName = md5(uniqid()).'.'.$img->guessExtension();
                $img->move(
                    $this->getParameter('img_directory'),
                    $imgName
                );
                $article->getImage()->setUrl($imgName);
            } else {
                $article->getImage()->setUrl($image);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirect('/blog/admin/article/'.$id);

        }

        return $this->render('DavidBlogBundle:Admin:modifyArticle.html.twig', array(
            'form'=>$form->createView(),
            'articleId'=>$id,
        ));
    }

    public function noPublishAction()
    {
        /*
         * Gere l'affichage des articles non-publiés.
         */
        $admin = $this->getUser();

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Article');

        $articles = $repository->findBy(array('published'=>false));

        $action = true;
        $publish = false;
        $noPublish = true;
        $all = false;

        return $this->render('DavidBlogBundle:Admin:index.html.twig', array(
            'articles' => $articles,
            'name'=>$admin->getUsername(),
            'action'=>$action,
            'publish'=>$publish,
            'noPublish'=>$noPublish,
            'all'=>$all
        ));
    }

    public function PublishAction()
    {
        /*
         * Gere l'affichage des articles publiés.
         */
        $admin = $this->getUser();

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Article');

        $articles = $repository->findBy(array('published'=>true));

        $action = true;
        $publish = true;
        $noPublish = false;
        $all = false;

        return $this->render('DavidBlogBundle:Admin:index.html.twig', array(
            'articles' => $articles,
            'name'=>$admin->getUsername(),
            'action'=>$action,
            'publish'=>$publish,
            'noPublish'=>$noPublish,
            'all'=>$all
        ));
    }

}