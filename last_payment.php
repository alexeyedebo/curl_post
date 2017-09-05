<?php

$host = 'localhost';
$user = 'usr';
$password = 'pass';


$databases = [
    ['id' => 1, 'db_name' => 'db', 'site_name' => 'sitename', 'url' => 'http://bla-bla-bla',],
];

$data = [];

foreach ($databases as $v) {
    $link = mysqli_connect($host, $user, $password, $v['db_name']) or die("Ошибка " . mysqli_error($link));

    mysqli_set_charset($link, "utf8");

    $query = "SELECT * FROM publication";

    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));

    mysqli_close($link);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = [
                'site' => $v['site_name'],
                'id' => $row['id'],
                'city' => $row['city'],
            ];
        }
    }

    mysqli_free_result($result);
}

$url = 'https://site';

$content = json_encode($data, JSON_UNESCAPED_UNICODE);

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-type: application/json"]);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

$json_response = curl_exec($curl);

$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ($status != 201) {
    die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
}

curl_close($curl);

$response = json_decode($json_response, true);
