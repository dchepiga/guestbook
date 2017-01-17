<?php
session_start();
include_once('config.php');
$errors = [
    'status' => 'error',
    'messages' => [
        'username' => '',
        'message' => '',
        'database' => ''
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
        $errors['messages']['username'] = 'Поле "Имя пользователя" пустое!';
    }
    if (!$message) {
        $errors['messages']['message'] = 'Поле "Сообщение" пустое!';

    }

    if (($username) && ($message)) {

        $stopList = $config['stopList'];
        $stopWordReplacement = $config['stopWordReplacement'];

        $messageSchema = [
            'username' => $username,
            'message' => censor($message, $stopList, $stopWordReplacement),
            'created_at' => date('Y-m-d H:i:s')
        ];
        if (file_exists($config['database'])) {

            $database = file_get_contents($config['database']);

            if (empty($database)) {
                array_push($messageStorage, $messageSchema);
                $serializedStorage = serialize($messageStorage);

                if (file_put_contents($config['database'], $serializedStorage)) {
                    $errors['status'] = 'Ok';
                    redirect($errors);

                } else {
                    $errors['messages']['database'] = 'Ошибка записи в базу данных.';
                    redirect($errors);
                }
            } else {
                //что делаем если в файле чтото есть
                $deserializedStorage = unserialize($database);
                if (!empty($deserializedStorage)) {
                    array_push($deserializedStorage, $messageSchema);
                    $serializedStorage = serialize($deserializedStorage);
                    if (file_put_contents($config['database'], $serializedStorage)) {
                        $errors['status'] = 'Ok';

                        redirect($errors);
                    } else {
                        $errors['messages']['database'] = 'Ошибка записи в базу данных.';

                        redirect($errors);
                    }
                }
            }

        } else {
            $errors['messages']['database'] = 'База данных недоступна.';
            redirect($errors);
        }

    } else {
        redirect($errors);
    }
}

function redirect($errors = null)
{
    $_SESSION['errors'] = $errors;

    $uri = $_SERVER['HTTP_ORIGIN'] . dirname($_SERVER['PHP_SELF']);
    header('Location: ' . $uri);
}

function censor($message, $stopList, $stopWordReplacement)
{
    $message = explode(' ', $message);
    foreach ($message as &$word) {
        if (in_array(strtolower($word), $stopList)) {
            $word = $stopWordReplacement;
        }
    }
    $message = implode(' ', $message);

    return $message;

}
