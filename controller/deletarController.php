<?php
include_once('../config/conexao.php');

// Sanitiza o ID recebido via GET
$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

try {
    if (!empty($id) && $id > 0) {
        // Prepara a consulta de seleção para verificar se o ID existe
        $sqlSelect = "SELECT * FROM tarefas WHERE id = ?";
        $stmtSelect = $conexao->prepare($sqlSelect);
        if ($stmtSelect === false) {
            throw new Exception("Erro ao preparar a consulta de seleção.");
        }
        $stmtSelect->bind_param("i", $id);
        $stmtSelect->execute();
        $result = $stmtSelect->get_result();
        
        if ($result->num_rows > 0) {
            // Prepara e executa a exclusão do registro
            $sqlDelete = "DELETE FROM tarefas WHERE id = ?";
            $stmtDelete = $conexao->prepare($sqlDelete);
            if ($stmtDelete === false) {
                throw new Exception("Erro ao preparar a consulta de exclusão.");
            }
            $stmtDelete->bind_param("i", $id);
            
            if ($stmtDelete->execute()) {
                $retorna = [
                    'status' => true,
                    'msg' => "<div class='alert alert-success' role='alert'>Tarefa apagada com sucesso!</div>"
                ];
            } else {
                throw new Exception("Erro ao executar a exclusão da tarefa.");
            }
            $stmtDelete->close();
        } else {
            $retorna = [
                'status' => false,
                'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nenhuma Tarefa encontrada!</div>"
            ];
        }
        $stmtSelect->close();
    } else {
        $retorna = [
            'status' => false,
            'msg' => "<div class='alert alert-danger' role='alert'>Erro: ID inválido!</div>"
        ];
    }
} catch (Exception $e) {
    $retorna = [
        'status' => false,
        'msg' => "<div class='alert alert-danger' role='alert'>Erro: " . $e->getMessage() . "</div>"
    ];
}

// Define o tipo de conteúdo da resposta como JSON e envia a resposta
header('Location:../index.php');
echo json_encode($retorna);
?>
