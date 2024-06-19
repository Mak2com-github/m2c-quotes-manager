function quoteStorage() {
  document.getElementById('addToQuote').addEventListener('click', function(event) {
    event.preventDefault();

    const button = this;
    const productId = button.getAttribute('data-product-id');
    const productName = button.getAttribute('data-product-name');
    const productImage = button.getAttribute('data-product-image');
    const productLink = button.getAttribute('data-product-link');
    const quantityInput = document.getElementById('quantity');
    const quantity = parseInt(quantityInput.value, 10);
    const minQuantity = parseInt(quantityInput.getAttribute('min'), 10) || 10;
    const maxQuantity = parseInt(quantityInput.getAttribute('max'), 10) || 100000;
    const uniqueId = Date.now();
    const productDetails = {
      uniqueId: uniqueId,
      productId: productId,
      productName: productName,
      imageUrl: productImage,
      link: productLink,
      quantity: quantity,
      options: {}
    };

    if (quantity < minQuantity || quantity > maxQuantity) {
      alert(`La quantité doit être entre ${minQuantity} et ${maxQuantity}.`);
      event.preventDefault();
      return;
    }
    document.querySelectorAll('#proProductOptions .form-option input:checked, #proProductOptions input[type="number"]').forEach(function(input) {
      productDetails.options[input.name] = input.value;
    });

    let devis = localStorage.getItem('devis');
    if (devis) {
      devis = JSON.parse(devis);
    } else {
      devis = [];
    }
    devis.push(productDetails);
    localStorage.setItem('devis', JSON.stringify(devis));

    updatePopupDevis()
    togglePopupDevis();
  });
}

function updatePopupDevis() {
  const devis = JSON.parse(localStorage.getItem('devis') || '[]');
  const devisItemsContainer = document.getElementById('devisItems');
  const devisCartList = document.getElementById('devisCartList');
  if (devisItemsContainer) {
    devisItemsContainer.innerHTML = '';
  }
  if (devisCartList) {
    devisCartList.innerHTML = '';
  }
  if (devis.length > 0) {
    devis.forEach((item) => {
      if (devisItemsContainer) {
        const productElement1 = createDevisElement(item);
        devisItemsContainer.appendChild(productElement1);
      }
      if (devisCartList) {
        const productElement2 = createDevisElement(item);
        devisCartList.appendChild(productElement2);
      }
    });
  } else {
    if (devisItemsContainer) {
      devisItemsContainer.innerHTML = `<p class="devis-item-no-products">Aucun produits ajoutés au devis</p>`;
    }
    if (devisCartList) {
      devisCartList.innerHTML = `<p class="devis-item-no-products">Aucun produits ajoutés au devis</p>`;
    }
  }
}

function createDevisElement(item) {
  const productElement = document.createElement('div');
  productElement.className = 'devis-item';
  productElement.innerHTML = `
            <div class="devis-item-image">
            <img src="${item.imageUrl}" alt="${item.productName}" />
            </div>
            <div class="devis-item-content">
            <p class="devis-item-content-name">${item.productName}</p>
            <p class="devis-item-content-quantity"><span>Quantité : </span><span>${item.quantity}</span></p>
            <a class="devis-item-content-modify" href="${item.link}">Modifier</a>
            </div>
            <div class="devis-item-actions">
            <a class="devis-item-actions-modify" href="${item.link}">Modifier</a>
            <button class="devis-item-actions-delete" onclick="deleteProduct('${item.uniqueId}')">
            <img src="/wp-content/plugins/mak2com-quotes/assets/img/trash.svg" alt="icone de corbeille">
            </button>
            </div>
        `;
  return productElement;
}

function togglePopupDevis() {
  const body = document.querySelector('body');
  const popup = document.getElementById('quotePopup');
  const popupBtn = document.getElementById('headerDevisBtn');
  const cartPopup = document.querySelector('.cart_sidebar_widget');
  if (!popup.classList.contains('active')) {
    if (cartPopup) {
      if (cartPopup.classList.contains('active')) {
        cartPopup.classList.remove('active')
      }
    }
    popup.classList.add('active')
    body.classList.add('overflow-full-hidden')
    popupBtn.setAttribute('aria-selected', 'true');
  } else {
    popupBtn.setAttribute('aria-selected', 'false');
    body.classList.remove('overflow-full-hidden')
    popup.classList.remove('active')
  }
}

