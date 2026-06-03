<?php

function pre($dados, $linha=false, $morrer=false){
    echo $linha ? "<hr><hr><pre>" : "<br><pre>";  
    echo var_dump($dados);
    echo "</pre>";
    if($morrer == true){
        die();
    }
}


function criar_conexao(){
    
    require_once "../../credenciais_secretas/credenciais_praias_portugal_bd.php";   
    
    $dsn        = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
    $opcoes     = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ];

    try {
        $conexao = new PDO($dsn, $username, $password, $opcoes);
    } catch(PDOException $e) {
        echo "A conexão falhou: " . $e->getMessage();
    }

    return $conexao;
}
?>