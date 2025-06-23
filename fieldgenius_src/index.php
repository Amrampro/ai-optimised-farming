<?php
// Affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

$result = null;
$image_src = "assets/mais.jpg";

// Définir les périodes de semailles pour chaque culture
$periods = [
    'maïs' => 'Mars à Mai et Août à Septembre',
    'haricot' => 'Mars à Mai et Septembre à Octobre',
    'arachide' => 'Avril à Juin et Août à Septembre'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les valeurs du formulaire
    $culture = isset($_POST['culture']) ? $_POST['culture'] : '';
    $superficie = isset($_POST['superficie']) ? $_POST['superficie'] : '';
    $seeds = isset($_POST['seeds']) ? $_POST['seeds'] : '';
    $soil_type = isset($_POST['soil_type']) ? $_POST['soil_type'] : 'normal';
    // Pour l'engrais, si la case est cochée, on récupère "1", sinon "0"
    $use_engrais = isset($_POST['use_engrais']) ? "1" : "0";

    // Mettre à jour l'image affichée en fonction de la culture
    switch ($culture) {
        case "maïs":
            $image_src = "assets/mais.jpg";
            break;
        case "haricot":
            $image_src = "assets/haricot.jpg";
            break;
        case "arachide":
            $image_src = "assets/arachide.jpg";
            break;
        default:
            $image_src = "assets/mais.jpg";
    }

    // Construction de la commande (adaptation selon votre configuration Python)
    $python = "python"; // ou "python3" selon votre config
    $command = $python . " predict.py --culture " . escapeshellarg($culture);

    if (!empty($superficie)) {
        $command .= " --superficie " . escapeshellarg($superficie);
    }
    if (!empty($seeds)) {
        $command .= " --seeds " . escapeshellarg($seeds);
    }
    // Ajout du type de sol
    if (!empty($soil_type)) {
        $command .= " --soil_type " . escapeshellarg($soil_type);
    }
    // Ajout de l'utilisation d'engrais
    $command .= " --use_engrais " . escapeshellarg($use_engrais);

    // Exécuter le script Python et récupérer la sortie
    $output = shell_exec($command);

    // Vérifier si $output est bien une chaîne
    if (is_string($output)) {
        $output = trim($output);
        // Conversion d'encodage si nécessaire
        $output = mb_convert_encoding($output, 'UTF-8', 'auto');

        // Tenter de décoder le JSON
        $decoded = json_decode($output, true);

        if ($decoded === null) {
            // Erreur de décodage JSON
            $result = [
                "error" => "Erreur lors du décodage JSON. Sortie brute : " . $output,
                "json_last_error" => json_last_error_msg()
            ];
        } else {
            // Ajout de la période de semailles dans le résultat
            if (isset($periods[$culture])) {
                $decoded['period'] = $periods[$culture];
            } else {
                $decoded['period'] = 'Période inconnue';
            }
            $result = $decoded;
        }
    } else {
        // $output est null ou non une chaîne
        $result = ["error" => "Aucune sortie renvoyée par le script Python."];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Prédiction de Rendement Agricole</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="container">
        <h1>Prédiction de Rendement Agricole</h1>
        <h3>Field Genius</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label for="culture">Culture :</label>
                <select name="culture" id="culture" required onchange="updateImage()">
                    <option value="maïs">Maïs</option>
                    <option value="haricot">Haricot</option>
                    <option value="arachide">Arachide</option>
                </select>
            </div>
            <div class="form-group">
                <label for="superficie">Superficie du champ (optionnel) : <br> <i><small>Estimé en hectare</small></i></label>
                <input type="number" step="any" name="superficie" id="superficie" placeholder="Ex: 10">
            </div>
            <div class="form-group">
                <label for="seeds">Quantité de semailles disponible (optionnel) : <br> <i><small>Estimé en seaux de 15L</small></i> </label>
                <input type="number" step="any" name="seeds" id="seeds" placeholder="Ex: 50">
            </div>
            <div class="form-group">
                <label for="soil_type">Type de sol :</label>
                <select name="soil_type" id="soil_type" required>
                    <option value="normal">Normal</option>
                    <option value="fertile">Fertile</option>
                    <option value="non_fertile">Non fertile</option>
                </select>
            </div>
            <div class="form-group">
                <label for="use_engrais">
                    <input type="checkbox" name="use_engrais" id="use_engrais" value="1">
                    Utiliser l'engrais
                </label>
            </div>
            <input type="submit" value="Calculer">
        </form>

        <div class="image-container">
            <img id="cultureImage" src="<?= $image_src ?>" alt="Image de culture">
        </div>

        <?php if (!empty($result)): ?>
            <div class="result">
                <h2>Résultat :</h2>
                <!-- <pre><?php print_r($result); ?></pre> -->
                <?php foreach ($result as $key => $value): ?>
                    <?php if ($key == "culture" && $value == "ma?s") {
                        $value = "Maïs";
                    }
                    ?>
                    <p><strong><?= $key ?>:</strong> <?= $value ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function updateImage() {
            var select = document.getElementById("culture");
            var image = document.getElementById("cultureImage");
            var selectedValue = select.value;

            switch (selectedValue) {
                case "maïs":
                    image.src = "assets/mais.jpg";
                    image.alt = "Image de maïs";
                    break;
                case "haricot":
                    image.src = "assets/haricot.jpg";
                    image.alt = "Image de haricot";
                    break;
                case "arachide":
                    image.src = "assets/arachide.jpg";
                    image.alt = "Image d'arachide";
                    break;
                default:
                    image.src = "";
                    image.alt = "Image de culture";
            }
        }
    </script>
</body>

</html>