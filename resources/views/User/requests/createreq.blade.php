@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Create Request</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{ route('user.requests.store') }}" method="POST" role="form text-left" id="requestForm">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ Auth::user()->department }}" 
                                   readonly
                                   id="department_display">
                            <input type="hidden" 
                                   name="department" 
                                   id="department"
                                   value="{{ Auth::user()->department }}"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="branch">Branch</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ Auth::user()->branch }}" 
                                   readonly
                                   id="branch_display">
                            <input type="hidden" 
                                   name="branch" 
                                   id="branch"
                                   value="{{ Auth::user()->branch }}"
                                   required>
                        </div>
                    </div>
                </div>

                <div id="items-container">
                    <div class="item-entry border rounded p-3 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Product Name</label>
                                    <select class="form-control product-select" name="items[0][product_name]" required>
                                        <option value="">Select Product</option>
                                        @foreach($supplies as $supply)
                                            <option value="{{ $supply->product_name }}" 
                                                data-quantity="{{ $supply->quantity }}"
                                                data-unit-type="{{ $supply->unit_type }}"
                                                class="{{ $supply->quantity < 10 ? 'text-danger' : '' }}">
                                                {{ $supply->product_name }} 
                                                @if($supply->quantity < 10)
                                                    (Low Stock - {{ $supply->quantity }} left)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Quantity</label>
                                    <input type="number" class="form-control quantity-input" name="items[0][quantity]" required min="1">
                                    <small class="text-danger quantity-error" style="display: none;">Not enough stock available</small>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="hidden" class="form-control" name="items[0][price]" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Unit type</label>
                                    <select class="form-control" name="items[0][category]" required>
                                        <option value="">Select Unit</option>
                                        <option value="Box">Box</option>
                                        <option value="Piece">Piece</option>
                                        <option value="Pack">Pack</option>
                                        <option value="Ream">Ream</option>
                                        <option value="Roll">Roll</option>
                                        <option value="Botle">Botle</option>
                                        <option value="Cartridges">Cartridges</option>
                                        <option value="Gallon">Gallon</option>
                                        <option value="Litre">Litre</option>
                                        <option value="Meter">Meter</option>
                                        <option value="Pound">Pound</option>
                                        <option value="Sheet">Sheet</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <button type="button" class="btn btn-danger btn-sm remove-item" style="display: none; background-color: #821131; border-color: #821131;">Remove Item</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-sm" id="add-item" style="background-color: #821131 !important; border-color: #821131 !important; color: white !important;">Add Another Item</button>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-md mt-4 mb-4" style="background-color: #821131; color: #fff;">Create Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.quantity-error {
    font-size: 0.875em;
    margin-top: 0.25rem;
}

.input-error {
    border-color: #dc3545;
}

.input-error:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Button styles */
.btn-submit {
    background-color: #821131;
    color: #fff;
    border-color: #821131;
}

.btn-submit:hover {
    background-color: #6e0f29;
    border-color: #6e0f29;
}

.btn-remove {
    background-color: #821131;
    border-color: #821131;
}

.btn-remove:hover {
    background-color: #6e0f29;
    border-color: #6e0f29;
}
</style>
@endsection

@push('scripts')
<script>
// Handle product selection change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-select')) {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const itemEntry = e.target.closest('.item-entry');
        const unitTypeSelect = itemEntry.querySelector('select[name$="[category]"]');
        const quantityInput = itemEntry.querySelector('.quantity-input');
        const quantityError = itemEntry.querySelector('.quantity-error');
        
        if (selectedOption.value) {
            const unitType = selectedOption.getAttribute('data-unit-type');
            const availableQuantity = parseInt(selectedOption.getAttribute('data-quantity'));
            
            // Set the unit type
            Array.from(unitTypeSelect.options).forEach(option => {
                if (option.value.toLowerCase() === unitType.toLowerCase()) {
                    option.selected = true;
                }
            });

            // Set max attribute and validate current value
            quantityInput.setAttribute('max', availableQuantity);
            validateQuantity(quantityInput, availableQuantity);
        }
    }
});

// Add quantity input validation
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('quantity-input')) {
        const itemEntry = e.target.closest('.item-entry');
        const productSelect = itemEntry.querySelector('.product-select');
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        
        if (selectedOption.value) {
            const availableQuantity = parseInt(selectedOption.getAttribute('data-quantity'));
            validateQuantity(e.target, availableQuantity);
        }
    }
});

