<?php
    include '../classes/User.php';

    //create an onject
    $user = new User;

    //call the method
    $user->store($_POST);


?>