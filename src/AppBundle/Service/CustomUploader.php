<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
class CustomUploader
{
    //simple service class for easy photo uploading
    private $dir;

    public function __construct($tDir)
    {
        $this->dir = $tDir;
    }

    public function upload(UploadedFile $file) {
        $newName = md5(time()) . '.' . $file->guessExtension();
        $file->move($this->getDir(), $newName);
        return $newName;
    }

    public function getDir() {
        return $this->dir;
    }


    public function test() {
        print_r('TEST<br>');
    }
}
