<tr class="ifgae-table-row">
	<td class='ifgae-table-cell info-cell'>
		<?php if( $item->getFullName() != ''): ?>
			<h4 class='igfae-show-title'><?php echo $item->getFullName(); ?></h4>
		<?php endif; ?>

		<ul class ="list-unstyled ifgae-show-contact">

		<?php if( count($item->getGroups()) > 0 ): ?>
			<li>
				<span class="igfae-type personal-group">
					<?php echo $item->printGroups(); ?>
				</span>
			</li>
		<?php endif; ?>

		<?php if($item->getPhone()): ?>
			<li>
				<i class="ti ti-mobile"></i>
				<strong><?php _e('Phone', 'ifgae-content-viewer'); ?></strong>: 
				<a href="tel:+34 8818<?php echo $item->getPhone(); ?>">
					+34 8818<?php echo $item->getPhone(); ?>
				</a>
			</li>
		<?php endif; ?>

		<?php if($item->getEmail()): ?>
			<li>
				<i class="ti ti-email"></i>
				<strong><?php _e('Email', 'ifgae-content-viewer'); ?></strong>: 
				<a href="mailto:<?php echo $item->getEmail(); ?>">
					<?php echo $item->getEmail(); ?>
				</a>
			</li>
		<?php endif; ?>

		<?php if($item->getWebpage()): ?>
			<li>
				<i class="ti ti-world"></i>
				<strong><?php _e('Webpage', 'ifgae-content-viewer'); ?></strong>: 
				<a href="<?php echo $item->getWebpage(); ?>">
					<?php echo $item->getWebpage(); ?>
				</a>
			</li>
		<?php endif; ?>

		<?php if($item->getRoom()): ?>
			<li>
				<i class="ti ti-home"></i>
				<strong><?php _e('Room', 'ifgae-content-viewer'); ?></strong>: 
				<?php echo $item->getRoom(); ?>
			</li>
		<?php endif; ?>

		<?php if($item->getCategory()): ?>
			<li>
				<i class="ti ti-user"></i>
				<strong><?php _e('Category', 'ifgae-content-viewer'); ?></strong>: 
				<?php echo $item->getCategory(); ?>
			</li>
		<?php endif; ?>
		</ul>

	</td>
</tr>