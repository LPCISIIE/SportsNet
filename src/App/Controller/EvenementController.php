<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Epreuve as Epreuve;
use App\Model\Evenement as Evenement;
use Respect\Validation\Validator as V;
use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Mimetype;
use Upload\Validation\Size;

class EvenementController extends Controller
{

    public function getFolderUpload($evenement)
    {
        return $this->settings['events_upload'] . $evenement->id . '/';
    }

    public function getPicture($evenement)
    {
        if (file_exists($this->getFolderUpload($evenement) . 'header.jpg') ) {
            return $this->getFolderUpload($evenement) . 'header.jpg';
        }

        return $this->getFolderUpload($evenement) . 'header.png';
    }

    public function create(Request $request, Response $response)
    {

        if ($request->isPost()) {
            $this->validator->validate($request, [
                'nom' => V::length(1, 100),
                'adresse' => V::length(1, 100),
                'date_debut' => V::date('d/m/Y'),
                'date_fin' => V::date('d/m/Y'),
                'telephone' => V::phone(),
                'discipline' => V::length(1, 50),
                'description' => V::notBlank()
            ]);

            if ($this->validator->isValid()) {
                $evenement = new Evenement([
                    'nom' => $request->getParam('nom'),
                    'adresse' => $request->getParam('adresse'),
                    'date_debut' => \DateTime::createFromFormat('d/m/Y', $request->getParam('date_debut')),
                    'date_fin' => \DateTime::createFromFormat('d/m/Y', $request->getParam('date_fin')),
                    'telephone' => $request->getParam('telephone'),
                    'discipline' => $request->getParam('discipline'),
                    'description' => $request->getParam('description'),
                    'etat' => Evenement::CREE,
                ]);
                $evenement->user()->associate($this->user());
                $evenement->save();

                mkdir($this->getFolderUpload($evenement).'epreuves',0777,true);

                $this->flash('success', 'L\'événement "' . $request->getParam('nom') . '" a bien été crée !');
                return $this->redirect($response, 'home');
            }
        }

        return $this->view->render($response, 'Evenement/create.twig');
    }


    public function show(Request $request, Response $response, array $args)
    {
        $evenement = Evenement::find($args['id_evenement']);

        if (!$evenement) {
            throw $this->notFoundException($request, $response);
        }

        return $this->view->render($response, 'Evenement/show.twig', [
            'evenement' => $evenement,
            'epreuves' => $evenement->epreuves
        ]);
    }

    public function edit(Request $request, Response $response, array $args)
    {
        $evenement = Evenement::find($args['id']);

        if (!$evenement) {
            throw $this->notFoundException($request, $response);
        }

        if ($request->isPost()) {

            $this->validator->validate($request, [
                'nom' => V::length(1, 100),
                'adresse' => V::length(1, 100),
                'date_debut' => V::date('d/m/Y'),
                'date_fin' => V::date('d/m/Y'),
                'telephone' => V::phone(),
                'discipline' => V::length(1, 50),
                'description' => V::notBlank(),
                'etat' => V::intVal()
            ]);

            $etat = $request->getParam('etat');


            $etats = [
                Evenement::CREE,
                Evenement::VALIDE,
                Evenement::OUVERT,
                Evenement::EN_COURS,
                Evenement::CLOS,
                Evenement::EXPIRE,
                Evenement::ANNULE
            ];

            if (!in_array($etat, $etats) || !$etat) {
                $this->validator->addError('etat', 'État non valide.');
            }

            $file = new File('image', new FileSystem($this->getFolderUpload($evenement), true));
            $file->setName('header');

            $file->addValidations([
                new Mimetype(['image/png', 'image/jpeg']),
                new Size('2M')
            ]);

            $fileUploaded = isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE;

            if ($fileUploaded) {
                if (!$file->validate()) {
                    $this->validator->addErrors('image', $file->getErrors());
                }
            }

            if ($this->validator->isValid()) {
                $evenement->fill([
                    'nom' => $request->getParam('nom'),
                    'adresse' => $request->getParam('adresse'),
                    'date_debut' => \DateTime::createFromFormat('d/m/Y', $request->getParam('date_debut')),
                    'date_fin' => \DateTime::createFromFormat('d/m/Y', $request->getParam('date_fin')),
                    'telephone' => $request->getParam('telephone'),
                    'discipline' => $request->getParam('discipline'),
                    'description' => $request->getParam('description'),
                    'etat' => $etat
                ]);

                $evenement->save();

                if ($fileUploaded) {
                    $file->upload();
                }

                $this->flash('success', 'L\'événement "' . $evenement->nom . '" a bien été modifié !');
                return $this->redirect($response, 'home');
            }
        }

        return $this->view->render($response, 'Evenement/edit.twig', [
            'evenement' => $evenement
        ]);
    }

    public function cancel(Request $request, Response $response, array $args)
    {
        $evenement = Evenement::find($args['id']);

        if (!$evenement) {
            throw $this->notFoundException($request, $response);
        }

        if ($evenement->user->id !== $this->user()->id) {
            $this->flash('danger', 'Cet événement ne vous appartient pas !');
            return $this->redirect($response, 'user.events');
        }

        $evenement->etat = Evenement::ANNULE;
        $evenement->save();

        $this->flash('success', 'L\'événement "' . $evenement->nom . '" a été annulé.');
        return $this->redirect($response, 'user.events');
    }

    public function delete(Request $request, Response $response, array $args)
    {
        $evenement = Evenement::find($args['id']);

        if (!$evenement) {
            throw $this->notFoundException($request, $response);
        }

        if ($evenement->user->id !== $this->user()->id) {
            $this->flash('danger', 'Cet événement ne vous appartient pas !');
            return $this->redirect($response, 'user.events');
        }

        $evenement->epreuves()->delete();
        $evenement->delete();

        $this->flash('success', 'L\'événement "' . $evenement->nom . '" a été supprimé.');
        return $this->redirect($response, 'user.events');
    }

     public function getParticipants($request, $response, $args) {

        //on récupère la liste des personne participants à l'évènement
        $epreuve_by_id = Epreuve::where('evenement_id','like',$args['id'])->get();
        $tab_csv = array();
        $tab_csv[0] = array();
        $tab_csv[1] = array();
        foreach($epreuve_by_id as $epreuve) {
            array_push($tab_csv[0],$epreuve['nom']);
            array_push($tab_csv[1],'---');
            $participants = $epreuve->sportifs()->get();
            $nb = 2;
            foreach ($participants as $participant) {
                if(sizeof($tab_csv) < ($nb+1)) {
                    $tab_csv[$nb] = array();
                }
                array_push($tab_csv[$nb],$participant['nom']." ".$participant['prenom']);
                $nb+=1;
            }
        }

        $filename = "liste_participant.csv";
        $delimiter = ",";

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');

        $f = fopen('php://output', 'w');

        foreach ($tab_csv as $line) {
            fputcsv($f, $line, $delimiter);
        }
    }
}
