(function () {
    'use strict';

    var form = document.getElementById('product-form');
    var tbody = document.getElementById('products-body');
    var errorBox = document.getElementById('form-error');
    var submitBtn = document.getElementById('submit-btn');
    var cancelBtn = document.getElementById('cancel-edit-btn');
    var formTitle = document.getElementById('form-title');
    var editingId = null;

    var money = function (n) { return '$' + Number(n).toFixed(2); };

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function render(products, sum) {
        tbody.innerHTML = '';
        if (!products.length) {
            var empty = document.createElement('tr');
            empty.id = 'empty-row';
            empty.innerHTML = '<td colspan="6" class="text-center text-muted py-4">No products submitted yet.</td>';
            tbody.appendChild(empty);
        }
        products.forEach(function (p) {
            var tr = document.createElement('tr');
            tr.dataset.id = p.id;
            tr.innerHTML =
                '<td class="product-name"></td>' +
                '<td class="product-quantity"></td>' +
                '<td class="product-price"></td>' +
                '<td class="product-submitted"></td>' +
                '<td class="product-total"></td>' +
                '<td class="text-nowrap">' +
                '<button type="button" class="btn btn-sm btn-outline-primary btn-edit">Edit</button> ' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-delete">Delete</button>' +
                '</td>';
            tr.querySelector('.product-name').textContent = p.name;
            tr.querySelector('.product-quantity').textContent = p.quantity;
            tr.querySelector('.product-price').textContent = money(p.price);
            tr.querySelector('.product-submitted').textContent = p.submitted_at;
            tr.querySelector('.product-total').textContent = money(p.total);
            tbody.appendChild(tr);
        });
        var sumRow = document.createElement('tr');
        sumRow.className = 'table-secondary fw-bold';
        sumRow.innerHTML =
            '<td colspan="4" class="text-end">Sum total</td>' +
            '<td class="sum-total"></td><td></td>';
        sumRow.querySelector('.sum-total').textContent = money(sum);
        tbody.appendChild(sumRow);
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }

    function clearError() {
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function resetForm() {
        editingId = null;
        form.reset();
        submitBtn.textContent = 'Add Product';
        formTitle.textContent = 'Add Product';
        cancelBtn.classList.add('d-none');
    }

    function startEdit(id) {
        var tr = tbody.querySelector('tr[data-id="' + id + '"]');
        if (!tr) return;
        editingId = id;
        document.getElementById('name').value = tr.querySelector('.product-name').textContent;
        document.getElementById('quantity').value = tr.querySelector('.product-quantity').textContent;
        document.getElementById('price').value = parseFloat(tr.querySelector('.product-price').textContent.replace(/[^0-9.]/g, ''));
        submitBtn.textContent = 'Update Product';
        formTitle.textContent = 'Edit Product';
        cancelBtn.classList.remove('d-none');
        document.getElementById('name').focus();
    }

    function api(url, method, body) {
        var opts = {
            method: method,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        };
        if (body) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(body);
        }
        return fetch(url, opts).then(function (res) {
            return res.json().then(function (data) { return { ok: res.ok, data: data }; });
        });
    }

    function payload() {
        return {
            name: document.getElementById('name').value,
            quantity: document.getElementById('quantity').value,
            price: document.getElementById('price').value
        };
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearError();
        var p = payload();
        var req = editingId
            ? api('/products/' + editingId, 'PUT', p)
            : api('/products', 'POST', p);
        req.then(function (r) {
            if (!r.ok) {
                var msg = r.data.message || 'Something went wrong.';
                if (r.data.errors) {
                    msg = Object.values(r.data.errors).flat().join(' ');
                }
                showError(msg);
                return;
            }
            render(r.data.products, r.data.sum);
            resetForm();
        }).catch(function () { showError('Request failed. Please try again.'); });
    });

    cancelBtn.addEventListener('click', resetForm);

    tbody.addEventListener('click', function (e) {
        var tr = e.target.closest('tr[data-id]');
        if (!tr) return;
        var id = tr.dataset.id;
        if (e.target.classList.contains('btn-edit')) {
            startEdit(id);
        } else if (e.target.classList.contains('btn-delete')) {
            if (!window.confirm('Delete this product?')) return;
            clearError();
            api('/products/' + id, 'DELETE').then(function (r) {
                if (!r.ok) { showError(r.data.message || 'Delete failed.'); return; }
                render(r.data.products, r.data.sum);
                if (editingId === id) resetForm();
            }).catch(function () { showError('Request failed. Please try again.'); });
        }
    });
})();
