<?php

include('DotRollApi.php');
$dotRoll = new DotRollApi('aaabbb', 'jelszo', '0123456789abcdef1234', true);
print_r($dotRoll->getDomainAvailablity('dotroll.com'));