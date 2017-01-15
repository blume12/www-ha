<?php

/**
 * Author: Jasmin Stern
 * Date: 18.12.2016
 * Time: 14:51
 */

namespace App\Helper\FileDirectory;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUpload
{
    /**
     * @var string
     */
    private $mainPath;

    /**
     * @var string
     */
    private static $mainUploadPath = '/../../../../public/data';

    /**
     * @var UploadedFile | null
     */
    protected $fileData = null;

    /**
     * FileUpload constructor.
     * @param $fileData
     */
    public function __construct($fileData)
    {
        if ($fileData != null) {
            $this->fileData = $fileData;
        }
        $this->mainPath = realpath(dirname(__FILE__) . self::$mainUploadPath);
        if (!is_dir($this->mainPath)) {
            mkdir(dirname(__FILE__) . self::$mainUploadPath);
            $this->mainPath = realpath(dirname(__FILE__) . self::$mainUploadPath);
        }
    }

    /**
     * Check the upload.
     *
     * @return bool
     */
    public function checkUpload()
    {
        // TODO: check specifics cases. eg: images size, image type, etc.
        $check = getimagesize($this->fileData);
        if ($check !== false) {
            return true;
        }
        return false;
    }

    /**
     * Save the file into a directory.
     *
     * @param $path
     * @param $filename
     * @param int $size
     * @param string $fileType
     */
    public function saveFile($path, $filename, $size = 600, $fileType = 'jpg')
    {
        //TODO: use size to copy the image in the right size
        if ($this->fileData != null) {
            $tmpPath = explode('/', $path);
            $newPath = $this->mainPath;
            foreach ($tmpPath as $partOfPath) {
                $newPath .= '/' . $partOfPath;
                if (!is_dir($newPath)) {
                    mkdir($newPath);
                }
            }
            copy($this->fileData->getRealPath(), $newPath . '/' . $filename . '.' . $fileType);
        }
    }

}