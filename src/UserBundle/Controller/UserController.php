<?php

namespace UserBundle\Controller;

use UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class UserController extends Controller
{

    /**
     * Deletes a user entity.
     *
     * @Route("/profile/delete/{id}", name="user_delete")
     * @Method("GET")
     */
    public function deleteAction(User $user)
    {
        if(isset($user))
        {
            $um = $this->container->get('fos_user.user_manager');
            $um->deleteUser($user);

            return $this->redirectToRoute('user_default_index');
        }
    }

    /**
     * Display all quizzes played by the logged-in user and his results
     *
     * @Route("/profile/historic", name="historic")
     */
    public function displayAction()
    {
        $user = $this->getUser();
        $score = $user->getScore();

        return $this->render('UserBundle:Default:display.html.twig', array(
            'user' => $user,
            'score' => $score,
            )
        );
    }

    /**
     * Display all quizzes played by the logged-in user and his results
     *
     * @Route("/historic", name="cookies_historic")
     */
    public function historicAction(Request $request)
    {
        $historic = $request->cookies->get('scores');
        $historic = json_decode($historic, true);

        return $this->render('UserBundle:Default:historic.html.twig', array(
            'historic' => $historic,
            )
        );
    }


}
