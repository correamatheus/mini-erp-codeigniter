<div class="container mt-5 mb-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><?= isset($produto) ? 'Editar Produto' : 'Novo Produto' ?></h2>
        </div>
        <div class="card-body">
            <div id="alert-messages" class="mt-3" style="display: none;"></div>

            <form id="productForm" method="post"  action="<?= isset($produto) ? base_url('produtos/update') : base_url('produtos/store') ?>">
                <?php if (isset($produto)): ?>
                    <input type="hidden" name="id" value="<?= $produto->id ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?= $produto->nome ?? '' ?>" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= $produto->descricao ?? '' ?></textarea>
                    <div class="invalid-feedback"></div>
                </div>

                <hr class="my-4">

                <h4>Variações e Estoque <span class="text-danger">*</span></h4>
                <div id="variations-container">
                    <div class="card mb-3 p-3 border variation-item-template d-none">
                        <input type="hidden" name="variacoes[__INDEX__][variacao_id]" value="">
                        <input type="hidden" name="variacoes[__INDEX__][acao]" value="nova">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="sku___INDEX__" class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sku___INDEX__" name="variacoes[__INDEX__][sku]" >
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="nome_variacao___INDEX__" class="form-label">Nome da Variação <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome_variacao___INDEX__" name="variacoes[__INDEX__][nome_variacao]" placeholder="Ex: Tamanho P, Cor Azul" >
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-2">
                                <label for="preco___INDEX__" class="form-label">Preço <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="preco___INDEX__" name="variacoes[__INDEX__][preco]"  min="0.01">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-2">
                                <label for="quantidade_estoque___INDEX__" class="form-label">Estoque <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantidade_estoque___INDEX__" name="variacoes[__INDEX__][quantidade_estoque]"  min="0">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger remove-variation-btn" title="Remover Variação">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($produto) && !empty($produto->variacoes)): ?>
                        <?php foreach ($produto->variacoes as $index => $variacao): ?>
                            <div class="card mb-3 p-3 border variation-item" data-variacao-id="<?= $variacao->variacao_id ?>">
                                <input type="hidden" name="variacoes[<?= $index ?>][variacao_id]" value="<?= $variacao->variacao_id ?>">
                                <input type="hidden" name="variacoes[<?= $index ?>][acao]" value="atualizar">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="sku_<?= $index ?>" class="form-label">SKU <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="sku_<?= $index ?>" name="variacoes[<?= $index ?>][sku]" value="<?= $variacao->sku ?>" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="nome_variacao_<?= $index ?>" class="form-label">Nome da Variação <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nome_variacao_<?= $index ?>" name="variacoes[<?= $index ?>][nome_variacao]" value="<?= $variacao->nome_variacao ?>" placeholder="Ex: Tamanho P, Cor Azul" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="preco_<?= $index ?>" class="form-label">Preço <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="preco_<?= $index ?>" name="variacoes[<?= $index ?>][preco]" value="<?= $variacao->preco ?>" required min="0.01">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="quantidade_estoque_<?= $index ?>" class="form-label">Estoque <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="quantidade_estoque_<?= $index ?>" name="variacoes[<?= $index ?>][quantidade_estoque]" value="<?= $variacao->estoque_quantidade ?>" required min="0">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger remove-variation-btn" title="Remover Variação">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>

                <div class="d-grid gap-2">
                    <button type="button" id="add-variation-btn" class="btn btn-info btn-sm mt-3">
                        <i class="bi bi-plus-circle"></i> Adicionar Variação
                    </button>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save"></i> Salvar Produto
                    </button>
                    <a href="<?= base_url('produtos') ?>" class="btn btn-secondary btn-lg">
                        <i class="bi bi-arrow-left-circle"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script>
    var BASE_URL = '<?= base_url() ?>';   
    var PRODUTOS_LIST_URL = '<?= base_url("produtos") ?>';
</script>