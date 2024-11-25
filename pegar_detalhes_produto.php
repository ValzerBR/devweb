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

// array de resposta
$resposta = array();
 
// verifica se todos os campos necessários foram enviados ao servidor
// adicionado os novos parametros de nome e email para fazer a verificação dos dados 
if (isset($_GET['id'])) {
 
    // o método trim elimina caracteres especiais/ocultos da string
	$id = trim($_GET['id']);
    // consulta produto de id especificado
    $consulta_produto = $db_con->prepare("SELECT nome, preco, descricao, usuarios_login, criado_em, img FROM produtos WHERE id='$id'");
	$consulta_produto->execute();
	if ($consulta_produto->rowCount() > 0) {
        $resposta["sucesso"] = 1;
        $dados_produto = $consulta_produto->fetch(PDO::FETCH_ASSOC);
        $resposta["produto"] = $dados_produto;
    }
    else{
        $resposta["sucesso"] = 0;
        $resposta["erro"] = "produto nao encontrado";
        $resposta["cod_erro"] = 4;
    }
}
else {
	// se não foram enviados todos os parâmetros para o servidor, 
	// indicamos que não houve sucesso
	// na operação e o motivo no campo de erro.
    $resposta["sucesso"] = 0;
	$resposta["erro"] = "faltam parametros";
	$resposta["cod_erro"] = 3;
}

// A conexão com o bd sempre tem que ser fechada
$db_con = null;

// converte o array de resposta em uma string no formato JSON e 
// imprime na tela.
echo json_encode($resposta);
?>