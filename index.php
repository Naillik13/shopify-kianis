
<?php
    include ('functions.php');
    include ('product.php');
    include ('country.php');

    $orders = array_slice(getOrderList(),0,10);
    $products = [];
    $country_list = [];
    $total_amount = 0;
    foreach ($orders as &$order) :
        $total_amount += $order['total_price'];
        if (array_key_exists($order['billing_address']['country'], $country_list)) {
            $country_list[$order['billing_address']['country']]->number += 1;
        } else {
            $temp_object  = new country();
            $temp_object->name = $order['billing_address']['country'];
            $temp_object->number = 1;
            $country_list[$order['billing_address']['country']] = $temp_object;
        }
        foreach ($order['line_items'] as &$item) :
            if (array_key_exists($item['product_id'], $products)) {
                $products[$item['product_id']]->number += 1;
            } else {
                $temp_object  = new product();
                $temp_object->id = $item['product_id'];
                $temp_object->number = 1;
                $products[$item['product_id']] = $temp_object;
            }
        endforeach;
    endforeach;
    $average_basket = round($total_amount / count($orders), 2);
    usort($products, "cmp");
    $products = array_slice($products, 0, 5)

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
    <body class="container mt-3">
        <div>
            <h2>Order by country</h2>
            <table id="orderTable" style="width:100%">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order) : ?>
                    <tr>
                        <td><?php echo $order['id'] ?></td>
                        <td><?php echo $order['total_price'] ?> €</td>
                        <td><?php echo $order['created_at'] ?></td>
                        <td><?php echo $order['email'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
            </table>
        </div>
        <div class="mt-5">
            <h2>Five best</h2>
            <ul class="list-group">
                <?php foreach ($products as $product) : ?>
                    <?php $product_details = getProductDetails($product->id); ?>
                    <li class="list-group-item"><?php echo $product_details['title'] . ' x ' . $product->number?></li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="mt-5">
            <h2>Average Basket</h2>
            <?php echo $average_basket; ?> €
        </div>
        <div class="mt-5">
            <h2>Country list</h2>
            <table style="width:100%" class="table d-none" id="dataTable"> 
                <thead>
                    <th>Country</th>
                    <th>Order number</th>
                </thead>
                <tbody>
                    <?php foreach ($country_list as $country) : ?>
                        <tr>
                            <th><?php echo $country->name ?></th>
                            <td><?php echo $country->number ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
            <div class="chart">
                <canvas id="myChart"></canvas>
            </div>
        </div>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
        <script>
            $(document).ready( function () {
                $('#orderTable').DataTable();
            } );
            function BuildChart(labels, values, chartTitle) {
                var ctx = document.getElementById("myChart").getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                    labels: labels, // Our labels
                    datasets: [{
                        label: chartTitle, // Name the series
                        data: values, // Our values
                        backgroundColor: [ // Specify custom colors
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [ // Add custom color borders
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1 // Specify bar border width
                    }]
                    },
                    options: {
                        responsive: true, // Instruct chart js to respond nicely.
                        maintainAspectRatio: false, // Add to prevent default behavior of full-width/height 
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    stepSize: 1
                                }
                            }]
                        },
                        legend: {
                            display: false
                        }
                    }
                });
                return myChart;
            }
            var table = document.getElementById('dataTable');
            var json = []; // First row needs to be headers 
            var headers =[];
            for (var i = 0; i < table.rows[0].cells.length; i++) {
            headers[i] = table.rows[0].cells[i].innerHTML.toLowerCase().replace(/ /gi, '');
            }

            // Go through cells 
            for (var i = 1; i < table.rows.length; i++) {
                var tableRow = table.rows[i];
                var rowData = {};
                for (var j = 0; j < tableRow.cells.length; j++) {
                    rowData[headers[j]] = tableRow.cells[j].innerHTML;
                }

                json.push(rowData);
            }

            console.log(json);

            var labels = json.map(function (e) {
                return e.country;
            });
            console.log(labels); // ["2016", "2017", "2018", "2019"]

            // Map JSON values back to values array
            var values = json.map(function (e) {
            return e.ordernumber;
            });
            console.log(values);
            var chart = BuildChart(labels, values, "");

        </script>
    </body>
</html>
