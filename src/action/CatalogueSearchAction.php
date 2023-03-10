<?php

namespace iutnc\ccd\action;
use iutnc\ccd\db\ConnectionFactory;
use iutnc\ccd\action\CatalogueAction;

class CatalogueSearchAction extends CatalogueAction
{
    public function executeWithArg(string $ch): string
    {
        if($ch == ""){
             $t = new CatalogueAction();
             return $t->execute();
        }else {
            $bd = ConnectionFactory::makeConnection();
            $stmt = $bd->prepare("select produit.id,categorie,produit.nom, prix, image from produit
                                    where nom like lower('%$ch%')");
            $res= '<form action="?" method="get" class="search-form">
                    <button type="submit" >Retour</button>
                    <input type="hidden" name="action" value="catalogue">
                    </form>';
            $res.= '<main class="main-catalogue">';
            $stmt -> execute();
            $rep = $bd->query("select * from produit");
            $res .= '<div class="group-button-produit-catalogue">';
            $count = $rep->rowCount();
            $res .= '<div class="catalogue-page">';
            foreach (range(1, ceil($count / 5)) as $page) {
                $res .= "<button class='catalogue-page-button' '>$page</button>";
            }
            $res .= '</div>';
            $res .= '<div class="group-produit-catalogue">';
            while ($row = $stmt->fetch()){
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

            return $res."</div></main>";
        }

    }
}