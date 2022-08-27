//default[data-pagination] logic, reuse everywhere
@page = [data-pagination] [data-page]
@page|deleteAllButFirst

@page|deleteAllButFirst

[data-pagination]|before = <?php $maxpages = 5; $visible_pages = 3; if(isset($this->count)) {

if (isset($this->limit)) $limit = $this->limit; else $limit = 10;
$pages = ceil($this->count / $limit);

$page = 1;
$page_stop = $pages;
if (isset($_GET['page'])) $pagenum = $_GET['page']; else 
if (isset($this->pagenum)) $pagenum = $this->pagenum; 
else $pagenum = 1;

$pagenum = max($pagenum, 1);

if ($pages > $maxpages)
{
	if ($pagenum > $visible_pages)
	{
		if (($pagenum + $visible_pages) > $pages)
		{
			$page = $pages - $maxpages - 1;
			$page_stop = $pages;
		} else 
		{
			$page = $pagenum - $visible_pages;
			$page_stop = $pagenum + $visible_pages;
		}
	} else
	{
		$page = 1;
		$page_stop = $maxpages;
	}
}
?>

@page|before = <?php  for (;$page <= $page_stop;$page++) {
?>

[data-pagination] [data-count] = $this->count
[data-pagination] [data-current-page] = $pagenum
[data-pagination] [data-pages] = $pages
@page [data-page-no] = $page
@page [data-page-url]|href = <?php echo htmlentities(Vvveb\url(['page' => $page] , true));?>
@page|addClass = <?php if ($pagenum == $page) echo 'active'?>

[data-pagination] [data-current-url]|action = <?php echo htmlentities(Vvveb\url(['page' => $pagenum], true));?>

[data-pagination] [data-first] [data-page-url]|href = <?php echo htmlentities(Vvveb\url(['page' => 1], true));?>
[data-pagination] [data-prev] [data-page-url]|href = <?php echo htmlentities(Vvveb\url(['page' => max($pagenum - 1, 1)], true));?>
[data-pagination] [data-next] [data-page-url]|href = <?php echo htmlentities(Vvveb\url(['page' => min($pagenum + 1, $pages)], true));?>
[data-pagination] [data-last] [data-page-url]|href = <?php echo htmlentities(Vvveb\url(['page' => $pages], true));?>

@page|after = <?php } ?>

[data-pagination]|after = <?php } ?>
