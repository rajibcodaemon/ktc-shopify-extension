<!DOCTYPE html>
<html>
    <head>
        <title>Warning!</title>
        <?php $this->load->view('common/front_css'); ?>
        <link href="<?php echo base_url();?>css/welcome_page.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
<!--        <header class="custom-head">
            <article>
                <div class="columns four">
                    <div class="logo-image align-left">
                        <img src="<?php //echo base_url(); ?>img/img_logo_with_text.png" alt="" width="200em">
                    </div>
                </div>
                <div class="columns eight align-right">
                    <p class="text-contact text-Navy">Need assistance? Call <span class="text-Teal">1-844-823-2869</span> or <a href="mailto:support@knowthycustomer.com" class="text-Teal">email support@knowthycustomer.com</a>
                    </p>
                </div>
            </article>
        </header>-->

        <section class="full-width">
            <article>
                <div class="card blue-bg">

                    <div class="columns">
                        <div class="return-link">
                            <a href="javascript: void(0);" class="rdord" data-href="https://<?= $_SESSION['shop'] ?>/admin/orders/<?= $_SESSION['oid'] ?>">
                                <i class="fa fa-arrow-left text-white" aria-hidden="true"></i>
                                <span class="return-text">Return to Orders</span>
                            </a>
                        </div>
                        <div class="content">
                            <img src="<?php echo base_url(); ?>img/warning_icon.png" class="warning-icon"/>
                            <p class="warning-heading">Your browser is preventing us from displaying your fraud check a new window</p>
                            <p class="warning-sub-text">Enable popups from our domain in order to access your report</p>
                            <button class="btn-Teal" id="form_submit_btn" onclick="window.open('https://www.knowthycustomer.com/blog/2017/06/12/how-to-enable-pop-ups/')">Learn how to do this</button>

                            <p class="warning-sub-text2">Enable popups from our domain in order to access your report</p>
                            <button onclick="window.open('<?php echo $newurl ?>')" class="btn-Teal margin-bottom-5" id="form_submit_btn">Re-launch your fraud check</button>
                        </div>
                    </div>

                </div>
            </article>

        </section>
        <?php $this->load->view('common/footer_js'); ?>
        <script>

            $(document).ready(function () {
                $('#form_submit_btn').click(function () {
                    $('input').each(function () {
                        if (!$(this).val()) {
                            //alert('Some fields are empty');

                            $(this).parent('.row').addClass('error');
                            return false;
                        }
                    });
                });
            });

        </script>
        <script>
            var url = '<?=$newurl?>';
            popUp = window.open(url, '_blank');            

            if (popUp == null || typeof(popUp)=='undefined') {

            }else{
                window.history.go(-1);
            }

            //setTimeout(function(){ window.history.go(-1); }, 3000);

            //var importantStuff = window.open(url, '_blank');
            //importantStuff.location.href = url;

            // var tabOpen = window.open(url, '_blank'),
            // xhr = new XMLHttpRequest();

            // xhr.open("GET", 'https://shopify.knowthycustomer.com', true);
            // xhr.onreadystatechange = function () {
            //     if (xhr.readyState == 4) {
            //         console.log(tabOpen);
            //         tabOpen.location = url;
            //     }
            // }
            // xhr.send(null);

            $(document).on('click', '.rdktc', function(event){
                event.preventDefault();
                var url = $('.rdktc').attr('data-href');
                window.open(url, '_blank');
                window.history.go(-1);
            });        

            $(document).on('click', '.rdord', function(event){
                event.preventDefault();
                var url = $('.rdord').attr('data-href');
                //window.location.href = url;
                //window.open(url, '_blank');
                window.history.go(-1);
            });

            // $.ajax({url:'https://shopify.knowthycustomer.com',type:'GET',data:{},dataType:'html',async: false,success:function(data){ 
            //     //$('.rdktc').trigger('click'); 
            //     var url = '<?=$newurl?>';
            //     window.open(url, '_blank');
            //     window.history.go(-1);
            // }});

            // $(document).ready(function () {
            //     var url = '<?=$newurl?>';
            //     window.open(url, '_blank');
            //     window.history.go(-1);
            // });
        </script>
    </body>
</html>