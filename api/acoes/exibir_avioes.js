document.addEventListener('DOMContentLoaded', function() {
    fetch('../private/exibir_avioes.php')
    .then(response => response.json())
    .then(data => {
        const container = document.querySelector('.row');
        data.forEach(aviao => {
            const col = document.createElement('div');
            col.className = 'col';
            col.innerHTML = `
                <div class="card mb-4 rounded-3 shadow-sm">
                    <div class="card-header py-3">
                        <h4 class="my-0 fw-normal">${aviao.modelo} / ${aviao.fabricante}</h4>
                    </div>
                    <div class="card-body">
                        <img src="aeronaves/${aviao.img}.png" alt="Imagem do ${aviao.modelo}" style="width: 100%; height: 100px; object-fit: contain;">
                        <h1 class="card-title pricing-card-title">R$ ${aviao.valor.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}</h1>
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>Modelo: ${aviao.modelo}</li>
                            <li>Fabricante: ${aviao.fabricante}</li>
                            <li>Capacidade: ${aviao.capacidade} passageiros</li>
                            <li>Velocidade: ${aviao.velocidade} km/h</li>
                        </ul>
                        <button id="comprar-${aviao.modelo}" type="button" class="w-100 btn btn-lg btn-outline-primary" value="${aviao.valor}">Comprar agora</button>
                    </div>
                </div>
            `;
            container.appendChild(col);

            // Adicionar evento de clique para cada botão de compra
            const btnComprar = document.getElementById(`comprar-${aviao.modelo}`);
            btnComprar.addEventListener('click', () => {
                comprarAviao(aviao.modelo, aviao.valor, aviao.img, aviao.fabricante, aviao.capacidade, aviao.velocidade);
            });
        });
    })
    .catch(error => console.log('Erro ao buscar dados dos aviões: ', error));
});

function comprarAviao(modelo, valor, img, fabricante, capacidade, velocidade) {
    // Abre o modal de confirmação
    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    const modalMessage = document.getElementById('modalMessage');
    const confirmButton = document.getElementById('confirmButton');

    // Define a mensagem do modal
    modalMessage.textContent = `Você confirma a compra do avião ${modelo} por R$ ${valor}?`;

    // Adiciona evento de clique ao botão de confirmação
    confirmButton.onclick = function() {
        // Envie uma solicitação AJAX para o servidor para processar a compra
        fetch('../private/comprar_aviao.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                modelo: modelo,
                valor: valor,
                img: img,
                fabricante: fabricante,
                capacidade: capacidade,
                velocidade: velocidade
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
            console.error('Erro ao processar compra: ', error);
            // Exibe modal de erro
            exibirErro('Ocorreu um erro ao processar a compra. Por favor, tente novamente.');
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

