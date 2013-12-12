setInterval(function() {
	$.ajax({
		url: "chat.php",
		success: function(data) {
			$('#chatbox').html(data);
		}
	});
}, 1000);