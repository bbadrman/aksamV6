<?php

namespace App\Http\Controllers;

use App\Models\Automobile;
use App\Models\Fiche;
use App\Models\transporteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{

    public function store(Request $request)
    {

        $auto = new Transporteur();
        $fiche = new Fiche;


        $auto->nom = $request->input('nom');
        $auto->prenom = $request->input('prenom');
        $auto->raison = $request->input('raison');
        $auto->activite = $request->input('activite');
        $auto->assure = $request->input('assure');
        $auto->ancienne = $request->input('ancienne');
        $auto->motif = $request->input('motif');
        $auto->code_postal = $request->input('code');
        $auto->email = $request->input('email');
        $auto->telephone = $request->input('tele');
        $auto->date_transporteur = date("Y-m-d H:i:s");






        if ($auto->ancienne == "NON")
            $auto->motif = "pas de motif";

        $fiche->nom = $request->input('nom');
        $fiche->prenom = $request->input('prenom');
        $fiche->raison_sociale = $request->input('raison');
        $fiche->activite = $request->input('activite');
        $fiche->assure = $request->input('assure');
        $fiche->ancienne = $request->input('ancienne');
        $fiche->motif = $request->input('motif');
        $fiche->code_postal = $request->input('code');
        $fiche->telephone = $request->input('tele');
        $fiche->email = $request->input('email');
        $fiche->date_fiche = date("Y-m-d H:i:s");
        $fiche->id_produit = 3;
        $fiche->id_traitement = 1;
        $fiche->dublique = 0;
        $fiche->id_source = 1;
        $fiche->id_motifcloture = 1;






        $auto->save();
        $fiche->save();

        $request->session()->flash('status', 'formulaire');
        // Envoyer les données à l'API
        $data = [
            'name' => $request->input('nom'),
            'lastname' => $request->input('prenom'),
            'phone' => $request->input('tele'),
            'email' => $request->input('email'),
            'raisonSociale' => $request->input('raison_sociale'),
            'assure' => $request->input('assure'),
            'lastAssure' => $request->input('ancienne'),
            'motifResil' => $request->input('motif'),
            'codePost' => $request->input('code_postal'),
            'typeProspect' => "2",
            'activites' => "1",
            'url' => "11",
            'product_id' => 6
        ];

        // Convertir les données en JSON
        $jsonData = json_encode($data);

        // Initialisation de cURL
        $curl = curl_init();

        // Options de cURL
        curl_setopt_array($curl, [
            CURLOPT_URL => 'http://aksama-assurance.azurewebsites.net/api/prospects',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ],
        ]);

        // Exécution de la requête cURL
        $response = curl_exec($curl);

        // Fermer la session cURL
        curl_close($curl);
        return redirect('/assurance/transporteur');
    }

    public function index()
    {
        return view('layout.form');
    }
}
