<?php foreach ($orders as $order) { 
    $desc = json_decode($order['description']);
    $province_code = $desc->shipping_address->province_code; ?>                    
    <tr>
        <td><a href="https://<?= $_SESSION['shop'] ?>/admin/orders/<?= $order['id'] ?>" target="_blank">#<?php echo $order['order_no']; ?></a></td>
        <td><?= date("M d, g:i A", strtotime($order['created_at'])) ?></td>
        <td>
            <address>
                <strong>
                    <?php echo $order['s_first_name'] . ' ' . $order['s_last_name']; ?>
                    <!--<a class="red-text" target="_blank" href="https://www.knowthycustomer.com/f/search/person?age=&city=&fn=<?= $order['first_name'] ?>&ln=<?= $order['last_name'] ?>&mn=&state=&address=&phone=&email=&ip="><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></a>-->
                </strong>
                <br><span class="font-normal">
                    <?php echo $order['s_address1'] . '<br>' . $order['s_city'] . ' ' . $province_code .' '.$order['s_zip'].'<br> ' . $order['s_country']; ?></span>
            </address>
        </td>
    <!--                                        <td>
            <span>
        <?php if (isset($order['shipping_address'])) { ?>
                    <button class="tip contact-btn" data-hover="<?= $order['shipping_address']['phone'] ?>" onclick="opennewtab('https://www.knowthycustomer.com/f/search/phone?phone=<?= $order['shipping_address']['phone'] ?>')"><i class="fa fa-phone-square fa-1x" aria-hidden="true"></i></button>
        <?php } ?>
                <button class="tip contact-btn" data-hover="<?= $order['contact_email'] ?>" onclick="opennewtab('https://www.knowthycustomer.com/f/search/email?email=<?= $order['contact_email'] ?>')"><i class="fa fa-envelope fa-1x" aria-hidden="true"></i></button>
        </td>-->
        <td>
            <?php
            $color = '';
            $payment = $order['payment_status'];
            if ($payment == 'paid') {
                $color = 'btn-success';
            }
            else if ($payment == 'partially_refunded') {
                $color = 'btn-alert';
            }
            else if ($payment == 'partially_paid') {
                $color = 'btn-info';
            }
            else if ($payment == 'pending') {
                $color = 'btn-warning';
            }
            else if ($payment == 'refunded') {
                $color = 'btn-primary';
            }
            else if ($payment == 'unpaid') {
                $color = 'btn-danger';
            }
            else if ($payment == 'voided') {
                $color = 'btn-dark';
            }
            ?>
            <span class="tag <?= $color ?>"><i class="fa fa-clock-o" aria-hidden="true"></i> <?= $payment ?></span>
        <td>
            <?php
            $fullfill = is_null($order['fulfillment_status']) ? 'unfulfilled' : $order['fulfillment_status'];
            if ($fullfill == 'unshipped') {
                $color = 'btn-danger';
            }
            else if ($fullfill == 'partial') {
                $color = 'btn-alert';
            }
            else if ($fullfill == 'unfulfilled') {
                $color = 'btn-warning';
            }
            ?>
            <span class="tag <?= $color ?>"><?= $fullfill ?></span>
        </td>
        <td><strong>$<?= $order['total_price'] ?></strong></td>
        <td>
            <?php //$b_address = $order['b_address1'].' '.$order['b_city'].' '.$order['b_province'].' '.$order['b_zip'].' '.$order['b_country']; ?>
            <?php //$s_address = $order['s_address1'].' '.$order['s_city'].' '.$order['s_province'].' '.$order['s_zip'].' '.$order['s_country']; ?>

            <?php
                $b_address = $order['b_address1'].' '.$order['b_city'].' '.$order['b_province'].' '.$order['b_zip'].' '.$order['b_country'];
                $s_address = $order['s_address1'].' '.$order['s_city'].' '.$order['s_province'].' '.$order['s_zip'].' '.$order['s_country'];
                // $check_link = 'https://www.knowthycustomer.com/f/generate/fraud?billing_first_name='.((isset($order['b_first_name']) && !empty($order['b_first_name']))?$order['b_first_name']:'Not Available').'&billing_middle_name=&billing_last_name='.((isset($order['b_last_name']) && !empty($order['b_last_name']))?$order['b_last_name']:'Not Available').'&billing_address='.((isset($b_address) && !empty($b_address))?$b_address:'Not Available').'&billing_ip_address='.$ip_address.'&billing_email='.((isset($order['contact_email']) && !empty($order['contact_email']))?$order['contact_email']:'Not Available').'&billing_phone='.((isset($order['contact_email']) && !empty($order['b_phone']))?$order['b_phone']:'Not Available').'&shipping_first_name='.((isset($order['s_first_name']) && !empty($order['s_first_name']))?$order['s_first_name']:'Not Available').'&shipping_middle_name=&shipping_last_name='.((isset($order['s_last_name']) && !empty($order['s_last_name']))?$order['s_last_name']:'Not Available').'&shipping_address='.((isset($s_address) && !empty($s_address))?$s_address:'Not Available').'&shipping_email='.((isset($order['contact_email']) && !empty($order['contact_email']))?$order['contact_email']:'Not Available').'&shipping_phone='.((isset($order['s_phone']) && !empty($order['s_phone']))?$order['s_phone']:'Not Available');

                $check_link = 'https://www.knowthycustomer.com/f/generate/fraud?billing_first_name='.$order['b_first_name'].'&billing_middle_name=&billing_last_name='.((isset($order['b_last_name']) && !empty($order['b_last_name']))?$order['b_last_name']:'Not Available').'&billing_address='.((isset($b_address) && !empty(trim($b_address)))?$b_address:'Not Available').'&billing_ip_address='.$ip_address.'&billing_email='.$order['contact_email'].'&billing_phone='.$order['b_phone'].'&shipping_first_name='.$order['s_first_name'].'&shipping_middle_name=&shipping_last_name='.$order['s_last_name'].'&shipping_address='.$s_address.'&shipping_email='.$order['contact_email'].'&shipping_phone='.$order['s_phone'];
            ?>

            <!--<button class="btn-Blue" onclick="opennewtab('https://www.knowthycustomer.com/f/search/property?address=<?= $order['s_address1'] ?>&city=<?= $order['s_city'] ?>&state=&zipcode=<?= $order['s_zip'] ?>')">-->
            <button class="btn-Blue" onclick="opennewtab('<?php echo $check_link; ?>')">
                Run Fraud Check <i class="fa fa-share-square-o" aria-hidden="true"></i>
            </button>
        </td>
    </tr>
<?php } ?>
    <tr class="display-none" id="pagecount"><td><?= $count ?></td></tr>