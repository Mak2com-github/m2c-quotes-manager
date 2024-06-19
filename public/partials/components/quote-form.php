<?php
/*
 * Formulaire de soumission du devis
 * @author Alexandre Celier
 * @version 1.0
 * @since 1.0
 * @link https://mak2com.fr
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 * @package Mak2com Quotes
 */
?>

<div class="quote-form-page">
    <div class="quote-breadcrumb">
        <ul class="quote-breadcrumb-list">
            <li class="quote-breadcrumb-item active-step">
                <p>Informations générales</p>
            </li>
            <li class="quote-breadcrumb-item">
                <p>Votre évènement</p>
            </li>
            <li class="quote-breadcrumb-item">
                <p>Autres besoins</p>
            </li>
            <li class="quote-breadcrumb-item">
                <p>Votre message</p>
            </li>
        </ul>
    </div>
    <div class="quote-form-container">
        <div id="success-message" style="display: none;">
            <h2>Votre demande a été soumise avec succès !</h2>
            <p>Merci de nous avoir contactés. Nous vous répondrons dans les plus brefs délais.</p>
            <button type="button" onclick="window.location.href='/'">Retourner à la page d'accueil</button>
        </div>
        <form id="quoteForm">
            <div class="form-step translate-in" id="step1">
                <div class="form-step-head">
                    <h2><span>Information générales</span> <span>1</span></h2>
                </div>
                <div class="form-step-body">
                    <div class="form-step-field">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" placeholder="Nom" required>
                    </div>
                    <div class="form-step-field">
                        <label for="prenom">Prénom</label>
                        <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>
                    </div>
                    <div class="form-step-field">
                        <label for="association">Nom de l'association ou de l'entreprise</label>
                        <input type="text" id="association" name="association" placeholder="Nom de l'association ou de l'entreprise" required>
                    </div>
                    <div class="form-step-field">
                        <label for="email">Mail</label>
                        <input type="email" id="email" name="email" placeholder="Mail" required>
                    </div>
                    <div class="form-step-field">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" placeholder="Téléphone" required>
                    </div>
                </div>
                <div class="form-step-footer">
                    <button type="button" class="form-next-step" onclick="nextStep(1, 2)"><span>Étape suivante</span></button>
                </div>
            </div>

            <div class="form-step translate-right" id="step2" style="display: none;">
                <div class="form-step-head">
                    <h2><span>Votre évènement</span> <span>2</span></h2>
                    <p>Renseignez ci-dessous les détails de votre évènement</p>
                </div>
                <div class="form-step-body">
                    <div class="form-step-field">
                        <label for="nom_evenement">Nom de votre évènement</label>
                        <input type="text" id="nom_evenement" name="nom_evenement" placeholder="Nom de votre événement" required>
                    </div>
                    <div class="form-step-field">
                        <label for="type_course">Type de course</label>
                        <select id="type_course" name="type_course" placeholder="Type de course" required>
                            <option value="">Type de course</option>
                            <option value="5km">5km</option>
                            <option value="10km">10km</option>
                            <option value="marathon">Marathon</option>
                        </select>
                    </div>
                    <div class="form-step-field">
                        <label for="kilometrage">Kilométrage</label>
                        <input type="number" id="kilometrage" name="kilometrage" placeholder="" min="1" max="10000" required>
                    </div>
                    <div class="form-step-field">
                        <label for="date_evenement">Date de l'évènement</label>
                        <input type="date" id="date_evenement" name="date_evenement" placeholder="jj/mm/aaaa" required>
                    </div>
                </div>
                <div class="form-step-footer">
                    <button type="button" class="form-prev-step" onclick="prevStep(2, 1)"><span>Étape précédente</span></button>
                    <button type="button" class="form-next-step" onclick="nextStep(2, 3)"><span>Étape suivante</span></button>
                </div>
            </div>

            <div class="form-step translate-right" id="step3" style="display: none;">
                <div class="form-step-head">
                    <p>Optionnel</p>
                    <h2><span>Vos autres besoins en communication</span> <span>3</span></h2>
                    <p>Avez-vous pensé à tout ?</p>
                    <p>Si par hasard il vous manque un élément, n'hésitez pas à revisiter les fiches produits en choisissant une catégorie ci-dessous.</p>
                    <p>Si tout est en ordre, vous pouvez passer à l'étape suivante en sélectionnant "Étape suivante".</p>
                </div>
                <div class="form-step-body">
                    <?php
                    $terms = get_terms(array(
                        'taxonomy' => 'category-pro',
                        'hide_empty' => true,
                        'parent' => 0,
                    ));
                    if (!empty($terms) && !is_wp_error($terms)) :
                    ?>
                    <div class="form-other-categories">
                        <?php foreach ($terms as $term) : ?>
                        <a class="form-other-categories-link" href="<?php echo esc_url(get_term_link($term)); ?>">
                            <span><?= $term->name ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="form-step-footer">
                    <button type="button" class="form-prev-step" onclick="prevStep(3, 2)"><span>Étape précédente</span></button>
                    <button type="button" class="form-next-step" onclick="nextStep(3, 4)"><span>Étape suivante</span></button>
                </div>
            </div>

            <div class="form-step translate-right" id="step4" style="display: none;">
                <div class="form-step-head">
                    <h2><span>Votre message</span> <span>4</span></h2>
                    <p>Ajoutez ci-dessous tous les renseignements liés à votre évènement ou toute autre demande</p>
                </div>
                <div class="form-step-body">
                    <div class="form-step-field">
                        <label for="message">Votre message :</label>
                        <textarea id="message" name="message" placeholder="Votre Message" rows="10"></textarea>
                    </div>
                    <div class="form-step-field">
                        <input type="checkbox" id="infos" name="infos">
                        <label for="infos">J'accepte de recevoir des infos de Bideantrail</label>
                    </div>
                    <div class="form-step-field">
                        <input type="checkbox" id="rgpd" name="rgpd" required>
                        <label for="rgpd">RGPD</label>
                    </div>
                </div>
                <div class="form-step-footer">
                    <button type="button" class="form-prev-step" onclick="prevStep(4, 3)"><span>Étape précédente</span></button>
                    <button type="submit" class="form-next-step"><span>Envoyer votre demande</span></button>
                </div>
            </div>
        </form>
        <div class="quote-form-sidelist">
            <div id="devisCartList" class="m2c_qp_list"></div>
        </div>
    </div>
</div>
