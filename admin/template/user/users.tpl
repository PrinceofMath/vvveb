import(common.tpl)
import(pagination.tpl)

@user = [data-v-users] [data-v-user]
@user|deleteAllButFirstChild

@user|before = <?php
$module = $module ?? 'user/user';
$id = $id ?? 'user_id';

if(isset($this->users) && is_array($this->users)) {
	foreach ($this->users as $index => $user) { 
		$url = Vvveb\url(['module' => $module, $id => $user[$id]]);
		$status = $user['status'] == 1 ?  'active' :($user['status'] == 0 ? 'inactive' : $user['status']);
		$status_class = $user['status'] == 1 ?  'bg-success' :($user['status'] == 0 ? 'bg-light text-muted' : $user['status']);
	?>
	
	@user [data-v-*]|innerText = $user['@@__data-v-([a-zA-Z_]+)__@@']

	@user img[data-v-*]|src = $user['@@__data-v-([a-zA-Z_]+)__@@']

	
	@user [data-v-url]|href =<?php echo htmlentities($url);?>
	@user [data-v-edit-url]|href =<?php echo htmlentities($url);?>
	@user [data-v-url]|title = $user['title']	
	@user [data-v-status]|addClass = <?php echo $status_class;?>
	@user [data-v-status] = <?php echo $status;?>
	
	
	@user|after = <?php 
	} 
}?>



