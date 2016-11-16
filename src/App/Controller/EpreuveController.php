<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as V;
use App\Model\Evenement;
use App\Model\Epreuve;
use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Mimetype;
use Upload\Validation\Size;

class EpreuveController extends Controller
{
    public function add(Request $request, Response $response, array $args)
    {
        $evenement = Evenement::find($args['event_id']);

        if (!$evenement) {
            throw $this->notFoundException($request, $response);
        }

        if ($evenement->user_id !== $this->user()->id) {
            $this->flash('danger', 'Cet événement ne vous appartient pas !');
            return $this->redirect($response, 'home');
        }

        if ($request->isPost()) {
            V::with('App\\Validation\\Rules\\');

            $this->validator->validate($request, [
                'epreuve_name' => V::notEmpty(),
                'date_debut' => V::date('d/m/Y'),
                'heure_debut' => V::date('H:i'),
                'date_fin' => V::date('d/m/Y'),
                'heure_fin' => V::date('H:i'),
                'epreuve_description' => V::notEmpty(),
                'capacite' => V::notEmpty()->numeric(),
                'prix' => V::notEmpty()->numeric()
            ]);

            $storage = new FileSystem($this->getUploadDir($evenement->id));
            $file = new File('epreuve_pic_link', $storage);
            $file->setName('header');

            $file->addValidations(array(
                new Mimetype(['image/png', 'image/jpeg']),
                new Size('2M'),
            ));

            if (!$file->validate()) {
                $this->validator->addErrors('epreuve_pic_link', $file->getErrors());
            }

            if ($this->validator->isValid()) {
                $dated = \DateTime::createFromFormat('d/m/Y H:i', $request->getParam('date_debut') . ' ' . $request->getParam('heure_debut'));
                $datef = \DateTime::createFromFormat('d/m/Y H:i', $request->getParam('date_fin') . ' ' . $request->getParam('heure_fin'));
                $epreuve = new Epreuve([
                    'nom' => $request->getParam('epreuve_name'),
                    'capacite' => $request->getParam('capacite'),
                    'date_debut' => $dated,
                    'date_fin' => $datef,
                    'etat' => Epreuve::CREE,
                    'description' => $request->getParam('epreuve_description'),
                    'prix' => $request->getParam('prix')
                ]);

                $epreuve->evenement()->associate($evenement);
                $epreuve->save();

                $file->upload();

                $this->flash('success', 'L\'épreuve a bien été créée !');
                return $this->redirect($response, 'home');
            }
        }

        return $this->view->render($response, 'Epreuve/add.twig');
    }

    public function edit(Request $request, Response $response, array $args)
    {
        $evenement = Evenement::find($args['event_id']);

        if (!$evenement) {
            throw $this->notFoundException($request, $response);
        }

        if ($evenement->user_id !== $this->user()->id) {
            $this->flash('danger', 'Cet événement ne vous appartient pas !');
            return $this->redirect($response, 'home');
        }

        $epreuve = Epreuve::find($args['trial_id']);

        if (!$epreuve) {
            throw $this->notFoundException($request, $response);
        }

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
                $dated = \DateTime::createFromFormat("d/m/Y H:i", $request->getParam('date_debut') . " " . $request->getParam('heure_debut'));
                $datef = \DateTime::createFromFormat("d/m/Y H:i", $request->getParam('date_fin') . " " . $request->getParam('heure_fin'));

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

        return $this->view->render($response, 'Epreuve/edit.twig', ['evenement' => $epreuve, 'evenement' => $epreuve]);
    }

    public function getUploadDir($eventId)
    {
        return $this->settings['events_upload'] . $eventId . '/epreuves';
    }

    public function getPicture(Epreuve $epreuve)
    {
        if (file_exists($this->getFolderUpload($epreuve) . $epreuve->id . '.jpg')) {
            return $this->getFolderUpload($epreuve) . $epreuve->id . '.jpg';
        }

        return $this->getFolderUpload($epreuve) . $epreuve->id . '.png';
    }
}