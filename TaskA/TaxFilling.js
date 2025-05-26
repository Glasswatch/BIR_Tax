const taxReturnScreen = document.getElementById('tax-return-screen');
const payTaxScreen = document.getElementById('pay-tax-screen');
const confirmationScreen = document.getElementById('confirmation-screen');
const calculateTaxBtn = document.getElementById('calculate-tax-btn');
const submitPaymentBtn = document.getElementById('submit-payment-btn');
const doneBtn = document.getElementById('done-btn');
const paymentModal = document.getElementById('payment-modal');
const closeModal = document.querySelector('.close');
const confirmPaymentBtn = document.getElementById('confirm-payment-btn');
const paymentMethod = document.getElementById('payment-method');
const creditCardFields = document.getElementById('credit-card-fields');
const bankFields = document.getElementById('bank-fields');

// Event listeners
calculateTaxBtn.addEventListener('click', () => {
    taxReturnScreen.style.display = 'none';
    payTaxScreen.style.display = 'block';
});

submitPaymentBtn.addEventListener('click', () => {
    paymentModal.style.display = 'block';
});

closeModal.addEventListener('click', () => {
    paymentModal.style.display = 'none';
});

confirmPaymentBtn.addEventListener('click', () => {
    paymentModal.style.display = 'none';
    payTaxScreen.style.display = 'none';
    confirmationScreen.style.display = 'block';
});

doneBtn.addEventListener('click', () => {
    confirmationScreen.style.display = 'none';
    taxReturnScreen.style.display = 'block';
});

paymentMethod.addEventListener('change', (e) => {
    creditCardFields.style.display = 'none';
    bankFields.style.display = 'none';

    if (e.target.value === 'credit') {
        creditCardFields.style.display = 'block';
    } else if (e.target.value === 'bank') {
        bankFields.style.display = 'block';
    }
});

// Close modal if clicked outside
window.addEventListener('click', (e) => {
    if (e.target === paymentModal) {
        paymentModal.style.display = 'none';
    }
});