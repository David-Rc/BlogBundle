<?php

namespace David\BlogBundle\Controller;

use David\BlogBundle\Entity\Comment;
use David\BlogBundle\Entity\Articles;
use David\BlogBundle\Entity\Article;
use David\BlogBundle\Entity\Image;
use David\BlogBundle\Form\CommentType;
use DavidUserBundle\Entity\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class PageController extends Controller
{
    public $articles;
    public $session;

//    public function __construct()
//    {
//        $this->articles = $this->initArticles();
//    }

//    public function initArticles()
//    {
//        for($i=0; $i<5; $i++)
//        {
//
//            $title = 'Article_';
//            $content = 'Hello World ';
//            $article = new Articles($i, $title.$i, $content.$i);
//            $articles[$i] = $article;
//
//        };
//
//        return $articles;
//
//    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */

//    // Création d'un admin.
//    public function addAction(Request $request)
//    {
//
//        $admin = new Admin();
//
//        $admin->setUsername('root');
//        $password = 'root';
//        $admin->setEmail('admin@localhost.com');
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



    public function indexAction(Request $request)
    {

        /**
         * @Route("/blog", name="blog_page_index")
         * @Security("has_role('ROLE_USER')")
         */

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

////////////////////////////////////////SELECT ALL ARTICLE : DOCTRINE//////////////////////////////////////////////

        $article = $repository->findBy(
            array(
                'published' => 1
        ));

/////////////////////////////////////////RENDER///////////////////////////////////////////////////////////

        return $this->render(
            'DavidBlogBundle:Page:index.html.twig', array(
            'articles'=>$article,
            'name'=>$user

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

        $session = $request->getSession();

        if(!$session->get('view')){

            $session->set('view', []);


        }

        $view = $session->get('view');
        array_push($view, $id);


     ////////////////////////////////////VIEW COUNT///////////////////////////////////////////
        //

        $x = 0;

        for($c=0; $c<= count($view) - 1; $c++)
        {
            if($view[$c] == $id)
            {
                $x++;
            }
        }

        $session->set('view', $view);


////////////////////////////////////// SELECT ID => ARTICLE : DOCTRINE //////////////////////////////////

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Article');

        $article = $repository->find($id);


        if (null === $article) {
            throw new NotFoundHttpException("L'article d'id :".$id." n'existe pas.");
        }

        ///////////////////////////////////FORM////////////////////////////////////////

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

            $this->redirect('/blog/article/'.$id);

        }

///////////////////////////////////  SELECT TASK ID => ARTICLE : DOCTRINE////////////////////////

        $repository2 = $this->getDoctrine()
            ->getManager()
            ->getRepository('DavidBlogBundle:Comment');

        $comments = $repository2->findBy(array('article'=>$article), array('id'=>'desc'));


        return $this->render('DavidBlogBundle:Page:article.html.twig', array
        (

            'form'=> $form->createView(),
            'article'=>$article,
            'comments'=>$comments,
            'x'=>$x,

        ));
    }

}

