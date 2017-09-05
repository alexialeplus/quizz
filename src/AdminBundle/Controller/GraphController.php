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
        $arrDate = explode("-", $todayDate);
        $year = $arrDate[0];
        $month = $arrDate[1];
        $day = $arrDate[2];
        $lastYear = ($year - 1) . '-' . $month . '-' . $day;

        if ($month !== "01") {
            $lastMonth = $year . '-0' . ($month - 1) . '-' . $day;
        }
        else
        {
           $lastMonth = $year . '- 12 -' . $day;
        }


        $repository = $this->getDoctrine()->getRepository(Score::class);

        $queryDay = $repository->createQueryBuilder('d')
                ->where('d.date LIKE :today')
                ->setParameter('today', $todayDate . '%')
                ->getQuery();

        $queryMonth = $repository->createQueryBuilder('m')
                ->where('m.date BETWEEN :lastmonth AND :today')
                ->setParameter('lastmonth', $lastMonth)
                ->setParameter('today', $todayDate)
                ->getQuery();

        $queryYear = $repository->createQueryBuilder('y')
                ->where('y.date BETWEEN :lastyear AND :today')
                ->setParameter('lastyear', $lastYear)
                ->setParameter('today', $todayDate)
                ->getQuery();


        $resultYear = $this->countAction($queryYear); 
        if ($resultYear !== FALSE) {
            $obYear = $this->createChart($resultYear, "chartYear", "Nombre de quizz par catégories sur l'année");
        }

        $resultMonth = $this->countAction($queryMonth);
        if ($resultMonth !== FALSE) {
            $obMonth = $this->createChart($resultMonth, "chartMonth", "Nombre de quizz par catégories sur le mois");
        }
        

    	return $this->render('AdminBundle:Default:index.html.twig', array(
        'chartYear' => $obYear,
        'chartMonth' => $obMonth
    	));
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
