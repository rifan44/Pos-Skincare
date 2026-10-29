<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Jenis catatan</label>
        <select name="direction" class="form-select" required>
            <option value="payable" @selected(old('direction') === 'payable')>Utang usaha</option>
            <option value="receivable" @selected(old('direction') === 'receivable')>Piutang customer</option>
        </select>
        <div class="form-text">Utang = kewajiban toko. Piutang = uang yang masih harus diterima toko.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Jenis pihak</label>
        <select name="party_type" class="form-select" required>
            <option value="supplier" @selected(old('party_type') === 'supplier')>Supplier</option>
            <option value="customer" @selected(old('party_type') === 'customer')>Customer</option>
            <option value="other" @selected(old('party_type', 'other') === 'other')>Lainnya</option>
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label">Nama pihak</label>
        <input name="party_name" class="form-control" value="{{ old('party_name') }}" required maxlength="255">
    </div>
    <div class="col-md-4">
        <label class="form-label">Referensi</label>
        <input name="reference" class="form-control" value="{{ old('reference') }}" maxlength="255" placeholder="Invoice / catatan">
    </div>
    <div class="col-md-6">
        <label class="form-label">Nominal</label>
        <div class="input-group"><span class="input-group-text">Rp</span><input name="amount" class="form-control" inputmode="numeric" value="{{ old('amount') }}" required></div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Jatuh tempo</label>
        <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
    </div>
    <div class="col-12">
        <label class="form-label">Catatan</label>
        <textarea name="notes" class="form-control" rows="3" maxlength="2000">{{ old('notes') }}</textarea>
    </div>
</div>
