import(common.tpl)

[data-v-requirements]|if_exists = <?php !empty($this->requirements) ?>

[data-v-requirements] [data-v-requirement]|deleteAllButFirstChild


[data-v-requirements]  [data-v-requirement]|before = <?php 
if(isset($this->requirements) && is_array($this->requirements)) 
{
	foreach ($this->requirements as $requirement) 
	{
	?>
	
	[data-v-requirements] [data-v-requirement] [data-v-requirement-text] = $requirement;

	
	[data-v-requirements]  [data-v-requirement]|after = <?php 
	} 
}?>
