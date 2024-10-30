<div class="wrap">
<form method="post" action="options.php">
    <?php settings_fields( 'iml-sg-login' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Логин</th>
        <td><input type="text" name="iml-login" value="<?php echo esc_html(get_option('iml-login')); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Пароль</th>
        <td><input type="password" name="iml-password" value="<?php echo esc_html(get_option('iml-password')); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Город отправителя</th>
        <td>
          <?php
          $departureCity = get_option('departureCity');
        	echo "<select id='departureCity' name='departureCity'>";
          array_unshift($places, '');
        	foreach($places as $placeKey => $placeItem) {
        		$selected = ($departureCity == $placeKey) ? 'selected="selected"' : '';
        		echo "<option value='{$placeKey}' $selected>{$placeItem['title']}</option>";
        	}
        	echo "</select>";
          ?>
        </tr>
    </table>
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Сохранить') ?>" />
    </p>

</form>
</div>
