<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 20:00
 */

namespace App\Controller\Frontend;


use App\Model\Program\Program;
use App\Model\Timescale\Timescale;
use Symfony\Component\HttpFoundation\Response;

class StartPageController extends FrontendController
{

    /**
     * The index action to the start page of the frontend.
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->setTemplateName('start-page');
        $this->setPageTitle('Tickets');
        $program = new Program($this->getConfig());
        $programData = $program->loadData(true, 6);
        foreach ($programData as $key => $data) {
            $programData[$key]['detailLink'] = $this->getRoutePath('programDetail', ['id' => $data['PId']]);
        }

        $timescale = new Timescale($this->getConfig());
        $timeScaleData = $timescale->loadData();
        if (count($timeScaleData) > 0) {
            $timeScaleData = $timeScaleData[0];
        }

        return $this->getResponse([
            'programLink' => $this->getRoutePath('programs'),
            'programData' => $programData,
            'timeScaleData' => $timeScaleData
        ]);
    }
}