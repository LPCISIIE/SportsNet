<?php
namespace App\Controller;
use App\Controller\Controller;
use Respect\Validation\Validator as v;
use App\Model\Epreuve;
use App\Model\Evenement;
use App\Model\Sportif;
class EpreuveController extends Controller
{
    public function getAddEpreuve($request, $response) {
        return $this->view->render($response, 'Epreuve/add.twig');
    }
    public function postAddEpreuve($request, $response) {
        //validateur
       v::with('App\\Validation\\Rules\\');
        $validation = $this->validator->validate($request, [
            // à valider avec les paramètres de $request
            'epreuve_name' => v::notEmpty(),
            'date_debut' => v::date('d-m-Y'),
            'heure_debut' => v::date('H:i'),
            'date_fin' => v::date('d-m-Y'),
            'heure_fin' => v::date('H:i'),
            'epreuve_pic_link' => v::ImageFormat()->ImageSize(),
            'epreuve_description' => v::notEmpty(),
            'capacite' => v::notEmpty()->numeric(),
            'prix' => v::notEmpty()->numeric(),
            'op' => v::equals('reg'),
        ]);
        /**
        * If the fields fail, then redirect back to signup
        */
        if (!($validation->isValid())) {
            return $this->getAddEpreuve($request,$response);
        }
        $dated = \DateTime::createFromFormat("d-m-Y H:i",$request->getParam('date_debut')." ".$request->getParam('heure_debut'));
        $datef = \DateTime::createFromFormat("d-m-Y H:i",$request->getParam('date_fin')." ".$request->getParam('heure_fin'));
        $epreuve = new Epreuve();
        $epreuve->nom=$request->getParam('epreuve_name');
        $epreuve->capacite=$request->getParam('capacite');
        $epreuve->date_debut=$dated;
        $epreuve->date_fin=$datef;
        /*
        0 - fermé
        1 - ouvert
        2 - annulé
        */
        $epreuve->etat=1;
        $epreuve->description=$request->getParam('epreuve_description');
        $epreuve->prix=$request->getParam('prix');
        //TODO LATER AJOUTER ID D'EVENEMENT
        //temporaire
        $epreuve->evenement_id=1;
        //
        $epreuve->save();


        return $this->view->render($response, 'Evenement/edit.twig');
    }

    public function join($request, $response,$args){
        if ($request->isPost()) {
            $nom = $request->getParam('nom');
            $prenom = $request->getParam('prenom');
            $email = $request->getParam('email');
            $birthday = $request->getParam('birthday');

            $this->validator->validate($request, [
                'nom' => V::length(1,50),
                'prenom' => V::length(1,50),
                'email' => V::noWhitespace()->email(),
                'birthday' => v::date('d-m-Y'),
            ]);

            $birthday = \DateTime::createFromFormat("d-m-Y",$birthday);
            $sportif=new Sportif();
            $sportif->nom=$nom;
            $sportif->prenom=$prenom;
            $sportif->email=$email;
            $sportif->birthday=$birthday;
            $sportif->save();

        }
        $evenement=Evenement::find($args["id_evenement"]);
        $epreuves = $evenement->epreuves()->get()->toArray();
        $evenement= $evenement->toArray();
        return $this->view->render($response, 'Epreuve/join.twig', compact('evenement','epreuves'));
    }
}
