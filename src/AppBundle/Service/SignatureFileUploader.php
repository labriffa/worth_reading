<?php
/**
 * User: lewisbriffa
 * Date: 23/12/2017
 * Time: 04:21
 */

namespace AppBundle\Service;

class SignatureFileUploader extends FileUploader
{
    public function __construct($targetDir)
    {
        parent::__construct($targetDir);
    }
}