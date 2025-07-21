// assets/js/main.js
document.addEventListener('DOMContentLoaded', function() {
    const productForm = document.getElementById('productForm');
    const variationsContainer = document.getElementById('variations-container');
    const addVariationBtn = document.getElementById('add-variation-btn');
    const alertMessages = document.getElementById('alert-messages');
    const cartCountElement = document.getElementById('cart-count');
    const cartAlertMessages = document.getElementById('cart-alert-messages');
    const cartTable = document.getElementById('cart-table'); 
    const cartTotalDisplay = document.getElementById('cart-total-display');

    function showCartAlert(message, type) {
        if (!cartAlertMessages) return; 
        cartAlertMessages.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                            ${message}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>`;
        cartAlertMessages.style.display = 'block';
        setTimeout(() => {
            cartAlertMessages.style.display = 'none';
        }, 5000);
    }

    if (cartTable) {
        cartTable.addEventListener('click', function(e) {
            const btnDecrease = e.target.closest('.btn-decrease');
            const btnIncrease = e.target.closest('.btn-increase');
            const removeItemBtn = e.target.closest('.remove-item-btn');

            let inputElement;
            if (btnDecrease || btnIncrease) {
                inputElement = (btnDecrease || btnIncrease).closest('.input-group').querySelector('.quantity-input');
            }

            if (inputElement) {
                let currentQuantity = parseInt(inputElement.value);
                const maxQuantity = parseInt(inputElement.max); 
                const quantityErrorMsg = inputElement.closest('td').querySelector('.quantity-error-msg');

                if (btnDecrease) {
                    if (currentQuantity > 1) {
                        inputElement.value = currentQuantity - 1;
                        quantityErrorMsg.style.display = 'none'; 
                        updateCartItemQuantity(inputElement);
                    } else {
                        showCartAlert('A quantidade mínima é 1. Para remover, use o botão de lixeira.', 'warning');
                    }
                } else if (btnIncrease) {
                    if (currentQuantity < maxQuantity) {
                        inputElement.value = currentQuantity + 1;
                        quantityErrorMsg.style.display = 'none'; 
                        updateCartItemQuantity(inputElement);
                    } else {
                        quantityErrorMsg.textContent = `Estoque máximo atingido (${maxQuantity}).`;
                        quantityErrorMsg.style.display = 'block';
                        showCartAlert('Não há estoque suficiente para esta quantidade.', 'warning');
                    }
                }
            } else if (removeItemBtn) {
                const row = removeItemBtn.closest('tr');
                const productId = row.dataset.produtoId;
                const variacaoId = row.dataset.variacaoId;
                if (confirm('Tem certeza que deseja remover este item do carrinho?')) {
                    removeItemFromCart(productId, variacaoId, row);
                }
            }
        });

        // Event listener para a mudança direta no input de quantidade
        cartTable.addEventListener('change', function(e) {
            const quantityInput = e.target.closest('.quantity-input');
            if (quantityInput) {
                let newQuantity = parseInt(quantityInput.value);
                const maxQuantity = parseInt(quantityInput.max);
                const quantityErrorMsg = quantityInput.closest('td').querySelector('.quantity-error-msg');

                if (isNaN(newQuantity) || newQuantity < 1) {
                    newQuantity = 1; // Garante que a quantidade mínima é 1
                    quantityInput.value = newQuantity;
                }

                if (newQuantity > maxQuantity) {
                    quantityInput.value = maxQuantity; // Limita à quantidade máxima em estoque
                    quantityErrorMsg.textContent = `Estoque máximo atingido (${maxQuantity}).`;
                    quantityErrorMsg.style.display = 'block';
                    showCartAlert('Você não pode adicionar mais do que o estoque disponível.', 'warning');
                } else {
                    quantityErrorMsg.style.display = 'none';
                }

                updateCartItemQuantity(quantityInput);
            }
        });

        // Event listener para o botão de esvaziar carrinho
        const emptyCartBtn = document.getElementById('empty-cart-btn');
        if (emptyCartBtn) {
            emptyCartBtn.addEventListener('click', function() {
                if (confirm('Tem certeza que deseja remover TODOS os itens do carrinho?')) {
                    emptyCart();
                }
            });
        }
    }

    function updateCartItemQuantity(inputElement) {
        const row = inputElement.closest('tr');
        const productId = row.dataset.produtoId;
        const variacaoId = row.dataset.variacaoId;
        const newQuantity = parseInt(inputElement.value);
        const currentQuantity = parseInt(inputElement.dataset.currentQuantity); 

        if (newQuantity === currentQuantity) {
            return; 
        }

        fetch(CARRINHO_UPDATE_QTD, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `produto_id=${productId}&variacao_id=${variacaoId}&quantidade=${newQuantity}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCartAlert(data.message, 'success');
                const itemSubtotalElement = row.querySelector('.item-subtotal');
                const price = parseFloat(row.querySelector('td:nth-child(3)').textContent.replace('R$', '').replace(',', '.'));
                itemSubtotalElement.textContent = 'R$ ' + (price * newQuantity).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                if (cartTotalDisplay) {
                    cartTotalDisplay.textContent = 'R$ ' + parseFloat(data.subtotal_carrinho).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
                updateCartCount(); 

                inputElement.dataset.currentQuantity = newQuantity; 

                if (newQuantity === 0) {
                    row.remove();
                    if (cartTable.querySelector('tbody tr') === null) { 
                         showCartAlert('Seu carrinho está vazio.', 'info');
                         setTimeout(() => window.location.reload(), 1500); 
                    }
                }
            } else {
                showCartAlert(data.message || 'Erro ao atualizar quantidade.', 'danger');
                inputElement.value = currentQuantity;
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar quantidade no carrinho:', error);
            showCartAlert('Ocorreu um erro ao atualizar a quantidade.', 'danger');
            inputElement.value = currentQuantity; 
        });
    }

    function removeItemFromCart(productId, variacaoId, rowElement) {
        fetch(CARRINHO_DELETE_ITEM, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `produto_id=${productId}&variacao_id=${variacaoId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCartAlert(data.message, 'success');
                rowElement.remove(); 
                updateCartCount();

                if (cartTotalDisplay) {
                    cartTotalDisplay.textContent = 'R$ ' + parseFloat(data.subtotal_carrinho).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                if (cartTable.querySelector('tbody tr') === null) { 
                    // Se o carrinho ficou vazio, você pode redirecionar ou mostrar a mensagem de vazio
                    showCartAlert('Seu carrinho está vazio.', 'info');
                    setTimeout(() => window.location.reload(), 1500); // Recarrega para mostrar o estado vazio
                }
            } else {
                showCartAlert(data.message || 'Erro ao remover item.', 'danger');
            }
        })
        .catch(error => {
            console.error('Erro ao remover item do carrinho:', error);
            showCartAlert('Ocorreu um erro ao remover o item.', 'danger');
        });
    }

    // Função para esvaziar o carrinho (AJAX)
    function emptyCart() {
        fetch(CARRINHO_CLEAR, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCartAlert(data.message, 'success');
                updateCartCount(); // Zera o contador no header
                if (cartTotalDisplay) {
                    cartTotalDisplay.textContent = 'R$ 0,00';
                }
                // Recarrega a página para mostrar o estado de carrinho vazio
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showCartAlert(data.message || 'Erro ao esvaziar o carrinho.', 'danger');
            }
        })
        .catch(error => {
            console.error('Erro ao esvaziar o carrinho:', error);
            showCartAlert('Ocorreu um erro ao esvaziar o carrinho.', 'danger');
        });
    }
   
    const productsAccordion = document.getElementById('productsAccordion'); // Pegamos o ID do acordeão principal
    const eventDelegateTarget = productsAccordion || document.body; // Usa o acordeão se existir, senão o body

    let variationIndex = variationsContainer ? variationsContainer.querySelectorAll('.variation-item').length : 0;

    if (productForm && variationIndex === 0) {
        addVariation();
    }

    function showAlert(message, type) {
        alertMessages.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                        ${message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>`;
        alertMessages.style.display = 'block';
        setTimeout(() => {
            alertMessages.style.display = 'none';
        }, 5000);
    }

    function clearValidationFeedback() {
        if (productForm) {
            productForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            productForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            alertMessages.style.display = 'none';
        }
    }

    function reindexVariations() {
        if (variationsContainer) {
            variationsContainer.querySelectorAll('.variation-item').forEach((item, index) => {
                item.querySelectorAll('[name^="variacoes["]').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/variacoes\[\d+\]/, `variacoes[${index}]`));
                        const id = input.getAttribute('id');
                        if (id) {
                            input.setAttribute('id', id.replace(/_\d+/, `_${index}`));
                            const label = document.querySelector(`label[for="${id}"]`);
                            if (label) {
                                label.setAttribute('for', id.replace(/_\d+/, `_${index}`));
                            }
                        }
                    }
                });
                item.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                item.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            });
        }
    }

    function addVariation() {
        const template = document.querySelector('.variation-item-template');
        if (!template) {
            console.error("Erro: Template de variação não encontrado. Verifique 'variation-item-template' no form.php");
            return;
        }

        const newVariation = template.cloneNode(true);
        newVariation.classList.remove('d-none', 'variation-item-template');
        newVariation.classList.add('variation-item');

        newVariation.querySelectorAll('input, textarea').forEach(input => {
            input.value = '';
            input.classList.remove('is-invalid');
            const name = input.getAttribute('name');
            const id = input.getAttribute('id');

            if (name) {
                input.setAttribute('name', name.replace('__INDEX__', variationIndex));
            }
            if (id) {
                input.setAttribute('id', id.replace('__INDEX__', variationIndex));
                const label = newVariation.querySelector(`label[for="${id}"]`);
                if (label) {
                    label.setAttribute('for', id.replace('__INDEX__', variationIndex));
                }
            }
        });
        newVariation.querySelector('input[name*="[acao]"]').value = 'nova';
        const variacaoIdInput = newVariation.querySelector('input[name*="[variacao_id]"]');
        if (variacaoIdInput) variacaoIdInput.value = '';

        variationsContainer.appendChild(newVariation);
        variationIndex++;
        reindexVariations();
    }

    if (addVariationBtn) {
        addVariationBtn.addEventListener('click', addVariation);
    }

    if (variationsContainer) {
        variationsContainer.addEventListener('click', function(event) {
            if (event.target.closest('.remove-variation-btn')) {
                const button = event.target.closest('.remove-variation-btn');
                const variationItem = button.closest('.variation-item');
                if (variationItem) {
                    const visibleVariations = variationsContainer.querySelectorAll('.variation-item:not([style*="display: none"])').length;
                    if (visibleVariations === 1 && variationItem.style.display !== 'none') {
                        showAlert('Não é possível remover a última variação ativa. Um produto deve ter pelo menos uma variação.', 'warning');
                        return;
                    }

                    const variacaoIdInput = variationItem.querySelector('input[name^="variacoes["][name$="[variacao_id]"]');
                    const acaoInput = variationItem.querySelector('input[name^="variacoes["][name$="[acao]"]');

                    if (variacaoIdInput && variacaoIdInput.value !== '') {
                        acaoInput.value = 'deletar';
                        variationItem.style.display = 'none';
                        variationItem.querySelectorAll('input, select, textarea, button').forEach(input => {
                            input.disabled = true;
                            input.required = false;
                        });
                    } else {
                        variationItem.remove();
                    }
                    reindexVariations();
                }
            }
        });
    }

    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearValidationFeedback();

            const formData = new FormData(this);
            const url = this.action;
            const submitBtn = this.querySelector('button[type="submit"]');

            const disabledInputs = productForm.querySelectorAll('.variation-item[style*="display: none"] input, .variation-item[style*="display: none"] select, .variation-item[style*="display: none"] textarea');
            disabledInputs.forEach(input => {
                input.disabled = false;
                input.removeAttribute('required');
            });

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                disabledInputs.forEach(input => input.disabled = true);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    if (!this.querySelector('input[name="id"]')) {
                        setTimeout(() => {
                            window.location.href = PRODUTOS_LIST_URL;
                        }, 1500);
                    } else {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                } else {
                    showAlert(data.message || 'Erro ao salvar produto.', 'danger');
                    if (data.errors) {
                        for (const field in data.errors) {
                            const errorMsg = data.errors[field];
                            let inputElement = document.getElementById(field);

                            if (!inputElement) {
                                const inputName = field;
                                inputElement = productForm.querySelector(`[name="${inputName}"]`);
                            }

                            if (inputElement) {
                                inputElement.classList.add('is-invalid');
                                let feedbackDiv = inputElement.nextElementSibling;
                                if (feedbackDiv && feedbackDiv.classList.contains('invalid-feedback')) {
                                    feedbackDiv.textContent = errorMsg;
                                } else {
                                    const newFeedbackDiv = document.createElement('div');
                                    newFeedbackDiv.classList.add('invalid-feedback');
                                    newFeedbackDiv.textContent = errorMsg;
                                    inputElement.parentNode.insertBefore(newFeedbackDiv, inputElement.nextSibling);
                                }
                            } else {
                                console.warn(`Elemento de input para o campo ${field} não encontrado no DOM.`);
                            }
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                showAlert('Ocorreu um erro inesperado. Tente novamente.', 'danger');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-save"></i> Salvar Produto';
            });
        });
    }

    
    eventDelegateTarget.addEventListener('click', function(e) {
        const deleteButton = e.target.closest('.delete-product-btn');
        if (deleteButton) {
            e.stopPropagation(); // Impede que o clique no botão de delete propague para o accordion-button se estiver dentro dele
            const productId = deleteButton.dataset.id;
            if (confirm('Tem certeza que deseja excluir este produto e todas as suas variações?')) {
                fetch(`${PRODUTOS_DELETE_URL}${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: '_method=DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        deleteButton.closest('.accordion-item').remove();
                        showAlert('Produto excluído com sucesso!', 'success');
                    } else {
                        showAlert(data.message || 'Erro ao excluir produto.', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showAlert('Ocorreu um erro ao excluir o produto.', 'danger');
                });
            }
        }
    });

    function updateCartCount() {
        if (!cartCountElement) return;

        fetch(CARRINHO_COUNT_URL, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data && typeof data.count !== 'undefined') {
                cartCountElement.textContent = data.count;
                cartCountElement.style.display = data.count > 0 ? 'inline-block' : 'none';
            }
        })
        .catch(error => {
            console.error('Erro ao buscar contador do carrinho:', error);
        });
    }
  
    eventDelegateTarget.addEventListener('click', function(e) {
        const buyButton = e.target.closest('.buy-btn'); 
        if (buyButton) { 
            e.preventDefault(); // Previne o comportamento padrão (se houver um link)
            console.log("ESTOU AQUI - Click no botão de Comprar detectado!"); // Agora deve disparar!

            const productId = buyButton.dataset.productId;
            const variacaoId = buyButton.dataset.variacaoId;
            const productName = buyButton.dataset.productName;
            const variacaoName = buyButton.dataset.variacaoName;
            const quantity = 1;

            fetch(CARRINHO_ADD_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `produto_id=${productId}&variacao_id=${variacaoId}&quantidade=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    updateCartCount();
                } else {
                    showAlert(data.message || 'Erro ao adicionar item ao carrinho.', 'danger');
                }
            })
            .catch(error => {
                console.error('Erro ao adicionar ao carrinho:', error);
                showAlert('Ocorreu um erro ao adicionar item ao carrinho.', 'danger');
            });
        }
    });
    updateCartCount();
});