<?php

$messageTemplate = file_get_contents($config['templatesDirectory'] . 'message.html');
$paginationTemplate = file_get_contents($config['templatesDirectory'] . 'pagination.html');

$database = file_get_contents($config['database']);
$messagesPerPage = $config['messagesCount'];


if (!empty($database)) {
    $deserializedMessages = unserialize($database);
    $deserializedMessages = array_reverse($deserializedMessages);

    foreach ($deserializedMessages as $value) {
        $from = ["%date%", "%username%", "%message%"];
        $to = [$value['created_at'], $value['username'], $value['message']];
        $messages[] = str_replace($from, $to, $messageTemplate);
    }

    $page = empty($_GET['page'])? 1: $_GET['page'];
    $messagesCount = count($deserializedMessages);

    showMessagesPerPage($page,$messagesPerPage, $messagesCount,$messages);

    showPagination($messagesPerPage, $messagesCount, $paginationTemplate);

}

function showMessagesPerPage($page,$messagesPerPage, $messagesCount,$messages)
{
    $from = ($page -1) * $messagesPerPage;
    $to = min(($page*$messagesPerPage)-1,$messagesCount);
    $array = array_slice($messages,$from,($to-$from)+1);

    foreach($array as $value)
    {
        echo $value;
    }

}

function showPagination($messagesPerPage, $messagesCount, $paginationTemplate)
{

    $numberOfPages = ($messagesCount <= $messagesPerPage) ? 1 : ceil($messagesCount / $messagesPerPage);
    $str = '';

    for ($i = 1; $i <= $numberOfPages; $i++) {
        $str .= '<li><a href="?page=' . $i . '">' . $i . '</a></li>';
    }
    echo str_replace("%pages%", $str, $paginationTemplate);

}