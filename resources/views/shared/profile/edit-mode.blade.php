@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('profile-form');
        const editButton = document.getElementById('edit-profile-button');
        const cancelButton = document.getElementById('cancel-profile-button');
        const saveButton = document.getElementById('save-profile-button');
        const editIndicators = Array.from(document.querySelectorAll('[data-edit-indicator]'));

        if (!form || !editButton || !cancelButton || !saveButton || !editIndicators.length) {
            return;
        }

        const fields = Array.from(form.querySelectorAll('input:not([type=hidden]):not([type=button]):not([type=submit]):not([type=reset]):not([readonly]), select:not([disabled]), textarea:not([disabled])'));
        const originalState = fields.map((field) => ({
            field,
            value: field.type === 'checkbox' || field.type === 'radio' ? field.checked : field.value,
        }));

        const setEditState = (isEditing) => {
            fields.forEach((field) => field.disabled = !isEditing);
            editIndicators.forEach((indicator) => indicator.classList.toggle('d-none', !isEditing));
            editButton.classList.toggle('d-none', isEditing);
            cancelButton.classList.toggle('d-none', !isEditing);
            saveButton.classList.toggle('d-none', !isEditing);
        };

        const resetFields = () => {
            originalState.forEach(({ field, value }) => {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    field.checked = value;
                } else {
                    field.value = value;
                }
                field.dispatchEvent(new Event('input', { bubbles: true }));
            });
        };

        const editModeOnLoad = new URLSearchParams(window.location.search).get('edit') === '1';
        setEditState(editModeOnLoad);

        editButton.addEventListener('click', () => setEditState(true));
        cancelButton.addEventListener('click', () => {
            resetFields();
            setEditState(false);
        });

        form.addEventListener('submit', () => {
            saveButton.disabled = true;
        });
    });
</script>
@endpush

@push('styles')
<style>
    #edit-profile-button:not(.d-none) {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        color: #fff !important;
        background-color: #1463d8 !important;
        border-color: #1463d8 !important;
        font-weight: 600 !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    #edit-profile-button:hover,
    #edit-profile-button:focus {
        color: #fff !important;
        background-color: #0f55ba !important;
        border-color: #0f55ba !important;
    }
    #cancel-profile-button:not(.d-none) {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        color: #374151 !important;
        background-color: #e5e7eb !important;
        border-color: #cbd5e1 !important;
        font-weight: 600 !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    #cancel-profile-button:not(.d-none):hover,
    #cancel-profile-button:not(.d-none):focus {
        color: #1f2937 !important;
        background-color: #d1d5db !important;
        border-color: #9ca3af !important;
    }
</style>
@endpush
