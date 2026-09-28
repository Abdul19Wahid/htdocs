<x-app-layout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="page-title mb-1">
                <i class="bi bi-plus-circle me-2 text-danger"></i>Add Expense
            </h1>
            <div class="small-muted">Record money you spend and keep track of your expenses.</div>
        </div>
        <a class="btn btn-light" href="{{ route('expenses.index') }}" title="Back to expenses list">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <!-- Form Container -->
    <div class="row g-4">
        <div class="col-lg-8">
            <x-card title="Expense Details" subtitle="Enter the details for your new expense">
                <form method="POST" enctype="multipart/form-data" action="{{ route('expenses.store') }}" class="needs-validation" novalidate>
                    @csrf

                    <!-- Basic Information Section -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="bi bi-info-circle me-2"></i>Basic Information
                        </h6>

                        <div class="row g-3">
                            <!-- Description -->
                            <div class="col-12">
                                <x-form-group label="Description" help="What did you spend money on?" required>
                                    <input type="text" 
                                           class="form-control @error('description') is-invalid @enderror" 
                                           name="description" 
                                           value="{{ old('description') }}" 
                                           placeholder="e.g. Groceries, Gas, Coffee"
                                           maxlength="255"
                                           required>
                                </x-form-group>
                            </div>

                            <!-- Amount -->
                            <div class="col-md-6">
                                <x-form-group label="Amount" help="How much did you spend?" required>
                                    <div class="input-group">
                                        <span class="input-group-text fw-semibold">{{ auth()->user()->currency }}</span>
                                        <input type="number" 
                                               class="form-control @error('amount') is-invalid @enderror" 
                                               name="amount" 
                                               value="{{ old('amount') }}" 
                                               placeholder="0.00"
                                               min="0.01" 
                                               step="0.01" 
                                               required
                                               inputmode="decimal">
                                    </div>
                                </x-form-group>
                            </div>

                            <!-- Date -->
                            <div class="col-md-6">
                                <x-form-group label="Date" help="When did you make this expense?" required>
                                    <input type="date" 
                                           class="form-control @error('date') is-invalid @enderror" 
                                           name="date" 
                                           value="{{ old('date', today()->format('Y-m-d')) }}" 
                                           max="{{ today()->format('Y-m-d') }}"
                                           required>
                                </x-form-group>
                            </div>
                        </div>
                    </div>

                    <!-- Categorization Section -->
                    <hr class="my-4">
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="bi bi-tag me-2"></i>Categorization
                        </h6>

                        <div class="row g-3">
                            <!-- Category -->
                            <div class="col-md-6">
                                <x-form-group label="Category" help="Select the spending category">
                                    <select class="form-select @error('category_id') is-invalid @enderror" name="category_id">
                                        <option value="">Choose a category...</option>
                                        @foreach($categories as $c)
                                            <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-form-group>
                            </div>

                            <!-- Payment Method -->
                            <div class="col-md-6">
                                <x-form-group label="Payment Method" help="How did you pay?">
                                    <select class="form-select @error('payment_method_id') is-invalid @enderror" name="payment_method_id">
                                        <option value="">Choose payment method...</option>
                                        @foreach($methods as $m)
                                            <option value="{{ $m->id }}" @selected(old('payment_method_id', $methods->firstWhere('is_default', true)?->id) == $m->id)>
                                                {{ $m->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-form-group>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <hr class="my-4">
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="bi bi-file-earmark me-2"></i>Additional Information
                        </h6>

                        <!-- Notes -->
                        <div class="mb-3">
                            <x-form-group label="Notes" help="Add any additional notes about this expense">
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          name="notes" 
                                          rows="4" 
                                          placeholder="Any additional details..."
                                          maxlength="1000">{{ old('notes') }}</textarea>
                                <small class="text-muted d-block mt-1">
                                    <span class="char-count">0</span> / 1000 characters
                                </small>
                            </x-form-group>
                        </div>

                        <!-- Receipt Upload -->
                        <div class="mb-3">
                            <x-form-group label="Receipt" help="Upload a receipt (JPG, PNG or PDF up to 5MB)">
                                <input type="file" 
                                       class="form-control @error('receipt') is-invalid @enderror" 
                                       name="receipt" 
                                       accept="image/jpeg,image/png,application/pdf"
                                       id="receiptInput">
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Accepted formats: JPG, PNG, PDF (Max 5MB)
                                </small>
                                <div id="receiptPreview" class="mt-3"></div>
                            </x-form-group>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 mt-5 pt-3 border-top">
                        <x-button variant="danger" type="submit" name="action" value="save" icon="bi-check-lg">
                            Save Expense
                        </x-button>
                        <button type="submit" name="action" value="save_and_new" class="btn btn-outline-danger">
                            <i class="bi bi-plus-lg me-1"></i>Save &amp; Add Another
                        </button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Quick Tips Sidebar -->
        <div class="col-lg-4">
            <x-card title="💡 Quick Tips" subtitle="Tips for better expense tracking">
                <div class="space-y-3">
                    <div class="p-3 bg-info-subtle rounded-lg border border-info-subtle">
                        <strong class="text-info d-block mb-1">Be Descriptive</strong>
                        <small class="text-muted">Use clear descriptions like "Grocery Store" or "Gas Station" to easily find expenses later.</small>
                    </div>
                    <div class="p-3 bg-success-subtle rounded-lg border border-success-subtle">
                        <strong class="text-success d-block mb-1">Organize by Category</strong>
                        <small class="text-muted">Assign categories to track spending patterns and monitor budget progress.</small>
                    </div>
                    <div class="p-3 bg-warning-subtle rounded-lg border border-warning-subtle">
                        <strong class="text-warning d-block mb-1">Keep Receipts</strong>
                        <small class="text-muted">Upload receipts to have proof of expenses and review details later.</small>
                    </div>
                    <div class="p-3 bg-primary-subtle rounded-lg border border-primary-subtle">
                        <strong class="text-primary d-block mb-1">Track Regularly</strong>
                        <small class="text-muted">Record expenses as you make them for accurate tracking and better insights.</small>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character counter for notes
        const notesField = document.querySelector('textarea[name="notes"]');
        const charCount = document.querySelector('.char-count');
        
        if (notesField && charCount) {
            notesField.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
            charCount.textContent = notesField.value.length;
        }

        // Receipt preview
        const receiptInput = document.getElementById('receiptInput');
        const receiptPreview = document.getElementById('receiptPreview');
        
        if (receiptInput) {
            receiptInput.addEventListener('change', function(e) {
                receiptPreview.innerHTML = '';

                if (this.files.length > 0) {
                    const file = this.files[0];
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);

                    // Catch oversized/wrong-type files before upload — the
                    // server enforces this too, but failing fast here avoids
                    // wasting time and bandwidth on a doomed upload.
                    const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                    const maxBytes = 5 * 1024 * 1024;
                    if (!allowedTypes.includes(file.type)) {
                        receiptPreview.innerHTML = `<div class="alert alert-danger py-2 mb-0"><i class="bi bi-exclamation-triangle me-2"></i>That file type isn't supported. Use JPG, PNG, or PDF.</div>`;
                        this.value = '';
                        return;
                    }
                    if (file.size > maxBytes) {
                        receiptPreview.innerHTML = `<div class="alert alert-danger py-2 mb-0"><i class="bi bi-exclamation-triangle me-2"></i>That file is ${fileSize} MB — receipts must be 5 MB or smaller.</div>`;
                        this.value = '';
                        return;
                    }

                    if (file.type.includes('image')) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            receiptPreview.innerHTML = `
                                <div class="position-relative">
                                    <img src="${event.target.result}" class="img-fluid rounded border" style="max-height: 200px;">
                                    <small class="text-muted d-block mt-2">${fileSize} MB - ${file.name}</small>
                                </div>
                            `;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        receiptPreview.innerHTML = `
                            <div class="alert alert-info">
                                <i class="bi bi-file-earmark-pdf me-2"></i>
                                <strong>${file.name}</strong>
                                <br>
                                <small>${fileSize} MB</small>
                            </div>
                        `;
                    }
                }
            });
        }

        // Form validation
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .space-y-3 > * + * {
        margin-top: 1rem;
    }

    .form-label {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.75rem;
    }

    .form-control, .form-select {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #ef4444;
    }

    .input-group-text {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .text-uppercase {
        letter-spacing: 0.05em;
    }

    @media (prefers-color-scheme: dark) {
        .form-label {
            color: #f1f5f9;
        }

        .input-group-text {
            background: #1e293b;
            border-color: #334155;
        }
    }
</style>
@endpush
