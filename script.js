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

// toggle between 360p and 720p videos
function updateResolution () {
	var resolution = document.getElementById('resolution').value;
	localStorage.videoResolution = resolution;
	var video      = document.getElementById('video');
	var format     = video.canPlayType('video/mp4') ? 'mp4' : 'webm';
	// workaround for bug in Chrome on Linux (mp4 doesn't play but is still advertized to play)
	if (video.canPlayType('video/webm') && navigator.userAgent.indexOf('Linux') && navigator.userAgent.indexOf('Chrome')) {
		format = 'webm';
	}
	// all browsers supporting <video> support querySelector
	var src = document.querySelector('source[type="video/'+format+'"][data-resolution="'+resolution+'"]').src;
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
