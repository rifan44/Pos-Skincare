@extends('layouts.app')
@section('title', 'Kasir | Skincare POS')
@section('content')

<style>
    body { background-color: var(--bg-cream); }
    .pos-panel { background: transparent; }
    .custom-input { border-radius: 1rem; border: 1px solid var(--border-color); padding: 0.8rem 1.2rem; background: #fff; transition: all 0.3s ease; }
    .custom-input:focus { border-color: var(--primary-color); box-shadow: 0 0 0 4px rgba(199, 139, 125, 0.15); }
    .cart-wrapper { border-radius: 1.5rem; background: #fff; border: 1px solid var(--border-color); box-shadow: 0 10px 40px rgba(59,51,49,0.04); display: flex; flex-direction: column; }
    .btn-pay { border-radius: 1rem; background: var(--primary-color); color: #fff; font-weight: 700; border: none; padding: 1rem; transition: all 0.3s; }
    .btn-pay:hover:not(:disabled) { background: var(--primary-hover); transform: translateY(-2px); }
    .btn-pay:disabled { background: #e9ecef; color: #adb5bd; }
    .hide-scroll::-webkit-scrollbar { display: none; }
    .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    .catalog-card { border: 1px solid var(--border-color); border-radius: 1.25rem; overflow: hidden; transition: transform .2s ease, box-shadow .2s ease; }
    .catalog-card:hover { transform: translateY(-4px); box-shadow: 0 1rem 2rem rgba(59,51,49,.10) !important; }
    .catalog-photo { height: 150px; background: linear-gradient(135deg, #fbf5f2, #f5e9e4); }
    .catalog-photo img { object-fit: contain; padding: .6rem; }
    .catalog-add { border-radius: 999px; background: var(--primary-color); color: #fff; }
    .catalog-add:hover { color: #fff; background: var(--primary-hover); }
    .cart-controls .form-control { min-width: 0; }
</style>

<div class="container-fluid py-4 px-lg-4">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show fw-medium border-0 shadow-sm rounded-4" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show fw-medium border-0 shadow-sm rounded-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4" role="alert">
        <div class="fw-bold mb-1">Transaksi belum dapat diproses:</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-7 col-xl-8">
            <div class="pos-panel h-100 d-flex flex-column" style="min-height: 80vh;">
                <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <span class="small text-uppercase fw-bold" style="letter-spacing:.1em;color:var(--primary-color);">Katalog Toko</span>
                        <h4 class="font-serif fw-bold text-dark mb-0">Pilih Skincare</h4>
                    </div>
                    <span class="badge rounded-pill bg-white text-muted border px-3 py-2">Klik produk untuk menambahkan</span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="barcode_input" class="form-label small fw-bold text-muted mb-1">Scanner Barcode</label>
                        <input type="text" id="barcode_input" class="form-control custom-input w-100" placeholder="Scan SKU lalu Enter" autocomplete="off" autofocus>
                    </div>
                    <div class="col-md-5">
                        <label for="search_manual" class="form-label small fw-bold text-muted mb-1">Pencarian</label>
                        <input type="search" id="search_manual" class="form-control custom-input w-100" placeholder="Nama, SKU, jenis, atau merek...">
                    </div>
                    <div class="col-md-3">
                        <label for="sort_products" class="form-label small fw-bold text-muted mb-1">Urutkan nama</label>
                        <select id="sort_products" class="form-select custom-input">
                            <option value="az">A–Z</option>
                            <option value="za">Z–A</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-md-6">
                        <label for="category_filter" class="form-label small fw-bold text-muted mb-1">Jenis Skincare</label>
                        <select id="category_filter" class="form-select custom-input">
                            <option value="">Semua jenis</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="brand_filter" class="form-label small fw-bold text-muted mb-1">Merek</label>
                        <select id="brand_filter" class="form-select custom-input">
                            <option value="">Semua merek</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 flex-grow-1 overflow-auto hide-scroll pb-4" id="product_grid" style="max-height: 60vh;">
                    <!-- Dirender JS -->
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5 col-xl-4">
            <div class="cart-wrapper p-4 h-100" style="min-height: 80vh;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="font-serif fw-bold text-dark mb-0">Keranjang</h4>
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2"><span id="item_count">0</span> Item</span>
                </div>
                
                <div class="flex-grow-1 overflow-auto hide-scroll mb-4" style="max-height: 350px;" id="cart_body">
                    <div class="text-center text-muted mt-5 pt-4" id="empty_cart_msg">
                        <i class="fas fa-shopping-bag fa-3x mb-3" style="color: var(--border-color);"></i>
                        <p class="fw-medium">Belum ada produk yang dipilih.</p>
                    </div>
                </div>

                <div class="mt-auto pt-3 border-top" style="border-color: var(--border-color) !important;">
                    <form action="{{ route('pos.store') }}" method="POST" id="form_transaksi">
                        @csrf
                        <input type="hidden" name="cart_data" id="cart_data_input">
                        <input type="hidden" name="total_amount" id="total_amount_input" value="0">

                        <div class="small mb-2">
                            <div class="d-flex justify-content-between text-muted mb-1"><span>Subtotal produk</span><span id="gross_subtotal_display">Rp 0</span></div>
                            <div class="d-flex justify-content-between text-muted mb-1"><span>Diskon produk</span><span id="product_discount_display">−Rp 0</span></div>
                            <div class="d-flex justify-content-between fw-semibold text-dark border-top pt-2 mt-2"><span>Subtotal setelah diskon produk</span><span id="subtotal_display">Rp 0</span></div>
                        </div>

                        <div class="mb-3">
                            <label for="order_discount_input" class="fw-bold text-muted small mb-2">Diskon seluruh transaksi (Rp)</label>
                            <input type="number" class="form-control custom-input" name="discount_amount" id="order_discount_input" value="0" min="0" step="1" placeholder="0">
                            <div class="form-text">Dibatasi maksimal sebesar subtotal keranjang.</div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="text-muted fw-bold">Total Tagihan</span>
                            <h2 class="font-serif fw-bold text-dark mb-0" id="total_display">Rp 0</h2>
                        </div>
                        
                        <div class="mb-3">
                            <label class="fw-bold text-muted small mb-2">Uang Diterima (Rp)</label>
                            <input type="number" class="form-control custom-input fs-5 fw-bold" name="pay_amount" id="pay_amount" required min="0" placeholder="0">
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4 px-3 py-3 rounded-4" style="background: var(--bg-cream); border: 1px solid var(--border-color);">
                            <span class="text-muted fw-bold small">Kembalian</span>
                            <span class="font-serif fw-bold fs-5 text-dark" id="kembalian_display">Rp 0</span>
                        </div>
                        
                        <button type="submit" class="btn-pay w-100 fs-6" id="btn_bayar" disabled>
                            Proses Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const products = @json($products);
    let cart = []; 

    const barcodeInput = document.getElementById('barcode_input');
    const searchManual = document.getElementById('search_manual');
    const categoryFilter = document.getElementById('category_filter');
    const brandFilter = document.getElementById('brand_filter');
    const sortFilter = document.getElementById('sort_products');
    const productGrid = document.getElementById('product_grid');
    const cartBody = document.getElementById('cart_body');
    const emptyMsg = document.getElementById('empty_cart_msg');
    const totalDisplay = document.getElementById('total_display');
    const totalAmountInput = document.getElementById('total_amount_input');
    const orderDiscountInput = document.getElementById('order_discount_input');
    const grossSubtotalDisplay = document.getElementById('gross_subtotal_display');
    const productDiscountDisplay = document.getElementById('product_discount_display');
    const subtotalDisplay = document.getElementById('subtotal_display');
    const payAmountInput = document.getElementById('pay_amount');
    const kembalianDisplay = document.getElementById('kembalian_display');
    const btnBayar = document.getElementById('btn_bayar');
    const cartDataInput = document.getElementById('cart_data_input');
    const itemCount = document.getElementById('item_count');

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[character]);
    }

    const productCollator = new Intl.Collator('id', { sensitivity: 'base', numeric: true });
    const money = value => Number(value || 0).toLocaleString('id-ID');

    function renderProducts() {
        const selectedCategory = categoryFilter.value;
        const selectedBrand = brandFilter.value;
        const searchQuery = searchManual.value.trim().toLocaleLowerCase('id');
        const direction = sortFilter.value === 'za' ? -1 : 1;
        const filteredProducts = products.filter(product => {
            const matchCategory = !selectedCategory || String(product.category_id) === selectedCategory;
            const matchBrand = !selectedBrand || String(product.brand_id ?? '') === selectedBrand;
            const searchableFields = [product.name, product.sku, product.category?.name, product.brand?.name];
            const matchSearch = !searchQuery || searchableFields.some(value =>
                String(value ?? '').toLocaleLowerCase('id').includes(searchQuery)
            );

            return matchCategory && matchBrand && matchSearch;
        }).sort((a, b) => direction * productCollator.compare(a.name, b.name));

        productGrid.innerHTML = '';
        if (filteredProducts.length === 0) {
            const emptyState = document.createElement('div');
            emptyState.className = 'col-12 text-center text-muted py-5';
            emptyState.innerHTML = '<i class="fas fa-magnifying-glass fs-2 mb-3 d-block opacity-50"></i>Produk tidak ditemukan.';
            productGrid.appendChild(emptyState);
            return;
        }

        filteredProducts.forEach(product => {
            const card = document.createElement('div');
            card.className = 'col-6 col-md-4 col-xl-3';
            const imagePath = product.image ? '/storage/' + product.image : 'https://placehold.co/400x400/fdfbf7/c78b7d?text=No+Image';
            const productName = escapeHtml(product.name);
            const categoryName = escapeHtml(product.category?.name || 'Jenis belum diatur');
            const brandName = product.brand?.name ? escapeHtml(product.brand.name) : '';

            card.innerHTML = `
                <article class="card catalog-card border-0 shadow-sm bg-white h-100">
                    <div class="catalog-photo position-relative d-flex align-items-center justify-content-center">
                        <img src="${escapeHtml(imagePath)}" class="w-100 h-100" alt="${productName}" loading="lazy">
                        <span class="badge position-absolute top-0 end-0 m-2 rounded-pill bg-white text-dark shadow-sm">Stok ${Number(product.stock)}</span>
                    </div>
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            <span class="badge bg-light text-dark border rounded-pill">${categoryName}</span>
                            ${brandName ? `<span class="badge text-white rounded-pill" style="background-color:var(--primary-color);">${brandName}</span>` : ''}
                        </div>
                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="${productName}">${productName}</h6>
                        <div class="small text-muted mb-3">${escapeHtml(product.sku || 'SKU tidak tersedia')}</div>
                        <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
                            <span class="fw-bold text-dark">Rp ${money(product.selling_price)}</span>
                            <button type="button" class="btn btn-sm catalog-add px-3" data-product-id="${Number(product.id)}" aria-label="Tambah ${productName}">
                                <i class="fas fa-plus me-1"></i>Tambah
                            </button>
                        </div>
                    </div>
                </article>`;
            productGrid.appendChild(card);
        });
    }

    function addToCart(product) {
        const existingItem = cart.find(item => item.id === Number(product.id));
        if (existingItem) {
            if (existingItem.qty >= Number(product.stock)) {
                Swal.fire({ icon: 'warning', title: 'Stok tidak mencukupi', text: 'Jumlah produk di keranjang sudah mencapai stok tersedia.' });
                return;
            }
            existingItem.qty += 1;
        } else {
            cart.push({ id: Number(product.id), name: product.name, price: Number(product.selling_price), qty: 1, discount: 0, stock: Number(product.stock) });
        }
        updateCartUI();
    }

    function getTotals() {
        const grossSubtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        const productDiscount = cart.reduce((sum, item) => sum + item.discount, 0);
        const subtotal = grossSubtotal - productDiscount;
        const requestedOrderDiscount = Math.max(0, Number.parseInt(orderDiscountInput.value, 10) || 0);
        const orderDiscount = Math.min(requestedOrderDiscount, subtotal);

        return { grossSubtotal, productDiscount, subtotal, orderDiscount, total: subtotal - orderDiscount };
    }

    function renderCartLines() {
        cartBody.innerHTML = '';
        if (cart.length === 0) {
            cartBody.appendChild(emptyMsg);
            return;
        }

        cart.forEach((item, index) => {
            const line = document.createElement('div');
            const lineSubtotal = item.price * item.qty - item.discount;
            line.className = 'card border-0 rounded-4 bg-light mb-3';
            line.dataset.cartIndex = String(index);
            line.innerHTML = `
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between gap-2 mb-2">
                        <div class="min-w-0">
                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</h6>
                            <small class="text-muted">Rp ${money(item.price)} / unit</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 align-self-start" data-cart-action="remove" aria-label="Hapus ${escapeHtml(item.name)} dari keranjang"><i class="fas fa-trash-can"></i></button>
                    </div>
                    <div class="row g-2 cart-controls">
                        <div class="col-5">
                            <label class="form-label small text-muted mb-1">Jumlah</label>
                            <div class="input-group input-group-sm">
                                <button type="button" class="btn btn-white border" data-cart-action="step" data-delta="-1" aria-label="Kurangi jumlah">−</button>
                                <input type="number" class="form-control text-center" data-cart-action="quantity" min="1" max="${item.stock}" step="1" value="${item.qty}" aria-label="Jumlah ${escapeHtml(item.name)}">
                                <button type="button" class="btn btn-white border" data-cart-action="step" data-delta="1" aria-label="Tambah jumlah">+</button>
                            </div>
                            <small class="text-muted">Stok ${item.stock}</small>
                        </div>
                        <div class="col-7">
                            <label class="form-label small text-muted mb-1">Diskon produk (Rp/baris)</label>
                            <input type="number" class="form-control form-control-sm" data-cart-action="discount" min="0" max="${item.price * item.qty}" step="1" value="${item.discount}" aria-label="Diskon untuk ${escapeHtml(item.name)}">
                            <small class="text-muted">Nilai baris Rp ${money(lineSubtotal)}</small>
                        </div>
                    </div>
                </div>`;
            cartBody.appendChild(line);
        });
    }

    function updateTotals(clampOrderDiscount = false) {
        const totals = getTotals();
        if (clampOrderDiscount) orderDiscountInput.value = String(totals.orderDiscount);
        orderDiscountInput.max = String(totals.subtotal);
        grossSubtotalDisplay.textContent = 'Rp ' + money(totals.grossSubtotal);
        productDiscountDisplay.textContent = '−Rp ' + money(totals.productDiscount);
        subtotalDisplay.textContent = 'Rp ' + money(totals.subtotal);
        totalAmountInput.value = String(totals.total);
        totalDisplay.textContent = 'Rp ' + money(totals.total);
        cartDataInput.value = JSON.stringify(cart.map(item => ({ id: item.id, qty: item.qty, discount: item.discount })));
        itemCount.textContent = String(cart.reduce((sum, item) => sum + item.qty, 0));
        calculateKembalian();
        return totals;
    }

    function updateCartUI(clampOrderDiscount = true) {
        renderCartLines();
        return updateTotals(clampOrderDiscount);
    }

    function calculateKembalian() {
        const total = Number.parseInt(totalAmountInput.value, 10) || 0;
        const paid = Number.parseInt(payAmountInput.value, 10) || 0;
        const change = paid - total;
        const paymentIsValid = payAmountInput.value !== '' && change >= 0 && cart.length > 0;

        kembalianDisplay.textContent = paymentIsValid ? 'Rp ' + money(change) : 'Uang Kurang';
        kembalianDisplay.style.color = paymentIsValid ? 'var(--text-dark)' : 'red';
        btnBayar.disabled = !paymentIsValid;
    }

    renderProducts();
    updateCartUI();

    categoryFilter.addEventListener('change', renderProducts);
    brandFilter.addEventListener('change', renderProducts);
    sortFilter.addEventListener('change', renderProducts);
    searchManual.addEventListener('input', renderProducts);
    barcodeInput.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        const scannedSku = this.value.trim().toLocaleLowerCase('id');
        const product = products.find(item => String(item.sku ?? '').trim().toLocaleLowerCase('id') === scannedSku);
        if (product) addToCart(product);
        else Swal.fire({ icon: 'error', title: 'Barcode tidak dikenali', text: 'Pastikan barcode produk terdaftar sebagai SKU.' });
        this.value = '';
    });

    productGrid.addEventListener('click', event => {
        const button = event.target.closest('[data-product-id]');
        if (!button) return;
        const product = products.find(item => Number(item.id) === Number(button.dataset.productId));
        if (product) {
            addToCart(product);
            barcodeInput.focus({ preventScroll: true });
        }
    });

    cartBody.addEventListener('click', event => {
        const button = event.target.closest('[data-cart-action]');
        if (!button) return;
        const line = button.closest('[data-cart-index]');
        if (!line) return;
        const index = Number(line.dataset.cartIndex);
        const item = cart[index];
        if (!item) return;

        if (button.dataset.cartAction === 'remove') cart.splice(index, 1);
        if (button.dataset.cartAction === 'step') {
            item.qty = Math.max(1, Math.min(item.stock, item.qty + Number(button.dataset.delta)));
            item.discount = Math.min(item.discount, item.price * item.qty);
        }
        updateCartUI();
    });

    cartBody.addEventListener('change', event => {
        const input = event.target.closest('[data-cart-action="quantity"], [data-cart-action="discount"]');
        if (!input) return;
        const line = input.closest('[data-cart-index]');
        const item = line ? cart[Number(line.dataset.cartIndex)] : null;
        if (!item) return;

        if (input.dataset.cartAction === 'quantity') {
            item.qty = Math.max(1, Math.min(item.stock, Number.parseInt(input.value, 10) || 1));
            item.discount = Math.min(item.discount, item.price * item.qty);
        } else {
            item.discount = Math.max(0, Math.min(item.price * item.qty, Number.parseInt(input.value, 10) || 0));
        }
        updateCartUI();
    });

    orderDiscountInput.addEventListener('input', () => updateTotals(false));
    orderDiscountInput.addEventListener('change', () => updateTotals(true));
    payAmountInput.addEventListener('input', calculateKembalian);

    document.getElementById('form_transaksi').addEventListener('submit', function (event) {
        event.preventDefault();
        const totals = updateTotals(true);
        if (!payAmountInput.reportValidity() || Number.parseInt(payAmountInput.value, 10) < totals.total) {
            calculateKembalian();
            return;
        }

        const paid = Number.parseInt(payAmountInput.value, 10) || 0;
        const change = paid - totals.total;
        Swal.fire({
            title: 'Proses Pembayaran?',
            html: `Subtotal: <b>Rp ${money(totals.subtotal)}</b><br>Diskon transaksi: <b>−Rp ${money(totals.orderDiscount)}</b><br>Total tagihan: <b>Rp ${money(totals.total)}</b><br>Diterima: <b>Rp ${money(paid)}</b><br>Kembalian: <b>Rp ${money(change)}</b>`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary-color)',
            cancelButtonColor: 'var(--border-color)',
            confirmButtonText: 'Konfirmasi',
            cancelButtonText: '<span style="color:var(--text-dark)">Batal</span>',
            customClass: { confirmButton: 'rounded-pill px-4', cancelButton: 'rounded-pill px-4' }
        }).then(result => {
            if (result.isConfirmed) this.submit();
        });
    });
</script>

@if(session('print_id'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: 'Transaksi Berhasil',
            text: 'Cetak struk untuk pelanggan?',
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary-color)',
            cancelButtonColor: 'var(--border-color)',
            confirmButtonText: 'Cetak Struk',
            cancelButtonText: '<span style="color:var(--text-dark)">Lewati</span>',
            allowOutsideClick: false,
            customClass: { confirmButton: 'rounded-pill px-4', cancelButton: 'rounded-pill px-4' }
        }).then((result) => {
            if (result.isConfirmed) window.open("{{ route('history.print', session('print_id')) }}", "_blank");
        });
    });
</script>
@endif
@endsection
