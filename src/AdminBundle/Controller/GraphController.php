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

        dump($this->countAction($queryDay));

        /*$dataLastDay = $queryDay->getResult();
        $dataLastMonth = $queryMonth->getResult();
        $dataLastYear = $queryYear->getResult();

        foreach ($dataLastDay as $quizz) {
            $arrDay[] = $quizz->getTheme()->getName();
        }

        foreach ($dataLastMonth as $quizz) {
            $arrMonth[] = $quizz->getTheme()->getName();
        }

        foreach ($dataLastYear as $quizz) {
            $arrYear[] = $quizz->getTheme()->getName();
        }

        $nbDataDay = array_count_values($arrDay);
        $nbDataMonth = array_count_values($arrMonth);
        $nbDataYear = array_count_values($arrYear);*/


    	$series = array(
        	array("name" => "Data Serie Name", "data" => array(1,2,4,5,6,3,8))
    	);

    	$ob = new Highchart();
    	$ob->chart->renderTo('linechart');  // The #id of the div where to render the chart
    	$ob->title->text('Chart Title');
    	$ob->xAxis->title(array('text'  => "Horizontal axis title"));
    	$ob->yAxis->title(array('text'  => "Vertical axis title"));
    	$ob->series($series);

    	return $this->render('AdminBundle:Default:index.html.twig', array(
        'chart' => $ob
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
}
