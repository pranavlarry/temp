<?php

namespace App\Controller;

use Google_Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EloquaController extends AbstractController
{
    #[Route('/enable', name: 'Eloqua Enable')]
    public function enableEloqua(Request $request) {
        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }   

    #[Route('/uninstall', name: 'Eloqua uninstall')]
    public function uninstallEloqua(Request $request) {
        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }   

    #[Route('/oauth', name: 'Eloqua OAuth')]
    public function eloquaOauth(Request $request) {
        
    }  

}