<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= base_url() ?>">Mini ERP</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= (isset($active_menu) && $active_menu == 'dashboard') ? 'active' : '' ?>" aria-current="page" href="<?= base_url('dashboard') ?>">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($active_menu) && $active_menu == 'produtos') ? 'active' : '' ?>" href="<?= base_url('produtos') ?>">Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($active_menu) && $active_menu == 'pedidos') ? 'active' : '' ?>" href="<?= base_url('pedidos') ?>">Pedidos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($active_menu) && $active_menu == 'estoque') ? 'active' : '' ?>" href="<?= base_url('estoque') ?>">Estoque</a>
                </li>
                </ul>
            
        </div>
    </div>
</nav>