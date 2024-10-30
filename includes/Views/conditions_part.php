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
                  <select name="<?php echo esc_attr($condKey)?>">
                    <option value="1" <?php selected( get_option($condKey), 1 ); ?>>
                      Разрешено
                    </option>
                    <option value="0" <?php selected( get_option($condKey), 0 ); ?>>
                      Запрещено
                    </option>                    
                  </select>                  
                </div>
              <?php endforeach;?>
            </div>
          </td>
        </tr>