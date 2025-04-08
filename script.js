// Abre modal e configura formulário
function openForm(product, buttonText, value) {
    const overlay = document.getElementById('form-overlay');
    const formTitle = document.getElementById('form-title');
    const submitBtn = document.getElementById('form-submit');
    document.getElementById('input-product').value = product;
    document.getElementById('input-value').value = value || '';
    formTitle.textContent = product;
    submitBtn.textContent = buttonText;
    overlay.classList.remove('hidden');
  }
  
  // Fecha modal
  function closeForm() {
    document.getElementById('form-overlay').classList.add('hidden');
  }
  
  // Envia formulário
  document.getElementById('lp-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);
    const resp = await fetch('sendEmail.php', {
      method: 'POST',
      body: data
    });
    const json = await resp.json();
    if (json.success) {
      alert('Obrigado! Em breve entraremos em contato.');
      form.reset();
      closeForm();
    } else {
      alert('Erro ao enviar. Tente novamente mais tarde.');
      console.error(json.error);
    }
  });
  