    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/jquery.bvalidator.js"></script>
    <script src="//cdn.shopify.com/s/assets/external/app.js?2016021109"></script>

    <script type="text/javascript">
    	ShopifyApp.init({
		    apiKey: "4ede6c02beb0886538bac12a4f6335e6",
		    shopOrigin: "https://<?php echo $_SESSION['shop']; ?>"
	  	});
	  	ShopifyApp.ready(function(){
	  		//   	ShopifyApp.Bar.initialize({
			// 	title: "Fraud Check by KTC",
			// 	icon: "https://shopify.knowthycustomer.com/img/logo.png",			  
			// });

			ShopifyApp.Bar.setBreadcrumb(undefined);
			ShopifyApp.Bar.setIcon("https://shopify.knowthycustomer.com/img/img_logo_dice.png");
			//ShopifyApp.Bar.setTitle("Fraud Check by KTC");
		});
    </script>
    