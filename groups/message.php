<?php

$group      = isset($_POST['group'])    ? $_POST['group']   : '';
$id         = 1;
$ip         = $_SERVER['REMOTE_ADDR'];
$name       = isset($_POST['name'])  ? $_POST['name'] : '';
$content    = isset($_POST['content'])  ? $_POST['content'] : '';

$group = strlen($group) !== 0 ? $group : 'default';

if (!preg_match('/^[a-z]{4,20}$/', $group)) {
    echo json_encode(array(
        'error' => '[1] Invalid group name'
    ));

    exit;
}

if (strlen($content) === 0) {
    echo json_encode(array(
        'error' => '[2] No content'
    ));

    exit;
}

if (strlen($name) === 0) {
    $name = 'unknow';
}

$filename = "$group.group";

if (file_exists($filename)) {
    $fileContent = file($filename);

    $lastTime = (int) $fileContent[1];

    if (time() - $lastTime < 2) {
        echo json_encode(array(
            'error' => '[3] It\'s needed to wait 2 seconds after of last message of this group'
        ));

        exit;
    }

    $id = (int) $fileContent[0] + 1;
}

$fileContent = array();

$fileContent[0] = $id;
$fileContent[1] = time();
$fileContent[2] = $ip;
$fileContent[3] = $name;
$fileContent[4] = preg_replace('/\r\n/', '\n', preg_replace('/\\\/', '\\\\\\', $content));

$fileContent = implode("\n", $fileContent);

echo $fileContent;

$file = fopen($filename, 'w');
fwrite($file, $fileContent);
fclose($file);

?>
