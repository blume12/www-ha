<?php
/**
 * User: Jasmin
 * Date: 25.11.2016
 */

namespace App\Controller\Backend;


use App\Helper\Helper;
use App\Model\Timescale\Timescale;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TimescaleController extends BackendController
{

    private static $mainTemplateName = 'timescale';
    private static $mainPageTitle = 'Zeitraum';
    private static $routeNameNewEntry = 'adminTimescaleNew';
    private static $routeNameList = 'adminTimescaleList';
    private static $routeNameDelete = 'adminTimescaleDelete';
    private static $routeNameEdit = 'adminTimescaleEdit';

    /**
     * Loads the action for the timescale list.
     *
     * @return bool|RedirectResponse|Response
     */
    public function indexAction()
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }

        $this->setTemplateName(self::$mainTemplateName . '-list');
        $this->setPageTitle('Zeiträume');

        $timescale = new Timescale($this->getConfig());
        $timescaleData = $timescale->loadData();

        foreach ($timescaleData as $key => $data) {
            $timescaleData[$key]['editRoute'] = $this->getRoutePath(self::$routeNameEdit, ['id' => $data['TId']]);
            $timescaleData[$key]['deleteRoute'] = $this->getRoutePath(self::$routeNameDelete, ['id' => $data['TId']]);
        }

        return $this->getResponse([
            'timescaleData' => $timescaleData,
            'newEntryRoute' => $this->getRoutePath(self::$routeNameNewEntry)
        ]);
    }

    /**
     * Loads the action for the timescale edit form.
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

        $this->setTemplateName(self::$mainTemplateName . '-edit');
        $this->setPageTitle(self::$mainPageTitle . ' bearbeiten');

        $timescale = new Timescale($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $timescale->loadSpecificEntry($id);
            if ($formData == null) {
                return new RedirectResponse($this->getRoutePath('adminNotFound'));
            }
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formError = $timescale->checkErrors($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $formData['id'] = $id;
            $timescale->saveData($formData);

            return new RedirectResponse($this->getRoutePath(self::$routeNameList));
        }


        return $this->getResponse([
            'formAction' => $this->getRoutePath(self::$routeNameEdit, ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
        ]);
    }


    /**
     * Loads the action for the timescale new form.
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

        $this->setTemplateName(self::$mainTemplateName . '-edit');
        $this->setPageTitle(self::$mainPageTitle . ' anlegen');

        $timescale = new Timescale($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $timescale->loadSpecificEntry($id);
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formError = $timescale->checkErrors($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $timescale->saveData($formData);

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
     * The action to delete a timescale.
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

        $this->setTemplateName(self::$mainTemplateName . '-delete');
        $this->setPageTitle(self::$mainPageTitle . ' löschen');

        $timescale = new Timescale($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $timescale->loadSpecificEntry($id);
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
            $timescale->deleteData($id);

            return new RedirectResponse($this->getRoutePath(self::$routeNameList));
        }

        return $this->getResponse([
            'formAction' => $this->getRoutePath(self::$routeNameDelete, ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
        ]);
    }
}