<div class="container mt-5 mb-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="bi bi-wallet-fill me-2"></i>Finalizar Pedido</h2>
        </div>
        <div class="card-body">
            <div id="checkout-alert-messages" class="mt-3" style="display: none;"></div>

            <h4 class="mb-3">Detalhes do Carrinho</h4>
            <div class="table-responsive mb-4">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th class="text-center">Qtd</th>
                            <th class="text-end">Preço Unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens_carrinho as $item_key => $item): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($item['nome_produto']) ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($item['nome_variacao']) ?></small>
                                </td>
                                <td class="text-center"><?= $item['quantidade'] ?></td>
                                <td class="text-end">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                                <td class="text-end">R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                            <td class="text-end fw-bold">R$ <span id="checkout-subtotal"><?= number_format($subtotal, 2, ',', '.') ?></span></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Frete:</td>
                            <td class="text-end fw-bold">R$ <span id="checkout-frete">0,00</span></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold fs-5">Total Geral:</td>
                            <td class="text-end fw-bold fs-5">R$ <span id="checkout-total-geral"><?= number_format($subtotal, 2, ',', '.') ?></span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <form id="checkout-form">
            <h4 class="mb-3">Informações de Contato</h4>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="nome_cliente" class="form-label">Nome Completo *</label>
                    <input type="text" class="form-control" id="nome_cliente" name="nome_cliente" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label for="email_cliente" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email_cliente" name="email_cliente" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4">
                    <label for="telefone_cliente" class="form-label">Telefone</label>
                    <input type="tel" class="form-control" id="telefone_cliente" name="telefone_cliente" placeholder="(XX) XXXXX-XXXX">
                    </div>
            </div>

            <h4 class="mb-3">Endereço de Entrega</h4>
            
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="cep" class="form-label">CEP *</label>
                        <input type="text" class="form-control" id="cep" name="cep" placeholder="Ex: 00000-000" maxlength="9" required>
                        <div class="invalid-feedback" id="cep-feedback"></div>
                    </div>
                    <div class="col-md-8">
                        <label for="logradouro" class="form-label">Logradouro *</label>
                        <input type="text" class="form-control" id="logradouro" name="logradouro" required readonly>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="numero" class="form-label">Número *</label>
                        <input type="text" class="form-control" id="numero" name="numero" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-5">
                        <label for="complemento" class="form-label">Complemento</label>
                        <input type="text" class="form-control" id="complemento" name="complemento">
                    </div>
                    <div class="col-md-4">
                        <label for="bairro" class="form-label">Bairro *</label>
                        <input type="text" class="form-control" id="bairro" name="bairro" required readonly>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="localidade" class="form-label">Cidade *</label>
                        <input type="text" class="form-control" id="localidade" name="localidade" required readonly>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-2">
                        <label for="uf" class="form-label">Estado *</label>
                        <input type="text" class="form-control" id="uf" name="uf" maxlength="2" required readonly>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg" id="finalizar-pedido-btn">
                        <i class="bi bi-credit-card-fill me-2"></i> Finalizar Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?= base_url('application/assets/js/main.js') ?>"></script>
