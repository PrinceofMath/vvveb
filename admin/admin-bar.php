<?php

/**
 * Vvveb.
 *
 * Copyright (C) 2022  Ziadin Givan
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */
if (! defined('DIR_ROOT')) {
	exit;
}
//if (function_exists('printAdminBarMenu')) return;

$menu       = $menu       = \Vvveb\config('admin-menu', []);
list($menu) = Vvveb\System\Event::trigger('admin', 'menu', $menu);

$template = Vvveb\getCurrentTemplate();
$url      =  Vvveb\getCurrentUrl();

$admin_path =  '/' . \Vvveb\config('admin.path', 'admin') . '/';
$design_url = $admin_path . Vvveb\url(['module' => 'editor/editor', 'template' => $template, 'url' => $url], false, false);
$urlData    = Vvveb\System\Routes::getUrlData($url);
$edit_url   = isset($urlData['edit']) ? $admin_path . $urlData['edit'] : '';

//if (! function_exists('printAdminBarMenu')) {

	function printAdminBarMenu($menu) {
		foreach ($menu as $menuEntry) {
			echo '<li>';

			if (isset($menuEntry['url'])) {
				echo '<a href="' . $menuEntry['url'] . '" ' . (isset($menuEntry['items']) ? 'class="has-submenu"' : '') . '>';

				if (isset($menuEntry['icon']) && $menuEntry['icon']) {
					echo '<i class="' . $menuEntry['icon'] . '"></i>';
				} else {
					echo '<i></i>';
				}
				echo $menuEntry['name'] . '</a>';
			} else {
				echo '<span>' . $menuEntry['name'] . '</span><hr/>';
			}

			if (isset($menuEntry['items'])) {
				echo '<ul class="submenu">';
				printAdminBarMenu($menuEntry['items']);
				echo '</ul>';
			}

			echo '</li>';
		}
	}
//}
?>

<div id="vvveb-admin">
	
	<ul>
		<li class="v-logo"><a href="https://www.vvveb.org" target="_blank"><div class="vvveb-logo"></div></a>
			<ul>
				<li><a href="https://www.vvveb.org" target="_blank"><i class="la la-home"></i>Vvveb Homepage</a></li>
				<li><a href="https://www.vvveb.org/documentation" target="_blank"><i class="la la-file-alt"></i>Documentation</a></li>
				<li><a href="https://forums.vvveb.org" target="_blank"><i class="la la-sms"></i>Forums</a></li>
			</ul>
		</li>
		<li>
			<a href=""><i class="ion-ios-pulse"></i>Admin</a>
			<ul>
				<?php printAdminBarMenu($menu); ?>
			</ul>				
		</li>
		<li><a href="<?php echo $design_url; ?>">
			<i class="la la-paint-brush"></i>Design page</a></li>
		<?php if ($edit_url) { ?>	
		<li><a href="<?php echo $edit_url; ?>">
			<i class="la la-pencil-alt"></i>Edit page</a>
		</li>
		<?php } ?>
		<li><a href=""><i class="la la-comments"></i>Comments</a></li>
		<li><a href=""><i class="la la-circle-notch"></i>Clear cache</a>
			<ul>
				<li><a href="#" target="_blank"><i class="la la-file-code"></i>Frontend assets cache</a></li>
				<li><a href="#" target="_blank"><i class="la la-code"></i>Compiled templates</a></li>
				<li><a href="#" target="_blank"><i class="la la-database"></i>Database</a></li>
				<li><a href="#" target="_blank"><i class="la la-file-alt"></i>Full page cache</a></li>
				<div class="dropdown-divider"><hr/></div>
				<li><a href="#" target="_blank"><i class="la la-circle-notch"></i>All cache</a></li>
			</ul>
		</li>
	</ul>
	
	
	<ul class="float-end">
		<li>
			<a href=""><i class="la la-user"></i>Super Admin</a>
			<ul>
				<li><a href=""><i class="la la-edit"></i>Edit profile</a></li>
				<li><a href=""><i class="la la-sign-out-alt"></i>Log out</a></li>
			</ul>
		</li>
	</ul>
</div>
