<?php
/**
 * User: Jasmin
 * Date: 25.11.2016
 */

namespace App\Controller\Backend;


use App\Helper\Helper;
use App\Model\Program\ProgramPrice;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProgramPriceController extends BackendController
{

    private static $mainTemplateName = 'program-price';
    private static $mainPageTitle = 'Preis';
    private static $routeNameNewEntry = 'adminProgramPriceNew';
    private static $routeNameList = 'adminProgramPriceList';
    private static $routeNameDelete = 'adminProgramPriceDelete';
    private static $routeNameEdit = 'adminProgramPriceEdit';

    /**
     * Loads the action for the programPrice list.
     *
     * @return bool|RedirectResponse|Response
     */
    public function indexAction()
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }
        if (!$this->getBackendUserPrivilege()->onlyAdminAllowed()) {
            return new RedirectResponse($this->getRoutePath('adminNotFound'));
        }

        $this->setTemplateName(self::$mainTemplateName . '-list');
        $this->setPageTitle(self::$mainPageTitle . 'e');

        $programPrice = new ProgramPrice($this->getConfig());
        $programPriceData = $programPrice->loadData();

        foreach ($programPriceData as $key => $data) {
            $programPriceData[$key]['editRoute'] = $this->getRoutePath(self::$routeNameEdit, ['id' => $data['PPId']]);
            $programPriceData[$key]['deleteRoute'] = $this->getRoutePath(self::$routeNameDelete, ['id' => $data['PPId']]);
        }

        return $this->getResponse([
            'programPriceData' => $programPriceData,
            'newEntryRoute' => $this->getRoutePath(self::$routeNameNewEntry)
        ]);
    }

    /**
     * Loads the action for the program price edit form.
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

        if (!$this->getBackendUserPrivilege()->onlyAdminAllowed()) {
            return new RedirectResponse($this->getRoutePath('adminNotFound'));
        }

        $this->setRequest($request);
        $id = $this->getRequest()->attributes->get('id');

        $this->setTemplateName(self::$mainTemplateName . '-edit');
        $this->setPageTitle(self::$mainPageTitle . ' bearbeiten');

        $programPrice = new ProgramPrice($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $programPrice->loadSpecificEntry($id);
            if ($formData == null) {
                return new RedirectResponse($this->getRoutePath('adminNotFound'));
            }
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formError = $programPrice->checkErrors($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $formData['id'] = $id;
            $programPrice->saveData($formData);

            return new RedirectResponse($this->getRoutePath(self::$routeNameList));
        }


        return $this->getResponse([
            'formAction' => $this->getRoutePath(self::$routeNameEdit, ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
        ]);
    }


    /**
     * Loads the action for the program price new form.
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
        if (!$this->getBackendUserPrivilege()->onlyAdminAllowed()) {
            return new RedirectResponse($this->getRoutePath('adminNotFound'));
        }

        $this->setRequest($request);
        $id = $this->getRequest()->attributes->get('id');

        $this->setTemplateName(self::$mainTemplateName . '-edit');
        $this->setPageTitle(self::$mainPageTitle . ' anlegen');

        $programPrice = new ProgramPrice($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $programPrice->loadSpecificEntry($id);
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formError = $programPrice->checkErrors($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $programPrice->saveData($formData);

            return new RedirectResponse($this->getRoutePath(self::$routeNameList));
        }


        return $this->getResponse([
            'formAction' => $this->getRoutePath(self::$routeNameNewEntry),
            'formData' => $formData,
            'errorData' => $formError,
            'newEntry' => true
        ]);
    }

    /**
     * The action to delete a program price.
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
        if (!$this->getBackendUserPrivilege()->onlyAdminAllowed()) {
            return new RedirectResponse($this->getRoutePath('adminNotFound'));
        }

        $this->setRequest($request);
        $id = $this->getRequest()->attributes->get('id');

        $this->setTemplateName(self::$mainTemplateName . '-delete');
        $this->setPageTitle(self::$mainPageTitle . ' löschen');

        $programPrice = new ProgramPrice($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $programPrice->loadSpecificEntry($id);
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
            $programPrice->deleteData($id);

            return new RedirectResponse($this->getRoutePath(self::$routeNameList));
        }

        return $this->getResponse([
            'formAction' => $this->getRoutePath(self::$routeNameDelete, ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
        ]);
    }
}