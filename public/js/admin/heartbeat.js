/*
 * Keep session open to avoid seeing login screen if not active for a few minutes.
*/

let HeartBeat = setInterval(function () {
	$.ajax(window.location.pathname + '?action=heartbeat');
}, 3 * 60 * 1000);//3 minutes

export {HeartBeat};
