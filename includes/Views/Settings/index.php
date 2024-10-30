
<h2 class="nav-tab-wrapper">
    <? foreach ($tabsActions as $key => $value) { 
      $active = ($imlSetActiveTab == $key) ? 'nav-tab-active' : '';
    ?>

        <a class="nav-tab <?php echo esc_html($active) ?>"
            href="<?php echo admin_url( "options-general.php?page=iml-settings&tab=". esc_html($key)) ?>"
        >
            <?php echo esc_html($value) ?>
        </a>
    <? } ?>
</h2>

<?php
    
    switch ($imlSetActiveTab) {
        case 'login':
            $this->renderLoginPage();
        break;

        case 'vars':
            $this->renderVarsPage();
        break;

        default:
            include_once dirname( __FILE__ ) . "/{$imlSetActiveTab}.php";
        break;
    }