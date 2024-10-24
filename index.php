<?php
require_once('conexao.php');

$resposta = array();

if ( isset( $_POST['novo_login']) && isset( $_POST['nova_senha']) ){

    $novo_login = trim($_POST['novo_login']);
    $nova_senha = trim($_POST['nova_senha']);

    $consulta_usuario_existe = $db_con->prepare("SELECT login FROM usuarios WHERE login = '$novo_login'");
    $consulta_usuario_existe->execute();

    if($consulta_usuario_existe->rowCount() > 0){
        $resposta["sucesso"] = 0;
        $resposta["msg"] = "usuário já cadastrado";
        $resposta["cod_erro"] = 1;
    }
    else {
        $token = password_hash($nova_senha, PASSWORD_DEFAULT);

        $consulta_insert = $db_con->prepare("INSERT INTO usuarios(login, token) VALUES ('$novo_login', '$token')"); 

        if ($consulta_insert->execute()){
            $resposta["sucesso"] = 1;
            $resposta["msg"] = "cadastrado com sucesso";
        }
        else{
            $resposta["sucesso"] = 0;
            $resposta["msg"] = "erro BD: " . $consulta_insert->errorInfo();
            $resposta["cod_erro"] = 2;
        }
    }
}
else {
    $resposta["sucesso"] = 0;
    $resposta["msg"] = "faltam parametros";
    $resposta["cod_erro"] = 3;
}

$db_con = null;

echo json_encode($resposta);
?>