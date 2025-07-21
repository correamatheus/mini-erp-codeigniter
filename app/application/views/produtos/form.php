<div class="container mt-5">
  <h2><?= isset($produto) ? 'Editar Produto' : 'Novo Produto' ?></h2>
  <form action="<?= isset($produto) ? base_url('produtos/atualizar') : base_url('produtos/salvar') ?>" method="post">
    <?php if (isset($produto)): ?>
      <input type="hidden" name="id" value="<?= $produto->id ?>">
    <?php endif; ?>
    <div class="mb-3">
      <label>Nome</label>
      <input type="text" name="nome" class="form-control" required value="<?= $produto->nome ?? '' ?>">
    </div>
    <div class="mb-3">
      <label>Preço</label>
      <input type="number" step="0.01" name="preco" class="form-control" required value="<?= $produto->preco ?? '' ?>">
    </div>
    <div class="mb-3">
      <label>Estoque</label>
      <input type="number" name="estoque" class="form-control" required value="<?= $produto->estoque ?? '' ?>">
    </div>
    <button type="submit" class="btn btn-primary">Salvar</button>
    <a href="<?= base_url('produtos') ?>" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
