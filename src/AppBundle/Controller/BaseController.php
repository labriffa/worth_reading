<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 07/05/2018
 * Time: 01:41
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BaseController extends Controller
{
    protected function convertBase64Image($path, $base64)
    {
        $filename = md5(uniqid());
        $fh = fopen( $path . '/' . $filename, "wb" );
        fwrite($fh, base64_decode($base64));
        fclose($fh);

        $file = new UploadedFile($path . '/' . $filename, $filename, "image/png", null, null, true);

        return $file;
    }
}