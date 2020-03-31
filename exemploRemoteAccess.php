<?php

require 'atualizaip.php';


$atualiza = new atualizaip(22,22,'tcp');


$acessoLocal = false;
var_dump($atualiza->atualizaIP($acessoLocal));

?>