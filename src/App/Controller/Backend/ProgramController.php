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
        $this->setRequest($request);
        $id = $this->getRequest()->attributes->get('id');

        $this->setTemplateName('program-edit');
        $this->setPageTitle('Programm bearbeiten');

        $program = new Program($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $program->loadSpecificEntry($id);
            if ($formData == null) {

                return new RedirectResponse($this->getRoutePath('adminNotFound'));
            }
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formError = $program->checkErrors($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $formData['id'] = $id;
            $program->saveData($formData);

            return new RedirectResponse($this->getRoutePath('adminProgramList'));
        }


        return $this->getResponse([
            'formAction' => $this->getRoutePath('adminProgramEdit', ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
        ]);
    }

}