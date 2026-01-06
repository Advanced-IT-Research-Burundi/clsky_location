<div class="row g-2 mb-2 detail-item">
    <div class="col-md-4">
        <input type="text"
               name="details[{{ $i }}][title]"
               class="form-control"
               value="{{ $detail->title ?? '' }}"
               placeholder="Nom"
               required>
    </div>

    <div class="col-md-4">
        <input type="text"
               name="details[{{ $i }}][value]"
               class="form-control"
               value="{{ $detail->value ?? '' }}"
               placeholder="Valeur"
               required>
    </div>

    <div class="col-md-3">
        <input type="text"
               name="details[{{ $i }}][description]"
               class="form-control"
               value="{{ $detail->description ?? '' }}"
               placeholder="Description">
    </div>

    <div class="col-md-1">
        <button type="button" class="btn btn-danger remove-detail">
            <i class="bi bi-trash"></i>
        </button>
    </div>
</div>
