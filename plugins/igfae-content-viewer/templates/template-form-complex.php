<form id="igfae-search-form" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
    <input type="hidden" name="action" value="igfae_filter_<?php echo $view;?>" />

    <input class="buscar" type='text' name='igfae-keywords' size='50' placeholder="<?php _e('Buscar...', 'ifgae-content-viewer'); ?>">

    <?php foreach ($campos as $key => $campo) { ?>

        <?php if($campo['tipo'] == 'select'){ ?>

            <div class="form-bloque">
                <h5 class="--font-size-xs azul"><?php _e($campo['label'], 'ifgae-content-viewer'); ?></h5>
                <select name="<?php echo $campo['nome'] ?>">
                    <?php foreach($campo['items'] as $key => $name): 
                        ?>
                        <?php if( array_key_exists('selected', $campo ) && $campo['selected'] == $key): ?>
                            <option selected value="<?php echo $key; ?>"><?php echo $name; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>     
            </div>   

        <?php }else if($campo['tipo'] == 'radio'){ ?>
            
            <div class="form-bloque">
                <h5 class="--font-size-xs azul"><?php _e($campo['label'], 'ifgae-content-viewer'); ?></h5>
                <ul class="radios">
                    <?php foreach($campo['items'] as $key => $name): 
                    $seleccionados = explode(',', $campo['selected']);
                        ?>
                        <?php if($name){ ?>
                        <li>
                            <input type="radio" id="<?php echo $campo['nome'] ?>-<?php echo $name; ?>" name="<?php echo $campo['nome'] ?>" value="<?php echo $key; ?>" <?php echo (in_array($key, $seleccionados)) ? 'checked': ''; ?>>
                            <label for="<?php echo $campo['nome'] ?>-<?php echo $name; ?>" class="--font-size-wws"><?php echo $name; ?></label>
                        </li>
                        <?php } ?>
                    <?php endforeach; ?>
                </ul>
            </div>

        <?php }else if($campo['tipo'] == 'checkbox'){ ?>
            
            <div class="form-bloque">
                <h5 class="--font-size-xs azul"><?php _e($campo['label'], 'ifgae-content-viewer'); ?></h5>
                <ul class="checkboxs">
                    <?php foreach($campo['items'] as $key => $name): 
                    $seleccionados = explode(',', $campo['selected']);
                        ?>
                        <?php if($name){ ?>
                        <li>
                            <input type="checkbox" id="<?php echo $campo['nome'] ?>-<?php echo $name; ?>" name="<?php echo $campo['nome'] ?>[]" value="<?php echo $key; ?>" <?php echo (in_array($key, $seleccionados)) ? 'checked': ''; ?>>
                            <label for="<?php echo $campo['nome'] ?>-<?php echo $name; ?>" class="--font-size-wws<?php echo ($key = 'all') ? ' all':''; ?>"><?php echo $name; ?></label>
                        </li>
                        <?php } ?>
                    <?php endforeach; ?>
                </ul>
            </div>

        <?php } ?>

    <?php } ?>

    <div class="form-bloque">
        <input class="btnnegro --font-size-xs" value='<?php _e('Buscar', 'ifgae-content-viewer')?>' type='submit'>
    </div>
</form>