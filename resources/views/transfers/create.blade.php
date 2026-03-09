@extends('layouts.app')

@section('content')
<style>
    .focus-ring-group:focus-within {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
<div class="mb-4">
    <h3 class="fw-bold">Send Money</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboards</a></li>
            <li class="breadcrumb-item active" aria-current="page">Send Money</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Transfer Details</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('send-money.review') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="recipient_name" class="form-label">Recipient Name</label>
                        <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" placeholder="e.g. Michael Scott" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="recipient_account" class="form-label">Recipient Account / IBAN</label>
                        <input type="text" class="form-control" id="recipient_account" name="recipient_account" value="{{ old('recipient_account') }}" placeholder="e.g. 1234567890 or IBAN" required>
                        <div class="form-text text-muted"><i class="bi bi-shield-lock"></i> We will store this encrypted; only masked values are shown later.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group input-group-lg border rounded-3 overflow-hidden focus-ring-group bg-white">
                            <select class="form-select border-0 bg-transparent fw-bold text-dark pe-4 ps-3" id="currency_selector" style="max-width: 110px; cursor: pointer; border-right: 1px solid #e9ecef !important;">
                                <option value="NGN" {{ $account->currency == 'NGN' ? 'selected' : '' }}>NGN</option>
                                <option value="USD" {{ $account->currency == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ $account->currency == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="IDR" {{ $account->currency == 'IDR' ? 'selected' : '' }}>IDR</option>
                            </select>
                            <input type="text" class="form-control border-0 fs-4 fw-bold ps-3 shadow-none" id="amount_display" placeholder="0.00" required>
                            <input type="hidden" id="amount" name="amount" value="{{ old('amount') }}">
                            <input type="hidden" id="currency" name="currency" value="{{ $account->currency }}">
                        </div>
                        
                        <!-- Quick Amount Buttons -->
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 quick-amount" data-value="100">100</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 quick-amount" data-value="500">500</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 quick-amount" data-value="1000">1,000</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 quick-amount" data-value="5000">5,000</button>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 quick-amount" data-value="{{ $account->balance }}">Max</button>
                        </div>

                        <div id="balance-error" class="text-danger small mt-1 d-none">
                            <i class="bi bi-exclamation-circle-fill me-1"></i> Insufficient balance
                        </div>
                        @error('amount')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="note" class="form-label">Note (Optional)</label>
                        <textarea class="form-control" id="note" name="note" rows="2" placeholder="What is this transfer for? (e.g. Rent, Dinner)">{{ old('note') }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4" id="submit-btn">Review Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card bg-primary text-white p-4">
            <h5 class="mb-4">Available Balance</h5>
            <div class="fs-2 fw-bold mb-1">{{ $account->currency }} <span id="current-balance">{{ number_format($account->balance, 2) }}</span></div>
            <div class="small opacity-75">Updated just now</div>
        </div>
        
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Security Tips</h6>
            <ul class="small text-muted ps-3 mb-0">
                <li class="mb-2">Never share your password or OTP.</li>
                <li class="mb-2">Verify recipient details before sending.</li>
                <li>Transfers are processed securely using AES encryption.</li>
            </ul>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountDisplay = document.getElementById('amount_display');
        const amountInput = document.getElementById('amount');
        const currencyInput = document.getElementById('currency');
        const currencySelector = document.getElementById('currency_selector');
        const balanceError = document.getElementById('balance-error');
        const submitBtn = document.getElementById('submit-btn');
        const maxBalance = {{ $account->balance }};
        const quickButtons = document.querySelectorAll('.quick-amount');

        // Map currency to locale
        const currencyLocales = {
            'IDR': 'id-ID',
            'USD': 'en-US',
            'EUR': 'de-DE', // or en-IE
            'NGN': 'en-NG'
        };

        // Get current locale based on selection
        function getCurrentLocale() {
            return currencyLocales[currencySelector.value] || 'en-US';
        }

        // Format number based on locale
        function formatNumber(num) {
            return new Intl.NumberFormat(getCurrentLocale(), {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }).format(num);
        }

        // Parse localized number string back to float
        function parseLocalizedNumber(str) {
            const locale = getCurrentLocale();
            const parts = new Intl.NumberFormat(locale).formatToParts(12345.6);
            const decimalChar = parts.find(p => p.type === 'decimal')?.value || '.';
            const groupChar = parts.find(p => p.type === 'group')?.value || ',';

            // Remove group separators
            let cleanStr = str.replaceAll(groupChar, '');
            // Replace decimal separator with dot
            cleanStr = cleanStr.replace(decimalChar, '.');
            
            return parseFloat(cleanStr) || 0;
        }

        // Validate balance
        function validateBalance(value) {
            if (value > maxBalance) {
                amountDisplay.classList.add('is-invalid', 'text-danger');
                balanceError.classList.remove('d-none');
                submitBtn.disabled = true;
            } else {
                amountDisplay.classList.remove('is-invalid', 'text-danger');
                balanceError.classList.add('d-none');
                submitBtn.disabled = false;
            }
        }

        // Handle Currency Change
        currencySelector.addEventListener('change', function() {
            currencyInput.value = this.value;
            // Re-format current value
            const currentVal = parseFloat(amountInput.value) || 0;
            if (currentVal > 0) {
                amountDisplay.value = formatNumber(currentVal);
            }
        });

        // Input event handler
        amountDisplay.addEventListener('input', function(e) {
            // Allow only numbers and separators (simplified regex for broad compatibility)
            // Real formatting happens on blur or we can do it live carefully
            
            // Get raw value for calculation
            // Note: This simple parsing assumes user types correctly for the locale
            // For robust live formatting, we'd need a masking library (like Cleave.js)
            // Here we just strip non-numeric/dot/comma
            
            // Temporary simple parsing for validation
            let val = this.value.replace(/[^0-9.,]/g, '');
            
            // Try to parse standard format
            let numericVal = parseLocalizedNumber(val);
            
            amountInput.value = numericVal;
            validateBalance(numericVal);
        });

        // Format on blur
        amountDisplay.addEventListener('blur', function() {
            const value = parseLocalizedNumber(this.value);
            if (value > 0) {
                amountInput.value = value; // Ensure clean float
                this.value = formatNumber(value);
            }
        });

        // Quick amount buttons
        quickButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const value = parseFloat(this.dataset.value);
                amountInput.value = value;
                amountDisplay.value = formatNumber(value);
                validateBalance(value);
            });
        });
    });
</script>
@endsection
