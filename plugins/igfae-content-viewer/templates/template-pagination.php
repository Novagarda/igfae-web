<?php 
    $display = 5;
    if ( ($actualPage - 1) > 0 ) {
        $min = ($actualPage - 1);
    } else {
        $min = 0;
    }

    if ( ($actualPage + 1) < $countPages-1 ) {
        $max = ($actualPage + 1);
    } else {
        $max = $countPages-1;
    }
?>

    <nav id="igfae-pagination" aria-label="igfae-pagination">
        <ul class="pagination">

            <?php if( 0 < $min ): ?>
                <li class="page-item">
                    <a class="page-link" data-page="0" href="#igfae-search-form">1</a>
                </li>
                <li>
                    <span>...</span>
                </li>
            <?php endif; ?>

            <?php foreach ( range($min, $max) as $i ): ?>

                <?php if( $actualPage == $i): ?>
                    <li class="page-item disabled active">
                <?php else: ?>
                    <li class="page-item">
                <?php endif; ?>
                    <a class="page-link" data-page="<?php echo $i ?>" href="#igfae-search-form">
                        <?php echo $i+1 ?>
                    </a>
                </li>
            <?php endforeach; ?>
            
            <?php if( $max < $countPages-1 ): ?>
                <li>
                    <span>...</span>
                </li>
                <li class="page-item">
                    <a class="page-link" data-page="<?php echo $countPages-1 ?>" href="#igfae-search-form">
                        <?php echo $countPages-1+1 ?>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>