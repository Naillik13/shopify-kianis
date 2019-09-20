
<?php

    function getOrderList() {
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
        return $decoded['orders'];
    }

    function getProductDetails($product_id) {
        //next example will recieve all messages for specific conversation
        $service_url = 'https://399a4c49d653393c2b2f1390499bcb1e:9022981471e71455d8776ce94d80dbc2@kianis-shop.myshopify.com/admin/api/2019-07/products/' . $product_id . '.json';
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
        return $decoded['product'];
    }
    class product {
        public $id = 0;
        public $number = 0;
    }
    function intcmp($a,$b) {
        if((int)$a == (int)$b)return 0;
        if((int)$a  < (int)$b)return 1;
        if((int)$a  > (int)$b)return -1;
    }
    function cmp($a, $b)
    {
        return intcmp($a->number, $b->number);
    }

    $orders = array_slice(getOrderList(),0,10);
    $products = [];
    foreach ($orders as &$order) :
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
    usort($products, "cmp");
    $products = array_slice($products, 0, 5)

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
    <body class="container mt-3">
        <div>
            <h2>Order list</h2>
            <table style="width:100%">
                <tr>
                    <th>Id</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Email</th>
                </tr>
                <?php foreach ($orders as $order) : ?>
                    <tr>
                        <td><?php echo $order['id'] ?></td>
                        <td><?php echo $order['total_price'] ?></td>
                        <td><?php echo $order['created_at'] ?></td>
                        <td><?php echo $order['email'] ?></td>
                    </tr>
                <?php endforeach ?>
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
    </body>
</html>
