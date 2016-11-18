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
    public function add(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $this->validator->validate($request, [
                'nom' => V::length(1, 100),
                'adresse' => V::length(1, 100),
                'date_debut' => V::date('d/m/Y'),
                'date_fin' => V::date('d/m/Y'),
                'telephone' => V::phone()->length(10, 10),
                'discipline' => V::length(1, 50),
                'description' => V::notBlank()
            ]);

            $file = new File('image', new FileSystem($this->settings['events_upload'], true));

            $file->addValidations([
                new Mimetype(['image/png', 'image/jpeg']),
                new Size('2M')
            ]);

            if (!$file->validate()) {
                $this->validator->addErrors('image', $file->getErrors());
            }

            if ($this->validator->isValid()) {
                $evenement = new Evenement([
                    'nom' => $request->getParam('nom'),
                    'adresse' => $request->getParam('adresse'),
                    'date_debut' => \DateTime::createFromFormat('d/m/Y', $request->getParam('date_debut')),
                    'date_fin' => \DateTime::createFromFormat('d/m/Y', $request->getParam('date_fin')),
                    'telephone' => $request->getParam('telephone'),
                    'discipline' => $request->getParam('discipline'),
                    'description' => $request->getParam('description'),
                    'etat' => $request->getParam('validate') ? Evenement::VALIDE : Evenement::CREE
                ]);
                $evenement->user()->associate($this->user());
                $evenement->save();

                mkdir($this->getUploadDir($evenement->id) . 'epreuves', 0777, true);
                $file = new File('image', new FileSystem($this->getUploadDir($evenement->id), true));
                $file->upload('header');

                $this->flash('success', 'L\'événement "' . $request->getParam('nom') . '" a bien été crée !');
                return $this->redirect($response, 'evenement.show', [
                    'id_evenement' => $evenement->id
                ]);
            }
        }

        return $this->view->render($response, 'Evenement/add.twig');
    }

    public function show(Request $request, Response $response, array $args)
    {
        $evenement = Evenement::find($args['id_evenement']);

        if (!$evenement) {
            throw $this->notFoundException($request, $response);
        }

        $files = glob($this->settings['events_upload'] . $args['id_evenement'] . '/*.{jpg,png,gif}', GLOB_BRACE);
        $size = sizeof($files) - 1;
        $files_link = array();
        for ($i = 1; $i <= $size; $i++) {
            array_push($files_link, $evenement->getImageWebPath($i));
        }

        return $this->view->render($response, 'Evenement/show.twig', [
            'evenement' => $evenement,
            'epreuves' => $evenement->epreuves,
            'pic_links' => $files_link
        ]);
    }

    public function edit(Request $request, Response $response, array $args)
    {
        $evenement = Evenement::find($args['id']);

        if (!$evenement) {
            throw $this->notFoundException($request, $response);
        }

        if ($request->isPost()) {
            v::with('App\\Validation\\Rules\\');
            $this->validator->validate($request, [
                'nom' => V::length(1, 100),
                'adresse' => V::length(1, 100),
                'date_debut' => V::date('d/m/Y'),
                'date_fin' => V::date('d/m/Y'),
                'telephone' => V::phone(),
                'discipline' => V::length(1, 50),
                'description' => V::notBlank(),
                'etat' => V::intVal(),
                'galerie' => V::ImageSize()->ImageFormat()
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

            $file = new File('image', new FileSystem($this->getUploadDir($evenement->id), true));
            $file->setName('header');

            //on gère l'upload multiple d'images
            //on modifie l'array pour que la strusture soit plus facile à manier
            $file_ary = array();
            $file_count = count($_FILES['galerie']['name']);
            $files_galerie = $_FILES['galerie'];

            for ($i = 0; $i < $file_count; $i++) {
                $img = array();
                $img['name'] = $files_galerie['name'][$i];
                $img['tmp_name'] = $files_galerie['tmp_name'][$i];
                $img['type'] = $files_galerie['type'][$i];
                $img['size'] = $files_galerie['size'][$i];
                $img['error'] = $files_galerie['error'][$i];
                array_push($file_ary, $img);
            }

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

                $galerie = array();
                $files = glob($this->settings['events_upload'] . $args['id'] . '/*.{jpg,png,gif}', GLOB_BRACE);
                $name_to_upload = sizeof($files);
                $a = 0;
                foreach ($file_ary as $file_ary_img) {
                    $tmp_name = $file_ary_img['tmp_name'];
                    $extension = explode('.', $file_ary_img['name']);
                    //echo "<pre>";
                    //print_r($this->settings['events_upload'].$args['id'].'/'.$name_to_upload.".".$extension[1]);
                    move_uploaded_file($tmp_name, $this->settings['events_upload'] . $args['id'] . '/' . $name_to_upload . '.' . $extension[1]);
                    $name_to_upload += 1;
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


    public function getParticipants(Request $request, Response $response, array $args)
    {
        //on récupère la liste des personne participants à l'évènement
        $epreuve_by_id = Epreuve::where('evenement_id', 'like', $args['id'])->get();
        $tab_csv = array();
        $tab_csv[0] = array();
        $tab_csv[1] = array();
        foreach ($epreuve_by_id as $epreuve) {
            array_push($tab_csv[0], $epreuve['nom']);
            array_push($tab_csv[1], '---');

            $participants = $epreuve->sportifs()->get();
            $nb = 2;

            foreach ($participants as $participant) {

                if (sizeof($tab_csv) < ($nb + 1)) {
                    $tab_csv[$nb] = array();
                }

                array_push($tab_csv[$nb], $participant['nom'] . ' ' . $participant['prenom']);
                $nb += 1;

                if (sizeof($tab_csv) < ($nb + 1)) {
                    $tab_csv[$nb] = array();
                }
                array_push($tab_csv[$nb], $participant['nom'] . ' ' . $participant['prenom']);
                $nb += 1;

            }
        }

        $filename = 'liste_participant.csv';
        $delimiter = ',';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $f = fopen('php://output', 'w');

        foreach ($tab_csv as $line) {
            fputcsv($f, $line, $delimiter);
        }
    }

    public function getUploadDir($eventId)
    {
        return $this->settings['events_upload'] . $eventId . '/';
    }

    public function getPicturePath($eventId)
    {
        $path = $this->getUploadDir($eventId) . '/header';
        return file_exists($path . '.jpg') ? $path . '.jpg' : $path . '.png';
    }
}
