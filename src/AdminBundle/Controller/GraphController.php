<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ob\HighchartsBundle\Highcharts\Highchart;
use UserBundle\Entity\Score;

class GraphController extends Controller
{
	/**
     * 
     *
     * @Route("/admin/graph", name="graph")
     */
	public function graphAction()
	{
		$todayDate = new \DateTime();
		$todayDate = $todayDate->format('Y-m-d');


        $repository = $this->getDoctrine()->getRepository(Score::class);

        $queryDay = $repository->createQueryBuilder('d')
                ->where('d.date LIKE :today')
                ->setParameter('today', $todayDate . '%')
                ->getQuery();

        $queryWeek = $repository->createQueryBuilder('w')
                ->where('w.date BETWEEN :aweekago AND :today')
                ->setParameter('aweekago', new \DateTime('-7 days'))
                ->setParameter('today', $todayDate)
                ->getQuery();

        $queryMonth = $repository->createQueryBuilder('m')
                ->where('m.date BETWEEN :lastmonth AND :today')
                ->setParameter('lastmonth', new \DateTime('-1 month'))
                ->setParameter('today', $todayDate)
                ->getQuery();

        $queryYear = $repository->createQueryBuilder('y')
                ->where('y.date BETWEEN :lastyear AND :today')
                ->setParameter('lastyear', new \DateTime('-1 year'))
                ->setParameter('today', $todayDate)
                ->getQuery();


        $resultDay = $this->countAction($queryDay);
        if (is_array($resultDay)) {
                $obDay = $this->createChart($resultDay, "chartDay", "Nombre de quizz par catégories sur le jour d'hier");
        }
        else
        {
            $obDay = $this->createChart(array(0), "chartDay", "Nombre de quizz par catégories aujourd'hui");
        }

        $resultWeek = $this->countAction($queryWeek);

        if (is_array($resultWeek)) {
                $obWeek = $this->createChart($resultWeek, "chartWeek", "Nombre de quizz par catégories sur la dernière semaine");
        }
        else
        {
            $obWeek = $this->createChart(array(0), "chartWeek", "Nombre de quizz par catégories sur la semaine dernière");
        }


        $resultMonth = $this->countAction($queryMonth);
        if ($resultMonth !== FALSE) {
            $obMonth = $this->createChart($resultMonth, "chartMonth", "Nombre de quizz par catégories sur le mois");
        }
        else
        {
            $obDay = $this->createChart(array(0), "chartMonth", "Nombre de quizz par catégories sur le mois");
        }

        $resultYear = $this->countAction($queryYear); 
        if (is_array($resultYear)) {
            $obYear = $this->createChart($resultYear, "chartYear", "Nombre de quizz par catégories sur l'année");
        }
        else
        {
            $obDay = $this->createChart(array(0), "chartYear", "Nombre de quizz par catégories sur l'année");
        }


    	return $this->render('AdminBundle:Default:index.html.twig', array(
            'chartYear' => $obYear,
            'chartMonth' => $obMonth,
            'chartDay' => $obDay,
            'chartWeek' => $obWeek
            )
        );
	}

    public function countAction($query) {

        if (isset($query) && !is_null($query)) {
            $data = $query->getResult();

            if (is_array($data) && !empty($data)) {

                foreach ($data as $quizz) {
                    $arrData[] = $quizz->getTheme()->getName();
                }

                $nbOccu = array_count_values($arrData);

                return $nbOccu;
            }

            else {

                return FALSE;
            }
        }
    }

    public function createChart($data, $id, $title) {

        if (isset($data) && is_array($data) && isset($id) && is_string($id) && isset($title) && is_string($title)) {

            foreach ($data as $nameQuizz => $nbQuizz) {
                $names[] = $nameQuizz;
                $nb[] = $nbQuizz;
            }

            $series = array(
            array("name" => "Nombre de quizz joués", "data" => $nb)
        );

            $ob = new Highchart();
            $ob->chart->renderTo($id);  // The #id of the div where to render the chart
            $ob->title->text($title);
            $ob->chart->type('column');
            $ob->xAxis->title(array('text'  => "Catégories"));
            $ob->yAxis->title(array('text'  => "Nombre de quizz"));
            $ob->xAxis->categories($names);
            $ob->series($series);

            return $ob;
        }
    }
}
