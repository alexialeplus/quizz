<?php

namespace QuizzBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use QuizzBundle\Entity\Category;
use QuizzBundle\Entity\Theme;
use QuizzBundle\Entity\Quizz;
use QuizzBundle\Entity\Answer;
use UserBundle\Entity\Score;

class DefaultController extends Controller
{
    /**
     * Render categories and themes on the homepage
     *
     * @Route("/{id}", name="home", requirements={"id": "\d+"})
     */
    public function indexAction(Category $id = null)
    {
    	if (is_null($id)) {
    		$allCategories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        	return $this->render('QuizzBundle:Default:index.html.twig', array(
        	'allCategories' => $allCategories,
        		)
        	);
    	}
    	else {
    		$allThemes = $this->getDoctrine()->getRepository(Theme::class)->findBy(
    			array('category' => $id, 'status' => 1)
    		);

        	return $this->render('QuizzBundle:Default:index.html.twig', array(
        	'allThemes' => $allThemes,
        		)
        	);
    	}

    }

    /**
     * Display all the questions, answers and the form
     *
     * @Route("/quizz/questions/{id}", name="quizz")
     */
    public function showAction(Theme $id)
    {
    	$theme = $this->getDoctrine()->getRepository(Theme::class)->find($id);

        $quizz = new Quizz();

        $form = $this->createFormBuilder($quizz)
                        ->add('submit', SubmitType::class, array('label' => 'Valider'))
                        ->getForm();


    	return $this->render('QuizzBundle:Default:show.html.twig', array(
    		'theme' => $theme,
            'form' => $form->createView(),
    		)
    	);
    }

    /**
     * Check user' answers, display and store the score
     *
     * @Route("/quizz/score/{id}", name="score")
     */
    public function resultAction(Request $request, Theme $id)
    {
        $userScore = 0;
        $userAnswers = [];
        $theme = $this->getDoctrine()->getRepository(Theme::class)->find($id);
        $user = $this->getUser();

        foreach ($request->request as $key => $value) {
            if (is_int($key)) {
                $userAnswers[] = $this->getDoctrine()->getRepository(Answer::class)->find($value);
            }
        }

        foreach ($userAnswers as $value) {
            if ($value->getStatus() === true) {
                $userScore++;
            }
        }

        if (!is_null($user)) {
            $score = new Score();
            $score->setUser($user);
            $score->setTheme($theme);
            $score->setScore($userScore);
            $score->setDate();

            $em = $this->getDoctrine()->getManager();
            $em->persist($score);
            $em->flush();
        }
        else
        {
            $response = new Response();

            if (!$request->cookies->get('scores')) {
                $historic = array($theme->getName() => $userScore);
            }
            else {
                $historic = $request->cookies->get('scores');
                $historic = json_decode($historic);
                $name = $theme->getName();
                $historic->$name = $userScore;
            }

            $historic = json_encode($historic);

            $response->headers->setCookie(new Cookie('scores', $historic));
            $response->send();
        }

        return $this->render('QuizzBundle:Default:result.html.twig', array(
            'score' => $userScore,
            'userAnswers' => $userAnswers,
            'theme' => $theme,
            )
        );
    }
}
