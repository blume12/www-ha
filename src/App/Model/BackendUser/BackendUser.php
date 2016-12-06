<?php

/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 20:42
 */

namespace App\Model\BackendUser;

use App\Helper\Session;
use App\Model\Database\DbBasis;

class BackendUser extends DbBasis
{

    /**
     * @var string
     */
    private static $sessionName = 'userid';

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
        $sql = "SELECT * FROM backendUser ";
        $dbqObject->query($sql);
        $i = 0;
        while ($row = $dbqObject->nextRow()) {
            $data[$i] = $row;
            $data[$i]['index'] = $i;
            // TODO: Change links to really routes
            $data[$i]['editRoute'] = '/admin/nutzer-bearbeiten/' . $row['BUId']; // later: adminProgramEdit -> id
            $data[$i]['deleteRoute'] = '/admin/nutzer-loeschen/' . $row['BUId']; // later: adminProgramDelete -> id
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

        //Check the password
        // TODO: use a safer hash procedur
        //if ($user !== false && password_verify($formData['password'], $user['password'])) {
        //    Session::setSession(self::getSessionName(), $user['BUId']);
        if ($formData['password'] != $formData['passwordConfirm']) {
            $formError['usernamePassword'] = 'Die Passwörter stimmen nicht überein.';
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
     */
    public function saveData($data)
    {
        $currentDate = new \DateTime();
        $createDate = $currentDate->format('Y-m-d H:i:s');

        $sqlData = [
            'username' => $data['username'],
            'password' => BackendUser::generateHashPassword($data['password']),
            'changeDate' => null,
            'loginDate' => null,
            'privilege' => $data['privilege']
        ];

        $dbqObject = $this->getDbqObject();
        $entry = $this->getUserById($data['id']);
        if ($entry == false || count($entry) <= 0) {
            $sql = "INSERT INTO backendUser ( 'username', 'password','createDate','changeDate','loginDate','privilege') 
                   VALUES (:username, :password, :createDate, :changeDate, :loginDate, :privilege)";
            $sqlData['createDate'] = $createDate;
        } else {
            if ($data['password'] == '') {
                unset($sqlData['password']);
                $sql = "UPDATE backendUser SET 'username' = :username, 
                    'changeDate' = :changeDate, 'loginDate' = :loginDate, 'privilege' = :privilege WHERE BUId = :BUId ";
            } else {
                $sql = "UPDATE backendUser SET 'username' = :username, 'password' = :password, 
                    'changeDate' = :changeDate, 'loginDate' = :loginDate, 'privilege' = :privilege WHERE BUId = :BUId ";
            }
            $sqlData['changeDate'] = $createDate;
            $sqlData['BUId'] = $data['id'];
        }

        $dbqObject->query($sql, $sqlData);
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