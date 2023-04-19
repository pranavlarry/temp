<?php

namespace App\Controller;

use Google_Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EloquaContentController extends AbstractController {
    #[Route('/createInstance', name: 'Eloqua Create Content Instance')]
    public function createInstance(Request $request) {
        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }

    #[Route('/deleteInstance', name: 'Eloqua Delete Content Instance')]
    public function deleteInstance(Request $request) {
        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }

    #[Route('/copyInstance', name: 'Eloqua Copy Content Instance')]
    public function copyInstance(Request $request) {
        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }

    #[Route('/notification', name: 'Eloqua Content Instance Landing Page Notification')]
    public function notificationLandingPage(Request $request) {
        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }
}