<?php
namespace App\Controller;
use App\Controller\Controller;
use Respect\Validation\Validator as v;
use App\Model\Epreuve;
class EpreuveController extends Controller
{


    public function getAddEpreuve($request, $response, $args)
    {
        $id = $args['id_evenement'];
        return $this->view->render($response, 'Epreuve/add.twig', compact('id'));
    }

    public function postAddEpreuve($request, $response, $args)
    {

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

        //on créer l'image et sa destination de stockage
        $storage = new \Upload\Storage\FileSystem(__DIR__.'/../../../public/uploads/evenements/'.$args['id_evenement'].'/epreuves');
        $file = new \Upload\File('epreuve_pic_link', $storage);

        $dated = \DateTime::createFromFormat("d-m-Y H:i",$request->getParam('date_debut')." ".$request->getParam('heure_debut'));
        $datef = \DateTime::createFromFormat("d-m-Y H:i",$request->getParam('date_fin')." ".$request->getParam('heure_fin'));
        $epreuve = new Epreuve();
        $epreuve->nom=$request->getParam('epreuve_name');
        $epreuve->capacite=$request->getParam('capacite');
        $epreuve->date_debut=$dated;
        $epreuve->date_fin=$datef;

        $epreuve->etat=1;
        $epreuve->description=$request->getParam('epreuve_description');
        $epreuve->prix=$request->getParam('prix');
        $epreuve->evenement_id=$args['id_evenement'];
        $epreuve->save();

        //on donne un nom à l'image à upload
        $new_filename = $epreuve->id;
        $file->setName($new_filename);

        // on peut accéder aux données au cas ou
        $data = array(
            'name'       => $file->getNameWithExtension(),
            'extension'  => $file->getExtension(),
            'mime'       => $file->getMimetype(),
            'size'       => $file->getSize(),
            'md5'        => $file->getMd5(),
            'dimensions' => $file->getDimensions()
        );

        // on tente l'upload d'image
        try {
            // Success!
            $file->upload();
        } catch (\Exception $e) {
            // Fail!
            $errors = $file->getErrors();
        }


        return $this->view->render($response, 'Evenement/edit.twig');
    }
}