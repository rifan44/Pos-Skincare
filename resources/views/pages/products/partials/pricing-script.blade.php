<script>
    (() => {
        const purchaseDisplay = document.getElementById('purchase_price_display');
        const sellingDisplay = document.getElementById('selling_price_display');
        const purchaseValue = document.querySelector('[name="purchase_price"]');
        const sellingValue = document.querySelector('[name="selling_price"]');
        const marginInput = document.querySelector('[data-margin-input]');
        const priceSource = document.querySelector('[data-price-source]');
        const marginHint = document.querySelector('[data-margin-hint]');
        const purchasePreview = document.getElementById('purchase_price_preview');
        const sellingPreview = document.getElementById('selling_price_preview');
        const digits = value => String(value || '').replace(/\D/g, '');
        const rupiah = value => (Number(value) || 0).toLocaleString('id-ID');

        function signedInteger(value) {
            const raw = String(value || '').trim();
            const numeric = digits(raw);
            if (!numeric) return null;

            return raw.startsWith('-') ? -Number(numeric) : Number(numeric);
        }

        function updatePreviews() {
            purchasePreview.textContent = 'Rp ' + rupiah(purchaseValue.value);
            sellingPreview.textContent = 'Rp ' + rupiah(sellingValue.value);
        }

        function setSellingPrice(value) {
            const amount = Math.round(value);
            if (amount < 0 || amount > 2147483647) {
                marginHint.textContent = 'Hasil harga jual di luar batas nominal produk.';

                return false;
            }

            sellingValue.value = String(amount);
            sellingDisplay.value = rupiah(amount);

            return true;
        }

        function syncMarginFromPrice() {
            const purchase = Number(purchaseValue.value) || 0;
            const selling = sellingValue.value === '' ? null : Number(sellingValue.value);

            if (selling === null) {
                marginInput.value = '';
                marginHint.textContent = 'Isi harga jual atau margin; nilai lainnya akan mengikuti otomatis.';
            } else {
                const rawMargin = purchase > 0 ? ((selling - purchase) / purchase) * 100 : 0;
                marginInput.value = String(Math.sign(rawMargin) * Math.round(Math.abs(rawMargin)));
                marginHint.textContent = `Harga jual Rp ${rupiah(selling)} menghasilkan margin ${marginInput.value}%.`;
            }

            updatePreviews();
        }

        function syncPriceFromMargin() {
            const purchase = Number(purchaseValue.value) || 0;
            const margin = signedInteger(marginInput.value);
            if (margin === null) return;

            if (margin < -100) {
                marginHint.textContent = 'Margin minimal -100% agar harga jual tidak negatif.';

                return;
            }

            const selling = Math.round(purchase * (100 + margin) / 100);
            if (!setSellingPrice(selling)) return;

            marginHint.textContent = purchase > 0
                ? `Margin ${margin}% menghasilkan harga jual Rp ${rupiah(selling)}.`
                : 'Isi HPP lebih dari Rp 0 untuk menghitung harga jual dari margin.';
            updatePreviews();
        }

        function updateFromPurchase() {
            if (priceSource.value === 'unchanged') priceSource.value = 'selling_price';

            if (priceSource.value === 'margin_percent' && marginInput.value !== '') {
                syncPriceFromMargin();
            } else {
                priceSource.value = 'selling_price';
                syncMarginFromPrice();
            }
        }

        [purchaseDisplay, sellingDisplay].forEach((display, index) => {
            const hidden = index === 0 ? purchaseValue : sellingValue;
            display.addEventListener('focus', () => { display.value = hidden.value; });
            display.addEventListener('input', () => {
                hidden.value = digits(display.value);
                display.value = hidden.value;

                if (index === 0) {
                    updateFromPurchase();
                } else {
                    priceSource.value = 'selling_price';
                    syncMarginFromPrice();
                }
            });
            display.addEventListener('blur', () => {
                display.value = hidden.value === '' ? '' : rupiah(hidden.value);
                updatePreviews();
            });
        });

        marginInput.addEventListener('input', () => {
            const raw = String(marginInput.value || '').trim();
            const negative = raw.startsWith('-');
            const numeric = digits(raw);
            marginInput.value = numeric ? (negative ? '-' : '') + numeric : (negative ? '-' : '');
            priceSource.value = 'margin_percent';
            syncPriceFromMargin();
        });
        marginInput.addEventListener('change', () => {
            if (marginInput.value === '' || marginInput.value === '-') marginInput.value = '0';
            syncPriceFromMargin();
        });

        document.getElementById('product_form').addEventListener('submit', () => {
            purchaseValue.value = digits(purchaseDisplay.value);
            sellingValue.value = digits(sellingDisplay.value);
        });

        purchaseDisplay.value = purchaseValue.value === '' ? '' : rupiah(purchaseValue.value);
        sellingDisplay.value = sellingValue.value === '' ? '' : rupiah(sellingValue.value);

        if (priceSource.value === 'selling_price' && sellingValue.value !== '') syncMarginFromPrice();
        if (priceSource.value === 'margin_percent' && marginInput.value !== '') syncPriceFromMargin();
        updatePreviews();
    })();
</script>
