function validateForm(form) {
    const name = form.medicine_name.value.trim();
    const price = parseFloat(form.medicine_price.value);
    const quantity = parseInt(form.medicine_quantity.value);

    if(name === '') { alert('Name cannot be empty'); return false; }
    if(isNaN(price) || price <= 0) { alert('Price must be a positive number'); return false; }
    if(isNaN(quantity) || quantity < 0) { alert('Quantity cannot be negative'); return false; }

    return true;
}
