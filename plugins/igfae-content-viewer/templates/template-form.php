<form id="igfae-search-form" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
    <input type="hidden" name="action" value="igfae_filter_<?php echo $view;?>" />

    <?php if($types && $types2): ?>
    
        <input type='text' name='igfae-keywords' size='50' placeholder="<?php _e('Buscar...', 'ifgae-content-viewer'); ?>">
        
        <select name="igfae-type">
            <?php foreach($types['items'] as $key => $name): ?>
                <?php if( array_key_exists('selected', $types ) && $types['selected'] == $key): ?>
                    <option selected value="<?php echo $key; ?>"><?php echo $name; ?></option>
                <?php else: ?>
                    <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>

        <select name="igfae-type2">
            <?php foreach($types2['items'] as $key => $name): ?>
                <?php if( array_key_exists('selected', $types2 ) && $types2['selected'] == $key): ?>
                    <option selected value="<?php echo $key; ?>"><?php echo $name; ?></option>
                <?php else: ?>
                    <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>

    <?php elseif($types): ?>

        <input type='text' name='igfae-keywords' size='50' placeholder="<?php _e('Buscar...', 'ifgae-content-viewer'); ?>">
        
        <select name="igfae-type">
            <?php foreach($types['items'] as $key => $name): ?>
                <?php if( array_key_exists('selected', $types ) && $types['selected'] == $key): ?>
                    <option selected value="<?php echo $key; ?>"><?php echo $name; ?></option>
                <?php else: ?>
                    <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>

    <?php else: ?>
        <input type='text' name='igfae-keywords' size='50' placeholder="<?php _e('Buscar...', 'ifgae-content-viewer'); ?>">
        
    <?php endif; ?>

        <input value='<?php _e('Search', 'ifgae-content-viewer')?>' type='submit'>
</form>