<?php

include 'includes/book-utilities.inc.php';
$customers = getCustomers();
$selectedId = isset($_GET['id']) ? (int)$_GET['id'] : array_key_first($customers);
$selectedCustomer = getCustomerById($selectedId);
$selectedOrders = getOrdersByCustomerId($selectedId);
$studentId = 'DC326312';
$fullName = 'HUANG SOFIA';

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function renderSparkline(array $values): string {
    if (empty($values)) {
        return '';
    }

    $min = min($values);
    $max = max($values);
    $w = 220.0;
    $h = 44.0;
    $step = count($values) > 1 ? ($w / (count($values) - 1)) : $w;
    $range = max(1, $max - $min);
    $points = [];

    foreach ($values as $i => $v) {
        $x = $i * $step;
        $y = $h - ((($v - $min) / $range) * ($h - 6.0)) - 3.0;
        $points[] = number_format($x, 1, '.', '') . ',' . number_format($y, 1, '.', '');
    }

    return '<svg class="spark-svg" viewBox="0 0 220 44" aria-label="sales trend">'
        . '<polyline fill="none" stroke="#673ab7" stroke-width="2" points="' . h(implode(' ', $points)) . '"></polyline>'
        . '</svg>';
}

function renderMiniBars(array $values): string {
    if (empty($values)) {
        return '';
    }

    $max = max($values);
    $max = $max > 0 ? $max : 1;
    $bars = [];
    foreach ($values as $v) {
        $height = max(2, (int)round(($v / $max) * 20));
        $bars[] = '<span class="mini-bar" style="height:' . $height . 'px"></span>';
    }
    return '<span class="mini-bars" aria-label="monthly sales">' . implode('', $bars) . '</span>';
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?=h($studentId)?> - <?=h($fullName)?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/material.min.css">
    <link rel="stylesheet" href="css/styles.css">
    
    <script src="js/material.min.js"></script>
    
  
</head>

<body>
    
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer
            mdl-layout--fixed-header">
            
    <?php include 'includes/header.inc.php'; ?>
    <?php include 'includes/left-nav.inc.php'; ?>
    
    <main class="mdl-layout__content mdl-color--grey-50">
        <section class="page-content">

            <div class="mdl-grid">

              <!-- mdl-cell + mdl-card -->
              <div class="mdl-cell mdl-cell--7-col card-lesson mdl-card  mdl-shadow--2dp">
                <div class="mdl-card__title mdl-color--orange">
                  <h2 class="mdl-card__title-text">Customers</h2>
                </div>
                <div class="mdl-card__supporting-text">
                    <table class="mdl-data-table  mdl-shadow--2dp">
                      <thead>
                        <tr>
                          <th class="mdl-data-table__cell--non-numeric">Name</th>
                          <th class="mdl-data-table__cell--non-numeric">University</th>
                          <th class="mdl-data-table__cell--non-numeric">City</th>
                          <th class="sales-cell">Sales</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr class="<?=($selectedCustomer && $customer['id'] === $selectedCustomer['id']) ? 'is-selected' : ''?>">
                                <td class="mdl-data-table__cell--non-numeric">
                                    <a href="?id=<?=$customer['id']?>"><?=h($customer['customerName'])?></a>
                                </td>
                                <td class="mdl-data-table__cell--non-numeric"><?=h($customer['university'])?></td>
                                <td class="mdl-data-table__cell--non-numeric"><?=h($customer['city'])?></td>
                                <td class="sales-cell"><?=renderMiniBars($customer['salesByMonth'])?></td>
                            </tr>
                        <?php endforeach; ?>
                                              
                      </tbody>
                    </table>
                </div>
              </div>  <!-- / mdl-cell + mdl-card -->
              
              
            <div class="mdl-grid mdl-cell--5-col">
    

       
                  <!-- mdl-cell + mdl-card -->
                  <div class="mdl-cell mdl-cell--12-col card-lesson mdl-card  mdl-shadow--2dp">
                    <div class="mdl-card__title mdl-color--deep-purple mdl-color-text--white">
                      <h2 class="mdl-card__title-text">Customer Details</h2>
                    </div>
                    <div class="mdl-card__supporting-text">
                        <?php if ($selectedCustomer): ?>
                            <h4><?=h($selectedCustomer['customerName'])?></h4>
                            <p><strong>Email:</strong> <?=h($selectedCustomer['email'])?></p>
                            <p><strong>University:</strong> <?=h($selectedCustomer['university'])?></p>
                            <p><strong>Address:</strong> <?=h($selectedCustomer['address'])?></p>
                            <p><strong>Phone:</strong> <?=h($selectedCustomer['phone'])?></p>
                        <?php else: ?>
                            <h4>No customer selected</h4>
                        <?php endif; ?>
     
                                                                                                                                                                           
                    </div>    
                  </div>  <!-- / mdl-cell + mdl-card -->   

                  <!-- mdl-cell + mdl-card -->
                  <div class="mdl-cell mdl-cell--12-col card-lesson mdl-card  mdl-shadow--2dp">
                    <div class="mdl-card__title mdl-color--deep-purple mdl-color-text--white">
                      <h2 class="mdl-card__title-text">Order Details</h2>
                    </div>
                    <div class="mdl-card__supporting-text">       
                               
                                                                      

                               <table class="mdl-data-table  mdl-shadow--2dp">
                              <thead>
                                <tr>
                                  <th class="mdl-data-table__cell--non-numeric">Cover</th>
                                  <th class="mdl-data-table__cell--non-numeric">ISBN</th>
                                  <th class="mdl-data-table__cell--non-numeric">Title</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php if (empty($selectedOrders)): ?>
                                    <tr>
                                        <td class="mdl-data-table__cell--non-numeric" colspan="3">No orders found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($selectedOrders as $order): ?>
                                        <tr>
                                            <td class="mdl-data-table__cell--non-numeric">
                                                <img src="<?=h($order['coverUrl'])?>" alt="cover" class="book-cover" onerror="this.style.display='none'">
                                            </td>
                                            <td class="mdl-data-table__cell--non-numeric"><?=h($order['isbn'])?></td>
                                            <td class="mdl-data-table__cell--non-numeric order-title"><?=h($order['title'])?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                    
                              </tbody>
                            </table>
       

                        </div>    
                   </div>  <!-- / mdl-cell + mdl-card -->             


               </div>   
           
           
            </div>  <!-- / mdl-grid -->    

            <footer class="page-footer">
                CISC3003 Web Programming: <?=h($studentId)?> <?=h($fullName)?> 2026
            </footer>
        </section>
    </main>    
</div>    <!-- / mdl-layout --> 
</body>
</html>