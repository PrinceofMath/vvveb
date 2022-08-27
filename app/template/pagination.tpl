@page = [data-pagination] [data-page]
@page|deleteAllButFirst

[data-pagination]|before = <?php $maxpages = 5; $visible_pages = 3; 


$component = $this->@@__data-v-parent-component__@@[@@__data-v-parent-index__@@];	

if(isset($component['count'])) {
	
if (isset($this->limit)) $limit = $this->limit; else $limit = 10;
	
$max_pages = ceil($component['count'] / $limit);

$page = 1;
$page_stop = $max_pages;
$url = '@@__data-v-url__@@';
if (empty($url)) {
	$url = Vvveb\System\Core\FrontController :: getRoute();
}

if (isset($_GET['page'])) {
	$current_page = $_GET['page']; 
} else  if (isset($this->pagenum)) {
	$current_page = $this->pagenum; 
} else {
	$current_page = 1;
}

$current_page = max($current_page, 1);

if ($max_pages > $maxpages)
{
	if ($current_page > $visible_pages)
	{
		if (($current_page + $visible_pages) > $max_pages)
		{
			$page = $max_pages - $maxpages - 1;
			$page_stop = $max_pages;
		} else 
		{
			$page = $current_page - $visible_pages;
			$page_stop = $current_page + $visible_pages;
		}
	} else
	{
		$page = 1;
		$page_stop = $maxpages;
	}
}
?>

@page|before = <?php  
	for (;$page <= $page_stop;$page++) {
?>

	[data-pagination] [data-pages] = $max_pages
	@page [data-page-no] = $page
	@page [data-page-url]|href = <?php echo htmlentities(Vvveb\url($url, ['page' => $page]));?>
	@page|addClass = <?php if ($current_page == $page) echo 'active'?>

@page|after = <?php 
	} 
?>

[data-pagination] [data-count] = $this->count
[data-pagination] [data-current-page] = $current_page
[data-pagination] [data-current-url]|action = <?php echo htmlentities(Vvveb\url($url, ['page' => $current_page]));?>
[data-pagination] [data-first] [data-page-url]|href = <?php echo htmlentities(Vvveb\url($url, ['page' => 1]));?>
[data-pagination] [data-prev] [data-page-url]|href = <?php echo htmlentities(Vvveb\url($url, ['page' => max($current_page - 1, 1)]));?>
[data-pagination] [data-next] [data-page-url]|href = <?php echo htmlentities(Vvveb\url($url, ['page' => min($current_page + 1, $max_pages)]));?>
[data-pagination] [data-last] [data-page-url]|href = <?php echo htmlentities(Vvveb\url($url, ['page' => $max_pages]));?>


[data-pagination]|after = <?php } ?>
