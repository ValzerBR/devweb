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
    if (isset($_POST['id']) && ((isset($_POST['novo_nome'])) or (isset($_POST['novo_preco'])) or (isset($_POST['nova_descricao'])) or (isset($_POST['nova_img'])))) {
        $id = trim($_POST['id']);
        if (isset($_POST['novo_nome'])) { 
            //usuario altera nome e email
            $novo_nome = trim($_POST['novo_nome']);
            $consulta = $db_con->prepare("UPDATE produtos SET nome='$novo_nome' WHERE (id='$id')");
            $consulta->execute();
            $resposta["sucesso"] = 1;
            $resposta["erro"] = "nome do produto atualizado";
        }
        if (isset($_POST['novo_preco'])) { 
            //usuario deseja alterar apenas nome 
            $novo_preco = trim($_POST['novo_preco']);
            $consulta = $db_con->prepare("UPDATE produtos SET preco ='$novo_preco' WHERE (id='$id')");
            $consulta->execute();
            $resposta["sucesso"] = 1;
            $resposta["erro"] = "preco atualizado";
        }
        if (isset($_POST['nova_descricao'])) { 
            //usuario altera email
            $nova_descricao = $_POST['nova_descricao']; //email permite caracteres especiais
            $consulta = $db_con->prepare("UPDATE produtos SET nome='$nova_descricao' WHERE (id='$id')");
            $consulta->execute();
            $resposta["sucesso"] = 1;
            $resposta["erro"] = "descricao atualizado";
        }
        if (isset($_POST['nova_img'])) { 
            //usuario altera email
            $nova_img = $_POST['nova_img']; //email permite caracteres especiais
            $consulta = $db_con->prepare("UPDATE produtos SET nova_img='$nova_img' WHERE (id='$id')");
            $consulta->execute();
            $resposta["sucesso"] = 1;
            $resposta["erro"] = "img atualizado";
        }
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