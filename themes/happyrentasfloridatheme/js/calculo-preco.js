document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("rentalForm");
    const totalPriceElement = document.getElementById("totalPrice");
    const produtoContainer = document.querySelector(".produto-container");

    const precoBase = parseFloat(produtoContainer.dataset.precoBase);
    const precoExtra = parseFloat(produtoContainer.dataset.precoExtra);
    const precoSeguro = 15;

    form.addEventListener("change", calcularPreco);

    function calcularPreco() {
        let dataEntrega = new Date(document.getElementById("dataEntrega").value);
        let dataRetorno = new Date(document.getElementById("dataRetorno").value);
        let diasAluguel = Math.ceil((dataRetorno - dataEntrega) / (1000 * 60 * 60 * 24));

        let precoFinal = precoBase;
        if (diasAluguel > 4) {
            precoFinal += (diasAluguel - 4) * precoExtra;
        }

        if (document.getElementById("seguroSim").checked) {
            precoFinal += precoSeguro;
        }

        totalPriceElement.textContent = `US$ ${precoFinal.toFixed(2)}`;
    }

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        let produtoID = produtoContainer.dataset.produtoId;
        let precoTotal = totalPriceElement.textContent.replace("US$ ", "");

        let data = new FormData();
        data.append("action", "adicionar_produto_ao_carrinho");
        data.append("produto_id", produtoID);
        data.append("preco", precoTotal);

        fetch(wp_ajax.ajaxurl, {
            method: "POST",
            body: data,
        }).then(response => response.json()).then(result => {
            if (result.success) {
                alert("Produto adicionado ao carrinho!");
                //window.location.href = "/carrinho/";
            }
        });
    });
});
