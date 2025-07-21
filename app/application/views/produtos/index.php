<div class="container mt-5 mb-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Lista de Produtos</h2>
            <a href="<?= base_url('produtos/new') ?>" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Adicionar Novo Produto
            </a>
        </div>
        <div class="card-body">
            <div id="alert-messages" class="mt-3" style="display: none;"></div>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($produtos)): ?>
                <div class="accordion" id="productsAccordion">
                    <?php foreach ($produtos as $produto): ?>
                        <div class="accordion-item product-item" data-id="<?= $produto->id ?>">
                            <h2 class="accordion-header" id="heading<?= $produto->id ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $produto->id ?>" aria-expanded="false" aria-controls="collapse<?= $produto->id ?>">
                                    <?= htmlspecialchars($produto->nome) ?>
                                </button>
                            </h2>
                            <div id="collapse<?= $produto->id ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $produto->id ?>" data-bs-parent="#productsAccordion">
                                <div class="accordion-body">
                                    <p><strong>Descrição:</strong> <?= htmlspecialchars($produto->descricao) ?: 'N/A' ?></p>
                                    <hr>
                                    <h5>Variações:</h5>
                                    <?php if (!empty($produto->variacoes)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>SKU</th>
                                                        <th>Variação</th>
                                                        <th class="text-end">Preço</th>
                                                        <th class="text-center">Estoque</th>
                                                        <th class="text-center">Ação</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($produto->variacoes as $variacao): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($variacao->sku) ?></td>
                                                            <td><?= htmlspecialchars($variacao->nome_variacao) ?></td>
                                                            <td class="text-end">R$ <?= number_format($variacao->preco, 2, ',', '.') ?></td>
                                                            <td class="text-center">
                                                                <span class="badge rounded-pill
                                                                    <?= ($variacao->estoque_quantidade > 10) ? 'bg-success' :
                                                                        (($variacao->estoque_quantidade > 0) ? 'bg-warning text-dark' : 'bg-danger') ?>">
                                                                    <?= $variacao->estoque_quantidade ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <button class="btn btn-primary btn-sm buy-btn"
                                                                        data-product-id="<?= $produto->id ?>"
                                                                        data-variacao-id="<?= $variacao->variacao_id ?>"
                                                                        data-product-name="<?= htmlspecialchars($produto->nome) ?>"
                                                                        data-variacao-name="<?= htmlspecialchars($variacao->nome_variacao) ?>"
                                                                        data-price="<?= $variacao->preco ?>"
                                                                        data-stock="<?= $variacao->estoque_quantidade ?>"
                                                                        <?= ($variacao->estoque_quantidade <= 0) ? 'disabled' : '' ?>
                                                                        title="<?= ($variacao->estoque_quantidade <= 0) ? 'Esgotado' : 'Adicionar ao Carrinho' ?>">
                                                                    <i class="bi bi-cart-plus me-1"></i> Comprar
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning mb-0">Nenhuma variação cadastrada para este produto.</div>
                                    <?php endif; ?>

                                    <hr>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?= base_url('produtos/edit/' . $produto->id) ?>" class="btn btn-warning btn-sm" title="Editar Produto">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </a>
                                        <button class="btn btn-danger btn-sm delete-product-btn" data-id="<?= $produto->id ?>" title="Excluir Produto">
                                            <i class="bi bi-trash"></i> Excluir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center" role="alert">
                    Nenhum produto cadastrado ainda. <a href="<?= base_url('produtos/new') ?>" class="alert-link">Adicione o primeiro produto!</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    var BASE_URL = '<?= base_url() ?>';
    var CARRINHO_ADD_URL = '<?= base_url("carrinho/adicionar_ajax") ?>';
    var CARRINHO_COUNT_URL = '<?= base_url("carrinho/get_carrinho_count_ajax") ?>';
    var PRODUTOS_DELETE_URL = '<?= base_url("produtos/delete/") ?>';
    var PRODUTOS_LIST_URL = '<?= base_url("produtos") ?>';
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="<?= base_url('application/assets/js/main.js') ?>"></script>