<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Evenement as Evenement;
use Respect\Validation\Validator as V;
use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Mimetype;
use Upload\Validation\Size;

class EvenementController extends Controller
{
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

                mkdir(__DIR__ . '/../../../public/uploads/evenements/'.$evenement->id);

                $this->flash('success', 'L\'événement "' . $request->getParam('nom') . '" a bien été crée !');
                return $this->redirect($response, 'home');
            }
        }

        return $this->view->render($response, 'Evenement/create.twig');
    }


    public function show(Request $request, Response $response, array $args){
        $id_evenement = $args["id_evenement"];
        $evenement = Evenement::find($id_evenement);
        $epreuves = $evenement->epreuves()->get()->toArray();
        $evenement = $evenement->toArray();
        return $this->view->render($response, 'Evenement/show.twig', [
            'evenement' => $evenement,
            'epreuves' => $epreuves
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

            if (!in_array($etat, $etats)) {
                $this->validator->addError('etat', 'État non valide.');
            }

            $file = new File('image', new FileSystem(__DIR__ . '/../../../public/uploads/evenements/' . $evenement->id, true));
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
}
