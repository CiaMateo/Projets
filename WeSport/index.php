<?php
// On génère une constante contenant le chemin vers la racine publique du projet
define('ROOT', str_replace('index.php','',$_SERVER['SCRIPT_FILENAME']));

require_once('autoload.php');
require_once(ROOT.'app/Controller.php');
require_once(ROOT.'app/Model.php');

// On sépare les paramètres et on les met dans le tableau $params
$params = explode('/', $_GET['p']);

function error404()
{
    // On envoie le code réponse 404
    http_response_code(404);
    require_once(ROOT.'views/404.php');
    die;
}

// On supprime les arguments vides
for ($i = count($params) - 1; $i >= 2; $i--)
{
    if($params[$i] == "")
        unset($params[$i]);
}



// Si au moins 1 paramètre existe
if($params[0] != ""){
    // On sauvegarde le 1er paramètre dans $controller en mettant sa 1ère lettre en majuscule
    $controller = ucfirst($params[0]) ?? 'main';
    // On sauvegarde le 2ème paramètre dans $action si il existe, sinon index
    $action = isset($params[1]) && !empty($params[1]) ? $params[1] : 'index';
    
    if(file_exists(ROOT.'controllers/'.$controller.'.php'))
    {
        // On appelle le contrôleur
        require_once(ROOT.'controllers/'.$controller.'.php');

        // On instancie le contrôleur
        $controller = new $controller();

        if(method_exists($controller, $action)){
            // On supprime les 2 premiers paramètres
            unset($params[0]);
            unset($params[1]);

            // On appelle la méthode $action du contrôleur $controller
            call_user_func_array([$controller,$action], $params);
        }else{
            error404();
        }
    }
}else{
    // Ici aucun paramètre n'est défini
    // On appelle le contrôleur par défaut
    require_once(ROOT.'controllers/Main.php');

    // On instancie le contrôleur
    $controller = new Main();

    // On appelle la méthode index
    $controller->index();
}