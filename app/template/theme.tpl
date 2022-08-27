import(common.tpl)

h1 =$this->theme['name']
.price = <?php if ($this->theme['price'] == 0) echo 'Free'; else echo $this->theme['price'];?>

