@extends('layouts.app')
@section('title', 'Kasir | Skincare POS')
@section('content')

<style>
    body { background-color: var(--bg-cream); }
    .pos-page { min-height: calc(100dvh - 88px); max-width: 1680px; margin-inline: auto; padding: 1.25rem 1rem 2rem !important; }
    .pos-intro { display: flex; justify-content: space-between; align-items: center; gap: 1.5rem; padding: 1.2rem 1.5rem; margin-bottom: 1.15rem; border: 1px solid rgba(199,139,125,.17); border-radius: 1.5rem; background: linear-gradient(110deg, color-mix(in srgb, var(--primary-color) 12%, white), #fff 62%, #fbf5f2); }
    .pos-kicker { display: inline-flex; align-items: center; gap: .45rem; margin-bottom: .25rem; color: var(--primary-color); font-size: .7rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .pos-title { margin: 0; color: var(--text-dark); font-size: clamp(1.5rem, 2vw, 2rem); font-weight: 700; }
    .pos-subtitle { margin: .2rem 0 0; color: var(--text-muted); font-size: .88rem; }
    .pos-intro-tip { display: flex; align-items: center; gap: .75rem; padding: .7rem 1rem; border: 1px solid rgba(199,139,125,.16); border-radius: 1rem; background: rgba(255,255,255,.8); color: var(--text-dark); }
    .pos-intro-tip-icon { width: 40px; height: 40px; display: grid; place-items: center; border-radius: 50%; background: color-mix(in srgb, var(--primary-color) 12%, white); color: var(--primary-color); font-size: 1.05rem; }
    .pos-intro-tip small { display: block; color: var(--text-muted); }
    .pos-layout { --bs-gutter-x: 1.15rem; --bs-gutter-y: 1rem; }
    .pos-panel { min-height: calc(100dvh - 188px); padding: 1.2rem; border: 1px solid var(--border-color); border-radius: 1.5rem; background: #fff; box-shadow: 0 14px 36px rgba(59,51,49,.055); }
    .catalog-heading { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: .9rem; }
    .catalog-heading h2 { margin: 0; font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 700; color: var(--text-dark); }
    .catalog-heading p { margin: .2rem 0 0; color: var(--text-muted); font-size: .83rem; }
    .catalog-availability { flex: 0 0 auto; padding: .45rem .75rem; border: 1px solid var(--border-color); border-radius: 999px; background: #fffaf7; color: var(--text-muted); font-size: .75rem; font-weight: 700; }
    .pos-toolbar { padding: .85rem; margin-bottom: .8rem; border-radius: 1.15rem; background: #faf7f3; }
    .pos-field-label { margin-bottom: .32rem; color: var(--text-muted); font-size: .72rem; font-weight: 800; letter-spacing: .025em; }
    .custom-input { min-height: 43px; padding: .55rem .75rem; border: 1px solid var(--border-color); border-radius: .8rem; background: #fff; color: var(--text-dark); transition: border-color .18s ease, box-shadow .18s ease; }
    .custom-input:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 17%, transparent); }
    .search-group .input-group-text { border: 1px solid var(--border-color); border-right: 0; border-radius: .8rem 0 0 .8rem; color: var(--text-muted); }
    .search-group .custom-input { border-left: 0; border-radius: 0 .8rem .8rem 0; }
    .pos-toolbar .form-select { min-width: 0; }
    .pos-instruction { color: var(--text-muted); white-space: nowrap; }
    .pos-panel #product_grid { align-content: start; padding: .2rem .1rem .45rem; }
    .catalog-card { overflow: hidden; border: 1px solid var(--border-color) !important; border-radius: 1.2rem !important; background: #fff; box-shadow: 0 5px 18px rgba(59,51,49,.045) !important; transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
    .catalog-card:hover { transform: translateY(-3px); border-color: color-mix(in srgb, var(--primary-color) 45%, white) !important; box-shadow: 0 14px 28px rgba(59,51,49,.11) !important; }
    .catalog-photo { height: 132px; background: linear-gradient(145deg, #fbf6f2, #f5e9e4); }
    .catalog-photo img { object-fit: contain; padding: .7rem; }
    .catalog-card .card-body { padding: .8rem !important; }
    .catalog-card h6 { line-height: 1.35; }
    .catalog-card .badge { font-size: .65rem; font-weight: 700; }
    .catalog-add { width: 38px; height: 38px; display: inline-grid; place-items: center; flex: 0 0 auto; border: 0; border-radius: 50%; background: color-mix(in srgb, var(--primary-color) 13%, white); color: var(--primary-color); transition: color .18s ease, background .18s ease, transform .18s ease; }
    .catalog-add:hover { transform: scale(1.05); background: var(--primary-color); color: #fff; }
    .cart-wrapper { position: sticky; top: 6rem; display: flex; height: calc(100dvh - 7rem); max-height: 820px; min-height: 390px; flex-direction: column; overflow: hidden; padding: 1rem !important; border: 1px solid rgba(199,139,125,.2); border-radius: 1.5rem; background: #fff; box-shadow: 0 18px 42px rgba(59,51,49,.09); }
    .cart-heading { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding-bottom: .8rem; border-bottom: 1px solid var(--border-color); }
    .cart-heading-copy { display: flex; align-items: center; gap: .7rem; }
    .cart-heading-icon { width: 42px; height: 42px; display: grid; place-items: center; border-radius: 14px; background: color-mix(in srgb, var(--primary-color) 12%, white); color: var(--primary-color); }
    .cart-heading h2 { margin: 0; font-family: 'Playfair Display', serif; font-size: 1.25rem; font-weight: 700; color: var(--text-dark); }
    .cart-heading small { color: var(--text-muted); }
    .cart-count { padding: .45rem .75rem; border: 1px solid color-mix(in srgb, var(--primary-color) 14%, white); border-radius: 999px; background: color-mix(in srgb, var(--primary-color) 9%, white); color: var(--primary-color); font-size: .78rem; font-weight: 800; white-space: nowrap; }
    .cart-items { flex: 1 1 auto; min-height: 52px; overflow-y: auto; padding: .7rem .2rem .55rem 0; scrollbar-color: var(--border-color) transparent; scrollbar-width: thin; }
    .cart-items::-webkit-scrollbar { width: 6px; }
    .cart-items::-webkit-scrollbar-thumb { border-radius: 999px; background: var(--border-color); }
    .cart-checkout { flex: 0 0 auto; padding-top: .8rem; border-top: 1px solid var(--border-color); }
    .cart-summary { margin-bottom: .7rem; font-size: .82rem; }
    .cart-summary .summary-subtotal { padding-top: .45rem; margin-top: .35rem; border-top: 1px dashed var(--border-color); font-size: .9rem; }
    .cart-fields { margin-bottom: .65rem; }
    .cart-fields .custom-input, .cart-fields .input-group-text { min-height: 39px; padding: .35rem .55rem; }
    .cart-fields .custom-input { border-radius: .75rem 0 0 .75rem !important; }
    .cart-fields .input-group-text { border: 1px solid var(--border-color); border-radius: 0 .75rem .75rem 0; color: var(--primary-color); font-weight: 800; }
    .cart-fields .cash-group .custom-input { border-radius: 0 .75rem .75rem 0 !important; }
    .cart-fields .cash-group .input-group-text { border-radius: .75rem 0 0 .75rem; color: var(--text-muted); font-weight: 600; }
    .cart-total { padding: .7rem .85rem; margin-bottom: .55rem; border: 1px solid color-mix(in srgb, var(--primary-color) 22%, var(--border-color)); border-radius: 1rem; background: linear-gradient(105deg, color-mix(in srgb, var(--primary-color) 9%, white), #fffaf8); }
    .cart-total-label { color: var(--text-muted); font-size: .8rem; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; }
    .cart-total #total_display { color: var(--primary-color) !important; font-size: 1.45rem; }
    .cart-change { margin-bottom: .65rem !important; color: var(--text-muted); font-size: .84rem; }
    .btn-pay { min-height: 46px; border: 0; border-radius: .9rem; background: var(--primary-color); color: #fff; font-weight: 800; letter-spacing: .02em; transition: background .18s ease, transform .18s ease, box-shadow .18s ease; }
    .btn-pay:hover:not(:disabled) { transform: translateY(-1px); background: var(--primary-hover); box-shadow: 0 8px 20px color-mix(in srgb, var(--primary-color) 26%, transparent); }
    .btn-pay:disabled { background: #eae5e2; color: #aa9e9a; }
    .cart-line { border: 1px solid var(--border-color) !important; border-radius: 1rem !important; background: #fff !important; box-shadow: none !important; }
    .cart-line .card-body { padding: .65rem .75rem !important; }
    .cart-line-title { font-size: .9rem; line-height: 1.2; }
    .cart-line-price { color: var(--text-muted); font-size: .75rem; white-space: nowrap; }
    .cart-mini-label { display: block; margin-bottom: .2rem; color: var(--text-muted); font-size: .66rem; font-weight: 800; line-height: 1; }
    .cart-controls { align-items: center !important; }
    .cart-controls .form-control { min-width: 0; }
    .cart-controls .btn { border-radius: .65rem; }
    .cart-controls .input-group-sm > .btn, .cart-controls .input-group-sm > .form-control, .cart-controls .input-group-sm > .input-group-text { min-height: 34px; padding-top: .25rem; padding-bottom: .25rem; }
    .cart-controls .input-group-text { border-color: var(--border-color); }
    .cart-controls .custom-input { border-radius: .65rem 0 0 .65rem !important; }
    .cart-controls .input-group-text:last-child { border-radius: 0 .65rem .65rem 0; background: #faf7f3 !important; }
    .cart-empty { display: grid; min-height: 150px; place-content: center; text-align: center; color: var(--text-muted); }
    .cart-empty-icon { width: 50px; height: 50px; display: grid; place-items: center; margin: 0 auto .65rem; border-radius: 50%; background: #faf3ef; color: color-mix(in srgb, var(--primary-color) 65%, white); font-size: 1.15rem; }
    @media (max-height: 760px) and (min-width: 992px) {
        .pos-page { padding-top: .8rem !important; }
        .pos-intro { padding: .8rem 1.1rem; margin-bottom: .8rem; }
        .pos-panel { min-height: calc(100dvh - 164px); padding: .9rem; }
        .cart-wrapper { position: static; height: auto; max-height: none; min-height: 0; padding: .8rem !important; }
        .catalog-photo { height: 108px; }
        .catalog-card .card-body { padding: .65rem !important; }
        .cart-items { flex: 0 1 auto; max-height: min(40dvh, 300px); min-height: 160px; }
    }
    @media (max-width: 1199.98px) {
        html, body { overflow-x: hidden; }
        .pos-page { padding: 1rem .8rem 1.5rem !important; }
        .pos-intro { padding: 1rem; }
        .pos-panel { min-height: 0; padding: 1rem; }
        .cart-wrapper { position: static; height: auto; max-height: none; min-height: 0; padding: 1rem !important; }
        .cart-items { flex: 0 1 auto; max-height: min(42dvh, 360px); min-height: 160px; }
    }
    @media (max-width: 575.98px) {
        .pos-page { padding: .75rem .55rem 1.25rem !important; }
        .pos-intro { align-items: flex-start; padding: .9rem; border-radius: 1.15rem; }
        .pos-intro-tip { padding: .55rem; }
        .pos-intro-tip-icon { width: 36px; height: 36px; }
        .pos-intro-tip small { display: none; }
        .pos-title { font-size: 1.45rem; }
        .pos-subtitle { max-width: 220px; font-size: .78rem; }
        .pos-panel, .cart-wrapper { padding: .8rem !important; border-radius: 1.2rem; }
        .catalog-heading { align-items: flex-start; }
        .catalog-heading h2 { font-size: 1.15rem; }
        .catalog-heading p { font-size: .75rem; }
        .catalog-availability { padding: .35rem .55rem; font-size: .68rem; }
        .pos-toolbar { padding: .65rem; }
        .catalog-photo { height: 102px; }
        .catalog-card .card-body { padding: .6rem !important; }
        .catalog-card .badge { font-size: .6rem; }
        .cart-count { padding: .4rem .6rem; }
    }
</style>

<div class="container-fluid py-2 px-2 px-lg-3 pos-page">
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

    <div class="pos-intro">
        <div>
            <span class="pos-kicker"><i class="fas fa-sparkles"></i> Point of sale</span>
            <h1 class="pos-title font-serif">Transaksi baru</h1>
            <p class="pos-subtitle">Pilih produk dari katalog, atur jumlah, lalu selesaikan pembayaran.</p>
        </div>
        <div class="pos-intro-tip">
            <span class="pos-intro-tip-icon"><i class="fas fa-barcode"></i></span>
            <div><strong class="small">Scanner barcode</strong><small>Scan SKU lalu tekan Enter</small></div>
        </div>
    </div>

    <div class="row g-3 pos-layout">
        <div class="col-12 col-xl-7">
            <div class="pos-panel d-flex flex-column">
                <div class="catalog-heading">
                    <div>
                        <h2>Katalog Produk</h2>
                        <p>Cari berdasarkan nama, SKU, jenis, atau merek.</p>
                    </div>
                    <span class="catalog-availability"><i class="fas fa-box-open me-1"></i>{{ $products->count() }} tersedia</span>
                </div>

                <div class="pos-toolbar">
                    <div class="row g-2 pos-filter-row">
                        <div class="col-6 col-md-3 order-1">
                            <label for="barcode_input" class="form-label pos-field-label">Barcode</label>
                            <input type="text" id="barcode_input" class="form-control custom-input w-100" placeholder="Scan SKU" autocomplete="off" autofocus>
                        </div>
                        <div class="col-12 col-md-6 order-3 order-md-2">
                            <label for="search_manual" class="form-label pos-field-label">Cari Produk</label>
                            <div class="input-group search-group">
                                <span class="input-group-text bg-white"><i class="fas fa-magnifying-glass"></i></span>
                                <input type="search" id="search_manual" class="form-control custom-input" placeholder="Nama, SKU, jenis, atau merek">
                            </div>
                        </div>
                        <div class="col-6 col-md-3 order-2 order-md-3">
                            <label for="sort_products" class="form-label pos-field-label">Urutkan</label>
                            <select id="sort_products" class="form-select custom-input">
                                <option value="az">A–Z</option>
                                <option value="za">Z–A</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-3 pb-2" id="product_grid">
                    <!-- Dirender JS -->
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="cart-wrapper">
                <div class="cart-heading">
                    <div class="cart-heading-copy">
                        <span class="cart-heading-icon"><i class="fas fa-bag-shopping"></i></span>
                        <div><h2>Keranjang</h2><small>Rincian pesanan</small></div>
                    </div>
                    <span class="cart-count"><span id="item_count">0</span> item</span>
                </div>
                
                <div class="cart-items" id="cart_body">
                    <div class="cart-empty" id="empty_cart_msg">
                        <span class="cart-empty-icon"><i class="fas fa-basket-shopping"></i></span>
                        <small>Keranjang masih kosong</small>
                    </div>
                </div>

                <div class="cart-checkout mt-auto">
                    <form action="{{ route('pos.store') }}" method="POST" id="form_transaksi">
                        @csrf
                        <input type="hidden" name="cart_data" id="cart_data_input">
                        <input type="hidden" name="total_amount" id="total_amount_input" value="0">
                        <input type="hidden" name="discount_amount" id="discount_amount_input" value="0">

                        <div class="small cart-summary">
                            <div class="d-flex justify-content-between text-muted"><span>Diskon item</span><span id="product_discount_display">−Rp 0</span></div>
                            <div class="d-flex justify-content-between fw-bold text-dark summary-subtotal"><span>Subtotal</span><span id="subtotal_display">Rp 0</span></div>
                            <div class="d-flex justify-content-between text-muted mt-1"><span>Diskon transaksi</span><span id="order_discount_display">−Rp 0</span></div>
                        </div>

                        <div class="row g-2 cart-fields">
                            <div class="col-6">
                                <label for="customer_type" class="form-label pos-field-label">Jenis pelanggan</label>
                                <select name="customer_type" id="customer_type" class="form-select custom-input" required>
                                    <option value="regular">Customer biasa</option>
                                    <option value="reseller">Reseller</option>
                                    <option value="internal">Internal</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="customer_id" class="form-label pos-field-label">Customer tersimpan <span class="fw-normal">(opsional)</span></label>
                                <select name="customer_id" id="customer_id" class="form-select custom-input">
                                    <option value="">Customer baru / tanpa master</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" data-type="{{ $customer->customer_type }}" data-name="{{ $customer->name }}" data-phone="{{ $customer->phone }}">{{ $customer->name }}{{ $customer->phone ? ' · '.$customer->phone : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="payment_method" class="form-label pos-field-label">Pembayaran</label>
                                <select name="payment_method" id="payment_method" class="form-select custom-input" required>
                                    <option value="cash">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="qris">QRIS</option>
                                    <option value="debit">Debit</option>
                                    <option value="credit">Kredit / Piutang</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="customer_name" class="form-label pos-field-label">Nama customer <span class="fw-normal">(opsional)</span></label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control custom-input" maxlength="255" placeholder="Nama customer / reseller">
                            </div>
                            <div class="col-6">
                                <label for="customer_phone" class="form-label pos-field-label">WhatsApp <span class="fw-normal">(opsional)</span></label>
                                <input type="tel" name="customer_phone" id="customer_phone" class="form-control custom-input" maxlength="30" placeholder="08xx...">
                            </div>
                            <div class="col-6">
                                <label for="order_discount_input" class="form-label pos-field-label">Diskon total</label>
                                <div class="input-group percentage-group">
                                    <input type="text" inputmode="decimal" maxlength="6" class="form-control custom-input" id="order_discount_input" value="0" placeholder="0" aria-describedby="order_discount_preview">
                                    <span class="input-group-text bg-white">%</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="pay_amount" class="form-label pos-field-label">Diterima</label>
                                <div class="input-group cash-group">
                                    <span class="input-group-text bg-white">Rp</span>
                                    <input type="text" inputmode="numeric" autocomplete="off" class="form-control custom-input fw-bold" name="pay_amount" id="pay_amount" required placeholder="0">
                                </div>
                            </div>
                        </div>

                        <small class="visually-hidden" id="order_discount_preview">Masukkan diskon transaksi dari 0 sampai 100 persen.</small>

                        <div class="cart-total d-flex justify-content-between align-items-center">
                            <span class="cart-total-label">Total bayar</span>
                            <h5 class="font-serif fw-bold mb-0" id="total_display">Rp 0</h5>
                        </div>

                        <div class="cart-change d-flex justify-content-between align-items-center mb-2 px-1">
                            <span class="fw-bold">Kembali</span>
                            <span class="font-serif fw-bold text-dark" id="kembalian_display">Rp 0</span>
                        </div>
                        
                        <button type="submit" class="btn-pay w-100" id="btn_bayar" disabled>
                            Bayar
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
    const sortFilter = document.getElementById('sort_products');
    const productGrid = document.getElementById('product_grid');
    const cartBody = document.getElementById('cart_body');
    const emptyMsg = document.getElementById('empty_cart_msg');
    const totalDisplay = document.getElementById('total_display');
    const totalAmountInput = document.getElementById('total_amount_input');
    const orderDiscountInput = document.getElementById('order_discount_input');
    const orderDiscountDisplay = document.getElementById('order_discount_display');
    const productDiscountDisplay = document.getElementById('product_discount_display');
    const subtotalDisplay = document.getElementById('subtotal_display');
    const payAmountInput = document.getElementById('pay_amount');
    const paymentMethodInput = document.getElementById('payment_method');
    const orderDiscountPreview = document.getElementById('order_discount_preview');
    const kembalianDisplay = document.getElementById('kembalian_display');
    const btnBayar = document.getElementById('btn_bayar');
    const cartDataInput = document.getElementById('cart_data_input');
    const itemCount = document.getElementById('item_count');
    const orderDiscountAmountInput = document.getElementById('discount_amount_input');

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[character]);
    }

    const productCollator = new Intl.Collator('id', { sensitivity: 'base', numeric: true });
    const money = value => Number(value || 0).toLocaleString('id-ID');
    const amount = input => Number.parseInt(String(input.value || '').replace(/\D/g, ''), 10) || 0;
    const normalizeAmountInput = input => { input.value = String(input.value || '').replace(/\D/g, ''); };
    const percentage = input => {
        const value = Number.parseFloat(String(input.value ?? '').replace(',', '.'));
        return Number.isFinite(value) ? Math.round(Math.min(100, Math.max(0, value)) * 100) / 100 : 0;
    };
    const discountAmount = (base, rate) => Math.min(base, Math.round(base * rate / 100));
    const productDiscountAmount = item => discountAmount(item.price * item.qty, item.discountPercent);

    function renderProducts() {
        const searchQuery = searchManual.value.trim().toLocaleLowerCase('id');
        const direction = sortFilter.value === 'za' ? -1 : 1;
        const filteredProducts = products.filter(product => {
            const searchableFields = [product.name, product.sku, product.category?.name, product.brand?.name];
            const matchSearch = !searchQuery || searchableFields.some(value =>
                String(value ?? '').toLocaleLowerCase('id').includes(searchQuery)
            );

            return matchSearch;
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
            card.className = 'col-6 col-md-4 col-xl-3 col-xxl-2';
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
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex flex-wrap gap-1 mb-1">
                            <span class="badge bg-light text-dark border rounded-pill">${categoryName}</span>
                            ${brandName ? `<span class="badge text-white rounded-pill" style="background-color:var(--primary-color);">${brandName}</span>` : ''}
                        </div>
                        <h6 class="fw-bold text-dark mb-2 text-truncate" title="${productName}">${productName}</h6>
                        <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
                            <span class="fw-bold text-dark text-nowrap">Rp ${money(product.selling_price)}</span>
                            <button type="button" class="btn btn-sm catalog-add" data-product-id="${Number(product.id)}" aria-label="Tambah ${productName} ke keranjang" title="Tambah ke keranjang">
                                <i class="fas fa-plus"></i>
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
            cart.push({ id: Number(product.id), name: product.name, price: Number(product.selling_price), qty: 1, discountPercent: 0, stock: Number(product.stock) });
        }
        updateCartUI();
    }

    function getTotals() {
        const grossSubtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        const productDiscount = cart.reduce((sum, item) => sum + productDiscountAmount(item), 0);
        const subtotal = grossSubtotal - productDiscount;
        const orderDiscount = discountAmount(subtotal, percentage(orderDiscountInput));

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
            line.className = 'card cart-line mb-2';
            line.dataset.cartIndex = String(index);
            line.innerHTML = `
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        <div class="d-flex align-items-baseline gap-2 min-w-0">
                            <h6 class="cart-line-title fw-bold text-dark mb-0 text-truncate" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</h6>
                            <small class="cart-line-price text-muted">Rp ${money(item.price)}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 align-self-start" data-cart-action="remove" aria-label="Hapus ${escapeHtml(item.name)} dari keranjang"><i class="fas fa-trash-can"></i></button>
                    </div>
                    <div class="row g-2 cart-controls">
                        <div class="col-5">
                            <div class="input-group input-group-sm">
                                <button type="button" class="btn btn-white border" data-cart-action="step" data-delta="-1" aria-label="Kurangi jumlah">−</button>
                                <input type="number" class="form-control text-center" data-cart-action="quantity" min="1" max="${item.stock}" step="1" value="${item.qty}" aria-label="Jumlah ${escapeHtml(item.name)}">
                                <button type="button" class="btn btn-white border" data-cart-action="step" data-delta="1" aria-label="Tambah jumlah">+</button>
                            </div>
                        </div>
                        <div class="col-7">
                            <label class="cart-mini-label">Diskon</label>
                            <div class="input-group input-group-sm">
                                <input type="text" inputmode="decimal" maxlength="6" class="form-control custom-input" data-cart-action="discount-percent" value="${item.discountPercent}" aria-label="Persentase diskon untuk ${escapeHtml(item.name)}">
                                <span class="input-group-text bg-white">%</span>
                            </div>
                        </div>
                    </div>
                </div>`;
            cartBody.appendChild(line);
        });
    }

    function updateTotals(clampOrderDiscount = false) {
        const totals = getTotals();
        if (clampOrderDiscount) orderDiscountInput.value = String(percentage(orderDiscountInput));
        productDiscountDisplay.textContent = '−Rp ' + money(totals.productDiscount);
        subtotalDisplay.textContent = 'Rp ' + money(totals.subtotal);
        orderDiscountDisplay.textContent = '−Rp ' + money(totals.orderDiscount);
        totalAmountInput.value = String(totals.total);
        orderDiscountAmountInput.value = String(totals.orderDiscount);
        totalDisplay.textContent = 'Rp ' + money(totals.total);
        orderDiscountPreview.textContent = `Diskon transaksi: ${percentage(orderDiscountInput)}%. Nilai rupiah dihitung otomatis.`;
        cartDataInput.value = JSON.stringify(cart.map(item => ({ id: item.id, qty: item.qty, discount: productDiscountAmount(item) })));
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
        const paid = amount(payAmountInput);
        const change = paid - total;
        const isCredit = paymentMethodInput.value === 'credit';
        const paymentIsValid = payAmountInput.value.trim() !== '' && (isCredit || change >= 0) && cart.length > 0;

        if (isCredit && paymentIsValid && change < 0) {
            kembalianDisplay.textContent = 'Piutang Rp ' + money(Math.abs(change));
        } else {
            kembalianDisplay.textContent = paymentIsValid ? 'Rp ' + money(change) : 'Uang Kurang';
        }
        kembalianDisplay.style.color = paymentIsValid ? 'var(--text-dark)' : 'red';
        btnBayar.disabled = !paymentIsValid;
    }

    renderProducts();
    updateCartUI();

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
        }
        updateCartUI();
    });

    cartBody.addEventListener('input', event => {
        const input = event.target.closest('[data-cart-action="discount-percent"]');
        if (!input) return;
        const line = input.closest('[data-cart-index]');
        const item = line ? cart[Number(line.dataset.cartIndex)] : null;
        if (!item) return;

        item.discountPercent = percentage(input);
        updateTotals(false);
    });

    cartBody.addEventListener('change', event => {
        const input = event.target.closest('[data-cart-action="discount-percent"]');
        if (!input) return;
        const line = input.closest('[data-cart-index]');
        const item = line ? cart[Number(line.dataset.cartIndex)] : null;
        if (!item) return;

        item.discountPercent = percentage(input);
        input.value = String(item.discountPercent);
        updateTotals(false);
    });

    cartBody.addEventListener('change', event => {
        const input = event.target.closest('[data-cart-action="quantity"]');
        if (!input) return;
        const line = input.closest('[data-cart-index]');
        const item = line ? cart[Number(line.dataset.cartIndex)] : null;
        if (!item) return;

        item.qty = Math.max(1, Math.min(item.stock, Number.parseInt(input.value, 10) || 1));
        updateCartUI();
    });

    orderDiscountInput.addEventListener('input', () => {
        updateTotals(false);
    });
    orderDiscountInput.addEventListener('change', () => {
        orderDiscountInput.value = String(percentage(orderDiscountInput));
        updateTotals(false);
    });
    payAmountInput.addEventListener('input', () => {
        normalizeAmountInput(payAmountInput);
        calculateKembalian();
    });
    paymentMethodInput.addEventListener('change', calculateKembalian);

    document.getElementById('customer_id').addEventListener('change', event => {
        const option = event.target.selectedOptions[0];
        const customerType = document.getElementById('customer_type');
        const customerName = document.getElementById('customer_name');
        const customerPhone = document.getElementById('customer_phone');
        const isStoredCustomer = Boolean(event.target.value);

        if (isStoredCustomer) {
            customerType.value = option.dataset.type || 'regular';
            customerName.value = option.dataset.name || '';
            customerPhone.value = option.dataset.phone || '';
        }

        customerName.readOnly = isStoredCustomer;
        customerPhone.readOnly = isStoredCustomer;
        customerType.disabled = isStoredCustomer;
    });

    document.getElementById('form_transaksi').addEventListener('submit', function (event) {
        event.preventDefault();
        const totals = updateTotals(true);
        const isCredit = paymentMethodInput.value === 'credit';
        if (!payAmountInput.reportValidity() || (!isCredit && amount(payAmountInput) < totals.total)) {
            calculateKembalian();
            return;
        }

        const paid = amount(payAmountInput);
        const change = paid - totals.total;
        const paymentSummary = isCredit && change < 0
            ? `Piutang: <b>Rp ${money(Math.abs(change))}</b>`
            : `Kembalian: <b>Rp ${money(Math.max(0, change))}</b>`;
        Swal.fire({
            title: 'Proses Pembayaran?',
            html: `Subtotal: <b>Rp ${money(totals.subtotal)}</b><br>Diskon transaksi: <b>−Rp ${money(totals.orderDiscount)}</b><br>Total tagihan: <b>Rp ${money(totals.total)}</b><br>Diterima: <b>Rp ${money(paid)}</b><br>${paymentSummary}`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary-color)',
            cancelButtonColor: 'var(--border-color)',
            confirmButtonText: 'Konfirmasi',
            cancelButtonText: '<span style="color:var(--text-dark)">Batal</span>',
            customClass: { confirmButton: 'rounded-pill px-4', cancelButton: 'rounded-pill px-4' }
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('customer_type').disabled = false;
                orderDiscountInput.value = String(percentage(orderDiscountInput));
                payAmountInput.value = String(amount(payAmountInput));
                this.submit();
            }
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
