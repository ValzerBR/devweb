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
    if (isset($_POST['id']) && ((isset($_POST['novo_nome'])) or (isset($_POST['novo_preco'])) or (isset($_POST['nova_descricao'])) or (isset($_FILES['nova_img'])))) {
        $id = trim($_POST['id']);
        $login = $GLOBALS['login'];
        $consulta_produto_existe = $db_con->prepare("SELECT nome FROM produtos WHERE (id='$id') AND (usuarios_login ='$login')");
        $consulta_produto_existe->execute();
        if ($consulta_produto_existe->rowCount() > 0) {
            if (isset($_POST['novo_nome'])) { 
                //usuario altera nome 
                $novo_nome = trim($_POST['novo_nome']);
                $consulta = $db_con->prepare("UPDATE produtos SET nome='$novo_nome' WHERE (id='$id')");
                $consulta->execute();
            }
            if (isset($_POST['novo_preco'])) { 
                //usuario deseja alterar preco 
                $novo_preco = trim($_POST['novo_preco']);
                $consulta = $db_con->prepare("UPDATE produtos SET preco ='$novo_preco' WHERE (id='$id')");
                $consulta->execute();
            }
            if (isset($_POST['nova_descricao'])) { 
                //usuario altera descricao
                $nova_descricao = $_POST['nova_descricao']; //email permite caracteres especiais
                $consulta = $db_con->prepare("UPDATE produtos SET descricao='$nova_descricao' WHERE (id='$id')");
                $consulta->execute();
            }
            if (isset($_FILES['nova_img'])) { 
                //usuario altera img
                $filename = $_FILES['nova_img']['tmp_name'];
                $client_id="ce5d3a656e2aa51";
                
                $curl = curl_init();
                
                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.imgur.com/3/image',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('image'=> new CURLFILE($filename),'type' => 'file','title' => 'Simple upload','description' => 'This is a simple image upload in Imgur'),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Client-ID ' . $client_id
                ),
                ));

                $imgur_response = curl_exec($curl);
                $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                if($http_code == 200) {
                    
                    $imgur_response_json = json_decode($imgur_response, true);
                    $img_url=$imgur_response_json['data']['link'];
                    
                    // A proxima linha insere um novo produto no BD.
                    // A variavel consulta indica se a insercao foi feita corretamente ou nao.
                    $consulta = $db_con->prepare("UPDATE produtos SET img = '$img_url' WHERE (id='$id')");
                    if ($consulta->execute()) {
                        // Se o produto foi inserido corretamente no servidor, o cliente 
                        // recebe a chave "sucesso" com valor 1
                        $resposta["sucesso"] = 1;
                    } else {
                        // Se o produto nao foi inserido corretamente no servidor, o cliente 
                        // recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
                        // motivo da falha.
                        $resposta["sucesso"] = 0;
                        $resposta["erro"] = "Erro ao criar produto no BD: " . $consulta->error;
                        $resposta["cod_erro"] = 2;
                    }
                }
                else {
                    // Se o envio da imagem para o IMGUR não funcionou, o cliente 
                    // recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
                    // motivo da falha.
                    $resposta["sucesso"] = 0;
                    $resposta["erro"] = "Erro ao enviar a imagem para o IMGUR. HTTP CODE: " . $http_code;
                    $resposta["cod_erro"] = 2;
                }
            }
            $resposta["sucesso"] = 1;
        }
        else{
            $resposta["sucesso"] = 0;
            $resposta["erro"] = "usuario nao criou produto ou id nao encontrado";
            $resposta["cod_erro"] = 3;
        }
    }

    else {
        // se não foram enviados nada para serem trocados, 
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