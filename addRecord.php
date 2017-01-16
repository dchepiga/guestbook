<?php
include_once('config.php');
$errors = [
    'status' => 'error',
    'messages' => [
        'username' => '',
        'message' => ''
    ]
];

$messageSchema = [
    'created_at' => '',
    'message' => '',
    'username' => ''

];

$messageStorage = [];


if (!empty($_POST)) {
    $username = (!empty($_POST['username'])) ? trim($_POST['username']) : null;
    $message = (!empty($_POST['message'])) ? trim($_POST['message']) : null;

    if (!$username) {
        $errors['messages']['username'] = 'Поле пустое!';

    }
    if (!$message) {
        $errors['messages']['message'] = 'Поле пустое!';

    }


    if (($username) && ($message)) {

        $stopList = $config['stopList'];
        $stopWordReplacement = $config['stopWordReplacement'];

        $messageSchema = [
            'username' => $username,
            'message' => censor($message,$stopList,$stopWordReplacement),
            'created_at' => date('Y-m-d H:i:s')
        ];
        if (file_exists($config['database'])) {
            $error['status'] = 'Ok';

            $database = file_get_contents($config['database']);

            if (empty($database)) {
                array_push($messageStorage, $messageSchema);
                $serializedStorage = serialize($messageStorage);

                if (file_put_contents($config['database'], $serializedStorage)) {
                }
            } else {
                //что делаем если в файле чтото есть
                $deserializedStorage = unserialize($database);
                if (!empty($deserializedStorage)) {
                    array_push($deserializedStorage, $messageSchema);
//                    var_dump($deserializedStorage);
                    $serializedStorage = serialize($deserializedStorage);
                    if (file_put_contents($config['database'], $serializedStorage)) {
                        die('file saved');
                    }
                }
            }

        }
    }

    function censor($message,$stopList,$stopWordReplacement){
        foreach($message as $word){

        }

    }
}
