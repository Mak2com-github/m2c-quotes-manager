document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('quoteForm');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var formData = new FormData(this);
      var devisItems = localStorage.getItem('devis');
      var formDataSerialized = new URLSearchParams(formData).toString();
      var bodyData = 'action=process_quote_form&security=' + mak2comQuotesAjax.nonce + '&data=' + encodeURIComponent(formDataSerialized) + '&devisItems=' + encodeURIComponent(devisItems);

      fetch(mak2comQuotesAjax.ajax_url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: bodyData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Hide the form and show the success message
            document.getElementById('quoteForm').style.display = 'none';
            document.getElementById('success-message').style.display = 'block';
          } else {
            console.error('Erreur:', data.message);
            alert('Une erreur est survenue: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Erreur:', error);
          alert('Une erreur est survenue. Veuillez réessayer.');
        });
    });
  }
});
