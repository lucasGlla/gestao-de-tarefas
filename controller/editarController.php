<?php
include_once "../config/conexao.php";

// Filtra e valida os dados de entrada
$dados = filter_input_array(INPUT_POST, [
    'id' => FILTER_VALIDATE_INT,
    'nome' => FILTER_SANITIZE_STRING,
    'custo' => FILTER_SANITIZE_STRING,
    'dataLimite' => FILTER_SANITIZE_STRING,
]);

// Verifica se os dados necessários estão presentes
if (!$dados['id'] || !$dados['nome'] || !$dados['custo'] || !$dados['dataLimite']) {
    $retorna = [
        'status' => false,
        'msg' => "<div class='alert alert-danger' role='alert'>Erro: Dados de entrada inválidos!</div>"
    ];
    header('Content-Type: application/json');
    echo json_encode($retorna);
    exit;
}

try {
     // Checagem de nome repetido
     $query_check_nome = "SELECT nome FROM tarefas WHERE nome = ? AND id != ?";
     $stmt_check_nome = $conexao->prepare($query_check_nome);
     $stmt_check_nome->bind_param('si', $dados['nome'], $dados['id']);
     $stmt_check_nome->execute();
     $stmt_check_nome->store_result();

    if ($stmt_check_nome->num_rows > 0) {
        echo json_encode([
            'status' => false,
            'msg' => "<div class='alert alert-danger' role='alert'>Erro: Tarefa já cadastrada!</div>"
        ]);
        $stmt_check_nome->close();
        exit;
    }
    $stmt_check_nome->close();

    // Prepara a query para atualizar o ticket
    $query_tarefas = "UPDATE tarefas SET nome=?, custo=?, dataLimite=? WHERE id=?";
    $edit_tarefas = $conexao->prepare($query_tarefas); // Corrigido para usar $query_tarefas
    $edit_tarefas->bind_param(
        'sssi',
        $dados['nome'],
        $dados['custo'],
        $dados['dataLimite'],
        $dados['id']
    );
    $edit_tarefas->execute();

    // Verifica o resultado da operação
    if ($edit_tarefas->affected_rows > 0) {
        $retorna = [
            'status' => true,
            'msg' => "<div class='alert alert-success' role='alert'>Tarefa alterada com sucesso!</div>"
        ];
    } else {
        $retorna = [
            'status' => false,
            'msg' => "<div class='alert alert-danger' role='alert'>Erro: Não foi possível alterar a tarefa!</div>"
        ];
    }
} catch (Exception $e) {
    // Captura e exibe qualquer erro
    $retorna = [
        'status' => false,
        'msg' => "<div class='alert alert-danger' role='alert'>Erro: Ocorreu um erro inesperado. Por favor, tente novamente.</div>"
    ];
}

// Define o tipo de conteúdo da resposta como JSON e envia a resposta
header('Content-Type: application/json');
echo json_encode($retorna);
?>
