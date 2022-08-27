import {Router} from './common/router.js';
import {Themes} from './admin/controller/themes.js';
import {Table} from './admin/controller/table.js';
import {HeartBeat} from './admin/heartbeat.js';

window.themes = Themes;
window.table = {};
		
jQuery(document).ready(function() {
	Router.init();
});