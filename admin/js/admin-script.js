document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.delete-quote-button').forEach(function(button) {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      if (confirm('Êtes-vous sûr de vouloir supprimer cette demande de devis ?')) {
        const url = this.href;

        fetch(url, {
          method: 'GET',
          credentials: 'same-origin',
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Refresh the table or remove the row
              alert('Demande de devis supprimée avec succès.');
              location.reload(); // Simple way to refresh the table
            } else {
              alert('Erreur lors de la suppression de la demande de devis.');
            }
          })
          .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression de la demande de devis.');
          });
      }
    });
  });
});
