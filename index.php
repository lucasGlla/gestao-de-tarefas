<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/css/style.css">
    <title>Gerenciamento de tarefas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <Header>
        <nav>
            <h1 class="logo">Sistema de tarefas</h1>
        </nav>
    </Header>

    <span id="msgAlerta" class="alert-success"></span>
    <div class="container">
        <span class="listar-tarefas content-table"></span>
        <!--Modal de atualizar-->
        <dialog id="modal-2">
                <form id="editTarefa-form">
                    <div class="modal-header">
                        <h1 class="model-title">
                            Atualizar usuario
                        </h1>
                        <button class="close-modal" data-modal="modal-2" type="button">
                            x
                        </button>
                    </div>

                    <div class="modal-body">
                        <span id="msgAlertaErro" class="alert-error"></span>
                        <div class="input-group">
                            <input type="hidden" name="id" id="editIdTarefa" class="inputUser">
                        </div>
                        <div class="input-group">
                            <input placeholder="Nome" type="text" name="nome" id="editNomeTarefa" class="inputUser" required>
                        </div>
                        <div class="input-group">
                            <input placeholder="Custo" type="number" name="custo" id="editCustoTarefa" class="inputUser" min="0" step="0.01" oninput="limitarCasasDecimais(this)" required>
                        </div>
                        <div class="input-group">
                            <input type="date" name="dataLimite" id="editDataLimiteTarefa" class="inputUser" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
            
                        <input type="submit" name="submit" id="editSubmit">
                    </div>
                </form>
            </dialog>
            <!--Fim do modal de atualizar -->
   
    </div>
    <button id="btn" class="open-modal" data-modal="modal-1">
                Incluir
            </button>
            <!--Modal de cadastro-->
            <dialog id="modal-1">
                <form id="tarefa-form">
                    <div class="modal-header">
                        <h1 class="model-title">
                            Incluir tarefa
                        </h1>
                        <button class="close-modal" data-modal="modal-1" type="button">
                            x
                        </button>
                    </div>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="error">
                        </div>
                    <?php endif; ?>
                    <div class="modal-body">
                        <span id="msgAlertaErro" class="alert-error"></span>
                        <div class="input-group">
                            <input placeholder="Nome" type="text" name="nome" id="nome" class="inputUser" required>
                        </div>
                        <div class="input-group">
                            <input placeholder="Custo" type="number" name="custo" id="custo" class="inputUser" min="0" step="0.01" oninput="limitarCasasDecimais(this)" required>
                        </div>
                        <div class="input-group">
                            <input type="date" name="dataLimite" id="dataLimite" class="inputUser" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <input type="submit" name="submit" id="submit">
                    </div>
                </form>
            </dialog>
            <!--Fim do modal de cadastro -->

    <script src="./src/js/script.js"></script>
    <script>
        function limitarCasasDecimais(input) {
            input.value = parseFloat(input.value).toFixed(2);
        }

        
    </script>
  
</body>
</html>