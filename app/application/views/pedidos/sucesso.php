<div class="container mt-5 mb-5">
    <div class="card shadow-sm text-center">
        <div class="card-header bg-success text-white">
            <h2 class="mb-0"><i class="bi bi-check-circle-fill me-2"></i>Pedido Realizado com Sucesso!</h2>
        </div>
        <div class="card-body">
            <p class="lead">Obrigado por sua compra, <strong><?= htmlspecialchars($pedido->cliente_nome) ?></strong>!</p>
            <p>Seu pedido foi processado com sucesso. Você receberá um email de confirmação em breve.</p>
            <h4 class="mt-4">Número do Seu Pedido: <span class="text-primary"><?= htmlspecialchars($pedido->hash_id) ?></span></h4>
            <p class="mb-4">Acompanhe o status do seu pedido usando este número.</p>

            <h5 class="mt-4">Detalhes do Pedido:</h5>
            <div class="table-responsive mx-auto" style="max-width: 600px;">
                <table class="table table-bordered table-sm">
                    <tbody>
                        <tr><th>Data do Pedido:</th><td><?= date('d/m/Y H:i', strtotime($pedido->created_at)) ?></td></tr>
                        <tr><th>Status:</th><td><span class="badge bg-info"><?= htmlspecialchars(ucfirst($pedido->status)) ?></span></td></tr>
                        <tr><th>Total:</th><td>R$ <?= number_format($pedido->valor_total, 2, ',', '.') ?></td></tr>
                        <tr><th>CEP de Entrega:</th><td><?= htmlspecialchars($pedido->cep) ?></td></tr>
                        <tr><th>Endereço:</th><td><?= htmlspecialchars($pedido->endereco) ?></td></tr>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($itens_pedido)): ?>
                <h5 class="mt-4">Itens do Pedido:</h5>
                <div class="table-responsive mx-auto mb-4" style="max-width: 600px;">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-end">Preço Unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itens_pedido as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item->nome_produto) ?></td>
                                    <td class="text-center"><?= $item->quantidade ?></td>
                                    <td class="text-end">R$ <?= number_format($item->preco_unitario, 2, ',', '.') ?></td>
                                    <td class="text-end">R$ <?= number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <a href="<?= base_url('produtos') ?>" class="btn btn-primary btn-lg me-2">
                <i class="bi bi-shop me-2"></i> Continuar Comprando
            </a>
            </div>
    </div>
</div>