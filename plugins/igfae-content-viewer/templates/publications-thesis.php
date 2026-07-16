<article class="list01">
	<div class="left">
		<?php 
		if($item->getDate()){
			$date = new DateTime($item->getDate());
			echo '<div class="date --font-size-xxs">' . $date->format('m') . ', ' . $date->format('d') . ' ' . '(' . $date->format('Y') . ')' . '</div>';
//			echo ($item->getDate())? '<div>'.$item->getDate().'</div>':''; 
		} ?>	
		<span class="btnazul btn-xxs"><?php _e('Thesis' ,'ifgae-content-viewer'); ?></span>
	</div>

	<div class="right">

		<?php echo ($item->getTitle()) ? '<h3 class="--font-size-s">'.$item->getTitle().'</h3>': ''; ?>

		<?php /*
		<?php if($item->getAuthor()): ?>
			<p class='igfae-show-main-author'><?php echo $item->getAuthor(); ?></p>
		<?php endif; ?>

		<?php if($item->getTutors()): ?>
			<strong><?php _e('Tutors','ifgae-content-viewer'); ?></strong>:
			<ul class='list-unstyled igfae-show-tutors'>
				<?php foreach($item->getTutors() as $tutor): ?>
				<li>
					<i class="ti-user"></i>
					<?php echo "{$tutor['surname']}, {$tutor['name']}"; ?>
				</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		*/ ?>
	</div>
	

</article>