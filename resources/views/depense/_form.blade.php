<div class="card-body">
  <div class="row">
      <!-- Titre -->
      <div class="col-md-6 mb-3">
          <label class="form-label">Titre</label>
          <input 
              type="text" 
              name="titre" 
              class="form-control"
              value="{{ old('titre', $depense->titre ?? '') }}"
              required
          >
      </div>

      <!-- Catégorie -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Catégorie</label>
            <select name="categorie" class="form-select" required>
                <option value="">Sélectionner une catégorie</option>
                @foreach($categories as $categorie)
                    <option value="{{ $categorie }}"
                        {{ old('categorie', $depense->categorie ?? '') == $categorie ? 'selected' : '' }}>
                        {{ $categorie }}
                    </option>
                @endforeach
            </select>
        </div>

      <!-- Montant -->
      <div class="col-md-6 mb-3">
          <label class="form-label">Montant</label>
          <input
              type="number" 
              step="0.01"
              name="montant" 
              class="form-control"
              value="{{ old('montant', $depense->montant ?? '') }}"
              required
          >
      </div>

    <!-- Mode de paiement -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Mode de paiement</label>
        <select name="mode_paiement" class="form-select" required>
            <option value="">Sélectionner un mode</option>
            @foreach($modes_paiement as $mode)
                <option value="{{ $mode }}"
                    {{ old('mode_paiement', $depense->mode_paiement ?? '') == $mode ? 'selected' : '' }}>
                    {{ $mode }}
                </option>
            @endforeach
        </select>
    </div>

      <!-- Référence -->
      <div class="col-md-6 mb-3">
          <label class="form-label">Référence</label>
          <input 
              type="text"
              name="reference"
              class="form-control"
              value="{{ old('reference', $depense->reference ?? '') }}"
          >
          <small class="text-muted">Optionnel</small>
      </div>

      <!-- Date -->
      <div class="col-md-6 mb-3">
          <label class="form-label">Date</label>
          <input 
              type="date" 
              name="date_depense" 
              class="form-control"
              value="{{ old('date_depense', $depense->date_depense ?? '') }}"
              required
          >
      </div>
      <!-- Justificatif -->
      <div class="col-md-12 mb-3">
          <label class="form-label">Justificatif (image ou PDF)</label>
          <input 
              type="file" 
              name="justificatif"
              class="form-control"
              accept="image/*,.pdf"
              required="{{ isset($depense) ? '': 'required' }}"
          >

          @if(isset($depense) && $depense->justificatif)
              <div class="mt-2">
                  <small class="text-muted">Justificatif actuel :</small><br>
                  <a href="{{ asset('storage/' . $depense->justificatif) }}" 
                    target="_blank"
                    class="btn btn-sm btn-outline-primary mt-1">
                      Voir le justificatif
                  </a>
              </div>
          @endif
      </div>
      
      <!-- Description -->
      <div class="col-md-12 mb-4">
          <label class="form-label">Description</label>
          <textarea 
              name="description" 
              class="form-control"
              rows="3">
              {{ old('description', $depense->description ?? '') }}</textarea>
      </div>

      <!-- Boutons -->
    <div class="mt-4">
        <button type="submit" class="btn btn-success">
            {{ isset($depense) ? 'Mettre à jour' : 'Ajouter' }}
        </button>
        @if (isset($depense))
            <a href="{{ route('depenses.show', $depense) }}" class="btn btn-secondary ms-2">Annuler</a>
        @endif
    </div>
</div>