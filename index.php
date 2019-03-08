<?php
if (!isset($_GET['url'])) {
  die();
  exit;
}else {
  $url = $_GET['url'];
}


include 'vendor/autoload.php';
use Masih\YoutubeDownloader\YoutubeDownloader;
header('Content-type: application/json');

$video_id = explode('watch?v=',$url);
$video_id2 = explode('.be/',$url);
$Go = true;
if (count($video_id) == 2) {
  $video_check = explode('&list=',$video_id[1]);

  if (count($video_check) == 1) {
    $video_id = $video_id[1];
  }else {
    $Go = false;
  }

}else if (count($video_id2) == 2) {
  $video_check = explode('&list=',$video_id2[1]);

  if (count($video_check) == 1) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $a = curl_exec($ch); // $a will contain all headers
    $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // This is what you need, it will return you the last effective URL
    var_dump($url);
    $video_id2 = explode('watch?v=',$url);
    $video_id = $video_id2[1];
    $video_id = str_replace('&feature=youtu.be','',$video_id);
    var_dump($video_id);
  }else {
    $Go = false;
  }

}else {
  $Go = false;
}

if (!$Go) {
  # code...
  die;
}


$youtube = new YoutubeDownloader($video_id);
$result = $youtube->getVideoInfo();
$video = array("video_info" => array('title'=>'','author'=>'','views'=>'','author'=>'' ,'description'=>'','img'=>''),"video_download" => array());


foreach ($result as $key => $value) {
  if ($key == 'image') {
    $video['video_info']['img'] = $value['medium_quality'];
  }

  if ($key == 'title') {
    $video['video_info']['title'] = $value;
  }

  if ($key == 'views') {
    $video['video_info']['views'] = $value;

  }

    if ($key == 'author') {
      $video['video_info']['author'] = $value;
    }

    if ($key == 'full_formats') {
      foreach ($value as $vkey => $download) {
        array_push($video['video_download'], $download);
      }
    }
}

$video = (object) $video;
echo json_encode($video);


?>
