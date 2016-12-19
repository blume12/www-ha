<?php

/**
 * Author: Jasmin Stern
 * Date: 18.12.2016
 * Time: 14:51
 */

namespace App\Helper\FileDirectory;

class FileUpload
{
    private $mainPath;

    public function __construct()
    {
        $this->mainPath = realpath(dirname(__FILE__).'/../../../../data/');
    }


    public function upload($fileData, $submit) {

        $target_dir = $this->mainPath;
        $target_file = $target_dir . basename($fileData);
        var_dump($target_dir);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
        if($submit) {

            $test = tempnam(sys_get_temp_dir(), $fileData);
            var_dump($test);
            $check = getimagesize($test);
            if($check !== false) {
                var_dump( "File is an image - " . $check["mime"] . ".");
                $uploadOk = 1;
            } else {
               var_dump("File is not an image.");
                $uploadOk = 0;
            }
        }
        var_dump("no submit");
        return $uploadOk;
    }

}