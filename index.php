<?php 
// PAGE D'ACCUEIL
// inclusion initiale
include_once("./includes/init.inc.php");

// traitement de la suppression
if(isset($_GET['action']) && $_GET['action']=== "delete") {
  if(isset($_GET['id'])){
    // Validation de l'id passée par URL
    if(!is_numeric($_GET['id']) || !is_int((int)$_GET['id'])) {
      header('location:' . HTTP_SITE_URL);
      exit();
    } 

    // on récupère l'id de l'employé dans l'url
    $idEmploye = (int)$_GET['id'];

    // suppression de l'employé
    $stmt = $pdo->prepare("
      DELETE FROM employes WHERE id_employes=:id_employes
    ");
    $stmt->bindParam(":id_employes", $idEmploye, PDO::PARAM_INT);

    try {
      $stmt->execute();
    } catch(PDOException $e) {
      if($env === 'dev') {
        die($e->getMessage());
      } else {
        header('location:' . HTTP_SITE_URL);
        exit();
      }
    }
  }
  $success[] = "Employé correctement supprimé!";
}

// Pagination
// Récupération du nombre d'enregistrements retournés
try {
  $stmt = $pdo->query("SELECT COUNT(*) as nbr FROM employes");
} catch(PDOException $e) {
  afficheErreur($e->getMessage());
}
$res = $stmt->fetch();
$nbreEmployes = $res['nbr'];

showVar($nbreEmployes);

if(isset($_GET['page']) && !empty($_GET['page'])){
  $currentPage = (int)$_GET['page'];
} else {
  $currentPage = 1;
}

$_SESSION['currentPage'] = $currentPage;

// calcul du nombre de pages (nbre total / parPage)
$pages = ceil($nbreEmployes/$parPage);
$premier = ($currentPage * $parPage) - $parPage;

// recupération de tous les employés
try {
    $stmt = $pdo->query("SELECT * FROM employes ORDER BY id_employes DESC LIMIT ". $premier . ", ". $parPage);
} catch(PDOException $e) {
    afficheErreur($e->getMessage());
}

$employes = $stmt->fetchAll();

// debug
if($env === 'dev') {
    incomingData();
    showVar($employes);
}

// titre de la page
$titrePrincipal = 'Accueil';

// id du body
$bodyId = 'home';

// Affichage de la page
// Header:
include_once("./includes/header.php");
?>
<!-- Contenu de la page -->

<a href="./create.php">
<button type="button" class="btn btn-primary mt-5 ms-3">Ajouter un employé</button>
</a>

<?php if(count($success)): ?>
    <div class="w-100 mt-3 mb-3 form-success-container">
        <ul>
            <?php foreach($success as $succes): ?>
                <li><?= $succes ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

<table id="listeEmployes" class="table table-success table-striped mt-5">
<thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Nom</th>
      <th scope="col">Prenom</th>
      <th scope="col">Genre</th>
      <th scope="col">Service</th>
      <th scope="col">Date d'embauche</th>
      <th scope="col">Salaire</th>
      <th scope="col" colspan="2">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($employes as $k => $v): ?>
    <tr>
      <th scope="row"><?= $v['id_employes'] ?></th>
      <td><?= $v['nom'] ?></td>
      <td><?= $v['prenom']?></td>
      <td><?= $v['sexe'] ?></td>
      <td><?= $v['service'] ?></td>
      <td><?= convertDateMysqlToFr($v['date_embauche']) ?></td>
      <td><?= $v['salaire'] ?> €</td>
      <td><a href="./update.php?id=<?=$v['id_employes']?>"><button type="button" class="btn btn-primary">Modifier</button></a></td>
      <td>
        <a href="./index.php?action=delete&id=<?=$v['id_employes']?>&page=<?= $_SESSION['currentPage'] ?>">
          <button type="button" class="btn btn-danger">Supprimer</button>
        </a>
      </td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>

<!-- début de pagination html -->

<nav aria-label="Page navigation example">
  <ul class="pagination">
    <!-- lien vers la page précédente -->
    <li class="page-item <?= $currentPage === 1 ? "disabled" : "" ?>">
      <a class="page-link" href="./?page=<?= $currentPage -1 ?>" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
    <!-- liens vers les pages suivantes -->
    <?php for($page=1;$page <= $pages; $page++ ): ?>
      <li class="page-item">
        <a class="page-link" href="./?page=<?= $page ?>"><?= $page ?></a>
      </li>
    <?php endfor ?>

    <!-- lien permettant de rejoindre la derniere page: -->
      <a class="page-link" href="<?= $currentPage === $page ? "disabled" : "" ?>" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  </ul>
</nav>

<!-- Fin de pagination html -->

<a href="./create.php">
<button type="button" class="btn btn-primary mt-3 ms-3 mb-5">Ajouter un employé</button>
</a>

<!-- /Contenu de la page -->
<?php 
include_once("./includes/footer.php");
?>


    