// Validation function
function validateQuantity(input, availableQuantity) {
    const quantity = parseInt(input.value);
    const error = input.parentElement.querySelector('.quantity-error');
    
    if (quantity > availableQuantity) {
        input.classList.add('input-error');
        error.style.display = 'block';
        error.textContent = `Not enough stock available (Max: ${availableQuantity})`;
        input.setCustomValidity(`Maximum available quantity is ${availableQuantity}`);
    } else if (quantity < 1) {
        input.classList.add('input-error');
        error.style.display = 'block';
        error.textContent = 'Quantity must be at least 1';
        input.setCustomValidity('Quantity must be at least 1');
    } else {
        input.classList.remove('input-error');
        error.style.display = 'none';
        input.setCustomValidity('');
    }
}

// Replace the existing add-item click handler with this improved version
document.getElementById('add-item').addEventListener('click', function() {
    console.log('Add item button clicked');
    
    const container = document.getElementById('items-container');
    const itemEntries = container.querySelectorAll('.item-entry');
    const newIndex = itemEntries.length;
    
    console.log('Current items count:', newIndex);
    
    // Clone the first item entry
    const template = itemEntries[0].cloneNode(true);
    
    // Reset all inputs and selects in the clone
    template.querySelectorAll('input, select').forEach(input => {
        // Update the index in the name attribute
        if (input.name) {
            const oldName = input.name;
            const newName = input.name.replace(/\[(\d+)\]/, `[${newIndex}]`);
            console.log(`Renaming ${oldName} to ${newName}`);
            input.name = newName;
        }
        
        // Reset values
        if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        } else if (input.type === 'hidden' && input.name && input.name.includes('[price]')) {
            input.value = '0';
        } else if (input.type !== 'hidden') {
            input.value = '';
        }
        
        // Clear any validation states
        input.classList.remove('input-error');
        if (input.setCustomValidity) {
            input.setCustomValidity('');
        }
    });

    // Reset error message
    const errorMsg = template.querySelector('.quantity-error');
    if (errorMsg) {
        errorMsg.style.display = 'none';
    }

    // Show remove button
    const removeButton = template.querySelector('.remove-item');
    if (removeButton) {
        removeButton.style.display = 'inline-block';
    }

    // Add the cloned template to the container
    container.appendChild(template);
    console.log('New item added. Total items:', container.querySelectorAll('.item-entry').length);

    // Show all remove buttons
    container.querySelectorAll('.remove-item').forEach(button => {
        button.style.display = container.children.length > 1 ? 'inline-block' : 'none';
    });
});

// Handle item removal
document.getElementById('items-container').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        const container = document.getElementById('items-container');
        e.target.closest('.item-entry').remove();

        // Hide all remove buttons if only one item remains
        if (container.children.length === 1) {
            container.querySelector('.remove-item').style.display = 'none';
        }

        // Reindex the remaining items
        container.querySelectorAll('.item-entry').forEach((item, index) => {
            item.querySelectorAll('input, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                }
            });
        });
    }
});

// Form submission handler
document.getElementById('requestForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Temporarily prevent form submission
    
    console.log('Form is being submitted');
    console.log('Form action:', this.action);
    console.log('Form method:', this.method);
    
    // Collect form data for debugging
    const formData = new FormData(this);
    const formDataObj = {};
    formData.forEach((value, key) => {
        if (!formDataObj[key]) {
            formDataObj[key] = value;
        } else {
            if (!Array.isArray(formDataObj[key])) {
                formDataObj[key] = [formDataObj[key]];
            }
            formDataObj[key].push(value);
        }
    });
    console.log('Form data:', formDataObj);
    
    // Validate items
    const itemEntries = document.querySelectorAll('.item-entry');
    let validItems = [];
    
    itemEntries.forEach((entry, index) => {
        const productName = entry.querySelector('[name^="items"][name$="[product_name]"]').value;
        const quantity = entry.querySelector('[name^="items"][name$="[quantity]"]').value;
        const category = entry.querySelector('[name^="items"][name$="[category]"]').value;
        
        if (productName && quantity && category) {
            validItems.push({
                index: index,
                product_name: productName,
                quantity: quantity,
                category: category
            });
        }
    });
    
    console.log('Valid items:', validItems);
    
    if (validItems.length === 0) {
        alert('Please fill in at least one item completely');
        return false;
    }
    
    // Check for any validation errors
    const hasErrors = document.querySelectorAll('.input-error').length > 0;
    if (hasErrors) {
        alert('Please fix the validation errors before submitting');
        return false;
    }
    
    // If validation passes, submit the form
    console.log('Form validation passed, submitting to:', this.action);
    this.submit();
});
</script>
@endpush