/* notifications */

[data-v-notifications] [data-v-notification-error]|before = 
<?php if (isset($this->errors)) foreach($this->errors as $message) {?>
	
[data-v-notifications] [data-v-notification-error] [data-v-notification-text] = <?php echo $message;?>
	
[data-v-notifications] [data-v-notification-error]|after =
<?php 
	}
?>
		
[data-v-notifications] [data-v-notification-success]|before = <?php 

if (isset($_GET['success'])) {
	if (!isset($this->success)) {
		$this->success = [];
	}
	$this->success[] = htmlentities($_GET['success']);
}

if (isset($this->success)) foreach($this->success as $message) {?>
	
[data-v-notifications] [data-v-notification-success] [data-v-notification-text] = <?php echo $message;?>
	
[data-v-notifications] [data-v-notification-success]|after =
<?php 
	}
?>


[data-v-notifications] [data-v-notification-info]|before = <?php 
/*
if (isset($_GET['info'])) {
	if (!isset($this->info)) {
		$this->info = [];
	}
	$this->info[] = $_GET['info'];
}
*/
if (isset($this->info)) foreach($this->info as $message) {?>
	
[data-v-notifications] [data-v-notification-info] [data-v-notification-text] = <?php echo $message;?>
	
[data-v-notifications] [data-v-notification-info]|after =
<?php 
	}
?>


//errors
[data-v-errors]|before = <?php if (isset($this->errors) && is_array($this->errors) && $this->errors) {?>
//[data-v-errors]|if_exists = $this->errors

[data-v-errors] [data-v-error]|deleteAllButFirstChild


[data-v-errors] [data-v-error]|before = <?php 
if(isset($this->errors) && is_array($this->errors)) 
{
	foreach ($this->errors as $error) { ?>
	
		[data-v-errors] [data-v-error] [data-v-error-text]|innerText = $error

	
	[data-v-errors]  [data-v-error]|after = <?php 
	} 
}?>

[data-v-errors]|after = <?php 
	}
?>

//messages
//[data-v-messages]|if_exists = <?php isset($this->messages) && is_array($this->messages) && $this->messages ?>
[data-v-messages]|before = <?php if (isset($this->messages) && is_array($this->messages) && $this->messages) {?>
[data-v-messages]|if_exists = $this->messages

[data-v-messages] [data-v-message]|deleteAllButFirstChild


[data-v-messages] [data-v-message]|before = <?php 
if(isset($this->messages) && is_array($this->messages)) 
{
	foreach ($this->messages as $message)  { ?>
	
		[data-v-messages] [data-v-message] [data-v-message-text]|innerText = $message
	
	[data-v-messages]  [data-v-message]|after = <?php 
	} 
}?>

[data-v-messages]|after = <?php 
	}
?>



//validation
/*
[data-v-validation-errors]|before =
<?php if (isset($this->validationErrors)) {?>
*/

	[data-v-validation-error]|before = 
	<?php 
	 if (isset($this->validationErrors))
	 foreach($this->validationErrors as $message) {?>
		
	[data-v-validation-errors] [data-v-validation-error] [data-v-validation-error-text] = <?php echo $message;?>
	
	[data-v-validation-errors] [data-v-validation-error]|after =
	<?php 
		}
	?>
	
/*	
[data-v-validation-errors]|after =
<?php 
}
?>
*/
