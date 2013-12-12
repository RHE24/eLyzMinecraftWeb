<?php
class Configurator {
	
	private $ThemeDir;
	private $Com = array();
	
	public function setThemeDir($dir) {
		$this->ThemeDir = $dir;
	}
	
	public function registerCom($from, $to) {
		$this->Com[$from] = $to;
	}
	
	public function paganation() {
		if(isset($_GET["user"])) {
			if(file_exists($this->ThemeDir . $_GET["user"] . ".latte")) {
				$inc_page = file_get_contents($this->ThemeDir . $_GET["user"] . ".latte");
				static::registerCom("{page}", $inc_page);
			} else {
				$inc_page = file_get_contents($this->ThemeDir . "404.latte") or die("Error");
				static::registerCom("{page}", $inc_page);
			}
		} else {
			// $news = file_get_contents($this->ThemeDir . "pages.php", true);
			static::registerCom("{page}", static::IncludeToVar($this->ThemeDir . "pages.php"));
		}
	}
	
	public static function IncludeToVar($path) {
		ob_start();
		require($path);
		return ob_get_clean();
	}
	
	public function user_login() {
		if(Core::isFunctionAllow("login")) {
			if(isset($_POST["login"])) {
			static::registerCom("{login_info}", "");
				if(isset($_POST["login"])) {
					$login_username = mysql_real_escape_string($_POST["login_username"]);
					$login_password = mysql_real_escape_string($_POST["login_password"]);
					if(!empty($login_username) && !empty($login_password)) {
						$username_exist = mysql_query("SELECT * FROM `users` WHERE `username`='" . $login_username . "'");
						if(mysql_num_rows($username_exist)) {
							$password_check = Core::mysqlFetch("SELECT `password` FROM `users` WHERE `username`='" . $login_username . "'", "password");
							if($password_check == Core::hashPassword($login_password)) {
								$check_user_activated = Core::mysqlFetch("SELECT `activate_code` FROM `users` WHERE `username`='" . $login_username . "'", "activate_code");
								if($check_user_activated == null) {
									$_SESSION["user_id"] = Core::hashPassword(Core::mysqlFetch("SELECT `id` FROM `users` WHERE `username`='" . $login_username . "'", "id"));
									header("location: ./");
								} else static::registerCom("{login_info}", "Tento účet není aktivovaný prosím aktivujte si ho prostřednictví linku který vám byl zaslán na email !");
							} else static::registerCom("{login_info}", "Špatné heslo !");
						} else static::registerCom("{login_info}", "Toto uživatelské jméno neexistuje !");
					} else static::registerCom("{login_info}", "Musíte vyplnit všechny udaje !");
				}
			} else static::registerCom("{login_info}", "");
		} else static::registerCom("{login_info}", "Přihlášení je vypnuto");
	}
	
	public function user_register() {
		if(Core::isFunctionAllow("registration")) {
			if(isset($_POST["register"])) {
				$reg_username = mysql_real_escape_string($_POST["reg_username"]);
				$reg_password = mysql_real_escape_string($_POST["reg_password"]);
				$reg_re_password = mysql_real_escape_string($_POST["reg_re_password"]);
				$reg_email = mysql_real_escape_string($_POST["reg_email"]);
				static::registerCom("{register_error}", "");
				if(!empty($reg_username) && !empty($reg_password) && !empty($reg_re_password) && !empty($reg_email)) {
					if($reg_password == $reg_re_password) {
						if(strlen($reg_username) >= 3) {
							if(strlen($reg_password) > 5) {
								if(filter_var($reg_email, FILTER_VALIDATE_EMAIL)) {
									$check_username = mysql_query("SELECT * FROM `users` WHERE `username`='" . $reg_username . "'");
									if(!mysql_num_rows($check_username)) {
										$check_email = mysql_query("SELECT * FROM `users` WHERE `email`='" . $reg_email . "'");
										if(!mysql_num_rows($check_email)) {
											$rand_activate_code = substr(sha1(md5($reg_username . rand())), 0, 32);
											mysql_query("INSERT INTO `users` values('', '" . $reg_username . "', '" . Core::hashPassword($reg_password) . "', '" . $reg_email . "', '" . date("Y.m.d H:i:s") . "', '" . $rand_activate_code . "')");
											mail($reg_email, "eLyz.cz - Minecraft registrace", "Dekujeme za registraci\nVase jmeno: " . $reg_username . "\nAktivatecni klic: " . rf_webLink() . "?activate=" . $rand_activate_code, "From: minecraft@elyz.cz");
											static::registerCom("{register_error}", "Registrace dokončena ! Prosím aktivujte účet odkazem, který vám byl zaslán na email.");
										} else static::registerCom("{register_error}", "Tento email již nekdo použil !");
									} else static::registerCom("{register_error}", "Toto jméno již exituje !");
								} else static::registerCom("{register_error}", "Špatný formát emalu !");
							} else static::registerCom("{register_error}", "Heslo musí mít vic jak 5 znaků !");
						} else static::registerCom("{register_error}", "Jméno musí míz víc jak 2 znaky !");
					} else static::registerCom("{register_error}", "Hesla se musí shodovat !");
				} else static::registerCom("{register_error}", "Prosím vypňte všechny údaje !");
			} else static::registerCom("{register_error}", "");
		} else static::registerCom("{register_error}", "Registrace je vypnuta !");
	}
	
	public function user_activation() {
		if(isset($_GET["activate"])) {
			$activate_link = mysql_real_escape_string($_GET["activate"]);
			$user_id = mysql_query("SELECT `id` FROM `users` WHERE `activate_code`='" . $activate_link . "'");
			if(mysql_num_rows($user_id)) {
				static::registerCom("{activate_script}", rf_alert($user_id));
				mysql_query("UPDATE `users` SET `activate_code`='' WHERE `id`='" . mysql_result($user_id, 0) . "'");
				static::registerCom("{activate_script}", rf_alert("Váš účet byl aktivován můřete se přihlásit !"));
			} else static::registerCom("{activate_script}", rf_alert("Neplatný aktivační kod !"));
		}
	}
	
	public function user_panel() {
		if(isset($_SESSION["user_id"])) {
			static::registerCom("{user_panel}", "Přihlášen jako: " . Core::get_username($_SESSION["user_id"]) . " <a href=\"?logout=true\">Odhlásit se</a>");
			static::registerCom("{admin_panel}", "");
		} else {
			static::registerCom("{admin_panel}", "");
			$login_top_form = file_get_contents($this->ThemeDir . 'login_top_form.latte');
			static::registerCom("{user_panel}", $login_top_form);
		}
		if(isset($_GET["logout"]) && $_GET["logout"] == "true") {
			Core::user_logout();
		}
	}
	
	public function run() {
		$Content = file_get_contents($this->ThemeDir . '@default.latte');
		$Body = file_get_contents($this->ThemeDir . 'body.latte');
		$Content = str_replace("{body}", $Body, $Content);
		foreach($this->Com as $key => $value) {
			$Content = str_replace($key, $value, $Content);
		}
		
		echo $Content;
	}
}