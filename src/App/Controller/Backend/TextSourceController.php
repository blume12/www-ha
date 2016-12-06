<?php
/**
 * User: Jasmin
 * Date: 25.11.2016
 */

namespace App\Controller\Backend;


use App\Helper\Helper;
use App\Model\TextSource\TextSource;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TextSourceController extends BackendController
{
    private static $mainTemplateName = 'text-source';
    private static $mainPageTitle = 'Textvorlage';
    private static $routeNameNewEntry = 'adminTextSourceNew';
    private static $routeNameList = 'adminTextSourceList';
    private static $routeNameDelete = 'adminTextSourceDelete';
    private static $routeNameEdit = 'adminTextSourceEdit';

    /**
     * Loads the action for the textSource list.
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
        $this->setPageTitle(self::$mainPageTitle . 'n');

        $textSource = new TextSource($this->getConfig());
        $textSourceData = $textSource->loadData();

        return $this->getResponse([
            'textSourceData' => $textSourceData,
            'newEntryRoute' => $this->getRoutePath(self::$routeNameNewEntry)
        ]);
    }

    /**
     * Loads the action for the textSource edit form.
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

        $textSource = new TextSource($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $textSource->loadSpecificEntry($id);
            if ($formData == null) {
                return new RedirectResponse($this->getRoutePath('adminNotFound'));
            }
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formError = $textSource->checkErrors($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $formData['id'] = $id;
            $textSource->saveData($formData);

            return new RedirectResponse($this->getRoutePath(self::$routeNameList));
        }


        return $this->getResponse([
            'formAction' => $this->getRoutePath(self::$routeNameEdit, ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
        ]);
    }


    /**
     * Loads the action for the textSource new form.
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

        $textSource = new TextSource($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $textSource->loadSpecificEntry($id);
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formError = $textSource->checkErrors($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $textSource->saveData($formData);

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
     * The action to delete a textSource.
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

        $textSource = new TextSource($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $textSource->loadSpecificEntry($id);
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
            $textSource->deleteData($id);

            return new RedirectResponse($this->getRoutePath(self::$routeNameList));
        }

        return $this->getResponse([
            'formAction' => $this->getRoutePath(self::$routeNameDelete, ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
        ]);
    }

}