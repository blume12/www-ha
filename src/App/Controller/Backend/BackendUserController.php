<?php
/**
 * User: Jasmin
 * Date: 25.11.2016
 */

namespace App\Controller\Backend;


use App\Helper\Helper;
use App\Model\BackendUser\BackendUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BackendUserController extends BackendController
{
    /**
     * Loads the action for the backend user list.
     *
     * @return bool|RedirectResponse|Response
     */
    public function indexAction()
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }
        $this->setTemplateName('backend-user-list');
        $this->setPageTitle('Nutzer');

        $backendUser = new BackendUser($this->getConfig());
        $backendUserData = $backendUser->loadData();

        return $this->getResponse([
            'backendUserData' => $backendUserData,
            'newEntryRoute' => $this->getRoutePath('adminBackendUserNew')
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

        $this->setTemplateName('backend-user-edit');
        $this->setPageTitle('Nutzer bearbeiten');

        $backendUser = new BackendUser($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $backendUser->getUserById($id);
            $formData['password'] = '';
            if ($formData == null) {
                return new RedirectResponse($this->getRoutePath('adminNotFound'));
            }
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();

            $formData['id'] = $id;
            $formError = $backendUser->checkErrors($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $formData['id'] = $id;
            $backendUser->saveData($formData);

            return new RedirectResponse($this->getRoutePath('adminBackendUserList'));
        }

        return $this->getResponse([
            'formAction' => $this->getRoutePath('adminBackendUserEdit', ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
        ]);
    }

    /**
     * Loads the action for the backend user new form.
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

        $this->setTemplateName('backend-user-edit');
        $this->setPageTitle('Nutzer anlegen');

        $backendUser = new BackendUser($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $backendUser->getUserById($id);
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formError = $backendUser->checkErrors($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $backendUser->saveData($formData);

            return new RedirectResponse($this->getRoutePath('adminBackendUserList'));
        }


        return $this->getResponse([
            'formAction' => $this->getRoutePath('adminBackendUserNew'),
            'formData' => $formData,
            'errorData' => $formError,
            'newEntry' => true
        ]);
    }

    /**
     * The action to delete a backend user.
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

        $this->setTemplateName('backend-user-delete');
        $this->setPageTitle('Nutzer löschen');

        $backendUser = new BackendUser($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $backendUser->getUserById($id);
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
            $backendUser->deleteData($id);
            return new RedirectResponse($this->getRoutePath('adminBackendUserList'));
        }

        return $this->getResponse([
            'formAction' => $this->getRoutePath('adminBackendUserDelete', ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
        ]);
    }
}