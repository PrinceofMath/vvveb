//keep post values
input|value = <?php if (isset($_POST['@@__name__@@'])) echo $_POST['@@__name__@@'];?>
#redirect|value = <?php 
	if (isset($this->redirect)) {
		echo $this->redirect;
	} else {
		//echo '/admin';
		//echo Vvveb\escUrl($_SERVER['REQUEST_URI']);
	}
?>

form|action = $this->action

import(common.tpl)
