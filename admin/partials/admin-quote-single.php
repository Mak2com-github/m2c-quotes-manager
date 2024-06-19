<?php
/*
 *
 */

$datas = [];
if (isset($devis)) :
    $datas = json_decode($devis->datas, true);
endif;
?>

<div class="quote_single_page">
    <h1 class="quote_single_title">Détails de la demande</h1>

    <div class="quote_single_body">
        <div class="quote_single_general">
            <h2 class="quote_single_general_title">Informations Générales</h2>
            <table class="quote_single_general_infos">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="quote_single_general_table">
                    <tr class="quote_single_general_table_row">
                        <td class="key">Nom</td>
                        <td class="value"><?php echo isset($datas['nom']) ? esc_html($datas['nom']) : ''; ?></td>
                    </tr>
                    <tr class="quote_single_general_table_row">
                        <td class="key">Prénom</td>
                        <td class="value"><?php echo isset($datas['prenom']) ? esc_html($datas['prenom']) : ''; ?></td>
                    </tr>
                    <tr class="quote_single_general_table_row">
                        <td class="key">Association</td>
                        <td class="value"><?php echo isset($datas['association']) ? esc_html($datas['association']) : ''; ?></td>
                    </tr>
                    <tr class="quote_single_general_table_row">
                        <td class="key">Email</td>
                        <td class="value"><?php echo isset($datas['email']) ? esc_html($datas['email']) : ''; ?></td>
                    </tr>
                    <tr class="quote_single_general_table_row">
                        <td class="key">Téléphone</td>
                        <td class="value"><?php echo isset($datas['telephone']) ? esc_html($datas['telephone']) : ''; ?></td>
                    </tr>
                    <tr class="quote_single_general_table_row">
                        <td class="key">Nom de l'évènement</td>
                        <td class="value"><?php echo isset($datas['nom_evenement']) ? esc_html($datas['nom_evenement']) : ''; ?></td>
                    </tr>
                    <tr class="quote_single_general_table_row">
                        <td class="key">Type de course</td>
                        <td class="value"><?php echo isset($datas['type_course']) ? esc_html($datas['type_course']) : ''; ?></td>
                    </tr>
                    <tr class="quote_single_general_table_row">
                        <td class="key">Kilométrage</td>
                        <td class="value"><?php echo isset($datas['kilometrage']) ? esc_html($datas['kilometrage']) : ''; ?></td>
                    </tr>
                    <tr class="quote_single_general_table_row">
                        <td class="key">Date de l'évènement</td>
                        <td class="value"><?php echo isset($datas['date_evenement']) ? esc_html($datas['date_evenement']) : ''; ?></td>
                    </tr>
                    <tr class="quote_single_general_table_row">
                        <td class="key">Message</td>
                        <td class="value"><?php echo isset($datas['message']) ? esc_html($datas['message']) : ''; ?></td>
                    </tr>
                    <tr class="quote_single_general_table_row">
                        <td class="key">Accord pour la newsletter</td>
                        <td class="value">
                            <?php if(isset($datas['newsletter'])) : ?>
                                <img class="icon" src="<?= plugin_dir_url(__FILE__) ?>../../assets/img/square-check.svg" alt="">
                            <?php else : ?>
                                <img class="icon" src="<?= plugin_dir_url(__FILE__) ?>../../assets/img/square-check.svg" alt="">
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="quote_single_products">
            <h2 class="quote_single_products_title">Produits</h2>
            <?php if (!empty($datas['devisItems'])): ?>
                <div class="quote_single_products_list">
                    <?php foreach ($datas['devisItems'] as $item): ?>
                        <div class="quote_single_product-details">
                            <div class="quote_single_product-col-left">
                                <h3 class="quote_single_product-details-title"><?php echo esc_html($item['productName']); ?></h3>
                                <a href="<?php echo esc_url($item['link']); ?>">
                                    <img class="quote_single_product-details-image" src="<?php echo esc_url($item['imageUrl']); ?>" alt="Image du produit">
                                </a>
                            </div>
                            <div class="quote_single_product-col-right">
                                <p class="quote_single_product-details-quantity"><span>Quantité :</span><span><?php echo esc_html($item['quantity']); ?></span></p>
                                <p class="quote_single_product-details-options">Options : </p>
                                <ul>
                                    <?php foreach ($item['options'] as $optionName => $optionValue): ?>
                                        <li>
                                            <span><?php echo esc_html($optionName); ?></span>
                                            <span><?php echo esc_html($optionValue); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Aucun produit ajouté à la demande.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
