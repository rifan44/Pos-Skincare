@php($supplier = $supplier ?? null)
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Nama supplier</label><input name="name" required class="form-control" value="{{ old('name', $supplier?->name) }}"></div>
    <div class="col-md-6"><label class="form-label">Nama kontak</label><input name="contact_name" class="form-control" value="{{ old('contact_name', $supplier?->contact_name) }}"></div>
    <div class="col-md-6"><label class="form-label">Nomor telepon</label><input name="phone" class="form-control" value="{{ old('phone', $supplier?->phone) }}"></div>
    <div class="col-12"><label class="form-label">Alamat</label><textarea name="address" class="form-control" rows="2">{{ old('address', $supplier?->address) }}</textarea></div>
    <div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $supplier?->notes) }}</textarea></div>
    <div class="col-12 form-check ms-1"><input type="hidden" name="is_active" value="0"><input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $supplier?->is_active ?? true))><label class="form-check-label" for="is_active">Supplier aktif</label></div>
</div>
