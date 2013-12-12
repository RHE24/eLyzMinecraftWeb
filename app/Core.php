<?php
class Core {
	
	private $InfoSocket;
	private $InfoData;
	public $ServerIP;
	public $ServerPort;
	
	public function __construct() {
		session_start();
		$MySQL = mysql_connect("wm50.wedos.net", "a31990_elyz", "aWF4UwWU") or die(mysql_error());
		mysql_select_db("d31990_elyz", $MySQL);
		mysql_query("SET NAMES 'utf8'");
	}
	
	public static function hashPassword($password) {
		$token = "JeLDaDA";
		$sha_pass = sha1(md5($token . $password));
		$sha_pass = substr($sha_pass, 0, 32);
		return $sha_pass;
	}
	public static function mysqlFetch($command, $rowName) {
		$query = mysql_query($command);
		while($row = mysql_fetch_array($query))
			return $row[$rowName];
	}
	
	public static function user_logout() {
		session_destroy();
		header("location: ./");
	}
	
	public static function get_username($hash_id) {
		$query = mysql_query("SELECT * FROM `users`");
		while($row = mysql_fetch_array($query)) {
			if($hash_id == static::hashPassword($row["id"])) return $row["username"];
		}
	}
	
	public static function isFunctionAllow($function) {
		$query = mysql_query("SELECT `allow` FROM `functions_acc` WHERE `function`='" . $function . "'");
		while($row = mysql_fetch_array($query)) if($row["allow"] == "true") return true; else return false;
	}
	
	public function getSereverData() {
		$query = mysql_query("SELECT * FROM `server`");
		while($row = mysql_fetch_array($query)) {
			$this->ServerIP = $row["ip"];
			$this->ServerPort = $row["port"];
			$this->InfoSocket = @fsockopen($row["ip"], $row["port"], $errno, $errstr, 1);
			if($this->InfoSocket) {
				fwrite($this->InfoSocket, "\xfe\x01");
				$data = fread($this->InfoSocket, 256);
				if (substr($data, 3, 5) == "\x00\xa7\x00\x31\x00"){
	            	$data = explode("\x00", mb_convert_encoding(substr($data, 15), 'UTF-8', 'UCS-2'));
		        }else{
		            $data = explode('§', mb_convert_encoding(substr($data, 3), 'UTF-8', 'UCS-2'));
		        }
		        $this->InfoData = array(
                	'version'        => $data[0],
                	'motd'           => $data[1],
                	'players'        => intval($data[2]),
                	'max_players'    => intval($data[3]),
            	);
		        
			}
		}
	}
	
	public function serverStatus($onlineMessage = "Online", $offlineMessage = "Offline") {
		if($this->InfoSocket != null) {
			return "<font color=\"#00FF00\">" . $onlineMessage . "</font>";
		} else {
			return "<font color=\"red\">" . $offlineMessage . "</font>";
		}
	}
	
	public function getServerVersion() {
		return $this->InfoData['version'];
	}
	
	public function getServerMotd() {
		return $this->InfoData['motd'];
	}
	
	public function getServerOnlinePlayers() {
		return $this->InfoData['players'];
	}
	
	public function getServerMaxPlayers() {
		return $this->InfoData['max_players'];
	}
	
	// Chat Methods
	

}