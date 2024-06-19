<?php
/*
 * Quote Popup Component
 * @author Alexandre Celier
 * @version 1.0
 * @since 1.0
 * @link https://mak2com.fr
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 * @package Mak2com Quotes
 */
?>

<div id="quotePopup" class="m2c_quotes_popup">
    <div class="m2c_qp_container">
        <h2 class="m2c_qp_title">Devis</h2>
        <div id="devisItems" class="m2c_qp_list"></div>
        <div class="m2c_qp_actions">
            <div class="m2c_qp_actions_row">
                <button id="closePopup" class="m2c_qp_close" onclick="togglePopupDevis()">
                    <span>Continuer mon devis</span>
                </button>
                <a class="m2c_qp_submit" href="<?= home_url() ?>/devis-pro">
                    <span>Valider mon devis</span>
                </a>
            </div>
        </div>
    </div>
</div>