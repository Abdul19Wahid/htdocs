<x-app-layout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="page-title mb-1">
                <i class="bi bi-plus-circle me-2 text-success"></i>Add Income
            </h1>
            <div class="small-muted">Record money you receive and keep your totals accurate.</div>
        </div>
        <a class="btn btn-light" href="{{ route('incomes.index') }}" title="Back to income list">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <x-card title="Income Details" subtitle="Enter the details for this income">
                <form method="POST" action="{{ route('incomes.store') }}" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="bi bi-info-circle me-2"></i>Basic Information
                        </h6>

                        <div class="row g-3">
                            <div class="col-12">
                                <x-form-group label="Description" help="What was this income for?" name="description">
                                    <input type="text"
                                           class="form-control @error('description') is-invalid @enderror"
                                           name="description"
                                           value="{{ old('description') }}"
                                           placeholder="e.g. Monthly salary, Freelance payment"
                                           maxlength="255">
                                </x-form-group>
                            </div>

                            <div class="col-md-6">
                                <x-form-group label="Amount" help="How much did you receive?" required name="amount">
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

                            <div class="col-md-6">
                                <x-form-group label="Date" help="When did you receive it?" required name="date">
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

                    <hr class="my-4">
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="bi bi-tag me-2"></i>Categorization
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-form-group label="Income Source" help="Where did this come from?" name="income_source_id">
                                    <select class="form-select @error('income_source_id') is-invalid @enderror" name="income_source_id">
                                        <option value="">Choose a source...</option>
                                        @foreach($sources as $s)
                                            <option value="{{ $s->id }}" @selected(old('income_source_id') == $s->id)>
                                                {{ $s->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-form-group>
                            </div>

                            <div class="col-md-6">
                                <x-form-group label="Payment Method" help="How was it received?" name="payment_method_id">
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

                    <hr class="my-4">
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="bi bi-file-earmark me-2"></i>Additional Information
                        </h6>

                        <div class="mb-3">
                            <x-form-group label="Notes" help="Add any additional notes about this income" name="notes">
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                          name="notes"
                                          rows="4"
                                          placeholder="Any additional details..."
                                          maxlength="2000">{{ old('notes') }}</textarea>
                                <small class="text-muted d-block mt-1">
                                    <span class="char-count">0</span> / 2000 characters
                                </small>
                            </x-form-group>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-5 pt-3 border-top">
                        <x-button variant="success" type="submit" name="action" value="save" icon="bi-check-lg">
                            Save Income
                        </x-button>
                        <button type="submit" name="action" value="save_and_new" class="btn btn-outline-success">
                            <i class="bi bi-plus-lg me-1"></i>Save &amp; Add Another
                        </button>
                        <a href="{{ route('incomes.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card title="💡 Quick Tips" subtitle="Tips for accurate income tracking">
                <div class="space-y-3">
                    <div class="p-3 bg-success-subtle rounded-lg border border-success-subtle">
                        <strong class="text-success d-block mb-1">Log It As It Arrives</strong>
                        <small class="text-muted">Record income the same day it lands so your balance stays accurate.</small>
                    </div>
                    <div class="p-3 bg-info-subtle rounded-lg border border-info-subtle">
                        <strong class="text-info d-block mb-1">Use a Source</strong>
                        <small class="text-muted">Tagging a source (Salary, Freelance, Gift) makes your income reports meaningful.</small>
                    </div>
                    <div class="p-3 bg-primary-subtle rounded-lg border border-primary-subtle">
                        <strong class="text-primary d-block mb-1">Separate Irregular Income</strong>
                        <small class="text-muted">One-off gifts or refunds are easier to spot later if you name them clearly.</small>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notesField = document.querySelector('textarea[name="notes"]');
        const charCount = document.querySelector('.char-count');

        if (notesField && charCount) {
            notesField.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
            charCount.textContent = notesField.value.length;
        }
    });
</script>
@endpush
