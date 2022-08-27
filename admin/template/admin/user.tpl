import(common.tpl)

[data-v-user] [data-v-user-*] = $this->user['@@__data-v-user-([a-zA-Z_]+)__@@']

/* input elements */
[data-v-user] input[data-v-user-*]|value = 
<?php
	 if (isset($_POST['@@__data-v-user-([a-zA-Z_]+)__@@'])) 
		echo $_POST['@@__data-v-user-([a-zA-Z_]+)__@@']; 
	 else if (isset($this->user['@@__data-v-user-([a-zA-Z_]+)__@@'])) 
		echo $this->user['@@__data-v-user-([a-zA-Z_]+)__@@'];
?>


/* textarea elements */
[data-v-user] textarea[data-v-user-*] = 
<?php
	 if (isset($_POST['@@__data-v-user-([a-zA-Z_]+)__@@'])) 
		echo $_POST['@@__data-v-user-([a-zA-Z_]+)__@@']; 
	 else if (isset($this->user['@@__data-v-user-([a-zA-Z_]+)__@@'])) 
		echo $this->user['@@__data-v-user-([a-zA-Z_]+)__@@'];
?>/* textarea elements */



[data-v-user] select[data-v-user-*]|before = 
<?php
	 $selected = '';	
	 if (isset($this->user['@@__data-v-user-([a-zA-Z_]+)__@@'])) 
	 $selected = $this->user['@@__data-v-user-([a-zA-Z_]+)__@@'];
?>


