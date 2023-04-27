<?php

namespace App\Controller;

use Google_Client;
use Google\Service\YouTube as Google_Service_YouTube;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]

    public function index(Request $request)
    {

        $session = $request->getSession();
        // $access_token = $session->get('access_token');
        if (!file_exists("accessToken.txt")) { 
            return $this->redirectToRoute('login');
        }
        $access_token = explode(",",file_get_contents("accessToken.txt"));
        // if (!$access_token) {
        //         return $this->redirectToRoute('login');
        // }

        $client = new Google_Client();
        $client->setAccessToken($access_token[0]);
    
        $youtube = new Google_Service_YouTube($client);

        $channelsResponse = $youtube->channels->listChannels('id,contentDetails', [
            'mine' => true,
        ]);
        $channelId = $channelsResponse[0]['id'];

        $searchResponse = $youtube->search->listSearch('id,snippet', [
            'order' => 'date',
            'channelId' => $channelId,
            'maxResults' => 500,
        ]);

        $videoIds = array();
        foreach ($searchResponse['items'] as $searchResult) {
            $videoIds[] = $searchResult['id']['videoId'];
        }

        if (empty($videoIds)) {
            return $this->render('home/index.html.twig', [
                'videos' => [],
                'noResults' => true,
            ]);
        }

        $videosResponse = $youtube->videos->listVideos('snippet,contentDetails', [
            'id' => implode(',', $videoIds),
        ]);

        $videos = array();
        foreach ($videosResponse as $video) {
            $durationString = $video->contentDetails->duration;
            $duration = new \DateInterval($durationString);
            $durationSeconds = $duration->h * 3600 + $duration->i * 60 + $duration->s;


            $durationReadable = gmdate('H:i:s', $durationSeconds);
            $videos[] = array(
                'title' => $video->snippet->title,
                'thumbnail' => $video->snippet->thumbnails->default->url,
                'duration' => $durationReadable,
                'videoId' => $video->id,
                'publishedAt' => $video->snippet->publishedAt,
            );
        }

        $keyword = $request->query->get('keyword');
        $limit = $request->query->get('limit', 5);

        if ($keyword) {
            $videos = array_filter($videos, function ($video) use ($keyword) {
                $title = $video['title'];
                return (strtolower($title[0]) == strtolower($keyword[0])) &&
                    str_contains(strtolower($title), strtolower($keyword));
            });
        }

        return $this->render('home/index.html.twig', [
            'videos' => $videos,
            'keyword' => $keyword,
            'noResults' => empty($videos),
            'limit' => $limit,
        ]);
    }
    #[Route('/videos/{videoId}', name: 'video_detail')]
    public function videoDetail(Request $request, string $videoId)
    {

        $session = $request->getSession();
        if (!file_exists("accessToken.txt")) { 
            return $this->redirectToRoute('login');
        }
        $access_token = explode(" ",file_get_contents("accessToken.txt"));
        // $access_token = $session->get('access_token');
        // if (!$access_token) {
        //     return $this->redirectToRoute('login');
        // }
        $client = new Google_Client();
        $client->setAccessToken($access_token[0]);
        

        $youtube = new Google_Service_YouTube($client);

        $videosResponse = $youtube->videos->listVideos('snippet', [
            'id' => $videoId,
        ]);

        $video = [
            'title' => $videosResponse[0]->snippet->title,
            'thumbnail' => $videosResponse[0]->snippet->thumbnails->high->url,
            'videoId' => $videosResponse[0]->id,
        ];

        return $this->render('home/next.html.twig', [
            'video' => $video,
        ]);
    }
    #[Route('/code/{videoId}', name: 'video_code')]
    public function videoCode(Request $request, string $videoId)
    {

        $session = $request->getSession();
        if (!file_exists("accessToken.txt")) { 
            return $this->redirectToRoute('login');
        }
        $access_token = explode(",",file_get_contents("accessToken.txt"));
        // $access_token = $session->get('access_token');
        // if (!$access_token) {
        //     return $this->redirectToRoute('login');
        // }
        $client = new Google_Client();
        
        $client->setAccessToken($access_token[0]);

        $youtube = new Google_Service_YouTube($client);
        $videosResponse = $youtube->videos->listVideos('snippet', [
            'id' => $videoId,
        ]);

        $video = [
            'title' => $videosResponse[0]->snippet->title,
            'thumbnail' => $videosResponse[0]->snippet->thumbnails->high->url,
            'videoId' => $videosResponse[0]->id,
        ];

        $width = $request->get('textbox1');
        $height = $request->get('textbox2');

        $autoplay = $request->get('autoplay', 0);
        $loop = $request->get('loop', 0);
        $disablekb = $request->get('disablekb', 0);

        $iframe = '<iframe width="' . $width . '" height="' . $height . '" 
        src="https://www.youtube.com/embed/' . $video['videoId'] . '?autoplay=' . $autoplay . '&disablekb=' . $disablekb . '&loop=' . $loop . '&playlist: ' . $video['videoId'] . ' " frameborder="0" allowfullscreen></iframe>';
        $embedCode = '
            <div id="player"></div>
        
            <script>
              
              var tag = document.createElement("script");
        
              tag.src = "https://www.youtube.com/iframe_api";
              var firstScriptTag = document.getElementsByTagName("script")[0];
              firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        
              var player;
              function onYouTubeIframeAPIReady() {
                player = new YT.Player("player", {
                  height: "' . $height . '",
                  width: "' . $width . '",
                  videoId: "' . $video['videoId'] . '",
                  
                  playerVars: {
                    disablekb: ' . $disablekb . ',
                    autoplay: ' . $autoplay . ',
                    loop: ' . $loop . ',
                    playlist: "' . $video['videoId'] . '",
                  },
                });
              }
            </script>';

        return $this->render('home/nextCode.html.twig', [
            'video' => $video,
            'iframe' => $iframe,
            'embedcode' => $embedCode

        ]);
    }
}
