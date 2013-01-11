'use strict';
function slider_next() {
	location.href = next;
}

function slider_preload() {
	var el=document.createElement('img');
	el.src= preload;
	el.style.display = 'none';
	el.alt = 'Fotoslider Preloader';
	document.body.appendChild(el);
}

// apply change in resolution to <video>
function updateResolution () {
	var resolution = document.getElementById('resolution').value;
	localStorage.videoResolution = resolution;
	var video      = document.getElementById('video');
	var codec = null;
	for (var i=0; i<codecs.length; i++) {
		if (video.canPlayType('video/'+codecs[i])) {
			codec = codecs[i];
		}
	}
	if (!codec) {
		// problem! Just choose the first. It probably won't play anyway.
		codec = codecs[0];
	}
	// workaround for bug in Chrome on Linux (mp4 doesn't play but is still advertized to play)
	if (video.canPlayType('video/webm') && navigator.userAgent.indexOf('Linux') && navigator.userAgent.indexOf('Chrome') && codecs.indexOf('webm') != -1) {
		codec = 'webm';
	}
	// all browsers supporting <video> support querySelector
	var src = document.querySelector('source[type="video/'+codec+'"][data-resolution="'+resolution+'"]').src;
	var position = video.currentTime;
	var playing  = !video.paused;
	video.src = src;
	video.addEventListener('loadedmetadata', function () {
		video.currentTime = position;
		if (playing)
			video.play();
	}, false);
}

if (type == 'photo') {
	if (sliding) {
		setTimeout('slider_next()', slider_timeout*1000);
	}
} else {
	window.addEventListener('DOMContentLoaded', function () {
		var video = document.getElementById('video');
		if (document.getElementById('resolution').value != localStorage.videoResolution) {
			if (localStorage.videoResolution)
				document.getElementById('resolution').value  = localStorage.videoResolution;
			updateResolution();
		}
		if (sliding) {
			video.addEventListener('ended', function () {
				// don't jump to the next immediately
				setTimeout(function () {
					if (!video.paused) return;
					slider_next();
				}, 1000);
			}, false);
		}
	}, false);
}
