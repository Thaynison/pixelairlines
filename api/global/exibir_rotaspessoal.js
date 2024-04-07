document.addEventListener('DOMContentLoaded', function() {
    // Armazena os aviões globalmente para uso posterior
    let avioesGlobal = [];
    let rotasGlovbal = [];

    // Primeiro, busca os aviões do usuário
    fetch('../private/buscar_aviao.php')
    .then(response => response.json())
    .then(avioes => {
        avioesGlobal = avioes; // Armazena os aviões para uso posterior
        fetchRotas(); // Agora, busca as rotas
    })
    .catch(error => console.log('Erro ao buscar aviões: ', error));

    fetch('../private/buscar_aviaoemrota.php')
    .then(response => response.json())
    .then(em_rotas => {
        rotasGlovbal = em_rotas; // Armazena os aviões para uso posterior
        fetchRotas(); // Agora, busca as rotas
    })
    .catch(error => console.log('Erro ao buscar aviões: ', error));

    // Função para buscar e exibir as rotas do usuário
    function fetchRotas() {
        fetch('../private/exibir_rotaspessoal.php')
        .then(response => response.json())
        .then(avioesDoUsuario => {
            const container = document.querySelector('.row'); // Ajuste o seletor conforme necessário
            container.innerHTML = ''; // Limpa o container antes de adicionar novos elementos
            avioesDoUsuario.forEach(rotas => {
                // Cria elementos HTML para cada rota do usuário
                const col = document.createElement('div');
                col.className = 'col';
                const selectHTML = buildSelectAvioes(avioesGlobal, rotasGlovbal);
                col.innerHTML = `
                    <div class="card mb-4 rounded-3 shadow-sm">
                        <div class="card-header py-3">
                            <h4 class="my-0 fw-normal">${rotas.pontoA} / ${rotas.pontoB}</h4>
                        </div>
                        <div class="card-body">
                            <img src="https://thumbs.dreamstime.com/b/airplane-line-path-vector-icon-air-plane-flight-route-start-point-dash-trace-illustration-188303844.jpg" alt="Imagem de rota" style="width: 100%; height: 100px; object-fit: contain;">
                            <h1 class="card-title pricing-card-title">R$ ${rotas.pagar.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}</h1>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>De: ${rotas.pontoA}</li>
                                <li>Para: ${rotas.pontoB}</li>
                                <li>Tempo: ${rotas.tempo}</li>
                                ${selectHTML}
                            </ul>
                            <button id="despachar-${rotas.rota_id}" type="button" class="w-100 btn btn-lg btn-outline-primary" value="${rotas.pagar}">Despachar</button>
                        </div>
                    </div>
                `;
                container.appendChild(col);

                // Adiciona evento de clique para cada botão de despachar
                // Adiciona evento de clique para cada botão de despachar
                document.getElementById(`despachar-${rotas.rota_id}`).addEventListener('click', function() {
                    // Captura o select de aviões mais próximo do botão "Despachar" clicado
                    const selectAviao = this.closest('.card-body').querySelector('.form-select');
                    const aviaoIdSelecionado = selectAviao.value; // Id do avião selecionado
                    // Encontra o avião selecionado com base no aviaoIdSelecionado
                    const aviaoSelecionado = avioesGlobal.find(aviao => aviao.aviao_id === aviaoIdSelecionado);
                    if(aviaoSelecionado) {
                        DespacharRota(rotas.rota_id, rotas.pontoA, rotas.pontoB, rotas.tempo, rotas.pagar, aviaoSelecionado.aviao_id, aviaoSelecionado.modelo, aviaoSelecionado.capacidade);
                    } else {
                        console.log('Nenhum avião selecionado');
                    }
                });

            });
        })
        .catch(error => console.log('Erro ao buscar dados das rotas pessoais: ', error));
    }

    // Função para construir o select de aviões
    function buildSelectAvioes(avioes, em_rotas) {
        let agora = new Date();
        let selectHTML = '<select class="form-select"><option value="">Selecione um avião</option>';
        avioes.forEach(aviao => {
            // Verifica se o avião está em alguma rota que ainda não foi liberada
            const rotaEmAndamento = em_rotas.find(rota => rota.aviao_id === aviao.aviao_id && new Date(rota.liberacao_valor) > agora);
            const disabledText = rotaEmAndamento ? ' disabled' : '';
            selectHTML += `<option value="${aviao.aviao_id}"${disabledText}>${aviao.modelo} - ${aviao.fabricante} | ${aviao.capacidade} Passageiros${disabledText ? ' (Em rota até ' + rotaEmAndamento.liberacao_valor + ')' : ''}</option>`;
        });
        selectHTML += '</select>';
        return selectHTML;
    }

    // Função para despachar uma rota - substitua por sua implementação
    function DespacharRota(rota_id, pontoA, pontoB, tempo, pagar, aviao_id, modelo, capacidade) {
        // console.log('Despachando rota:', rota_id, aviao_id, modelo);
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        const modalMessage = document.getElementById('modalMessage');
        const confirmButton = document.getElementById('confirmButton');

        const ValorReceber = capacidade * pagar;
        const partes = tempo.split(":");

        // Converte horas, minutos e segundos para números e calcula o total de milissegundos
        const milissegundos = (parseInt(partes[0], 10) * 3600 + parseInt(partes[1], 10) * 60 + parseInt(partes[2], 10)) * 1000;
        
        // Adiciona os milissegundos à data/hora atual
        const agora = new Date();
        agora.setTime(agora.getTime() + milissegundos);
        
        // Formata a data/hora resultante no formato desejado (YYYY-MM-DD HH:MM:SS)
        const dataHora = agora.getFullYear() + "-" + 
                    ("0" + (agora.getMonth() + 1)).slice(-2) + "-" + 
                    ("0" + agora.getDate()).slice(-2) + " " + 
                    ("0" + agora.getHours()).slice(-2) + ":" + 
                    ("0" + agora.getMinutes()).slice(-2) + ":" + 
                    ("0" + agora.getSeconds()).slice(-2);
        
        modalMessage.textContent = `Você confirma o despache do avião de ${pontoA} para ${pontoB} com os valores de passagem no valor de R$ ${pagar}?`;

        confirmButton.onclick = function() {
            // Envie uma solicitação AJAX para o servidor para processar a compra
            fetch('../private/salvar_despachopessoa.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    rota_id: rota_id,
                    pontoA: pontoA,
                    pontoB: pontoB,
                    tempo: dataHora,
                    receber: ValorReceber,
                    aviao_id: aviao_id,
                    modelo: modelo,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Exibe mensagem de sucesso
                    exibirSucesso(data.message);
                    // Atualizar a página para refletir as alterações no saldo do usuário
                    location.reload();
                } else {
                    // Exibe mensagem de erro
                    exibirErro(data.message);
                }
            })
            .catch(error => {
                console.error('Erro ao processar o despache: ', error);
                // Exibe modal de erro
                exibirErro('Ocorreu um erro ao processar o despache. Por favor, tente novamente.');
            });
        };
    
        // Abre o modal de confirmação
        modal.show();
    }
    
    function exibirSucesso(mensagem) {
        // Abre o modal de sucesso
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        const successMessage = document.getElementById('successMessage');
    
        // Define a mensagem de sucesso
        successMessage.textContent = mensagem;
    
        // Abre o modal de sucesso
        successModal.show();
    }
    
    function exibirErro(mensagem) {
        // Abre o modal de erro
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        const errorMessage = document.getElementById('errorMessage');
    
        // Define a mensagem de erro
        errorMessage.textContent = mensagem;
    
        // Abre o modal de erro
        errorModal.show();
    }
    
    
});