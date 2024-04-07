document.addEventListener("DOMContentLoaded", function() {
    const select1 = document.getElementById('select1');
    const select2 = document.getElementById('select2');
    let opcoesOriginais = [];

    // Função para carregar os dados nos selects
    function carregarDados() {
        fetch('../private/buscarRotas.php')
        .then(response => response.json())
        .then(data => {
            opcoesOriginais = data;
            preencherSelect(select1, data);
            preencherSelect(select2, data);
        })
        .catch(error => console.error('Error:', error));
    }

    // Função para preencher os selects
    function preencherSelect(select, data, valorExcluir='') {
        select.innerHTML = '';
        select.add(new Option('Selecione', '', valorExcluir === ''));
        data.forEach(opcao => {
            if (opcao !== valorExcluir) {
                let option = new Option(opcao, opcao);
                select.add(option);
            }
        });
    }

    // Função para atualizar os selects baseados na seleção do outro
    function atualizarSelects(selectOrigem, selectDestino) {
        const valorSelecionadoOrigem = selectOrigem.value;
        const valorSelecionadoDestino = selectDestino.value;

        preencherSelect(selectDestino, opcoesOriginais, valorSelecionadoOrigem);
        if (valorSelecionadoDestino === valorSelecionadoOrigem) {
            selectDestino.value = '';
        } else {
            selectDestino.value = valorSelecionadoDestino;
        }
    }

    select1.addEventListener('change', () => atualizarSelects(select1, select2));
    select2.addEventListener('change', () => atualizarSelects(select2, select1));

    // Associar evento de clique ao botão "Comprar Rota"
    document.getElementById('comprarRota').addEventListener('click', function() {
        comprar(select1.value, select2.value);
    });

    carregarDados();
});

function comprar(select1, select2) {
    const modalMessage = "Deseja comprar a rota de '" + select1 + "' para '" + select2 + "' pelo valor de R$ 2000000?";
    document.getElementById("modalMessage").innerText = modalMessage;

    // Mostrar o modal de confirmação com Bootstrap 5
    var confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    confirmationModal.show();

    document.getElementById("confirmButton").addEventListener('click', function() {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../private/comprarRotasSalvar.php", true); // Ajuste o endpoint conforme necessário
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById("successMessage").innerText = xhr.responseText;
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            } else if (xhr.status === 400) {
                document.getElementById("errorMessage").innerText = xhr.responseText;
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            } else {
                document.getElementById("errorMessage").innerText = "Erro ao comprar a rota: " + xhr.statusText;
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            }
        };       
        xhr.onerror = function() {
            document.getElementById("errorMessage").innerText = "Erro ao comprar a rota: " + xhr.statusText;
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        };
        xhr.send("select1=" + encodeURIComponent(select1) + "&select2=" + encodeURIComponent(select2));
        
        console.log("Origem:", select1, "Destino:", select2);
    });
}
