document.addEventListener('DOMContentLoaded', function() {
    fetch('../private/exibir_avioespessoal.php')
    .then(response => response.json())
    .then(avioesDoUsuario => {
        const container = document.querySelector('.row');
        avioesDoUsuario.forEach(aviao => {
            // Crie elementos HTML para cada avião do usuário
            const col = document.createElement('div');
            col.className = 'col';
            col.innerHTML = `
                <div class="card mb-4 rounded-3 shadow-sm">
                    <div class="card-header py-3">
                        <h4 class="my-0 fw-normal">${aviao.modelo} / ${aviao.fabricante}</h4>
                    </div>
                    <div class="card-body">
                        <img src="../acoes/aeronaves/${aviao.img}.png" alt="Imagem do ${aviao.modelo}" style="width: 100%; height: 100px; object-fit: contain;">
                        <h1 class="card-title pricing-card-title">R$ ${aviao.valor.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}</h1>
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>Modelo: ${aviao.modelo}</li>
                            <li>Fabricante: ${aviao.fabricante}</li>
                            <li>Capacidade: ${aviao.capacidade} passageiros</li>
                            <li>Velocidade: ${aviao.velocidade} km/h</li>
                            <!-- Adicione outras informações do avião conforme necessário -->
                        </ul>
                        <button id="comprar-${aviao.modelo}" type="button" class="w-100 btn btn-lg btn-outline-primary" value="${aviao.valor}">Vender agora</button>
                    </div>
                </div>
            `;
            container.appendChild(col);
        });
    })
    .catch(error => console.log('Erro ao buscar dados dos aviões pessoais: ', error));
});
