<?php

/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 20:42
 */

namespace App\Model\BackendUser;

use App\Helper\Session;
use App\Helper\Validator;
use App\Model\Database\DbBasis;

class BackendUser extends DbBasis
{

    /**
     * @var array
     */
    private static $privilegeValues = [
        'admin' => 'Adminnutzer (Alle Rechte)',
        'user' => 'Kassenpersonal'
    ];

    /**
     * @var string
     */
    private static $sessionName = 'userid';


    /**
     * Return the array if the parameter is null. Otherwise it return the privilege value to a key.
     *
     * @param null $privilege
     * @return array|mixed
     */
    public static function getPrivilege($privilege = null)
    {
        if ($privilege == null) {
            return self::$privilegeValues;
        }
        return self::$privilegeValues[$privilege];
    }

    /**
     * Return the user data filtered by the username.
     *
     * @param $username
     * @return mixed
     */
    public function getUserByName($username)
    {
        $sql = 'SELECT * FROM backendUser WHERE username = :username LIMIT 1';
        $dbq_object = $this->getDbqObject();
        $dbq_object->query($sql, ['username' => $username]);
        $user = $dbq_object->nextRow();
        return $user;
    }


    /**
     * Return the user data filtered by the id.
     *
     * @param $buId
     * @return mixed
     */
    public function getUserById($buId)
    {
        $sql = 'SELECT * FROM backendUser WHERE BUId = :BUId LIMIT 1';
        $dbq_object = $this->getDbqObject();
        $dbq_object->query($sql, ['BUId' => $buId]);
        $user = $dbq_object->nextRow();
        return $user;
    }

    /**
     * Return the data array of all backend users.
     *
     * @return array
     */
    public function loadData()
    {
        $dbqObject = $this->getDbqObject();

        $data = [];
        $sql = "SELECT * FROM backendUser WHERE privilege IS NOT 'superAdmin' ORDER BY lastname ASC,firstname ASC ";
        $dbqObject->query($sql);
        $i = 0;
        while ($row = $dbqObject->nextRow()) {
            $data[$i] = $row;
            $data[$i]['index'] = $i;
            $i++;
        }

        return $data;
    }


    /**
     * Return the session name for the backend user.
     *
     * @return string
     */
    public static function getSessionName()
    {
        return self::$sessionName;
    }

    /**
     * Error Handling for the backend user at the login.
     *
     * @param $formData
     * @return array
     */
    public function checkErrorsByLogin($formData)
    {
        $formError = [];
        $user = $this->getUserByName($formData['username']);

        //Check the password
        // TODO: use a safer hash procedure
        if ($user !== false && password_verify($formData['password'], $user['password'])) {
            Session::setSession(self::getSessionName(), $user['BUId']);
        } else {
            $formError['usernamePassword'] = 'Der Username oder das Passwort ist ungültg.';
        }

        return $formError;
    }

    /**
     * Check Errors before save Data.
     *
     * @param $formData
     * @return array
     */
    public function checkErrors($formData)
    {
        $formError = [];

        if (!Validator::isAlpha($formData['appellation'], true)) {
            $formError['appellation'] = 'Bitte geben Sie eine Anrede an.';
        }

        if (!Validator::isAlpha($formData['firstname'], true)) {
            $formError['firstname'] = 'Bitte geben Sie einen Vornamen an.';
        }

        if (!Validator::isAlpha($formData['lastname'], true)) {
            $formError['lastname'] = 'Bitte geben Sie einen Nachnamen an.';
        }

        if (!Validator::isAlpha($formData['username'], true)) {
            $formError['username'] = 'Bitte geben Sie einen Nutzernamen an.';
        }

        if ((!isset($formData['id']) || $formData['id'] == '') && $formData['password'] == '') {
            $formError['password'] = 'Bitte geben Sie ein Passwort an.';
        }

        // The confirm password must be the same
        if ($formData['password'] != $formData['passwordConfirm']) {
            $formError['passwordConfirm'] = 'Die Passwörter stimmen nicht überein.';
        }

        if (!Validator::isAlpha($formData['privilege'], true)) {
            $formError['privilege'] = 'Bitte geben Sie Nutzerrecht an.';
        }

        return $formError;
    }

    /**
     * Generate the hash for the password.
     *
     * @param $password
     * @return bool|string
     */
    public static function generateHashPassword($password)
    {
        // TODO: use a safer hash procedure
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Save the backend user to a db.
     *
     * @param $data
     * @return bool
     */
    public function saveData($data)
    {
        $currentDate = new \DateTime();
        $createDate = $currentDate->format('Y-m-d H:i:s');

        $sqlData = [
            'appellation' => $data['appellation'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'username' => $data['username'],
            'password' => BackendUser::generateHashPassword($data['password']),
            'changeDate' => null,
            'loginDate' => null,
            'privilege' => $data['privilege']
        ];

        $dbqObject = $this->getDbqObject();
        $entry = false;
        if (isset($data['id'])) {
            $entry = $this->getUserById($data['id']);
        } else {
            // if its a new entry and the user name exits, cancel this save #
            if ($this->getUserByName($data['username'])) {
                return false;
            }
        }
        if ($entry == false || count($entry) <= 0) {
            $sql = "INSERT INTO backendUser ('appellation', 'firstname', 'lastname', 'username', 'password','createDate',
                    'changeDate','loginDate','privilege') 
                   VALUES (:appellation, :firstname, :lastname, :username, :password, :createDate, :changeDate, :loginDate, :privilege)";
            $sqlData['createDate'] = $createDate;
        } else {
            if ($data['password'] == '') {
                unset($sqlData['password']);
                $sql = "UPDATE backendUser SET 'username' = :username, 'appellation' = :appellation,'firstname' = :firstname,lastname = :lastname,
                    'changeDate' = :changeDate, 'loginDate' = :loginDate, 'privilege' = :privilege WHERE BUId = :BUId ";
            } else {
                $sql = "UPDATE backendUser SET 'username' = :username, 'password' = :password,  'appellation' = :appellation,
                      'firstname' = :firstname,lastname = :lastname,
                    'changeDate' = :changeDate, 'loginDate' = :loginDate, 'privilege' = :privilege WHERE BUId = :BUId ";
            }
            $sqlData['changeDate'] = $createDate;
            $sqlData['BUId'] = $data['id'];
        }

        $dbqObject->query($sql, $sqlData);
        return true;
    }

    /**
     * Delete a data by the id
     *
     * @param $id integer
     */
    public function deleteData($id)
    {
        $dbqObject = $this->getDbqObject();
        $dataSql = [];

        $sql = "DELETE FROM backendUser WHERE BUId=:BUId ";

        $dataSql['BUId'] = $id;
        $dbqObject->query($sql, $dataSql);
    }
}