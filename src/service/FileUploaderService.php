<?php

namespace App\service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploaderService
{
    private SluggerInterface $slugger;
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * upload the file
     * @param File $postImage
     * @param string $uploadDirectory
     * @return string file name
     */
    public function upload(File $postImage, string $uploadDirectory): string
    {
        $originalFileName = pathinfo($postImage->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFileName = $this->slugger->slug($originalFileName);
        $newFileName = $safeFileName.'-'.uniqid().'.'.$postImage->guessExtension();

        try {
            $postImage->move(
                $uploadDirectory,
                $newFileName
            );
        }catch (\Exception $e){

        }
        return $newFileName;
    }
}