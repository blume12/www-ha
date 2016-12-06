<?php
/**
 * Author: Jasmin Stern
 * Date: 06.12.2016
 * Time: 20:51
 */

namespace App\Controller\Frontend;


use App\Model\Program\Program;
use Symfony\Component\HttpFoundation\Response;

class ProgramController extends FrontendController
{
    /**
     * Loads the program action for the frontend.
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->setTemplateName('program-list');
        $this->setPageTitle('Programmblöcke');

        $program = new Program($this->getConfig());
        $programData = $program->loadData();

        return $this->getResponse([
            'programData' => $programData
        ]);
    }
}