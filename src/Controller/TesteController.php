<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TesteController
{
    /**
    * @Route("/test")
    */    
    public function teste(Request $req){
        echo 'teste';
        exit();
    }
}