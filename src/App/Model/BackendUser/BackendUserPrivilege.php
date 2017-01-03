<?php
/**
 * Author: Jasmin Stern
 * Date: 03.01.2017
 * Time: 20:07
 */

namespace App\Model\BackendUser;


use App\Helper\Session;
use App\Model\Database\DbBasis;

class BackendUserPrivilege extends DbBasis
{
    /**
     * @var array|mixed
     */
    private $backendUserData = [];

    /**
     * BackendUserPrivilege constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $userId = Session::getSessionByKey(BackendUser::getSessionName());;
        $backendUser = new BackendUser($this->getConfig());
        $this->backendUserData = $backendUser->getUserById($userId);
    }

    /**
     * Return true, if its only allowed for admin user.
     *
     * @return bool
     */
    public function onlyAdminAllowed()
    {
        if ($this->backendUserData['privilege'] == 'admin' || $this->backendUserData['privilege'] == 'superAdmin') {
            return true;
        }
        return false;
    }

}