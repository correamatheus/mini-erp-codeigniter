<?php $this->load->view('layout/header', ['title' => 'Produtos', 'active_menu' => 'produtos']); ?>
<?php $this->load->view('layout/navbar'); ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Produtos</h2>
        <a href="<?= base_url('produtos/novo') ?>" class="btn btn-success">Novo Produto</a>
    </div>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($produtos)): ?>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?= $produto->id ?></td>
                        <td><?= $produto->nome ?></td>
                        <td>R$ <?= number_format($produto->preco, 2, ',', '.') ?></td>
                        <td><?= $produto->estoque ?></td>
                        <td>
                            <a href="<?= base_url('produtos/editar/' . $produto->id) ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="<?= base_url('produtos/deletar/' . $produto->id) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Nenhum produto encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $this->load->view('layout/footer'); ?>