<?php 
// PAGE D'ACCUEIL
// inclusion initiale
include_once("./includes/init.inc.php");

// Validation de l'id passée par URL
if(!is_numeric($_GET['id'])) {
    header("location: ./index.php");
    exit();
} else {
    if(!is_int((int)$_GET['id'])) {
        header("location: ./index.php");
        exit();
    }
}

// recupération de l'id de l'employé concerné
$idEmploye = (int)$_GET['id'];

// recupération de l'employé dont on a récupéré l'id
$stmt = $pdo->prepare("
    SELECT * FROM employes WHERE id_employes=:id;
");
$stmt->bindParam(":id", $idEmploye, PDO::PARAM_INT);

try {
    $stmt->execute();
} catch(PDOException $e) {
    if($env === 'dev'){
        $errors[] = $e->getMessage();
    } else {
        $errors[] = "Une erreur inattendue est survenue";
    }
}

$employe = $stmt->fetch();

// debug
if($env === 'dev') {
    incomingData();
    showvar($employe);
}

// tableau des genres:
$tabGenre = [
    'm' => 'Masculin',
    'f' => 'Féminin',
];


$formValue = $employe;

// traitement du formulaire
// récupération des données:
if(isset($_POST['updateEmployeeSubmitBtn'])) {
    // le bouton de soumission du formulaire a été cliqué
    extract($_POST);

    // Validation des données
    // le nom:
    if(!validateData($nom, TYPE_STRING)) {
        $errors['nom'] = "Le champs 'nom' n'est pas valide!";
    }

    if(!validateData($prenom, TYPE_STRING)) {
        $errors['prenom'] = "Le champs 'prenom' n'est pas valide!";
    }

    if(!validateData($sexe, TYPE_STRING)) {
        $errors['sexe'] = "Le champs 'genre' n'est pas valide!";
    }

    if(empty($sexe)){
        $errors['sexe'] = "Le champs 'genre' est obligatoire!";
    }

    if(!in_array($sexe, ['m', 'f'], true)){
        $errors['sexe'] = "Le champs 'genre' n'est pas valide!";
    }

    if(!validateData($service, TYPE_STRING)) {
        $errors['service'] = "Le champs 'service' n'est pas valide!";
    }

    if(!validateDate($date_embauche)){
        $errors['date'] = "Le champs 'date' n'est pas valide!";
    }
    
    if(!is_numeric($salaire)){
        $errors['salaire'] = "Le champs 'salaire' doit obligatoirement être un entier!";
    }

    if(!count($errors)){
        // pas d'erreur de validation trouvées

        // on prepare la requete
        $stmt = $pdo->prepare("
            UPDATE employes
            SET nom=:nom,
            prenom=:prenom,
            sexe=:sexe,
            service=:service,
            date_embauche=:date_embauche,
            salaire=:salaire

            WHERE id_employes=:id_employes
        ");

        // on relie les marqueurs de position aux valeurs à vérifier puis inserer
        $stmt->bindParam(':id_employes', $idEmploye, PDO::PARAM_INT);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':sexe', $sexe, PDO::PARAM_STR);
        $stmt->bindParam(':service', $service, PDO::PARAM_STR);
        $stmt->bindParam(':date_embauche', $date_embauche, PDO::PARAM_STR);
        $stmt->bindParam(':salaire', $salaire, PDO::PARAM_INT);

        // on execute la requete
        try {
            $stmt->execute();
        } catch(PDOException $e) {
            if($env === 'dev'){
                $errors[] = $e->getMessage();
            } else {
                $errors[] = "Une erreur inattendue est survenue";
            }
        }

        if(!count($errors)) {
            $success[] = 'Employé correctement modifié.';
            $stmt = $pdo->prepare("
                SELECT * FROM employes WHERE id_employes=:id;
            ");
            $stmt->bindParam(":id", $idEmploye, PDO::PARAM_INT);

            try {
                $stmt->execute();
            } catch(PDOException $e) {
                if($env === 'dev'){
                    $errors[] = $e->getMessage();
                } else {
                    $errors[] = "Une erreur inattendue est survenue";
                }
            }

            $employe = $stmt->fetch();
            $formValue = $employe;
        }
        
    }

}



// titre de la page
$titrePrincipal = 'Modification';

// id du body
$bodyId = 'update-employee';

// Affichage de la page
// Header:
include_once("./includes/header.php");
?>
<!-- Contenu de la page -->
<div class="container-col">
    <div class="container-struct form-container">
        <h3>Mettre à jour un employé</h3>

        <!-- Zone de notif erreur -->
        <?php if(count($errors)): ?>
            <div class="w-100 mt-3 mb-3 form-error-container">
                <ul>
                    <?php foreach($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <!-- Zone de notif succes -->
        <?php if(count($success)): ?>
            <div class="w-100 mt-3 mb-3 form-success-container">
                <ul>
                    <?php foreach($success as $succes): ?>
                        <li><?= $succes ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <!-- bouton de retour  à la liste -->
        <a href="<?= HTTP_SITE_URL . '/?page=' . $_SESSION['currentPage'] ?>">
        <button type="button" class="btn btn-primary mt-3 ms-3 mb-5"> <-- Retour à la liste</button>
        </a>

        <!-- Formulaire de modification de l'employés -->
        <form method="post" id="updateForm">

            <div class="input-group mb-3">
                <span class="input-group-text" id="nom">Nom</span>
                <input type="text" class="form-control <?= isset($errors['nom']) ? 'red-border' : '' ?>" aria-label="nom" aria-describedby="nom" name="nom" value="<?= $formValue['nom'] ?>" required>
                <?php if(isset($errors['nom'])): ?>
                    <div style="color: red; font-size: 10px; width: 100%;"><?= $errors['nom'] ?></div>
                <?php endif ?>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="prenom">Prénom</span>
                <input type="text" class="form-control <?= isset($errors['prenom']) ? 'red-border' : '' ?>" aria-label="prenom" aria-describedby="prenom" name="prenom" value="<?= $formValue['prenom'] ?>"  required>
                <?php if(isset($errors['prenom'])): ?>
                    <div style="color: red; font-size: 10px; width: 100%;"><?= $errors['prenom'] ?></div>
                <?php endif ?>
            </div>

            <select class="form-select mb-3 <?= isset($errors['sexe']) ? 'red-border' : '' ?>" aria-label="choix" name="sexe" value="<?= $formValue['sexe'] ?>" required>
                <option value="" <?= $formValue['sexe'] === "" ? 'SELECTED' : '' ?>>Sexe: veuillez choisir une option</option>

                <?php foreach($tabGenre as $k => $v): ?>
                <option value="<?= $k ?>" <?= $formValue['sexe'] === $k ? 'SELECTED' : '' ?>><?= $v ?></option>
                <?php endforeach ?>
            </select>

            <div class="input-group mb-3">
                <span class="input-group-text" id="service">Service</span>
                <input type="text" class="form-control <?= isset($errors['service']) ? 'red-border' : '' ?>" aria-label="service" aria-describedby="service" name="service" value="<?= $formValue['service'] ?>"  required>
                <?php if(isset($errors['service'])): ?>
                    <div style="color: red; font-size: 10px; width: 100%;"><?= $errors['service'] ?></div>
                <?php endif ?>
            </div>

            <div class="form-group mb-3">
                <span class="input-group-text" id="date">Date embauche</span>
                <input type="date" class="form-control <?= isset($errors['date_embauche']) ? 'red-border' : '' ?>" id="date" name="date_embauche" value="<?= $formValue['date_embauche'] ?>" required>
                <?php if(isset($errors['date_embauche'])): ?>
                    <div style="color: red; font-size: 10px; width: 100%;"><?= $errors['date_embauche'] ?></div>
                <?php endif ?>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="salaire">Salaire €</span>
                <input type="text" class="form-control <?= isset($errors['salaire']) ? 'red-border' : '' ?>" aria-label="salaire" aria-describedby="salaire" name="salaire" value="<?= $formValue['salaire'] ?>" required>
                <?php if(isset($errors['salaire'])): ?>
                    <div style="color: red; font-size: 10px; width: 100%;"><?= $errors['salaire'] ?></div>
                <?php endif ?>
            </div>

            <button type="submit" class="btn btn-primary" name="updateEmployeeSubmitBtn">Envoyer</button>
        </form>
    </div>
</div>

<!-- /Contenu de la page -->
<?php 
include_once("./includes/footer.php");
?>
