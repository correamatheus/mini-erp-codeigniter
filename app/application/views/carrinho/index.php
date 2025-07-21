<div class="container mt-5 mb-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0"><i class="bi bi-cart me-2"></i>Seu Carrinho de Compras</h2>
            <a href="<?= base_url('produtos') ?>" class="btn btn-info">
                <i class="bi bi-arrow-left-circle me-1"></i> Continuar Comprando
            </a>
        </div>
        <div class="card-body">
            <div id="cart-alert-messages" class="mt-3" style="display: none;"></div>

            <?php if (!empty($itens_carrinho)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="cart-table">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">SKU</th>
                                <th class="text-end">Preço Unitário</th>
                                <th class="text-center" style="width: 150px;">Quantidade</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itens_carrinho as $item_key => $item): ?>
                                <tr data-item-key="<?= $item_key ?>"
                                    data-produto-id="<?= $item['produto_id'] ?>"
                                    data-variacao-id="<?= $item['variacao_id'] ?>"
                                    data-estoque-disponivel="<?= $item['estoque_disponivel'] ?>">
                                    <td>
                                        <strong><?= htmlspecialchars($item['nome_produto']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($item['nome_variacao']) ?></small>
                                    </td>
                                    <td class="text-center"><?= htmlspecialchars($item['sku']) ?></td>
                                    <td class="text-end">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm quantity-input-group mx-auto" style="max-width: 120px;">
                                            <button class="btn btn-outline-secondary btn-decrease" type="button" title="Diminuir Quantidade">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number" class="form-control text-center quantity-input"
                                                   value="<?= $item['quantidade'] ?>"
                                                   min="1"
                                                   data-current-quantity="<?= $item['quantidade'] ?>"
                                                   max="<?= $item['estoque_disponivel'] ?>"
                                                   aria-label="Quantidade">
                                            <button class="btn btn-outline-secondary btn-increase" type="button" title="Aumentar Quantidade">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                        <small class="text-danger quantity-error-msg mt-1 d-block" style="display: none;"></small>
                                        <small class="text-muted mt-1 d-block">Em estoque: <?= $item['estoque_disponivel'] ?></small>
                                    </td>
                                    <td class="text-end item-subtotal">R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm remove-item-btn" title="Remover Item">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fs-5"><strong>Total:</strong></td>
                                <td class="text-end fs-5" id="cart-total-display"><strong>R$ <?= number_format($subtotal, 2, ',', '.') ?></strong></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" id="empty-cart-btn" title="Esvaziar Carrinho">
                                        <i class="bi bi-cart-x"></i> Esvaziar
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="<?= base_url('checkout') ?>" class="btn btn-success btn-lg"><i class="bi bi-check-circle me-2"></i> Finalizar Compra</a>
                </div>

            <?php else: ?>
                <div class="alert alert-info text-center py-4" role="alert">
                    Seu carrinho está vazio. <a href="<?= base_url('produtos') ?>" class="alert-link">Comece a adicionar produtos!</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script>
    var BASE_URL = '<?= base_url() ?>';
    var CARRINHO_UPDATE_QTD = '<?= base_url("carrinho/atualizar_quantidade_ajax") ?>';
    var CARRINHO_CLEAR = '<?= base_url("carrinho/esvaziar_ajax") ?>';
    var CARRINHO_DELETE_ITEM = '<?= base_url("carrinho/remover_ajax") ?>';
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="<?= base_url('application/assets/js/main.js') ?>"></script>