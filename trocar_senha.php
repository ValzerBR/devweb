<?php
 
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
 
/*
 * 
 * Códigos de erro:
 * 0 : falha de autenticação
 * 1 : usuário já existe
 * 2 : falha banco de dados
 * 3 : faltam parametros
 * 4 : entrada não encontrada no BD
 * 
 */

require_once('conexao_db.php');
require_once('autenticacao.php');

// array de resposta
$resposta = array();
 
// verifica autenticação do usuário

if(autenticar($db_con)){
    if (isset($_POST['nova_senha'])) { 
        $nova_senha = trim($_POST['nova_senha']);
        $token = password_hash($nova_senha, PASSWORD_DEFAULT);
        $login = trim($GLOBALS['login']);
        // altera para senha desejada
        $consulta = $db_con->prepare("UPDATE usuarios SET token='$token' WHERE (login='$login')");
        $consulta->execute();
        $resposta["sucesso"] = 1;
        }

    else {
        // se não foram enviados nem email e nem nome para serem trocados, 
        // indicamos que não houve sucesso
        // na operação e o motivo no campo de erro.
        $resposta["sucesso"] = 0;
        $resposta["erro"] = "faltam parametros";
        $resposta["cod_erro"] = 3;
    }
}

else {
	// senha ou usuario nao confere
	$resposta["sucesso"] = 0;
	$resposta["erro"] = "usuario ou senha não confere";
	$resposta["cod_erro"] = 0;
}
// A conexão com o bd sempre tem que ser fechada
$db_con = null;

// converte o array de resposta em uma string no formato JSON e 
// imprime na tela.
echo json_encode($resposta);
?>