<?php
function getOrderList() {
    //next example will recieve all messages for specific conversation
    $service_url = getenv("SHOPIFY_URL").'orders.json';
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
    $service_url = getenv("SHOPIFY_URL").'products/' . $product_id . '.json';
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

function intcmp($a,$b) {
    if((int)$a == (int)$b)return 0;
    if((int)$a  < (int)$b)return 1;
    if((int)$a  > (int)$b)return -1;
}
function cmp($a, $b)
{
    return intcmp($a->number, $b->number);
}
?>