@categories = [data-v-component-categories] [data-v-cats]
@category = [data-v-component-categories] [data-v-cats] [data-v-cat]

@category input|id = <?php echo 'm' . $category['taxonomy_id'];?>
@category label|for = <?php echo 'm' . $category['taxonomy_id'];?>

@category|addClass = <?php if ($category['active']) echo 'active';?>

