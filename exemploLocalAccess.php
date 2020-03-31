<?php

require 'atualizaip.php';


$atualiza = new atualizaip(-1,-1,'-1');
$acessoLocal = true;
var_dump($atualiza->atualizaIP($acessoLocal));

?>