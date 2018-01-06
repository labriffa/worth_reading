<?php
/**
 * User: lewisbriffa
 * Date: 24/12/2017
 * Time: 01:45
 */

namespace AppBundle\Service;

class CoverFileUploader extends FileUploader
{
    public function __construct($targetDir)
    {
        parent::__construct($targetDir);
    }
}