<?php

session_start();
require_once('config.php');

require_once($config['templatesDirectory'].'header.html');


require_once($config['templatesDirectory'].'form.html');

if(array_key_exists('errors', $_SESSION) && $_SESSION['errors'])
{
    if($_SESSION['errors']['status']=='error')
    {
        $messagesError = $_SESSION['errors']['messages'];
        foreach($messagesError as $key =>$value){
            echo "<span class=\"label label-warning\">{$value}</span> ";
        }
        unset($_SESSION['errors']);
    }
}


require_once('showRecord.php');


require_once($config['templatesDirectory'].'footer.html');