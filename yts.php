<?php

class ytsApi{

  const APIBASE = 'https://yts.am/api/v2/';
  const SEARCH_END_POINT = 'list_movies.json';
  const SEARCH_KEY = '?query_term=';
  const BLURAY = '1080p';
  const DVD = '720p';

  public function __construct(){
  }
  protected function _buildSearchQuery($query){
    return self::APIBASE . self::SEARCH_END_POINT . self::SEARCH_KEY . urlencode($query);
  }
  private function _sendRequest($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output);
  }
  public function search($query){
    return $this->_sendRequest($this->_buildSearchQuery($query));
  }
  public function movieWasFound($ytsResponse){
    return $ytsResponse->data->movie_count ? true : false;
  }
  public function extractTorrentHash($ytsResponse){
    foreach($ytsResponse->data->movies as $movie){
      $index = count($movie->torrents);
      while($index--){
        if($movie->torrents[$index]->quality == self::BLURAY){
          return $movie->torrents[$index]->hash;
        }elseif($movie->torrents[$index]->quality == self::DVD){
          return $movie->torrents[$index]->hash;
        }
      }
    }
    return false;
  }
  public function extractDownloadUri($ytsResponse){
    foreach($ytsResponse->data->movies as $movie){
      $index = count($movie->torrents);
      while($index--){
        if($movie->torrents[$index]->quality == self::BLURAY){
          return $movie->torrents[$index]->url;
        }elseif($movie->torrents[$index]->quality == self::DVD){
          return $movie->torrents[$index]->url;
        }
      }
    }
    return false;
  }
}
