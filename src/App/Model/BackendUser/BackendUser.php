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
     * Return the session name for the backend user.
     *
     * @return string
     */
    public static function getSessionName()
    {
        return self::$sessionName;
    }

    /**
     * Error Handling for the backend user.
     *
     * @param $formData
     * @return array
     */
    public function checkErrors($formData)
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
        $dbqObject = $this->getDbqObject();
        $currentDate = new \DateTime();
        $createDate = $currentDate->format('Y-m-d H:i:s');

        $sqlData = [
            'username' => $data['username'],
            'password' => BackendUser::generateHashPassword($data['password']),
            'createDate' => $createDate,
            'changeDate' => null,
            'loginDate' => null,
            'privilege' => $data['privilege']
        ];
        $insert = "INSERT INTO backendUser ( 'username', 'password','createDate','changeDate','loginDate','privilege') 
                   VALUES (:username, :password, :createDate, :changeDate, :loginDate, :privilege)";
        $dbqObject->query($insert, $sqlData);
    }
}