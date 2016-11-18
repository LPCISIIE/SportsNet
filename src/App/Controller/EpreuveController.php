<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as V;
use Illuminate\Database\QueryException;
use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Mimetype;
use Upload\Validation\Extension;
use Upload\Validation\Size;
use App\Model\Epreuve;
use App\Model\Sportif;
use App\Model\Evenement;

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
            $this->validator->validate($request, [
                'nom' => V::notBlank(),
                'date_debut' => V::date('d/m/Y'),
                'heure_debut' => V::date('H:i'),
                'date_fin' => V::date('d/m/Y'),
                'heure_fin' => V::date('H:i'),
                'description' => V::notBlank(),
                'capacite' => V::notBlank()->numeric(),
                'prix' => V::notBlank()->numeric()
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
                $dated = $request->getParam('date_debut');
                $datef = $request->getParam('date_fin');
                $heured = $request->getParam('heure_debut');
                $heuref = $request->getParam('heure_fin');

                $dated = \DateTime::createFromFormat('d/m/Y H:i', $dated . ' ' . $heured);
                $datef = \DateTime::createFromFormat('d/m/Y H:i', $datef . ' ' . $heuref);

                $epreuve = new Epreuve([
                    'nom' => $request->getParam('nom'),
                    'capacite' => $request->getParam('capacite'),
                    'date_debut' => $dated,
                    'date_fin' => $datef,
                    'etat' => Epreuve::CREE,
                    'description' => $request->getParam('description'),
                    'prix' => $request->getParam('prix')
                ]);

                $epreuve->evenement()->associate($evenement);
                $epreuve->save();

                $file->setName($epreuve->id);
                $file->upload();

                $this->flash('success', 'L\'épreuve a bien été créée !');
                return $this->redirect($response, 'home');
            }
        }

        return $this->view->render($response, 'Epreuve/add.twig', compact('evenement'));
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
            $validation = $this->validator->validate($request, [
                'nom' => V::notBlank(),
                'date_debut' => V::date('d/m/Y'),
                'heure_debut' => V::date('H:i'),
                'date_fin' => V::date('d/m/Y'),
                'heure_fin' => V::date('H:i'),
                'description' => V::notBlank(),
                'capacite' => V::notBlank()->numeric(),
                'prix' => V::notBlank()->numeric()
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

            $file = new File('epreuve_pic_link', new FileSystem($this->getUploadDir($evenement->id), true));
            $filecsv = new File('result_csv', new FileSystem($this->getUploadDir($evenement->id), true));

            $file->addValidations([
                new Mimetype(['image/png', 'image/jpeg']),
                new Size('2M')
            ]);

            $filecsv->addValidations([
                new Extension('csv'),
            ]);

            $fileUploaded = isset($_FILES['epreuve_pic_link']) && $_FILES['epreuve_pic_link']['error'] != UPLOAD_ERR_NO_FILE;
            $fileUploaded2 = isset($_FILES['result_csv']) && $_FILES['result_csv']['error'] != UPLOAD_ERR_NO_FILE;

            if ($fileUploaded) {
                if (!$file->validate()) {
                    $this->validator->addErrors('epreuve_pic_link', $file->getErrors());
                }
            }

            if ($fileUploaded2) {
                if (!$filecsv->validate()) {
                    $this->validator->addErrors('result_csv', $filecsv->getErrors());
                }
            }

            if ($validation->isValid()) {
                $dated = $request->getParam('date_debut');
                $datef = $request->getParam('date_fin');
                $heured = $request->getParam('heure_debut');
                $heuref = $request->getParam('heure_fin');

                $dated = \DateTime::createFromFormat('d/m/Y H:i', $dated . ' ' . $heured);
                $datef = \DateTime::createFromFormat('d/m/Y H:i', $datef . ' ' . $heuref);

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

                if ($fileUploaded) {
                    unlink($this->getPicturePath($evenement->id, $epreuve->id));
                    $file->setName($epreuve->id);
                    $file->upload();
                }

                if ($fileUploaded2) {
                    $filecsv->setName($epreuve->id);
                    $filecsv->upload();
                }

                $this->flash('success', 'L\'épreuve "' . $epreuve->nom . '" a bien été modifiée !');
                return $this->redirect($response, 'home');
            }
        }

        return $this->view->render($response, 'Epreuve/edit.twig', [
            'epreuve' => $epreuve
        ]);
    }

    public function show(Request $request, Response $response, array $args)
    {
        $evenement = Evenement::find($args['event_id']);

        if (!$evenement) {
            throw $this->notFoundException($request, $response);
        }

        $epreuve = Epreuve::find($args['trial_id']);

        if (!$epreuve) {
            throw $this->notFoundException($request, $response);
        }

        return $this->view->render($response, 'Epreuve/show.twig', [
            'epreuve' => $epreuve,
            'evenement' => $evenement
        ]);
    }

    public function resultat(Request $request, Response $response, array $args)
    {
        $idEpreuve = $args['trial_id'];
        $idEvenement = $args['event_id'];
        $file = $this->getUploadDir($idEvenement) . '/' . $idEpreuve . '.csv';

        if (file_exists($file)) {
            $file = fopen($file, 'r');
            $head = fgetcsv($file, 4096, ';', '"');

            $participants = [];
            $i = 0;

            while ($column = fgetcsv($file, 4096, ';', '"')) {
                $column = array_combine($head, $column);

                if (!empty($column['Classement'])) {
                    $participants[$i++] = $column;
                }
            }

            function sort($tab)
            {
                $temp = [];
                foreach ($tab as $p) {
                    if (empty($temp[(int) $p['Classement'] - 1])) {
                        $temp[(int) $p['Classement'] - 1] = $p;
                    } else {
                        $temp[(int) $p['Classement']] = $p;
                    }
                }
            }

            return $this->view->render($response, 'Epreuve/classement.twig', [
                'participants' => $participants,
                'epreuve' => Epreuve::find($idEpreuve),
                'evenement' => Evenement::find($idEvenement),
            ]);
        }

        $tel = Evenement::find($idEvenement)->telephone;

        $message = ($tel) ? 'Aucun fichier de résultat pour cet épreuve veuillez contacter le ' . $tel : 'Evenement innexistant';

        $this->flash('danger', $message);
        return $this->redirect($response, 'recherchePerso', [
            'event_id' => $idEvenement,
            'trial_id' => $idEpreuve
        ]);
    }

    public function resultatPerso(Request $request, Response $response, array $args)
    {
        if ($request->isPost()) {
            $idEpreuve = $args['trial_id'];
            $idEvenement = $args['event_id'];
            $file = $this->getUploadDir($idEvenement) . '/' . $idEpreuve . '.csv';

            if (file_exists($file)) {

                $target = $request->getParam('numeroSportif'); // not the supermarket lol
                $file = fopen($file, 'r');
                $head = fgetcsv($file, 4096, ';', '"');

                $participant = NULL;

                while ($column = fgetcsv($file, 4096, ';', '"')) {

                    $column = array_combine($head, $column);
                    if ($column['Numéro participant'] == $target) {
                        $participant = $column;
                    }
                }

                if ($participant == NULL) {
                    $this->flash('danger', 'Participant introuvable');
                    return $this->redirect($response, 'recherchePerso', ['event_id' => $idEvenement, 'trial_id' => $idEpreuve]);
                };


                return $this->view->render($response, 'Epreuve/afficherResultat.twig',
                    ['participant' => $participant,
                        'epreuve' => Epreuve::find($idEpreuve),
                        'evenement' => Evenement::find($idEvenement),
                    ]);

            }

            $tel = Evenement::find($idEvenement)->telephone;

            $message = ($tel) ? 'Aucun fichier de résultat pour cet épreuve veuillez contacter le ' . $tel : 'Evenement innexistant';

            $this->flash('danger', $message);

            return $this->redirect($response, 'recherchePerso', ['event_id' => $idEvenement, 'trial_id' => $idEpreuve]);

        }

        return $this->view->render($response, 'Epreuve/recherchePerso.twig');
    }


    public function join($request, $response, $args)
    {
        $evenement_id = $args['id_evenement'];
        $evenement = Evenement::find($args['id_evenement']);
        $epreuves = $evenement->epreuves()->get()->toArray();

        if (!$evenement) {
            throw $this->notFoundException($request, $response);
        }

        if (
            $evenement->etat == Evenement::ANNULE ||
            $evenement->etat == Evenement::CLOS ||
            $evenement->etat == Evenement::EXPIRE
        ) {
            return $this->view->render($response, 'Error/error.twig', [
                'title' => 'Événement ' . lcfirst($evenement->getState()),
                'description' => 'L\'événement est ' . lcfirst($evenement->getState()) . '. Vous ne pouvez pas rejoindre une épreuve.'
            ]);
        }

        if ($evenement->etat == Evenement::CREE) {
            return $this->view->render($response, 'Error/error.twig', [
                'title' => 'Événement en cours de création',
                'description' => 'L\'événement est n\'a pas encore été validé. Vous ne pouvez pas rejoindre une épreuve.'
            ]);
        }

        if ($request->isPost()) {
            $nom = $request->getParam('nom');
            $prenom = $request->getParam('prenom');
            $email = $request->getParam('email');
            $epreuvesSelection = $request->getParam('epreuves');

            if (!$this->user()) {
                $validation = $this->validator->validate($request, [
                    'nom' => V::notEmpty()->length(1, 50),
                    'prenom' => V::notEmpty()->length(1, 50),
                    'email' => V::notEmpty()->noWhitespace()->email(),
                ]);
            } else {
                $validation = $this->validator->validate($request, [
                    'nom' => V::notEmpty()->length(1, 50),
                    'prenom' => V::notEmpty()->length(1, 50),
                ]);
            }

            if ($validation->isValid()) {
                $sportif = null;
                if (!$this->user()) {
                    $sportif = Sportif::where('email', $email)->first();
                } else if ($this->user()) {
                    $email = $this->user()->email;
                    $sportif = Sportif::where('email', $email)->first();
                }
                if (!$sportif) {
                    $sportif = new Sportif();
                    $sportif->nom = $nom;
                    $sportif->prenom = $prenom;
                    $sportif->email = $email;
                    $sportif->save();
                }

                $prixTotal = 0;
                if (isset($epreuvesSelection)) {
                    foreach ($epreuvesSelection as $epreuve) {
                        try {
                            $sportif->epreuves()->attach($epreuve);
                            if ($this->user()) {
                                $this->user()->sportif()->save($sportif);
                            }
                            $prixTotal += Epreuve::find($epreuve)->prix;
                        } catch (QueryException $e) {
                            $errorCode = $e->errorInfo[1];
                            if ($errorCode == 1062) {
                                $this->flash('error', 'Vous vous êtes déjà inscrit à l\'épreuve ' . Epreuve::find($epreuve)->nom);
                                return $this->redirect($response, 'epreuve.join', ['id_evenement' => $evenement_id]);
                            }
                        }
                    }
                } else {
                    $this->flash('error', 'Selectionnez au moins une épreuve');
                    return $this->redirect($response, 'epreuve.join', ['id_evenement' => $evenement_id]);
                }

                return $this->view->render($response, 'Epreuve/payment.twig', compact('prixTotal', 'evenement_id'));

            }
        }
        return $this->view->render($response, 'Epreuve/join.twig', compact('evenement', 'epreuves'));
    }

    public function getUploadDir($eventId)
    {
        return $this->settings['events_upload'] . $eventId . '/epreuves';
    }

    public function getPicturePath($eventId, $trialId)
    {
        $path = $this->getUploadDir($eventId) . '/' . $trialId;
        return file_exists($path . '.jpg') ? $path . '.jpg' : $path . '.png';
    }
}
