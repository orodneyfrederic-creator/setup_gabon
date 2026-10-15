<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lignes de langage de validation en français
    |--------------------------------------------------------------------------
    */

    'accepted' => 'Le champ :attribute doit être accepté.',
    'active_url' => 'Le champ :attribute n\'est pas une URL valide.',
    'after' => 'Le champ :attribute doit être une date postérieure au :date.',
    'after_or_equal' => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    'alpha' => 'Le champ :attribute doit contenir uniquement des lettres.',
    'alpha_dash' => 'Le champ :attribute doit contenir uniquement des lettres, des chiffres et des tirets.',
    'alpha_num' => 'Le champ :attribute doit contenir uniquement des lettres et des chiffres.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'before' => 'Le champ :attribute doit être une date antérieure au :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale au :date.',

    'between' => [
        'numeric' => 'La valeur de :attribute doit être comprise entre :min et :max.',
        'file' => 'Le fichier :attribute doit avoir une taille entre :min et :max kilo-octets.',
        'string' => 'Le texte :attribute doit contenir entre :min et :max caractères.',
        'array' => 'Le tableau :attribute doit contenir entre :min et :max éléments.',
    ],

    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'image' => 'Le champ :attribute doit être une image.',
    'integer' => 'Le champ :attribute doit être un entier.',

    'max' => [
        'numeric' => 'La valeur de :attribute ne peut pas être supérieure à :max.',
        'file' => 'Le fichier :attribute est trop lourd. Sa taille ne doit pas dépasser :max kilo-octets (environ '.round(1024 / 1024, 1).' Mo).',
        'string' => 'Le texte de :attribute ne peut pas dépasser :max caractères.',
        'array' => 'Le tableau :attribute ne peut pas avoir plus de :max éléments.',
    ],

    'mimes' => 'Le fichier :attribute doit être un fichier de type : :values.',
    'mimetype' => 'Le fichier :attribute doit être un fichier de type : :values.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'required' => 'Le champ :attribute est obligatoire.',

    'attributes' => [
        'image' => 'l\'image',
        'video' => 'la vidéo',
        'nom' => 'le nom',
        'email' => 'l\'adresse e-mail',
        'password' => 'le mot de passe',
    ],

];
