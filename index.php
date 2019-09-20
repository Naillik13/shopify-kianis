
<?php
    //next example will recieve all messages for specific conversation
    $service_url = 'https://399a4c49d653393c2b2f1390499bcb1e:9022981471e71455d8776ce94d80dbc2@kianis-shop.myshopify.com/admin/api/2019-07/orders.json';
    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($curl);
    if ($curl_response === false) {
        $info = curl_getinfo($curl);
        curl_close($curl);
        die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }
    curl_close($curl);
    $decoded = json_decode($curl_response,true);
    if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
        die('error occured: ' . $decoded->response->errormessage);
    }
    $best_selling_products
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
    <body class="container mt-3">
        <h2>Order list</h2>
        <ul class="list-group">
            <?php foreach ($decoded as &$order) : ?>
                <li class="list-group-item">Order n°<?php echo $order[0]['id'] ?></li>
            <?php endforeach ?>
        </ul>
    </body>
</html>
