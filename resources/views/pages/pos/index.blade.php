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
                <h4 class="font-serif fw-bold text-dark mb-4">Pilih Skincare</h4>

                <div class="row g-3 mb-4">
                    <div class="col-md-7">
                        <input type="text" id="barcode_input" class="form-control custom-input w-100" placeholder="Scan Barcode disini..." autofocus>
                    </div>
                    <div class="col-md-5">
                        <input type="text" id="search_manual" class="form-control custom-input w-100" placeholder="Cari nama produk...">
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
    const productGrid = document.getElementById('product_grid');
    const cartBody = document.getElementById('cart_body');
    const emptyMsg = document.getElementById('empty_cart_msg');
    const totalDisplay = document.getElementById('total_display');
    const totalAmountInput = document.getElementById('total_amount_input');
    const payAmountInput = document.getElementById('pay_amount');
    const kembalianDisplay = document.getElementById('kembalian_display');
    const btnBayar = document.getElementById('btn_bayar');
    const cartDataInput = document.getElementById('cart_data_input');
    const itemCount = document.getElementById('item_count');

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[character]);
    }

    function renderProducts() {
        productGrid.innerHTML = ''; 
        const selectedCategory = categoryFilter.value;
        const selectedBrand = brandFilter.value;
        const searchQuery = searchManual.value.trim().toLowerCase();

        let filteredProducts = products.filter(p => {
            const matchCategory = !selectedCategory || String(p.category_id) === selectedCategory;
            const matchBrand = !selectedBrand || String(p.brand_id ?? '') === selectedBrand;
            const searchableFields = [p.name, p.sku, p.category?.name, p.brand?.name];
            const matchSearch = !searchQuery || searchableFields.some(value =>
                String(value ?? '').toLowerCase().includes(searchQuery)
            );

            return matchCategory && matchBrand && matchSearch;
        });

        if(filteredProducts.length === 0) {
            productGrid.innerHTML = `<div class="col-12 text-center text-muted mt-5 pt-5 fw-medium">Produk tidak ditemukan.</div>`;
            return;
        }

        filteredProducts.forEach(p => {
            let card = document.createElement('div');
            card.className = 'col-6 col-md-4 col-xl-3 mb-3';
            let imagePath = p.image ? '/storage/' + p.image : 'https://placehold.co/400x400/fdfbf7/c78b7d?text=No+Image';
            const productName = escapeHtml(p.name);
            const categoryName = escapeHtml(p.category?.name || 'Jenis belum diatur');
            const brandName = p.brand?.name ? escapeHtml(p.brand.name) : '';
            
            card.innerHTML = `
                <div class="card border-0 shadow-sm p-2 d-flex flex-column bg-white h-100" 
                     style="border-radius: 1.25rem; cursor: pointer; transition: all 0.3s ease; border: 1px solid var(--border-color) !important;" 
                     onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='var(--primary-color)'" 
                     onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='var(--border-color)'" 
                     onclick="addToCartManual(${p.id})">
                    
                    <div class="position-relative w-100 mb-2" style="height: 120px; border-radius: 1rem; overflow: hidden; background: var(--bg-cream);">
                        <img src="${escapeHtml(imagePath)}" class="w-100 h-100" style="object-fit: cover;" alt="${productName}">
                        <span class="badge position-absolute top-0 start-0 m-2 shadow-sm" style="background-color: var(--primary-color); font-size: 0.7rem; font-weight: 500;">
                            Stok: ${p.stock}
                        </span>
                    </div>
                    
                    <div class="px-2 d-flex flex-column flex-grow-1 pb-1">
                        <div class="d-flex flex-wrap gap-1 mb-1">
                            <span class="badge bg-light text-dark border">${categoryName}</span>
                            ${brandName ? `<span class="badge text-white" style="background-color: var(--primary-color);">${brandName}</span>` : ''}
                        </div>
                        <h6 class="font-serif fw-bold text-dark mt-1 mb-1 text-truncate" style="font-size: 1rem;" title="${productName}">
                            ${productName}
                        </h6>
                        <div class="mt-auto">
                            <h6 class="fw-bold mb-0" style="color: var(--text-muted); font-size: 0.95rem;">
                                Rp ${p.selling_price.toLocaleString('id-ID')}
                            </h6>
                        </div>
                    </div>
                </div>
            `;
            productGrid.appendChild(card);
        });
    }
    renderProducts();

    categoryFilter.addEventListener('change', renderProducts);
    brandFilter.addEventListener('change', renderProducts);
    searchManual.addEventListener('input', renderProducts);
    barcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); 
            let sku = this.value.trim();
            let product = products.find(p => p.sku === sku);
            if (product) addToCart(product);
            else alert('Barcode tidak dikenali!');
            this.value = ''; 
        }
    });

    window.addToCartManual = function(id) {
        let product = products.find(p => p.id === id);
        if (product) addToCart(product);
    }

    function addToCart(product) {
        let existingItem = cart.find(item => item.id === product.id);
        if (existingItem) {
            if(existingItem.qty < product.stock) {
                existingItem.qty += 1;
                existingItem.subtotal = existingItem.qty * existingItem.price;
            } else { alert('Stok tidak mencukupi!'); }
        } else {
            cart.push({ id: product.id, name: product.name, price: product.selling_price, qty: 1, subtotal: product.selling_price });
        }
        updateCartUI();
    }

    function updateCartUI() {
        cartBody.innerHTML = '';
        let total = 0;
        let qtyCount = 0;

        if(cart.length === 0) {
            if(emptyMsg) cartBody.appendChild(emptyMsg);
        } else {
            cart.forEach((item, index) => {
                total += item.subtotal;
                qtyCount += item.qty;
                let div = document.createElement('div');
                div.className = 'd-flex justify-content-between align-items-center mb-3 pb-3';
                div.style.borderBottom = '1px solid var(--border-color)';
                div.innerHTML = `
                    <div class="pe-2">
                        <h6 class="font-serif fw-bold mb-1 text-dark" style="font-size: 0.95rem;">${item.name}</h6>
                        <span class="text-muted small">${item.qty} x Rp ${item.price.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="text-end" style="min-width: 80px;">
                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">Rp ${item.subtotal.toLocaleString('id-ID')}</h6>
                        <button type="button" class="btn btn-link text-danger p-0 border-0 small text-decoration-none" onclick="removeFromCart(${index})"><i class="fas fa-times-circle"></i> Batal</button>
                    </div>
                `;
                cartBody.appendChild(div);
            });
        }

        itemCount.innerText = qtyCount;
        totalAmountInput.value = total;
        totalDisplay.innerText = 'Rp ' + total.toLocaleString('id-ID');
        cartDataInput.value = JSON.stringify(cart.map(item => ({ id: item.id, qty: item.qty })));
        btnBayar.disabled = cart.length === 0;
        calculateKembalian(); 
    }

    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        updateCartUI();
    }

    payAmountInput.addEventListener('input', calculateKembalian);

    function calculateKembalian() {
        let total = parseInt(totalAmountInput.value) || 0;
        let bayar = parseInt(payAmountInput.value) || 0;
        let kembalian = bayar - total;

        if (kembalian >= 0 && cart.length > 0) {
            kembalianDisplay.innerText = 'Rp ' + kembalian.toLocaleString('id-ID');
            kembalianDisplay.style.color = 'var(--text-dark)';
            btnBayar.disabled = false;
        } else {
            kembalianDisplay.innerText = 'Uang Kurang';
            kembalianDisplay.style.color = 'red';
            btnBayar.disabled = true;
        }
    }

    document.getElementById('form_transaksi').addEventListener('submit', function(e) {
        e.preventDefault();
        let total = parseInt(totalAmountInput.value) || 0;
        let bayar = parseInt(payAmountInput.value) || 0;
        let kembalian = bayar - total;

        Swal.fire({
            title: 'Proses Pembayaran?',
            html: `Tagihan: <b>Rp ${total.toLocaleString('id-ID')}</b><br>Diterima: <b>Rp ${bayar.toLocaleString('id-ID')}</b><br>Kembalian: <b style="color:var(--text-dark);">Rp ${kembalian.toLocaleString('id-ID')}</b>`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary-color)',
            cancelButtonColor: 'var(--border-color)',
            confirmButtonText: 'Konfirmasi',
            cancelButtonText: '<span style="color:var(--text-dark)">Batal</span>',
            customClass: { confirmButton: 'rounded-pill px-4', cancelButton: 'rounded-pill px-4' }
        }).then((result) => {
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
