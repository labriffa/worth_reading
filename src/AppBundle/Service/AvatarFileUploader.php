<?php
/**
 * User: lewisbriffa
 * Date: 23/12/2017
 * Time: 04:20
 */

namespace AppBundle\Service;

class AvatarFileUploader extends FileUploader
{
    public function __construct($targetDir)
    {
        parent::__construct($targetDir);
    }
}