<?php
use App\Helpers\Component;
?>

<div class="cart-modal-backdrop" id="checkoutM">
<div id="modalCustom" class="cart-modal-container">

  <div class="modal-custom-overlay" onclick="closeCustomModal()"></div>

  <div class="modal-custom-dialog">

    <div class="modal-custom-header">
      <span id="modalCustomTitle" class="modal-custom-title"></span>

      <button class="modal-custom-close" onclick="closeCustomModal()">
        &times;
      </button>
    </div>

    <div class="modal-custom-body">
      <iframe id="modalCustomIframe"></iframe>
    </div>

  </div>

</div>
</div>

<script>

function openCustomModal(title, url)
{
  const modal = document.getElementById('modalCustom');
  const modalAll = document.getElementById('checkoutM');
  const modalTitle = document.getElementById('modalCustomTitle');
  const iframe = document.getElementById('modalCustomIframe');

  modalTitle.innerText = title;
  iframe.src = url;

  modal.classList.add('active');
  document.body.style.overflow = 'hidden';

  modalAll.classList.add('active');
}


function closeCustomModal()
{
  const modalAll = document.getElementById('checkoutM');
  const modal = document.getElementById('modalCustom');
  const iframe = document.getElementById('modalCustomIframe');

  modal.classList.remove('active');
  modalAll.classList.remove('active');
  iframe.src = '';

  document.body.style.overflow = '';
}


document.addEventListener('keydown', function(e)
{
  if (e.key === "Escape")
  {
    closeCustomModal();
  }
});

</script>