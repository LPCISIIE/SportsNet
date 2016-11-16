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
       v::with('App\\Validation\\Rules\\');

        $validation = $this->validator->validate($request, [
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

        if (!($validation->isValid())) {
            return $this->view->render($response, 'Epreuve/edit.twig');
        }
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

        return $this->view->render($response, 'Epreuve/edit.twig');
    }


    public function editTrial($request, $response)
    {

        if ($request->isPost()) {

            $epreuve = Epreuve::find($arg['id_epreuve']);
            if (!$epreuve) {
                throw $this->notFoundException($request, $response);
            }

            v::with('App\\Validation\\Rules\\');

            $validation = $this->validator->validate($request, [
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


            if ($validation->isValid()) {
                    $dated = \DateTime::createFromFormat("d-m-Y H:i",$request->getParam('date_debut')." ".$request->getParam('heure_debut'));
                    $datef = \DateTime::createFromFormat("d-m-Y H:i",$request->getParam('date_fin')." ".$request->getParam('heure_fin'));

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

        return $this->view->render($response, 'Epreuve/edit.twig');

    }








}