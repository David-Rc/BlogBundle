<?php

namespace David\BlogBundle\Controller;

use David\BlogBundle\Entity\Comment;
use David\BlogBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class PageController extends Controller
{
    public $articles;
    public $session;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */

// Création d'un utilisateur administrateur (Utile dans le cadre de la certification Simplon)
//
//   public function addAction(Request $request)
//    {
//
//        $admin = new Admin();
//
//        $admin->setUsername('root');
//        $password = 'root';
//        $admin->setEmail('role@localhost.com');
//        $admin->setRoles('ROLE_ADMIN');
//        $admin->setIsActivate(true);
//
//        $hash = $this->get('security.password_encoder')->encodePassword($admin, $password);
//
//        $admin->setPassword($hash);
//
//        $em = $this->getDoctrine()->getManager();
//
//        $em->persist($admin);
//
//        $em->flush();
//
//        return $this->render('DavidBlogBundle:Page:add.html.twig');
//    }
//

    public function indexAction(Request $request)
    {

        /**
         * @Route("/blog", name="blog_page_index")
         * @Security("has_role('ROLE_USER')")
         */

        //Verification de l'identification

        if($this->getUser()){
            $user = $this->getUser()->getUsername();
        } else {
            $user = 'Visiteur';
        }


        $session = $request->getSession();


        if(!$session->get('view')){

            $session->set('view', []);

        }

    ///////////////////////////////////////CONNEXION A DOCTRINE////////////////////////////////////////
        //Ce connecte à la base de données et recupére les données correspondante à l'entité Article.

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Article');

////////////////////////////////////////SELECT ARTICLE WHERE PUBLISHED = TRUE : DOCTRINE//////////////////////////////////////////////
        //Récupere les articles dont l'option publié est vrai.

        $articles = $repository->findBy(
            array(
                'published' => 1
        ));

/////////////////////////////////////////RENDER///////////////////////////////////////////////////////////
                return $this->render(
                    'DavidBlogBundle:Page:index.html.twig', array(
                    'articles'=>$articles,
                    'name'=>$user,
                ));
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function viewAction(Request $request, $id)
    {

        /**
         * @Route("/blog/article/{id}", name='blog_page_article')
         */

////////////////////////////////////////////INIT SESSION//////////////////////////////////////
        // Petit test de session qui permet de comptabiliser la lecture d'un l'article

        $session = $request->getSession();

        if(!$session->get('view')){

            $session->set('view', []);


        }

        $view = $session->get('view');
        array_push($view, $id);


     ////////////////////////////////////VIEW COUNT///////////////////////////////////////////
        //Vous avez lu cet article x fois.

        $x = 0;

        for($c=0; $c<= count($view) - 1; $c++)
        {
            if($view[$c] == $id)
            {
                $x++;
            }
        }

        $session->set('view', $view);


////////////////////////////////////// SELECT ARTICLE => ID : DOCTRINE //////////////////////////////////
        //Récupere l'article qui correspond à l'id et au titre de la page index. Fonctionne au click


        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Article');

        $article = $repository->find($id);


        if (null === $article) {
            throw new NotFoundHttpException("L'article d'id :".$id." n'existe pas.");
        }

        ///////////////////////////////////FORM////////////////////////////////////////
        if($this->getUser())
        {

            $comment = new Comment();
            $date = new \DateTime();
            $comment->setDate($date);
            $comment->setArticle($article);
            $comment->setAuthor($this->getUser());

            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {

                $comment = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();
                unset($comments);
                unset($form);
                $comment = new Comment();
                $form = $this->createForm(CommentType::class, $comment);
                $this->redirect('/blog/article/'.$id);

            }
        }


///////////////////////////////////  SELECT TASK ID => ARTICLE : DOCTRINE////////////////////////
        //Recupere les commentaires liée à l'article

        $repository2 = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Comment');

        $comments = $repository2->findBy(array('article'=>$article), array('id'=>'desc'));

//////////////////////////////////// RETURN THIS RENDER/////////////////////////////////////
        /*
         * Si l'utilisateur est identifier, alors il peut posté des commentaire,
         * sinon il est considérer comme visiteur et ne peux que lire les articles et leurs commentaires.
         */

        if($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN'))
        {
            return $this->render('DavidBlogBundle:Page:article.html.twig', array
            (
                'form'=> $form->createView(),
                'article'=>$article,
                'comments'=>$comments,
                'x'=>$x,
            ));
        }else
        {
            return $this->render('DavidBlogBundle:Page:article.html.twig', array
            (
                'article'=>$article,
                'comments'=>$comments,
                'x'=>$x,
            ));
        }

    }
}

