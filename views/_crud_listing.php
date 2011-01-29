<table cellpadding="5" cellspacing="0" width="100%">
    <tr>
        <?php
        //print_r($listing_display_keys);
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
    foreach($dataset as $data_item) {
        ?>
    <tr class="<?php echo ($counter++ % 2 ? 'row_even' : 'row_odd'); ?>">
            <?php
            foreach ($listing_display_keys as $display_key => $display_value) {
                if($data_item->{$display_key}=='1') {
                    echo '<td>'.html::image('../assets/images/icons/ok.png').'</td>'."\n";
                }else {
                    if(strpos($display_key, '_id') > 0){
                        $val = strtolower(substr($display_key ,0 ,-3));
                        echo '<td>'.$data_item->{$val}->name.'</td>'."\n";
                    }else{
                        echo '<td>'.$data_item->{$display_key}.'</td>'."\n";
                    }
                }
            }
            ?>
            <?php
            foreach($action_key as $key => $item):?>
        <td><?php echo html::anchor('admin/'.strtolower($model_friendly_name).'/'.  strtolower($key).'/'.$data_item->id, html::image('../assets/images/icons/'.strtolower($item['alt']).'.png'), $item); ?></td>
            <?php endforeach; ?>


    </tr>
        <?php
    }
    ?>

</table>