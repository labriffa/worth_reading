<?php
/**
 * User: lewisbriffa
 * Date: 23/12/2017
 * Time: 04:14
 */

namespace AppBundle\Service;

use \Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDir;

    public function __construct(String $targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid() . '.' . $file->guessExtension());
        $file->move($this->getTargetDir(), $fileName);
        return $fileName;
    }

    public function getTargetDir() : String
    {
        return $this->targetDir;
    }
}