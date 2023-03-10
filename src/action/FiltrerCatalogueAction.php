<?php
declare(strict_types=1);

namespace iutnc\ccd\action;

use iutnc\ccd\db\ConnectionFactory;
use \PDO;


class FiltrerCatalogueAction extends Action{

    public function execute(): string{
        $res = "";
        $res.= '<form action="?" method="get" class="search-form">
                    <button type="submit" >Retour</button>
                    <input type="hidden" name="action" value="catalogue">
                    </form>';
        if ($this->http_method == 'POST') {

            $lieu= filter_var($_POST['lieu'],FILTER_SANITIZE_STRING);
                $categorie = filter_var($_POST['categorie'],FILTER_SANITIZE_STRING);
            if($_POST['lieu'] === "" || $_POST['categorie'] ==="") {
                    $sql = "select produit.id,categorie,produit.nom, prix, image from produit 
                    where lieu = ? 
                    union 
                    select produit.id,categorie,produit.nom, prix, image from produit 
                    inner join categorie on produit.categorie = categorie.id 
                    where categorie.nom = ?";
                }
                else{
                    $sql = "select produit.id,categorie,produit.nom, prix, image from produit
                            where lieu = ? and produit.categorie 
                            IN (select produit.categorie from produit 
                            inner join categorie on produit.categorie = categorie.id where categorie.nom = ?);";
                }
                $bd = ConnectionFactory::makeConnection();
                $stmt = $bd->prepare($sql);
                $stmt -> bindParam(1,$lieu);
                $stmt -> bindParam(2,$categorie);
                $stmt -> execute();
                $res.= '<main class="main-catalogue">';
                $rep = $bd->query("select * from produit");
                $res .= '<div class="group-button-produit-catalogue">';
                $count = $rep->rowCount();
                $res .= '<div class="catalogue-page">';
                foreach (range(1, ceil($count / 5)) as $page) {
                    $res .= "<button class='catalogue-page-button' '>$page</button>";
                }
                $res .= '</div>';
                $res .= '<div class="group-produit-catalogue">';                while ($row = $stmt->fetch()){
                    $res.="<div class='item-produit-catalogue'>
                           <div class='img-item-catalogue'>
                               <a href=?action=produit&id=".$row[0].">
                                   <img class='img-produit' src='image/{$row['image']}'>
                              </a>
                         </div>
                         <a class=\"group-produit\" href=?action=produit&id=".$row[0].">
                                ".$row[2]."
                            </a>
                     </div>";
             }
        }
        return $res."</div></main>";
    }

}