<?php wp_head(); ?>
	<?php 
	  mail("dina.websites@gmail.com", "payment status completed", var_export($_REQUEST,true));
	   if ($_REQUEST['payer_status']=='verified'){
          	?>
	     <script type="text/javascript">
			ga('require', 'ecommerce');
			ga('ecommerce:addTransaction', {
                 'id': '<?php echo $_REQUEST['txn_id']; ?>',                     // Transaction ID. Required.
                 'affiliation': 'Dietgirl',   // Affiliation or store name.
                 'revenue': '<?php echo $_REQUEST['payment_gross']; ?>',               // Grand Total.
                 'shipping': '<?php echo $_REQUEST['shipping']; ?>',                  // Shipping.
                  'tax': '<?php echo $_REQUEST['tax']; ?>'                     // Tax.
             });
			 
			 ga('ecommerce:addItem', {
                    'id': '<?php echo $_REQUEST['txn_id']; ?>',                     // Transaction ID. Required.
                    'name': '<?php echo $_REQUEST['item_name']; ?>',    // Product name. Required.
                    'sku': '<?php echo $_REQUEST['item_number']; ?>',                 // SKU/code.
                    'price': '<?php echo $_REQUEST['payment_gross']; ?>',                 // Unit price.
                    'quantity': '1'                   // Quantity.
             });
		    ga('ecommerce:send');
         </script>		
		   
	<?php }	?>
<?php //wp_redirect('/nutrition-profile/');
?>