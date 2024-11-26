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
    if (isset($_POST['id'])){
        // deleta produto de id especificado
        $id = trim($_POST['id']);
        $login = $GLOBALS['login'];
        $consulta_produto_existe = $db_con->prepare("SELECT nome FROM produtos WHERE (id='$id') AND (usuarios_login ='$login')");
        $consulta_produto_existe->execute();
        if ($consulta_produto_existe->rowCount() > 0) {
            $consulta = $db_con->prepare("DELETE FROM produtos WHERE (usuarios_login = '$login') AND (id='$id')");
            $consulta->execute();
            $resposta["sucesso"] = 1;
        }
        else{
            $resposta["sucesso"] = 0;
            $resposta["erro"] = "usuario nao criou produto ou id nao encontrado";
            $resposta["cod_erro"] = 4;
        }
    }
    else{
        // faltam parametros
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