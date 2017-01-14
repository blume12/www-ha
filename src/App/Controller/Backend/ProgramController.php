<?php
/**
 * User: Jasmin
 * Date: 25.11.2016
 */

namespace App\Controller\Backend;

use App\Helper\Helper;
use App\Model\Program\Program;
use App\Model\Program\ProgramPrice;
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
        foreach ($programData as $key => $data) {
            $programData[$key]['editRoute'] = $this->getRoutePath('adminProgramEdit', ['id' => $data['PId']]);
            $programData[$key]['deleteRoute'] = $this->getRoutePath('adminProgramDelete', ['id' => $data['PId']]);
            $programData[$key]['programListRoute'] = $this->getRoutePath('adminReservationListPerProgram', ['id' => $data['PId']]);
        }

        return $this->getResponse([
            'programData' => $programData,
            'newEntryRoute' => $this->getRoutePath('adminProgramNew')
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
        $programPrice = new ProgramPrice($this->getConfig());

        $program->initImageUpload($this->getRequest()->files->get("fileToUpload"));

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $program->loadSpecificEntry($id);
            $formData['priceData'] = $programPrice->loadData();
            if ($formData == null) {

                return new RedirectResponse($this->getRoutePath('adminNotFound'));
            }
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formData['priceData'] = $programPrice->loadData();
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


    /**
     * Loads the action for the program new form.
     *
     * @param Request $request
     * @return bool|RedirectResponse|Response
     */
    public function newEntryAction(Request $request)
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }
        $this->setRequest($request);
        $id = $this->getRequest()->attributes->get('id');

        $this->setTemplateName('program-edit');
        $this->setPageTitle('Programm anlegen');

        $program = new Program($this->getConfig());
        $programPrice = new ProgramPrice($this->getConfig());
        $program->initImageUpload($this->getRequest()->files->get("fileToUpload"));
        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $program->loadSpecificEntry($id);
            $formData['priceData'] = $programPrice->loadData();
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formData['priceData'] = $programPrice->loadData();
            $formError = $program->checkErrors($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $program->saveData($formData);

            return new RedirectResponse($this->getRoutePath('adminProgramList'));
        }


        return $this->getResponse([
            'formAction' => $this->getRoutePath('adminProgramNew'),
            'formData' => $formData,
            'errorData' => $formError,
            'newEntry' => true
        ]);
    }


    /**
     * The action to delete a program.
     *
     * @param Request $request
     * @return bool|RedirectResponse|Response
     */
    public function deleteAction(Request $request)
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }
        $this->setRequest($request);
        $id = $this->getRequest()->attributes->get('id');

        $this->setTemplateName('program-delete');
        $this->setPageTitle('Programm löschen');

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
            $formError = Helper::checkErrorForDelete($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $program->deleteData($id);

            return new RedirectResponse($this->getRoutePath('adminProgramList'));
        }

        return $this->getResponse([
            'formAction' => $this->getRoutePath('adminProgramDelete', ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
        ]);
    }

}