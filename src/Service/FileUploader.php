<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{

    public function __construct(private string $targetDirectory, private SluggerInterface $slugger)
    {
    }

    public function upload(UploadedFile $file, string $directory):string{
        //ex: c:\\tmp\Mon Fichier.jpg
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        //ex devient: Mon Fichier
        $safeFilename = $this->slugger->slug($originalFilename); //Cré un name save en supprimant les espaces et autres perturbateurs
        //ex devient: mon-fichier
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension(); //ajout d'un identifiant unique au nom du fichier
        //ex devient: mon-fichier-151sd5dd5dz61.jpg

        try {
            $file->move($this->getTargetDirectory() . $directory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }


    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

}