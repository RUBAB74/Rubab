// main.js
document.addEventListener('DOMContentLoaded', function(){
  // enable bootstrap tooltips if used
  var t = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  t.forEach(function(el){ new bootstrap.Tooltip(el) });
});

// validate product form (used by add/edit)
function validateProductForm(){
  var name = document.getElementById('name')?.value?.trim();
  var price = parseFloat(document.getElementById('price')?.value || '0');
  var stock = parseInt(document.getElementById('stock')?.value || '0', 10);
  var errors = [];
  if (!name) errors.push('Name required');
  if (isNaN(price) || price < 0) errors.push('Invalid price');
  if (!Number.isInteger(stock) || stock < 0) errors.push('Invalid stock');
  if (errors.length){ alert(errors.join('\n')); return false;}
  return true;
}
