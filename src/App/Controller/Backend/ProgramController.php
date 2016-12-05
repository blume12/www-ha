<?php
/**
 * User: Jasmin
 * Date: 25.11.2016
 */

namespace App\Controller\Backend;


use App\Model\Program\Program;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProgramController extends BackendController
{

    /**
     * Loads the action for the program list.
     *
     * @return bool|RedirectResponse|Response
     */
    public function indexAction()
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }

        $this->setTemplateName('program-list');
        $this->setPageTitle('Programme');

        $program = new Program($this->getConfig());
        $programData = $program->loadData();


        // TODO: Change links to really routes
        return $this->getResponse([
            'programData' => $programData
        ]);
    }

    /**
     * Loads the action for the program edit form.
     *
     * @param Request $request
     * @return bool|RedirectResponse|Response
     */
    public function editAction(Request $request)
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }

        $id = $request->attributes->get('id');

        $this->setTemplateName('program-edit');
        $this->setPageTitle('Programm bearbeiten');

        // TODO: create the form request

        $program = new Program($this->getConfig());
        $programData = $program->loadSpecificEntry($id);

        return $this->getResponse([
            'formAction' => $this->getRoutePath('adminProgramEdit'),
            'formData' => $programData
        ]);
    }

}