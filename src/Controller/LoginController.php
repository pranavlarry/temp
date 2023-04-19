<?php

namespace App\Controller;

use Google\Auth\AccessToken;
use Google_Client;
use Google\Service\YouTube as Google_Service_YouTube;
use Google_Service_Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */

    public function login(Request $request)
    {
        $session = $request->getSession();
        $access_token = $session->get('access_token');

        if ($access_token) {
            return $this->redirectToRoute('home');
        }
        
        return $this->render('first_login.html.twig');
    }

    /**
     * @Route("/login_with_google", name="login_with_google")
     */

    public function loginWithGoogle(Request $request)
    {
        $clientId = "328000882849-mb5o55rongrsa65c6m0vvcupqp5atbl8.apps.googleusercontent.com";
        $clientSecret = "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";
        $client = new Google_Client();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($this->generateUrl('login_callback', [], UrlGeneratorInterface::ABSOLUTE_URL));
        $client->addScope(Google_Service_YouTube::YOUTUBE_READONLY);

        
        $authUrl = $client->createAuthUrl();
        return $this->redirect($authUrl);
    }

    /**
     * @Route("/login_callback", name="login_callback")
     */
    public function loginCallback(Request $request)
    {
        $response = new Response;
        $session = $request->getSession();
        // $access_token = $session->get('access_token');
        if (file_exists("accessToken.txt")) {
            $access_token = explode(",",file_get_contents("accessToken.txt"));
            if ($access_token) {
                return $this->redirectToRoute('home');
            }
        }
        
        $client = new Google_Client();
        $client->setClientId('422400716364-hpj5blornolke07li4pumktspvdqrd8t.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $code = $_POST['code'];
        $client->addScope(Google_Service_YouTube::YOUTUBE_READONLY);
        $client->setRedirectUri("postmessage");
        $access_token = $client->fetchAccessTokenWithAuthCode($code);
        $session->set('access_token', $access_token);
        // $cookie = Cookie::create('access_token')
        //             ->withValue(implode(" ",$access_token))
        //             ->withExpires(0)
        //             ->withDomain("5a24-171-49-206-241.ngrok-free.app")
        //             ->withSecure(true)
        //             ->withHttpOnly(false)
        //             ->withSameSite(Cookie::SAMESITE_NONE);
        $fh = fopen("accessToken.txt", 'w');
        fwrite($fh, implode(",",$access_token));
        fclose($fh);
        $response->setContent(implode(",",$access_token). gettype($access_token));
        // $response->headers->set("Location","/home");
        // $response->headers->setCookie($cookie);
        $response->setStatusCode(302);
        return $response;
        
    }
}
