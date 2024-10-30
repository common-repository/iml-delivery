        <tr>
          <th>Условия выдачи</th>
          <td>
            <div class="ext_options">
              <?php 
              foreach ($conditions as $condKey => $condition): ?>
                <div>
                  <span class="cond-option">
                    <?php echo esc_html($condition['Name']) ?>
                  </span>
                  <select 
                  <?php 
                  if(isset($isDisabled))
                  {
                    $isDisabled();
                  }
                  ?>
                  class="sel-cond-option" name="<?php echo $condKey?>">
                    <option value="1" <?php selected( esc_html(get_option($condKey)), 1 ); ?>>
                      Разрешено
                    </option>
                    <option value="0" <?php selected( esc_html(get_option($condKey)), 0 ); ?>>
                      Запрещено
                    </option>                    
                  </select>                  
                </div>
              <?php endforeach;?>
            </div>
          </td>
        </tr>
<style type="text/css">
  .ext_options .cond-option
  {
    display: inline-block;
    width: 400px;
  }
  .ext_options select.sel-cond-option
  {
    width: 120px;
    font-weight: 500;
  }

  .ext_options select.sel-cond-option option
  {
  font-weight: 500;
  }

</style>