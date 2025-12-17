<script>
    // Brazilian regions for phone number formatting
    const southernSoutheasternAreaCodes = [
        '11', '12', '13', '14', '15', '16', '17', '18', '19',  // SP
        '21', '22', '24',  // RJ
        '27', '28',        // ES
        '31', '32', '33', '34', '35', '37', '38',  // MG
        '41', '42', '43', '44', '45', '46',        // PR
        '51', '53', '54', '55',                    // RS
        '47', '48', '49',                          // SC
        '79',                                      // SE
        '61', '62', '64', '65', '66', '67', '68', '69', // GO
        '71', '72', '73', '74', '75', '76', '77', '78', '79', // AL
        '81', '82', '83', '84', '85', '86', '87', '88', '89', // PE
        '91', '92', '93', '94', '95', '96', '97', '98', '99'  // RN
    ];
    
    // Auto-format phone number as user types
    function autoFormatPhoneNumber(input) {
        if (input.dataset.phoneMaskInitialized) return;
        input.dataset.phoneMaskInitialized = 'true';

        let isDeleting = false;
        let previousValue = '';
        
        // Add event listener for real-time formatting
        input.addEventListener('input', function(e) {
            const currentValue = e.target.value;
            isDeleting = currentValue.length < previousValue.length;
            previousValue = currentValue;
            
            let value = currentValue.replace(/\D/g, '');
            
            // Check if we're deleting and at the beginning of the input
            if (isDeleting && e.target.selectionStart <= 4) {
                // Allow deletion of country code
                if (value.startsWith('55')) {
                    value = value.substring(2);
                }
            } else {
                // Add country code automatically if not present and we have enough digits
                if (value.length > 0 && !value.startsWith('55')) {
                    value = '55' + value;
                }
            }
            
            // Check if we have enough digits to determine area code
            if (value.length >= 4) {
                const areaCode = value.substring(2, 4);
                
                // For Southern and Southeastern regions with 13 digits (55 + 9 + 8 digits)
                // we remove the 9 digit
                if (southernSoutheasternAreaCodes.includes(areaCode) && 
                    value.length === 13 && value.charAt(4) === '9') {
                    // Remove the 9 for Southern/Southeastern regions
                    value = value.substring(0, 4) + value.substring(5);
                }
            }
            
            // Format for display: +55 XX XXXXX-XXXX or +55 XX XXXX-XXXX
            let formattedValue = '';
            if (value.length <= 2) {
                formattedValue = value;
            } else if (value.length <= 4) {
                formattedValue = `+${value.substring(0, 2)} ${value.substring(2)}`;
            } else if (value.length <= 8) {
                formattedValue = `+${value.substring(0, 2)} ${value.substring(2, 4)} ${value.substring(4)}`;
            } else if (value.length <= 12) {
                // With 9 digit (Northern, Northeastern, and Midwest regions)
                formattedValue = `+${value.substring(0, 2)} ${value.substring(2, 4)} ${value.substring(4, 9)}${value.substring(9)}`;
            } else if (value.length <= 13) {
                // Without 9 digit (Southern/Southeastern regions)
                formattedValue = `+${value.substring(0, 2)} ${value.substring(2, 4)} ${value.substring(4, 8)}${value.substring(8)}`;
            } else {
                // Limit to maximum length
                if (southernSoutheasternAreaCodes.includes(value.substring(2, 4))) {
                    // Format for Southern/Southeastern (12 digits total)
                    formattedValue = `+${value.substring(0, 2)} ${value.substring(2, 4)} ${value.substring(4, 8)}${value.substring(8, 12)}`;
                } else {
                    // Format for others (13 digits total)
                    formattedValue = `+${value.substring(0, 2)} ${value.substring(2, 4)} ${value.substring(4, 9)}${value.substring(9, 13)}`;
                }
            }
            
            // Only update the value if it's different to prevent cursor jumping
            if (e.target.value !== formattedValue) {
                const cursorPosition = e.target.selectionStart;
                const oldLength = e.target.value.length;
                e.target.value = formattedValue;
                
                // Adjust cursor position after formatting
                const newLength = formattedValue.length;
                const diff = newLength - oldLength;
                const newCursorPosition = cursorPosition + diff;
                e.target.setSelectionRange(newCursorPosition, newCursorPosition);
                // Trigger input event to update Livewire/Filament state
                e.target.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
        
        // Handle keydown for better backspace/delete behavior
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' || e.key === 'Delete') {
                isDeleting = true;
            }
            
            // Allow deletion of formatting characters (parentheses, spaces, hyphens)
            const formattingChars = ['(', ')', ' ', '-'];
            if (e.key === 'Backspace' && formattingChars.includes(e.target.value[e.target.selectionStart - 1])) {
                e.preventDefault();
                const start = e.target.selectionStart;
                const end = e.target.selectionEnd;
                const value = e.target.value;
                
                // Remove the character before the cursor
                e.target.value = value.substring(0, start - 1) + value.substring(end);
                e.target.setSelectionRange(start - 1, start - 1);
                // Trigger input event
                e.target.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
    }

    // Initialize on load for Filament and normal pages
    document.addEventListener('DOMContentLoaded', function() {
        // Target by class
        const inputs = document.querySelectorAll('.phone-mask-input');
        inputs.forEach(input => autoFormatPhoneNumber(input));
        
        // Also target by ID 'numero' if present (for register-link)
        const numeroInput = document.getElementById('numero');
        if (numeroInput && !numeroInput.classList.contains('phone-mask-input')) {
            autoFormatPhoneNumber(numeroInput);
        }
    });

    // Setup observer for Filament dynamic content (Livewire updates)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element
                    if (node.classList && node.classList.contains('phone-mask-input')) {
                        autoFormatPhoneNumber(node);
                    }
                    // Also check children
                    const nestedInputs = node.querySelectorAll('.phone-mask-input');
                    nestedInputs.forEach(input => autoFormatPhoneNumber(input));
                }
            });
        });
    });
    
    observer.observe(document.body, { childList: true, subtree: true });
</script>
