<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 20:56
 */

namespace App\Controller\Backend;


use App\Controller\Controller;
use App\Helper\Session;
use App\Model\BackendUser\BackendUser;
use App\Helper\Menu\Menu;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class BackendController extends Controller
{
    /**
     * String to backend path.
     *
     * @var string
     */
    protected $path = '/../../../templates/backend/';

    /**
     * @var string
     */
    protected $notFoundRoute = 'adminNotFound';

    /**
     * BackendController constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $config['frontend'] = false;
        parent::__construct($config);
        $menu = new Menu();
        $menu->addMenu('Startseite', $this->getRoutePath('adminStart'));
        $menu->addMenu('Programme', $this->getRoutePath('adminProgramList'), false);
        $menu->addMenu('Preise', $this->getRoutePath('adminProgramPriceList'), false);
        $menu->addMenu('Zeiträume', $this->getRoutePath('adminTimescaleList'), false);
        $menu->addMenu('Textvorlagen', $this->getRoutePath('adminTextSourceList'), false);
        $menu->addMenu('Nutzer', $this->getRoutePath('adminBackendUserList'), false);
        $menu->addMenu('Logout', $this->getRoutePath('adminLogout'), false);
        $this->menuArray = $menu->getMenuArray();
    }

    /**
     * @var null |integer
     */
    private $userId = null;

    /**
     * @var bool
     */
    protected $loggedIn = false;

    /**
     * Set the variable for logged in to true.
     */
    protected function setLoggedIn()
    {
        $this->loggedIn = true;
    }

    /**
     * Set the variable for logged in to false.
     */
    protected function setLoggedOut()
    {
        $this->loggedIn = false;
    }

    /**
     * Return the user id from the session.
     *
     * @return int|null
     */
    protected function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set the Content data with the logged in parameter.
     *
     * @param array $data
     */
    protected function setContentData($data = [])
    {
        parent::setContentData($data);
        $this->contentData = array_merge($this->contentData, array('loggedIn' => $this->loggedIn));
    }

    /**
     * Check if the current user as the permission to be logged in. If the user has no permission anymore,
     * it will redirect to the login screen.
     *
     * @return bool|RedirectResponse
     */
    protected function checkLogin()
    {
        $this->userId = Session::getSessionByKey(BackendUser::getSessionName());
        if ($this->getUserId() == false) {
            $this->setLoggedOut();
            Session::removeSession();
            return new RedirectResponse($this->getRoutePath('adminLogin'));
        }
        $this->setLoggedIn();
        return true;
    }
}