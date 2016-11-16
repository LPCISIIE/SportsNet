<?php
namespace App\Controller;
use App\Model\Evenement;
use Respect\Validation\Validator as v;
use Illuminate\Database\QueryException;
use App\Model\Epreuve;
use App\Model\Evenement;
use App\Model\Sportif;
use App\Model\Participe;
class EpreuveController extends Controller
{

    public function getFolderUpload(Epreuve $epreuve)
    {
        $id = $epreuve->evenement->id;
        return $this->settings['events_upload'] . $id . '/epreuves/';
    }

    public function getPicture(Epreuve $epreuve)
    {
        if ( file_exists($this->getFolderUpload($epreuve) . $epreuve->id . '.jpg') ) {
            return $this->getFolderUpload($epreuve) . $epreuve->id . '.jpg';
        }

        return $this->getFolderUpload($epreuve) . $epreuve->id . '.png';
    }


    public function getAddEpreuve($request, $response, $args)
    {
        $id = $args['id_evenement'];
        return $this->view->render($response, 'Epreuve/add.twig', compact('id'));
    }

    public function postAddEpreuve($request, $response, $args)
    {
       v::with('App\\Validation\\Rules\\');

        $validation = $this->validator->validate($request, [
            'epreuve_name' => v::notEmpty(),
            'date_debut' => v::date('d/m/Y'),
            'heure_debut' => v::date('H:i'),
            'date_fin' => v::date('d/m/Y'),
            'heure_fin' => v::date('H:i'),
            'epreuve_description' => v::notEmpty(),
            'capacite' => v::notEmpty()->numeric(),
            'prix' => v::notEmpty()->numeric(),
            'op' => v::equals('reg'),
        ]);

        if (!($validation->isValid())) {
            return $this->view->render($response, 'Epreuve/add.twig');
        }



        $file->addValidations(array(
            //You can also add multi mimetype validation
            new \Upload\Validation\Mimetype(array('image/png', 'image/jpeg')),

            // Ensure file is no larger than 5M (use "B", "K", M", or "G")
            new \Upload\Validation\Size('2M'),
        ));


        $dated = \DateTime::createFromFormat("d/m/Y H:i",$request->getParam('date_debut')." ".$request->getParam('heure_debut'));
        $datef = \DateTime::createFromFormat("d/m/Y H:i",$request->getParam('date_fin')." ".$request->getParam('heure_fin'));
        $epreuve = new Epreuve();
        $epreuve->nom=$request->getParam('epreuve_name');
        $epreuve->capacite=$request->getParam('capacite');
        $epreuve->date_debut=$dated;
        $epreuve->date_fin=$datef;
        $epreuve->etat=Epreuve::CREE;
        $epreuve->description=$request->getParam('epreuve_description');
        $epreuve->prix=$request->getParam('prix');
        $epreuve->evenement_id=$args['id_evenement'];
        $epreuve->save();

        $storage = new \Upload\Storage\FileSystem($this->getFolderUpload($epreuve));
        $file = new \Upload\File('epreuve_pic_link', $storage);


        $file->addValidations(array(
            new \Upload\Validation\Mimetype(array('image/png', 'image/jpeg')),
            new \Upload\Validation\Size('2M'),
        ));



        $new_filename = $epreuve->id;
        $file->setName($new_filename);

        try {
            $file->upload();
        } catch (\Exception $e) {
            $this->validator->addErrors('epreuve_pic_link',$file->getErrors());
        }

        return $this->view->render($response, 'Evenement/edit.twig');
    }


    public function edit($request, $response)
    {

        if ($request->isPost()) {

            $epreuve = Epreuve::find($args['id_epreuve']);
            $evenement = Evenement::find($args['id_evenement']);

            if (!$epreuve) {
                throw $this->notFoundException($request, $response);
            }

            v::with('App\\Validation\\Rules\\');

            $validation = $this->validator->validate($request, [
                'epreuve_name' => v::notEmpty(),
                'date_debut' => v::date('d/m/Y'),
                'heure_debut' => v::date('H:i'),
                'date_fin' => v::date('d/m/Y'),
                'heure_fin' => v::date('H:i'),
                'epreuve_pic_link' => v::ImageFormat()->ImageSize(),
                'epreuve_description' => v::notEmpty(),
                'capacite' => v::notEmpty()->numeric(),
                'prix' => v::notEmpty()->numeric(),
                'op' => v::equals('reg'),
            ]);


            if ($validation->isValid()) {
                    $dated = \DateTime::createFromFormat("d/m/Y H:i",$request->getParam('date_debut')." ".$request->getParam('heure_debut'));
                    $datef = \DateTime::createFromFormat("d/m/Y H:i",$request->getParam('date_fin')." ".$request->getParam('heure_fin'));

                    $epreuve->fill([
                        'nom' => $request->getParam('nom'),
                        'capacite' => $request->getParam('capacite'),
                        'date_debut' => $dated,
                        'date_fin' => $datef,
                        'etat' => $request->getParam('etat'),
                        'description' => $request->getParam('description'),
                        'prix' => $request->getParam('prix'),
                    ]);

                    $epreuve->save();

                    $this->flash('success', 'L\'épreuve "' . $request->getParam('nom') . '" a bien été modifié !');
                    return $this->redirect($response, 'home');
            }
        }

        return $this->view->render($response, 'Epreuve/edit.twig',['evenement' => $epreuve, 'evenement' => $epreuve ]);

    }

    public function join($request, $response,$args)
    {
        $evenement_id=$args['id_evenement'];
        $evenement=Evenement::find($args["id_evenement"]);
        $epreuves = $evenement->epreuves()->get()->toArray();
        $evenement= $evenement->toArray();
        if ($request->isPost()) {
            $nom = $request->getParam('nom');
            $prenom = $request->getParam('prenom');
            $email = $request->getParam('email');
            $birthday = $request->getParam('birthday');
            $epreuvesSelection = $request->getParam('epreuves');
            $this->validator->validate($request, [
                'nom' => V::length(1,50),
                'prenom' => V::length(1,50),
                'email' => V::noWhitespace()->email(),
                'birthday' => v::date('d/m/Y'),
            ]);

            /*Test si pas deja inscrit*/
            $sportif = Sportif::where('email',$email)->first();

            if ($sportif==null) {
                $birthday = \DateTime::createFromFormat("d-m-Y",$birthday);
                $sportif=new Sportif();
                $sportif->nom=$nom;
                $sportif->prenom=$prenom;
                $sportif->email=$email;
                $sportif->birthday=$birthday;
                $sportif->save();
            }
            $prixTotal=0;
            if (isset($epreuvesSelection)) {
                foreach ($epreuvesSelection as $epreuve) {
                    try{
                        $sportif->epreuves()->attach($epreuve);
                        $prixTotal+=Epreuve::find($epreuve)->prix;
                    }
                    catch (QueryException $e){
                        $errorCode = $e->errorInfo[1];
                        if($errorCode == 1062){
                            $this->flash('error', 'Vous vous êtes déjà inscrit à l\'épreuve '.Epreuve::find($epreuve)->nom);
                            return $this->redirect($response, 'epreuve.join', ['id_evenement'=>$evenement_id]);
                        }
                    }
                }
            }
            else {
                $this->flash('error', 'Selectionnez au moins une épreuve');
                return $this->redirect($response, 'epreuve.join', ['id_evenement'=>$evenement_id]);
            }

            return $this->view->render($response, 'Epreuve/payment.twig',compact('prixTotal','evenement_id'));


        }
        return $this->view->render($response, 'Epreuve/join.twig', compact('evenement','epreuves'));
    }
}
