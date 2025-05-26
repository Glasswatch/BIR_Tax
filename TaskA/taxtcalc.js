// Tax calculation utility for compensation income
// This tool helps estimate withholding tax based on various income components

// Payroll period definitions
const payrollPeriods = {
    "daily": 264,
    "weekly": 66,
    "semi_monthly": 24,
    "monthly": 12,
    "annual": 1
};

// Helper function to parse numeric input with commas
function parseInputValue(value) {
    return value ? parseFloat(value.replace(/,/g, '')) || 0 : 0;
}

// Input value getters
function getSelectedPayrollPeriod() {
    const form = document.forms["withholding_tax_calculator"];
    return payrollPeriods[form.elements["payroll_period"].value] || 0;
}

function getInputValue(fieldName) {
    const form = document.forms["withholding_tax_calculator"];
    const input = form.elements[fieldName];
    return parseInputValue(input.value);
}

// Regular income components
function getBasicSalary() { return getInputValue("basicSalary"); }
function getRepresentationAllowance() { return getInputValue("representationAllowance"); }
function getTransportationAllowance() { return getInputValue("transportationAllowance"); }
function getCostOfLivingAllowance() { return getInputValue("costOfLivingAllowance"); }
function getFixedHousingAllowance() { return getInputValue("fixedHousingAllowance"); }
function getOtherTaxableRegular() { return getInputValue("otherTaxableRegular"); }

// Supplementary income components
function getCommission() { return getInputValue("commission"); }
function getProfitSharing() { return getInputValue("profitSharing"); }
function getFeesPlusDirector() { return getInputValue("fees"); }
function getHazardPay() { return getInputValue("hazardPay"); }
function getOvertimePay() { return getInputValue("overtimePay"); }
function getOtherTaxableSupplementary() { return getInputValue("otherTaxableSupplementary"); }

// Non-taxable components
function getBasicSalaryNt() { return getInputValue("basicSalaryNt"); }
function getHolidayPayNt() { return getInputValue("holidayPayNt"); }
function getOvertimePayNt() { return getInputValue("overtimePayNt"); }
function getNightShiftDifferentialNt() { return getInputValue("nightShiftDifferentialNt"); }
function getHazardPayNt() { return getInputValue("hazardPayNt"); }
function getOtherBenefitsNt() { return getInputValue("otherBenefitsNt"); }
function getDeMinimisBenefitsNt() { return getInputValue("deMinimisBenefitsNt"); }
function getSssGsisPagibigNt() { return getInputValue("sssGsisPagibigNt"); }
function getSalariesOtherCompensationNt() { return getInputValue("salariesOtherComepnsationNt"); }
function getOtherCompensationNt1() { return getInputValue("otherCompensationNt1"); }
function getPremiumPaidInsurance() { return getInputValue("paidInsurance"); }

// Calculation functions
function getTotalRegular() {
    return getBasicSalary() + getRepresentationAllowance() + 
           getTransportationAllowance() + getCostOfLivingAllowance() + 
           getFixedHousingAllowance() + getOtherTaxableRegular();
}

function getTaxableBenefits() {
    const otherBenefitsNt = getOtherBenefitsNt();
    if (otherBenefitsNt > 90000) {
        document.getElementById('otherBenefits').value = formatNumber(otherBenefitsNt - 90000);
        return otherBenefitsNt - 90000;
    }
    document.getElementById('otherBenefits').value = formatNumber(0);
    return 0;
}

function getTotalSupplementary() {
    return getCommission() + getProfitSharing() + getFeesPlusDirector() + 
           getTaxableBenefits() + getHazardPay() + getOvertimePay() + 
           getOtherTaxableSupplementary();
}

function getTNTOtherBenefits() {
    const otherBenefitsNt = getOtherBenefitsNt();
    return otherBenefitsNt > 90000 ? 90000 : otherBenefitsNt;
}

function getTotalNonTaxable() {
    return getBasicSalaryNt() + getHolidayPayNt() + getOvertimePayNt() + 
           getNightShiftDifferentialNt() + getHazardPayNt() + 
           getTNTOtherBenefits() + getDeMinimisBenefitsNt() + 
           getSssGsisPagibigNt() + getSalariesOtherCompensationNt() + 
           getOtherCompensationNt1();
}

function getTotalCompensationIncome() {
    return getTotalRegular() + getTotalSupplementary();
}

function getGrossCompensationIncome() {
    const totalCompensation = getTotalCompensationIncome();
    const totalNonTaxable = getTotalNonTaxable();
    return totalCompensation + totalNonTaxable;
}

function getTaxableCompensationIncome() {
    const totalCompensation = getTotalCompensationIncome();
    const sssGsisPagibig = getSssGsisPagibigNt();
    const period = getSelectedPayrollPeriod();
    
    return period < 4 ? totalCompensation : totalCompensation - sssGsisPagibig;
}

function getNetTaxableCompensationIncome() {
    const taxableIncome = getTaxableCompensationIncome();
    const sssGsisPagibig = getSssGsisPagibigNt();
    const period = getSelectedPayrollPeriod();
    
    let netIncome = period === "annual" ? taxableIncome : taxableIncome + sssGsisPagibig;
    return netIncome < 0 ? 0 : netIncome;
}

