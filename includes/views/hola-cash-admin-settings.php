<?php
    defined('ABSPATH') || die('No Script Kiddies Please');
?>
<h3><?php _e('Hola.Cash options', 'hola-cash-wc'); ?></h3>

<table class="form-table">
    <?php $this->generate_settings_html(); ?>
</table>

<div class='hola-cash-instructions'>
    <h4>Instructions: </h4>
    <p>Set the Webhook url to : <?php echo admin_url('admin-ajax.php?action=hola_cash_wc_listen'); ?></p>
    
</div>