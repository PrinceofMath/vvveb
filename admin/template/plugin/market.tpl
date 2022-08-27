import(common.tpl)


[data-v-plugins] [data-v-plugin]|deleteAllButFirstChild


[data-v-plugins]  [data-v-plugin]|before = <?php
if(isset($this->plugins) && is_array($this->plugins)) 
{
	foreach ($this->plugins as $index => $plugin) 
	{
	?>
	
    [data-v-plugins] [data-v-plugin] [data-v-plugin-*]|innerText  = $plugin['@@__data-v-plugin-(.+)__@@']
    [data-v-plugins] [data-v-plugin] a[data-v-plugin-*]|href  = $plugin['@@__data-v-plugin-(.+)__@@']
    [data-v-plugins] [data-v-plugin] img[data-v-plugin-*]|src  = $plugin['@@__data-v-plugin-(.+)__@@']
    [data-v-plugins] [data-v-plugin] img[data-v-plugin-icon]|src  = $plugin['icons']['1x']

    [data-v-plugins] [data-v-plugin] [data-v-plugin-author]  = <?php echo $plugin['author'];?>
    
	[data-v-plugins]  [data-v-plugin]|after = <?php 
	} 
}?>

