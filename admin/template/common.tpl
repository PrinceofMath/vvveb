//example <div data-v-copy-from="index.html,#element">
[data-v-copy-from]|outerHTML = from(@@__data-v-copy-from:([^\,]+)__@@|@@__data-v-copy-from:[^\,]+\,([^\,]+)__@@)


//im_port(components/categories.tpl, .component_categories)
import(components/posts.tpl)
import(components/products.tpl)
import(components/orders.tpl)
import(components/customers.tpl)
import(components/languages.tpl)
import(components/user.tpl)
import(components/admin.tpl)
import(components/comments.tpl)
import(components/news.tpl)
import(components/product/reviews.tpl)
import(components/stats.tpl)
import(components/states.tpl)
import(components/sites.tpl)
import(components/pagination.tpl)
//im_port(editor.tpl)
import(menu.tpl)

[data-v-url]|href = <?php echo htmlentities(Vvveb\url('@@__data-v-url__@@'));?>
form[data-v-url]|action = <?php echo htmlentities(Vvveb\url('@@__data-v-url__@@'));?>

[data-v-url-params]|href = <?php echo Vvveb\url(@@__data-v-url-params__@@);?>
form[data-v-url-params]|action = <?php echo Vvveb\url(@@__data-v-url-params__@@);?>

[data-v-url][data-v-url-params]|href = <?php echo Vvveb\url('@@__data-v-url__@@' , @@__data-v-url-params__@@);?>
form[data-v-url][data-v-url-params]|action = <?php echo Vvveb\url('@@__data-v-url__@@' , @@__data-v-url-params__@@);?>


/* modifiers */
//.capitalize|register_filter = <?php ucfirst($content, $arg1, $arg2);?>

				  
/*body|prepend = <?php var_dump($this);?>*/

head base|href = <?php echo Vvveb\themeUrlPath()?>

[data-v-admin-live-url]|href = <?php echo $this->live_url;?>
//csrf
[data-v-csrf]|value = <?php echo \Vvveb\session('csrf');?>


[data-v-validator-json] = <?php if (isset($this->validator_json)) echo 'validator_json = ' . $this->validator_json;?>

@@_CONSTANT_PUBLIC_PATH_@@ = <?php echo PUBLIC_PATH;?>

import(ifmacros.tpl)
import(notifications.tpl)
