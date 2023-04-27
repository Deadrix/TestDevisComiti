<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis abonnement club</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>

<?php

session_start();

if (isset($_POST['nb_adherents'], $_POST['nb_sections'], $_POST['federation'])) {


// Récupération des données entrées par l'utilisateur
    $nb_adherents = intval($_POST['nb_adherents']);
    $nb_sections = intval($_POST['nb_sections']);
    $federation = $_POST['federation'];

// Calcul du prix HT pour les adhérents en fonction du nombre d'adhérents
    if ($nb_adherents <= 100) {
        $prix_adherents_ht = 10 * 12;
    } elseif ($nb_adherents <= 200) {
        $prix_adherents_ht = 0.10 * $nb_adherents * 12;
    } elseif ($nb_adherents <= 500) {
        $prix_adherents_ht = 0.09 * $nb_adherents * 12;
    } elseif ($nb_adherents <= 1000) {
        $prix_adherents_ht = 0.08 * $nb_adherents * 12;
    } elseif ($nb_adherents <= 10000) {
        $nb_tranches = ceil($nb_adherents / 1000);
        $prix_adherents_ht = 70 * $nb_tranches * 12;
    } else {
        $prix_adherents_ht = 1000 * 12;
    }

// Calcul du prix HT pour les sections en fonction du nombre de sections
    if ($nb_adherents > 1000) {
        $nb_sections_offertes = 1;
    } else {
        $nb_sections_offertes = 0;
    }
// Calcul de l'avantage de la fédération Natation
    if ($federation == 'Natation') {
        $nb_sections_offertes += 3;
    }

// Avant l'ajout de dernière minute
// $prix_sections_ht = max($nb_sections - $nb_sections_offertes, 0) * 5 * 12;

// Ajout du chef de projet
    $MoisCourant = intval(date("m"));
//    $MoisCourant = 5;

    $sectionsDemi = 0;
    $sectionsPleine = 0;

    for ($i = 1; $i <= $nb_sections; $i++) {
        if ($i % $MoisCourant == 0) {
            $sectionsDemi += 1;
        } else {
            $sectionsPleine += 1;
        }
    }
    $sectionsOffertesRestantes = $nb_sections_offertes;
    $sectionsPleineAFacturer = max($sectionsPleine - $sectionsOffertesRestantes, 0);
    $sectionsOffertesRestantes -= min($nb_sections_offertes, $sectionsPleine);
    // Ensuite, on offre les sections demi tarif
    $sectionsDemiAFacturer = max($sectionsDemi - $sectionsOffertesRestantes, 0);

    $prix_sections_ht = ($sectionsPleineAFacturer * 5 + $sectionsDemiAFacturer * 3) * 12;
// Affichage des variables pour le debug
//    var_dump(array(
//        '$MoisCourant' => $MoisCourant,
//        '$nb_sections_offertes' => $nb_sections_offertes,
//        '$sectionsPleine' => $sectionsPleine,
//        '$sectionsDemi' => $sectionsDemi,
//        '$sectionsPleineAFacturer' => $sectionsPleineAFacturer,
//        '$sectionsDemiAFacturer' => $sectionsDemiAFacturer,
//    ));


// Calcul de l'avantage de la fédération
    switch ($federation) {
        case 'Gymnastique':
            $prix_adherents_ht *= 0.85;
            break;
        case 'Basketball':
            $prix_sections_ht *= 0.7;
            break;
        default:
            // Aucun avantage pour les autres fédérations
            break;
    }

// Calcul du prix TTC
    $prix_ht = $prix_adherents_ht + $prix_sections_ht;
    $tva = 0.2;
    $prix_ttc = $prix_ht * (1 + $tva);
    $_SESSION['resultat'] = round($prix_ttc, 2);
}
?>

<body class="vh-100 d-flex flex-column">

<header class="bg-secondary p-3">
    <h1 class="text-center text-bg-secondary">Devis Annuel Comiti Sport</h1>
</header>

<main class="container d-flex flex-column flex-grow-1 justify-content-center">

    <!--Action pour retourner sur la même page apres l'envoi du formulaire-->
    <!--Remplissage du formulaire avec les données saisies précédemment-->
    <!--echo isset($_POST['nb_adherents']) ? $_POST['nb_adherents'] : '';-->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="mb-3">
            <label for="nb_adherents" class="form-label">Nombre d'adhérents</label>
            <input type="number" class="form-control" id="nb_adherents" name="nb_adherents"
                   value="<?php echo isset($_POST['nb_adherents']) ? $_POST['nb_adherents'] : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="nb_sections" class="form-label">Nombre de sections</label>
            <input type="number" class="form-control" id="nb_sections" name="nb_sections"
                   value="<?php echo isset($_POST['nb_sections']) ? $_POST['nb_sections'] : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="federation" class="form-label">Fédération</label>
            <select class="form-select" id="federation" name="federation">
                <option value="">Choisissez une option (aucune par défaut)</option>
                <option value="Natation" <?php if (isset($_POST['federation']) && $_POST['federation'] == "Natation") {
                    echo "selected";
                } ?>>Natation
                </option>
                <option value="Gymnastique" <?php if (isset($_POST['federation']) && $_POST['federation'] == "Gymnastique") {
                    echo "selected";
                } ?>>Gymnastique
                </option>
                <option value="Basketball" <?php if (isset($_POST['federation']) && $_POST['federation'] == "Basketball") {
                    echo "selected";
                } ?>>Basketball
                </option>
                <option value="Autre" <?php if (isset($_POST['federation']) && $_POST['federation'] == "Autre") {
                    echo "selected";
                } ?>>Autre
                </option>
            </select>
        </div>
        <div class="row row-cols-md-3 row-cols-1 row-col">
            <div class="col">
                <button type="submit" class="btn btn-lg btn-outline-success w-100">Calculer le prix</button>
            </div>

            <div class="col">
                <button type="button" class="btn btn-lg btn-outline w-100 fw-bold" disabled>
                    <!--Affichage du résultat sous le formulaire-->
                    <?php echo (isset($_SESSION['resultat'], $_POST['nb_adherents'], $_POST['nb_sections'], $_POST['federation'])) ? $_SESSION['resultat'] . " €" : "....€" ?>
                </button>
            </div>

            <div class="col">
                <!--Reset du formulaire via reload de la page pour vider la variable $_POST-->
                <button type="button" class="btn btn-lg btn-outline-danger w-100"
                        onclick="location.href='<?php echo $_SERVER['PHP_SELF']; ?>'"> Vider le formulaire
                </button>
            </div>
        </div>
    </form>

</main>

<footer class="p-3 bg-secondary">
    <div class="text-center">
        <span class="text-bg-secondary">© 2023 Bouriche Alexandre</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
</body>
</html>


