<th class="text-center" style="width: 36px;">
    <input type="checkbox" id="checkTous" class="form-check-input" title="Tout sélectionner / désélectionner">
</th>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkTous = document.getElementById('checkTous');
    if (!checkTous) return;

    checkTous.addEventListener('change', function () {
        document.querySelectorAll('.ligne-check').forEach(cb => {
            cb.checked = checkTous.checked;
            cb.closest('tr').classList.toggle('table-active', checkTous.checked);
        });
    });

    document.querySelectorAll('.ligne-check').forEach(cb => {
        cb.addEventListener('change', function () {
            this.closest('tr').classList.toggle('table-active', this.checked);
            const total    = document.querySelectorAll('.ligne-check').length;
            const coches   = document.querySelectorAll('.ligne-check:checked').length;
            checkTous.indeterminate = coches > 0 && coches < total;
            checkTous.checked      = coches === total;
        });
    });
});
</script>
@endpush
@endonce
