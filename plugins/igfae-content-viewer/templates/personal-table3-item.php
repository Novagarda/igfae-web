    <div class="row">
        <div class="nome left">
            <a class="stretched-link bread-link carga-loading" href="<?php echo get_permalink(get_field('paxina_de_persoa', 'option')); ?><?php echo sanitize_title_for_query(sanitize_file_name($item->getFullName())); ?>/<?php echo $item->getId(); ?>" data-breadcurrent="persoal" data-breadparent="equipo">
                <?php echo $item->getSurname(); ?>, <?php echo $item->getName(); ?>
            </a>
        </div>

        <div class="categoria">
            <?php echo $item->getCategory(); ?>
        </div>

        <div class="email">
            <a href="mailto:<?php echo $item->getEmail(); ?>"><?php echo $item->getEmail(); ?></a> 
        </div>

        <div class="tlfo right">
        <a href="tel:<?php echo ($item->getPhone()) ? '(+34) 8818'.$item->getPhone(): ''; ?>"><?php echo ($item->getPhone()) ? '(+34) 8818'.$item->getPhone(): ''; ?></a> 

            
        </div>
    </div>
