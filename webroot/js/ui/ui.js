var showing = false;

function showOverlay(message) {
	showing = true;
	document.getElementById('top_notification_message').innerHTML = message;
	Effect.SlideDown('top_notification', { duration: 1.0 });
	initDivMouseOver();
	setTimeout('hideOverlay();',8000);
}

function hideOverlay() {
	div = document.getElementById("top_notification");
	if (showing) {
		if (!div.mouseIsOver) {
			Effect.SlideUp('top_notification', { duration: 1.0 });
			showing = false;
		} else {
			setTimeout('hideOverlay();',8000);
		}
	}	
}

function initDivMouseOver()   {
   var div = document.getElementById("top_notification");
   div.mouseIsOver = false;
   div.onmouseover = function()   {
      this.mouseIsOver = true;
   };
   div.onmouseout = function()   {
      this.mouseIsOver = false;
   }
}

function clearForm(el) {
	el.value = "";
}