<?php
declare(strict_types=1);
abstract class Controller{
    /**
     * Afficher une vue
     *
     * @param string $fichier
     * @param array $data
     * @param string $layout
     * @return void
     */
    protected function render(string $fichier, array $data = [], string $layout = "default"){
        $flexDirection = "flex-column";
        extract($data);

        // On démarre le buffer de sortie
        ob_start();

        // On génère la vue
        require(ROOT.'views/'.strtolower(get_class($this)).'/'.$fichier.'.php');

        // On stocke le contenu dans $content
        $content = ob_get_clean();

        // On fabrique le "template"
        if($layout != "")
            require(ROOT."layouts/$layout.php");
        else
            echo $content;
    }

    /**
     * Permet de charger un modèle
     *
     * @param string $model
     * @return void
     */
    protected function loadModel(string $model){
        // On va chercher le fichier correspondant au modèle souhaité
        require_once(ROOT.'models/'.$model.'.php');

        // On crée une instance de ce modèle. Ainsi "Article" sera accessible par $this->Article
        $this->$model = new $model();
    }
}