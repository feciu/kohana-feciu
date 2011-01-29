<table cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<?php
		foreach ($listing_display_keys as $display_key => $display_value) {
			if(in_array($display_key, $listing_ordering_keys)) {
				echo '<th><a href="#">'.$display_value.'</a></th>'."\n";
			} else {
				echo '<th>'.$display_value.'</th>'."\n";
			}
		}
		?>
                <?php foreach($action_key as $key => $item):?>
                    <th><?php echo $key?></th>
                <?php endforeach; ?>

	</tr>
<?php
	$counter = 0;
	foreach($dataset as $data_item){
?>      
	<tr class="<?php echo ($counter++ % 2 ? 'row_even' : 'row_odd'); ?>">
		<?php
			echo '<td>'.$data_item->name.'</td>'."\n";
		?>
                <?php

                        foreach($action_key as $key => $item){
                            switch ($key){
                                case 'Active':
                                    echo ($data_item->show == 1 ? '<td>'.html::image(url::base().'assets/images/icons/accept.png').'</td>' : '<td></td>');
                                    break;
                                case 'Build':
                                    ($data_item->show == 1 ? $link = 'rebuild' : $link = 'build');
                                    echo '<td>'.html::anchor('admin/panel/'.$link.'/'.$data_item->id, html::image(url::base().'assets/images/icons/'.strtolower($item['alt']).'.png'), $item).'</td>';
                                    break;
                                default:
                                    echo '<td>'.html::anchor('admin/panel/'.  strtolower($key).'/'.$data_item->id, html::image(url::base().'assets/images/icons/'.strtolower($item['alt']).'.png'), $item).'</td>';
                            }
                        }
               ?>


	</tr>
<?php
	}
?>

</table>