// Tax calculation functions
function calculateDailyTax() {
    const regularIncome = getTotalRegular();
    const supplementary = getTotalSupplementary();
    
    if (regularIncome >= 21918) return ((regularIncome - 21918) + supplementary) * 0.35 + 6034.30;
    if (regularIncome >= 5479) return ((regularIncome - 5479) + supplementary) * 0.30 + 1102.60;
    if (regularIncome >= 2192) return ((regularIncome - 2192) + supplementary) * 0.25 + 280.85;
    if (regularIncome >= 1096) return ((regularIncome - 1096) + supplementary) * 0.20 + 61.65;
    if (regularIncome >= 685) return ((regularIncome - 685) + supplementary) * 0.15;
    return ((regularIncome - 0) + supplementary);
}

function calculateWeeklyTax() {
    const regularIncome = getTotalRegular();
    const supplementary = getTotalSupplementary();
    
    if (regularIncome >= 153846) return ((regularIncome - 153846) + supplementary) * 0.35 + 42355.65;
    if (regularIncome >= 38462) return ((regularIncome - 38462) + supplementary) * 0.30 + 7740.45;
    if (regularIncome >= 15385) return ((regularIncome - 15385) + supplementary) * 0.25 + 1971.20;
    if (regularIncome >= 7692) return ((regularIncome - 7692) + supplementary) * 0.20 + 432.60;
    if (regularIncome >= 4808) return ((regularIncome - 4808) + supplementary) * 0.15;
    return 0;
}

function calculateSemiMonthlyTax() {
    const regularIncome = getTotalRegular();
    const supplementary = getTotalSupplementary();
    
    if (regularIncome >= 333333) return ((regularIncome - 333333) + supplementary) * 0.35 + 91770.70;
    if (regularIncome >= 83333) return ((regularIncome - 83333) + supplementary) * 0.30 + 16770.70;
    if (regularIncome >= 33333) return ((regularIncome - 33333) + supplementary) * 0.25 + 4270.70;
    if (regularIncome >= 16667) return ((regularIncome - 16667) + supplementary) * 0.20 + 937.50;
    if (regularIncome >= 10417) return ((regularIncome - 10417) + supplementary) * 0.15;
    return 0;
}

function calculateMonthlyTax() {
    const regularIncome = getTotalRegular();
    const supplementary = getTotalSupplementary();
    
    if (regularIncome >= 666667) return ((regularIncome - 666667) + supplementary) * 0.35 + 183541.80;
    if (regularIncome >= 166667) return ((regularIncome - 166667) + supplementary) * 0.30 + 33541.80;
    if (regularIncome >= 66667) return ((regularIncome - 66667) + supplementary) * 0.25 + 8541.80;
    if (regularIncome >= 33333) return ((regularIncome - 33333) + supplementary) * 0.20 + 1875;
    if (regularIncome >= 20833) return ((regularIncome - 20833) + supplementary) * 0.15;
    return 0;
}

function calculateAnnualTax() {
    const regularIncome = getTotalRegular();
    const supplementary = getTotalSupplementary();
    
    if (regularIncome >= 8000000) return ((regularIncome - 8000000) + supplementary) * 0.35 + 2202500;
    if (regularIncome >= 2000000) return ((regularIncome - 2000000) + supplementary) * 0.30 + 402500;
    if (regularIncome >= 800000) return ((regularIncome - 800000) + supplementary) * 0.25 + 102500;
    if (regularIncome >= 400000) return ((regularIncome - 400000) + supplementary) * 0.20 + 22500;
    if (regularIncome >= 250000) return ((regularIncome - 250000) + supplementary) * 0.15;
    return 0;
}

// Helper functions
function formatNumber(num) {
    return num.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function calculateTotals() {
    const grossIncome = getGrossCompensationIncome();
    const nonTaxable = getTotalNonTaxable();
    const netIncome = getNetTaxableCompensationIncome();
    
    document.getElementById('grossCompensationIncome').value = formatNumber(grossIncome);
    document.getElementById('totalNonTaxableNt').value = formatNumber(nonTaxable);
    document.getElementById('netIncome').value = formatNumber(netIncome);
}

function calculateTax() {
    const period = document.getElementById("payroll_period").value;
    let tax = 0;
    
    switch(period) {
        case 'daily': tax = calculateDailyTax(); break;
        case 'weekly': tax = calculateWeeklyTax(); break;
        case 'semi_monthly': tax = calculateSemiMonthlyTax(); break;
        case 'monthly': tax = calculateMonthlyTax(); break;
        case 'annual': tax = calculateAnnualTax(); break;
    }
    
    document.getElementById('totalWithholdingTax').value = formatNumber(tax);
}

function makeEnableAnnualFields() {
    const period = getSelectedPayrollPeriod();
    const insuranceField = document.withholding_tax_calculator.paidInsurance;
    
    insuranceField.disabled = period < 4 && getGrossCompensationIncome() > 250000;
    calculateTotals();
}

function clearForm() {
    document.forms["withholding_tax_calculator"].reset();
    document.getElementById('grossCompensationIncome').value = '';
    document.getElementById('totalNonTaxableNt').value = '';
    document.getElementById('netIncome').value = '';
    document.getElementById('totalWithholdingTax').value = '';
}

function allowNumericOnly(e) {
    const key = e.charCode || e.keyCode;
    // Allow backspace, delete, tab, navigation keys, and numbers
    if (![8, 9, 13, 27, 46, 110, 190].includes(key) && 
        (key < 48 || key > 57) && 
        (key < 96 || key > 105)) {
        e.preventDefault();
    }
}

function numFormat(value) {
    // This function should format the input value with commas as the user types
    // Implementation depends on your specific formatting needs
}

function calculate() {
    calculateTotals();
}

function calculateForm() {
    calculateTotals();
    calculateTax();
}

function insurancePopper() {
    alert("Health insurance premium deduction (max P2,400/year or P200/month) for families earning ≤ P250,000/year");
}