<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        const cepInput = document.getElementById('cep');
        const logradouroInput = document.getElementById('logradouro');
        const bairroInput = document.getElementById('bairro');
        const localidadeInput = document.getElementById('localidade');
        const ufInput = document.getElementById('uf');
        const numeroInput = document.getElementById('numero');
        const complementoInput = document.getElementById('complemento');
        const checkoutForm = document.getElementById('checkout-form');
        const checkoutAlertMessages = document.getElementById('checkout-alert-messages');
        const checkoutFreteDisplay = document.getElementById('checkout-frete');
        const checkoutTotalGeralDisplay = document.getElementById('checkout-total-geral');
        const checkoutSubtotalDisplay = document.getElementById('checkout-subtotal'); // Para referência visual

        let currentSubtotal = parseFloat(checkoutSubtotalDisplay.textContent.replace('R$', '').replace('.', '').replace(',', '.'));

        function showCheckoutAlert(message, type) {
            checkoutAlertMessages.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                                ${message}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>`;
            checkoutAlertMessages.style.display = 'block';
            setTimeout(() => {
                checkoutAlertMessages.style.display = 'none';
            }, 5000);
        }

        function clearAddressFields() {
            logradouroInput.value = '';
            bairroInput.value = '';
            localidadeInput.value = '';
            ufInput.value = '';
        }

        function formatCEP(value) {
            value = value.replace(/\D/g, ''); 
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            return value;
        }

        cepInput.addEventListener('input', function() {
            this.value = formatCEP(this.value);
            this.classList.remove('is-invalid');
            document.getElementById('cep-feedback').textContent = '';
        });

        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, ''); // Remove o hífen para enviar para a API
            if (cep.length === 8) {
                // Remove validação anterior
                this.classList.remove('is-invalid');
                document.getElementById('cep-feedback').textContent = '';

                fetch('<?= base_url('checkout/buscar_cep_ajax') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `cep=${cep}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        logradouroInput.value = data.data.logradouro;
                        bairroInput.value = data.data.bairro;
                        localidadeInput.value = data.data.localidade;
                        ufInput.value = data.data.uf;
                        numeroInput.focus();

                        calcularFrete(cep);

                    } else {
                        clearAddressFields();
                        this.classList.add('is-invalid');
                        document.getElementById('cep-feedback').textContent = data.message || 'CEP não encontrado.';
                        showCheckoutAlert(data.message || 'CEP não encontrado ou inválido. Por favor, verifique.', 'danger');
                        checkoutFreteDisplay.textContent = '0,00';
                        checkoutTotalGeralDisplay.textContent = currentSubtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar CEP:', error);
                    clearAddressFields();
                    this.classList.add('is-invalid');
                    document.getElementById('cep-feedback').textContent = 'Erro ao conectar com o serviço de CEP.';
                    showCheckoutAlert('Erro ao buscar CEP. Tente novamente mais tarde.', 'danger');
                    checkoutFreteDisplay.textContent = '0,00';
                    checkoutTotalGeralDisplay.textContent = currentSubtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                });
            } else if (cep.length > 0) {
                clearAddressFields();
                this.classList.add('is-invalid');
                document.getElementById('cep-feedback').textContent = 'CEP deve ter 8 dígitos.';
                checkoutFreteDisplay.textContent = '0,00';
                checkoutTotalGeralDisplay.textContent = currentSubtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        });

        function calcularFrete(cep) {
            fetch('<?= base_url('checkout/calcular_frete_ajax') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `cep_destino=${cep}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    checkoutFreteDisplay.textContent = data.frete.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    checkoutTotalGeralDisplay.textContent = data.total_com_frete.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    showCheckoutAlert(`Frete calculado: R$ ${data.frete.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (Prazo: ${data.prazo})`, 'info');
                } else {
                    checkoutFreteDisplay.textContent = '0,00';
                    checkoutTotalGeralDisplay.textContent = currentSubtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    showCheckoutAlert(data.message || 'Erro ao calcular frete. Verifique o CEP.', 'warning');
                }
            })
            .catch(error => {
                console.error('Erro ao calcular frete:', error);
                checkoutFreteDisplay.textContent = '0,00';
                checkoutTotalGeralDisplay.textContent = currentSubtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                showCheckoutAlert('Ocorreu um erro ao calcular o frete.', 'danger');
            });
        }

        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            showCheckoutAlert('Funcionalidade de finalizar pedido ainda não implementada.', 'info');
            const formData = new FormData(this);
            const dadosDoPedido = Object.fromEntries(formData.entries());
            console.log('Dados do Pedido (para enviar ao backend):', dadosDoPedido);
            // fetch('<?= base_url('pedidos/store') ?>', { /* ... */ });
        });

        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const finalizarBtn = document.getElementById('finalizar-pedido-btn');
            finalizarBtn.disabled = true;
            finalizarBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processando...';

            const formData = new FormData(this);
            const dadosDoPedido = Object.fromEntries(formData.entries());

         
            fetch('<?= base_url('pedidos/store') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(dadosDoPedido).toString() 
            })
            .then(response => response.json())
            .then(data => {
               if (data.success) {
                    showCheckoutAlert(data.message + '<br>Redirecionando...', 'success');
                    setTimeout(() => {
                        window.location.href = '<?= base_url('pedidos/sucesso/') ?>' + data.hash_id;
                    }, 2000);
                } else {
                    showCheckoutAlert(data.message || 'Erro ao finalizar o pedido. Tente novamente.', 'danger');
                    if (data.errors) {
                         console.log("Erros de validação:", data.errors);
                    }
                }
            })
            .catch(error => {
                console.error('Erro na requisição de finalizar pedido:', error);
                showCheckoutAlert('Ocorreu um erro inesperado ao finalizar o pedido.', 'danger');
            })
            .finally(() => {
                finalizarBtn.disabled = false;
                finalizarBtn.innerHTML = '<i class="bi bi-credit-card-fill me-2"></i> Finalizar Pedido';
            });
        });
    });
</script>