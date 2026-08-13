@php
    $service = $service ?? null;
@endphp

<div class="form-group">
    <label for="name">Service Name <span class="text-danger">*</span></label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $service->name ?? '') }}" placeholder="e.g. Regular Home Cleaning">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Enter service description">{{ old('description', $service->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="base_price">Base Price <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="base_price" id="base_price" class="form-control @error('base_price') is-invalid @enderror" value="{{ old('base_price', $service->base_price ?? '') }}" placeholder="0.00">
        @error('base_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group col-md-4">
        <label for="duration_minutes">Duration (Minutes)</label>
        <input type="number" min="1" name="duration_minutes" id="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes', $service->duration_minutes ?? '') }}" placeholder="e.g. 120">
        @error('duration_minutes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group col-md-4">
        <label for="status">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
            <option value="active" {{ old('status', $service->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $service->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
