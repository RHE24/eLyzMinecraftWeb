<?php
	function rf_webLink() {
		return "./";
	}
	function rf_styleDir() {
		return rf_weblink() . "app/theme/style/";
	}
	function rf_jsDir() {
		return rf_styledir() . "js/";
	}
	function rf_styleImagesDir() {
		return rf_styleDir() . "images/";
	}
	function rf_alert($message) {
		return "<script type=\"text/javascript\">alert('" . $message . "');</script>";
	}