var mapa = L.map('mapa').setView([0, 0], 2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
}).addTo(mapa);

var localSelecionado;

function onMapClick(e) {
    if (localSelecionado) {
        mapa.removeLayer(localSelecionado);
    }
    localSelecionado = L.marker(e.latlng).addTo(mapa);
}

mapa.on('click', onMapClick);

document.getElementById('salvarLocal').onclick = function() {
    if (localSelecionado) {
        var nome = prompt("Nome do local:");
        if (!nome) {
            alert("Por favor, dê um nome ao local.");
            return;
        }
        var latlng = localSelecionado.getLatLng();
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../private/salvarAeroporto.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert("Local salvo com sucesso!");
                console.log("Dados salvos: ", xhr.responseText);
            } else {
                console.error("Erro ao salvar os dados.");
            }
        };
        xhr.send("local=" + nome + "&latitude=" + latlng.lat + "&longitude=" + latlng.lng);
    } else {
        alert("Por favor, selecione um local no mapa.");
    }
};

document.getElementById('salvarLocalAdmin').onclick = function() {
    if (localSelecionado) {
        var nome = prompt("Nome do local:");
        if (!nome) {
            alert("Por favor, dê um nome ao local.");
            return;
        }
        var latlng = localSelecionado.getLatLng();
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../private/salvarAeroporto.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert("Local salvo com sucesso!");
                console.log("Dados salvos: ", xhr.responseText);
            } else {
                console.error("Erro ao salvar os dados.");
            }
        };
        xhr.send("local=" + nome + "&latitude=" + latlng.lat + "&longitude=" + latlng.lng);
    } else {
        alert("Por favor, selecione um local no mapa.");
    }
};


// Função para verificar a posse do local e logar o resultado no console
function verificarPosseLocal(local, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../private/verificarLocalUsuario.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200) {
            var resposta = JSON.parse(xhr.responseText);
            if (resposta.possui) {
                // console.log("O usuário possui o local: " + local);
            } else {
                // console.log("O usuário NÃO possui o local: " + local);
            }
            callback(resposta.possui); // Chama o callback com o resultado da posse
        } else {
            console.error("Erro ao verificar a posse do local.");
        }
    };
    xhr.send("local=" + encodeURIComponent(local));
}

function abrirHangar(local) {
    // Supondo que você queira passar o 'local' como uma query string para hangar.php
    window.location.href = '../hangar/hangar.php';
}

function verificarRotas(local) {
    // Supondo que você queira passar o 'local' como uma query string para rotas.php
    window.location.href = '../global/rotas.php';
}

// Função para adicionar popup personalizado com base na posse do local
function adicionarPopup(waypoint) {
    verificarPosseLocal(waypoint.local, function(possui) {
        var popupContent;
        if (possui) {
            popupContent = '<b>' + waypoint.local + '</b><br>' +
                           '<button onclick="abrirHangar(\'' + waypoint.local + '\')">Abrir hangar</button>' +
                           '<button onclick="verificarRotas(\'' + waypoint.local + '\')">Verificar rotas</button>' +
                           '<button onclick="vender(\'' + waypoint.local + '\', ' + waypoint.valor + ')">Vender hangar</button>';
        } else {
            popupContent = '<b>' + waypoint.local + '</b><br>' +
                           '<button onclick="comprar(\'' + waypoint.local + '\', ' + waypoint.valor + ')">Comprar hangar</button>';
        }
        
        var popupOptions = {
            maxWidth: 200
        };
        
        var iconeCustomizado = L.divIcon({
            className: possui ? 'fa-icon-blue' : 'fa-icon',
            html: '<i class="fas fa-landmark"></i>',
            iconSize: [30, 30]
        });
        
        var marker = L.marker([waypoint.latitude, waypoint.longitude], {icon: iconeCustomizado})
                       .addTo(mapa)
                       .bindPopup(popupContent, popupOptions);
    
        marker.waypointData = waypoint;
    });
}


function carregarWaypoints() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "../private/carregarWaypoints.php", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var waypoints = JSON.parse(xhr.responseText);
            waypoints.forEach(adicionarPopup);
        } else {
            console.error("Erro ao carregar os waypoints.");
        }
    };
    xhr.send();
}


function comprar(local, valor) {
    var modalMessage = "Deseja comprar o local '" + local + "' por $" + valor + "?";
    document.getElementById("modalMessage").innerText = modalMessage;
    $('#confirmationModal').modal('show');

    // Quando o botão de confirmação é clicado, envia a solicitação de compra
    document.getElementById("confirmButton").onclick = function() {
        $('#confirmationModal').modal('hide');
        // Aqui você pode enviar a solicitação de compra
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../private/comprarAeroporto.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Exibir mensagem de sucesso após a compra
                document.getElementById("successMessage").innerText = xhr.responseText;
                $('#successModal').modal('show');
                // Recarregar os waypoints após a compra (opcional)
                carregarWaypoints();
            } else {
                // Exibir mensagem de erro em caso de falha na compra
                document.getElementById("errorMessage").innerText = "Erro ao comprar o aeroporto: " + xhr.statusText;
                $('#errorModal').modal('show');
            }
        };
        xhr.send("local=" + encodeURIComponent(local) + "&valor=" + encodeURIComponent(valor));
    };
}


// Função para vender o aeroporto
function vender(local) {
    aeroportoSelecionado = local;
    // Implemente a lógica para vender o aeroporto
    console.log("Vender o aeroporto:", local);
}

function desenharRotas() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "../private/carregarRotas.php", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var rotas = JSON.parse(xhr.responseText);
            rotas.forEach(function(rota) {
                var pontoA = L.latLng(rota.latitudeA, rota.longitudeA);
                var pontoB = L.latLng(rota.latitudeB, rota.longitudeB);
                var linhaPontilhada = L.polyline([pontoA, pontoB], {
                    color: 'black',
                    dashArray: '5, 10',
                    weight: 2
                }).addTo(mapa);

                // Adiciona a seta na linha
                var decorator = L.polylineDecorator(linhaPontilhada, {
                    patterns: [
                        // Define o padrão da seta
                        {offset: '100%', repeat: 0, symbol: L.Symbol.arrowHead({pixelSize: 15, polygon: false, pathOptions: {stroke: true, color: 'black'}})}
                    ]
                }).addTo(mapa);
            });
        } else {
            console.error("Erro ao carregar as rotas.");
        }
    };
    xhr.onerror = function() {
        console.error("Erro na requisição para carregar as rotas.");
    };
    xhr.send();
}




document.addEventListener('DOMContentLoaded', function() {
    carregarWaypoints();
    desenharRotas();
});