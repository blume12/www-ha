<?php

/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 20:42
 */

namespace App\Model\BackendUser;

class BackendUser
{

    /**
     * Error Handling for the backend user.
     *
     * @param $formData
     * @return array
     */
    public function checkErrors($formData)
    {
        $formError = [];
        if ($formData['username'] == '') {
            // TODO: Look, if the username exists in the database!
            $formError['username'] = 'Der Username ist ungültg.';
        }
        if ($formData['password'] == '') {
            // TODO: Look, if the password is correct to the username in the database!
            $formError['password'] = 'Das Passwort ist falsch.';
        }
        return $formError;
    }

}