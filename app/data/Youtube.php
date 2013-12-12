<?php
class Youtube {
	
	private $videoID;
	private $videoContent = array();
	
	private static function editString($str){
		$from = array("\n", "\r");
		return str_replace($from, "<br>", $str);
	}
	
	public function getVideoId($username){
		$id = NULL;
		$xml = simplexml_load_file(sprintf('http://gdata.youtube.com/feeds/base/users/%s/uploads?alt=rss&v=2&orderby=published', $username));
		if (!empty($xml->channel->item[0]->link)) {
			parse_str(parse_url($xml->channel->item[0]->link, PHP_URL_QUERY), $url_query);
			if(!empty($url_query['v'])) $this->videoID = $url_query['v'];
		}
		return $this;
	}
	public function getVideoContent($vW = false, $vH = false){
		if(!empty($this->videoID)){
			if($vW && $vH){
				$this->videoContent["iframe"] = "<iframe width=\"" . $vW . "\" height=\"" . $vH . "\" src=\"//www.youtube.com/embed/" . $this->videoID . "\" frameborder=\"0\" allowfullscreen></iframe>";
			} else {
				$this->videoContent["iframe"] = "<iframe width=\"560\" height=\"315\" src=\"//www.youtube.com/embed/" . $this->videoID . "\" frameborder=\"0\" allowfullscreen></iframe>";
			}
			$url = "http://gdata.youtube.com/feeds/api/videos/" . $this->videoID;
			$youtube = simplexml_load_file($url);
			$this->videoContent["mini"] = "<img src=\"http://i1.ytimg.com/vi/" . $this->videoID . "/mqdefault.jpg\">";
			$this->videoContent["title"] = $youtube->title[0];
			$this->videoContent["content"] = static::editString($youtube->content[0]);
		}
		return $this;
	}
	
	public function show($iframe = false, $mini = false, $title = false, $content = false){
		if($iframe){
			echo $this->videoContent["iframe"] . "<br>";
		}
		if($mini){
			echo $this->videoContent["mini"] . "<br>";
		}
		if($title){
			echo $this->videoContent["title"] . "<br>";
		}
		if($content){
			echo $this->videoContent["content"];
		}
		return $this;
	}
}
?>