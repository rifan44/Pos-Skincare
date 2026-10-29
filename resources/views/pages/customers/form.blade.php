<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Nama customer</label>
        <input name="name" class="form-control" value="{{ old('name', $customer?->name) }}" required maxlength="255">
    </div>
    <div class="col-md-4">
        <label class="form-label">Tipe</label>
        <select name="customer_type" class="form-select" required>
            @foreach(['regular' => 'Customer biasa', 'reseller' => 'Reseller', 'internal' => 'Internal', 'other' => 'Lainnya'] as $value => $label)
                <option value="{{ $value }}" @selected(old('customer_type', $customer?->customer_type ?? 'regular') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">WhatsApp / Telepon</label>
        <input name="phone" class="form-control" value="{{ old('phone', $customer?->phone) }}" maxlength="30">
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $customer?->email) }}" maxlength="255">
    </div>
    <div class="col-12">
        <label class="form-label">Alamat</label>
        <input name="address" class="form-control" value="{{ old('address', $customer?->address) }}" maxlength="1000">
    </div>
    <div class="col-12">
        <label class="form-label">Catatan</label>
        <textarea name="notes" class="form-control" rows="3" maxlength="1000">{{ old('notes', $customer?->notes) }}</textarea>
    </div>
    <div class="col-12">
        <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $customer?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Customer aktif dan dapat dipilih di POS</label>
        </div>
    </div>
</div>
