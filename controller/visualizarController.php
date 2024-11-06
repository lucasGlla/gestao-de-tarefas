<?php
include_once "../config/conexao.php";

// Define o cabeçalho para o retorno como JSON
header('Content-Type: application/json');

// Obtém o parâmetro 'id' da URL e sanitiza como número inteiro
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id && filter_var($id, FILTER_VALIDATE_INT)) {
    // Prepara a consulta usando mysqli
    $query_tarefas = $conexao->prepare("SELECT * FROM tarefas WHERE id = ?");
    
    if ($query_tarefas) {
        $query_tarefas->bind_param('i', $id);
        $query_tarefas->execute();
        
        $result_tarefas = $query_tarefas->get_result();
        
        if ($result_tarefas && $result_tarefas->num_rows > 0) {
            $row_tarefas = $result_tarefas->fetch_assoc();
            $retorna = ['status' => true, 'dados' => $row_tarefas];
        } else {
            $retorna = [
                'status' => false,
                'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nenhuma tarefa encontrada!</div>"
            ];
        }
    } else {
        $retorna = [
            'status' => false,
            'msg' => "<div class='alert alert-danger' role='alert'>Erro: Falha na preparação da consulta!</div>"
        ];
    }
} else {
    $retorna = [
        'status' => false,
        'msg' => "<div class='alert alert-danger' role='alert'>Erro: ID inválido!</div>"
    ];
}

// Retorna o JSON
echo json_encode($retorna);
?>
