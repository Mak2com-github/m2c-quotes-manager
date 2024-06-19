<!DOCTYPE html>
<html lang="fr">
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { width: 80%; margin: auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 10px 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { background-color: #f0f0f0; padding: 10px 20px; text-align: center; font-size: 12px; }
        .content-title { margin-bottom: 1rem; font-size: 30px; }
    </style>
    <title>Notification de demande de devis</title>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Nouvelle demande de devis</h1>
        <p>Une nouvelle demande de devis a été envoyée depuis le site BideanTrail</p>
    </div>
    <div class="content">
        <h2 class="content-title">Informations de la Demande : </h2>
        <h3>Client & Évènement :</h3>
        <div style="display: flex; flex-direction: row; justify-content: space-evenly;">
            <ul style="width: 45%;">
                <li style="margin: 0.5rem 0;">
                    <p style="font-size: 16px;"><span style="font-weight: 800;">Nom : </span><span><?= $form_data['nom']; ?></span></p>
                </li>
                <li style="margin: 0.5rem 0;">
                    <p style="font-size: 16px;"><span style="font-weight: 800;">Prénom : </span><span><?= $form_data['prenom']; ?></span></p>
                </li>
                <li style="margin: 0.5rem 0;">
                    <p style="font-size: 16px;"><span style="font-weight: 800;">Email : </span><span><?= $form_data['email']; ?></span></p>
                </li>
                <li style="margin: 0.5rem 0;">
                    <p style="font-size: 16px;"><span style="font-weight: 800;">Téléphone : </span><span><?= $form_data['telephone']; ?></span></p>
                </li>
                <li style="margin: 0.5rem 0;">
                    <p style="font-size: 16px;"><span style="font-weight: 800;">Inscription à la newsletter : </span><span><?php if ($form_data['infos'] === 'on') { echo 'Oui'; } else { echo 'Non'; } ?></span></p>
                </li>
            </ul>
            <ul style="width: 45%;">
                <li style="margin: 0.5rem 0;">
                    <p style="font-size: 16px;"><span style="font-weight: 800;">Nom de l'association ou de l'entreprise : </span><span><?= $form_data['association']; ?></span></p>
                </li>
                <li style="margin: 0.5rem 0;">
                    <p style="font-size: 16px;"><span style="font-weight: 800;">Nom de l'évènement : </span><span><?= $form_data['nom_evenement']; ?></span></p>
                </li>
                <li style="margin: 0.5rem 0;">
                    <p style="font-size: 16px;"><span style="font-weight: 800;">Type de course : </span><span><?= $form_data['type_course']; ?></span></p>
                </li>
                <li style="margin: 0.5rem 0;">
                    <p style="font-size: 16px;"><span style="font-weight: 800;">Kilométrage : </span><span><?= $form_data['kilometrage']; ?></span></p>
                </li>
                <li style="margin: 0.5rem 0;">
                    <p style="font-size: 16px;"><span style="font-weight: 800;">Date de l'évènement : </span><span><?= $form_data['date_evenement']; ?></span></p>
                </li>
            </ul>
        </div>
        <div>
            <p style="font-weight: 800;">Message : </p>
            <p><?= $form_data['message']; ?></p>
        </div>
        <div>
            <?php
            if (!empty($form_data['devisItems'])) {
                ?>
                <h2 class='content-title'>Liste des produits</h2>
                <ul>
                <?php
                foreach ($form_data['devisItems'] as $item) {
                    ?>
                    <li style="display: flex; flex-direction: row; justify-content: space-evenly;">
                        <div style="width: 45%; text-align: center;">
                            <h3><?= $item['productName'] ?></h3>
                            <img style="display: block; margin: 0 auto;" src="<?= $item['imageUrl'] ?>" alt="<?= $item['productName'] ?>" width="200px" height="auto">
                            <a style="border: 1px solid black; padding: 5px 15px; display: block; margin: 1rem auto 0; width: fit-content;" href="<?= $item['link'] ?>" target="_blank" rel="noopener noreferrer">Voir le produit</a>
                        </div>
                        <div style="width: 45%;">
                            <p><span style="font-weight: 800">Quantitée : </span><span><?= $item['quantity'] ?></span></p>
                            <h3 style="margin: 1rem 0; font-weight: 800; font-size: 25px;">Options : </h3>
                            <ul>
                                <?php
                                foreach ($item['options'] as $key => $value) {
                                    ?>
                                    <li><strong><?= htmlspecialchars($key) ?>:</strong> <?= htmlspecialchars($value) ?></li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </li>
                <?php
                }
                ?>
                </ul>
            <?php
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>