function deleteProduct(uniqueId) {
  let devis = JSON.parse(localStorage.getItem('devis') || '[]');
  const updatedDevis = devis.filter(item => item.uniqueId != uniqueId);
  localStorage.setItem('devis', JSON.stringify(updatedDevis));
  updatePopupDevis();
}

function preselectOptions() {
  const currentProductId = document.querySelector('.pro-single-page').getAttribute('data-product-id');
  const devis = JSON.parse(localStorage.getItem('devis') || '[]');
  const productInDevis = devis.find(item => item.productId === currentProductId);

  if (productInDevis) {
    document.getElementById('quantity').value = productInDevis.quantity;
    Object.keys(productInDevis.options).forEach(optionName => {
      const optionValue = productInDevis.options[optionName];
      const input = document.querySelector(`input[name="${optionName}"][value="${optionValue}"]`);
      if (input) {
        input.checked = true;
      }
    });
  }
}

function adjustFormHeight(stepElement) {
  var form = document.getElementById('quoteForm');
  var stepHeight = stepElement.offsetHeight;
  form.style.height = `${stepHeight}px`;
}

function updateBreadcrumb(activeStepIndex) {
  // Sélectionner tous les éléments de la liste de navigation
  const breadcrumbItems = document.querySelectorAll('.quote-breadcrumb-item');
  // Supprimer la classe 'active-step' de tous les éléments
  breadcrumbItems.forEach(item => {
    item.classList.remove('active-step');
  });
  // Ajouter la classe 'active-step' à l'élément de l'étape actuelle
  if(breadcrumbItems[activeStepIndex]) {
    breadcrumbItems[activeStepIndex].classList.add('active-step');
  }
}

document.getElementById('quoteForm').addEventListener('submit', function(event) {
  // Rendre toutes les étapes visibles temporairement
  const steps = document.querySelectorAll('.form-step');
  steps.forEach(step => {
    step.style.display = 'block';
  });

  // Vérifier la validité du formulaire
  if (!this.checkValidity()) {
    event.preventDefault();
    // Si invalide, restaurer l'affichage des étapes initiales
    steps.forEach((step, index) => {
      if (index !== steps.length - 1) {
        step.style.display = 'none';
      }
    });
    alert('Veuillez remplir tous les champs obligatoires.');
    return false;
  }

  // Si valide, restaurer l'affichage des étapes initiales avant la soumission
  steps.forEach((step, index) => {
    if (index !== steps.length - 1) {
      step.style.display = 'none';
    }
  });
});

function nextStep(currentStep, nextStep) {
  var currentEl = document.getElementById(`step${currentStep}`);
  var nextEl = document.getElementById(`step${nextStep}`);

  currentEl.classList.remove('translate-in');
  currentEl.classList.add('translate-left');
  setTimeout(function() {
    currentEl.style.display = 'none';
    nextEl.style.display = 'flex';
    adjustFormHeight(nextEl);
    nextEl.classList.add('translate-in');
    nextEl.classList.remove('translate-right');
    updateBreadcrumb(nextStep - 1);
  }, 500);
}

function prevStep(currentStep, prevStep) {
  var currentEl = document.getElementById(`step${currentStep}`);
  var prevEl = document.getElementById(`step${prevStep}`);

  currentEl.classList.remove('translate-in');
  currentEl.classList.add('translate-right');
  setTimeout(function() {
    currentEl.style.display = 'none';
    prevEl.style.display = 'flex';
    adjustFormHeight(prevEl);
    prevEl.classList.add('translate-in');
    prevEl.classList.remove('translate-left');
    updateBreadcrumb(prevStep - 1);
  }, 500);
}

document.addEventListener("DOMContentLoaded", function () {
  let body = document.querySelector('body')
  if (body.classList.contains('single-produit-pro')) {
    preselectOptions()
    quoteStorage()
  }
  updatePopupDevis()
});