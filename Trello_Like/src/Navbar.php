<?php

class Navbar{
    public static function getBasicNavbar(){
        $auth = new SecureUserAuthentication();
        $profileButton = $auth->getProfileOptionButton();
        $brandName = GlobalValues::HTML_BRANDNAME;
        return <<<HTML
    <nav class="navbar shadow-sm sticky-top navbar-light bg-light">
        <div class="container-fluid">
            <div class="d-flex flex-row justify-content-between w-100">
                <a class="brand-small mx-3 no-underline" href="/">$brandName</a>
                $profileButton
            </div>
        </div>
    </nav>
HTML;
    }

    public static function getFeedSearchNavbar(){
        $auth = new SecureUserAuthentication();
        $profileButton = $auth->getProfileOptionButton();
        $brandName = GlobalValues::HTML_BRANDNAME;
        $filter = [
            'title' => $_POST['filterTitle'] ?? "",
            'content' => $_POST['filterContent'] ?? "",
            'author' => $_POST['filterAuthor'] ?? "",
            'place' => $_POST['filterCity'] ?? "",
            'skillLevel' => $_POST['filterSkill'] ?? "",
            'dateAfter' => $_POST['filterAfterDate'] ?? "",
            'dateBefore' => $_POST['filterBeforeDate'] ?? "",
        ];
        $html = <<<HTML
    <nav class="navbar shadow-sm sticky-top navbar-light bg-light">
        <div class="container-fluid mb-2">
            <div class="d-flex flex-row justify-content-between w-100">
                <a class="brand-small mx-3 no-underline" href="/">$brandName</a>
                $profileButton
            </div>
        </div>
        <div class="container-fluid d-flex flex-column align-items-stretch">
            <div class="accordion" id="filterAccordion">
                <div class="accordion-item">
                    <span class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse">
                            Filtrer les résultats
                        </button>
                    </span>
                    <div id="filterCollapse" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#filterAccordion">
                        <form class="accordion-body" name="filterActitive" method="post">
                            <div class="row g-3 mb-2">
                                <div class="col-lg-3">
                                    <input type="text" class="form-control" placeholder="Filtrer par titre" name="filterTitle" aria-label="Title" value="{$filter['title']}">
                                </div>
                                <div class="col-lg-3">
                                    <input type="text" class="form-control" placeholder="Filtrer par auteur" name="filterAuthor" aria-label="Author" value="{$filter['author']}">
                                </div>
                                <div class="col-lg-3">
                                    <input type="text" class="form-control" placeholder="Filtrer par ville" name="filterCity" aria-label="City" value="{$filter['place']}">
                                </div>
                                <div class="col-lg-3">
                                    <input type="text" class="form-control" placeholder="Filtrer par niveau" name="filterSkill" aria-label="Skill" value="{$filter['skillLevel']}">
                                </div>
                            </div>
                            <div class="row g-3 mb-2">
                                <div class="col-lg-2">
                                    Chercher les activités prévu pour après :
                                </div>
                                <div class="col-lg-2">
                                    <input type="date" class="form-control" aria-label="Title" name="filterAfterDate" value="{$filter['dateAfter']}">
                                </div>
                                <div class="col-lg-2">
                                    Chercher les activités prévu pour avant :
                                </div>
                                <div class="col-lg-2">
                                    <input type="date" class="form-control" aria-label="Author" name="filterBeforeDate" value="{$filter['dateBefore']}">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" class="form-control" placeholder="Filtrer par contenu" name="filterContent" aria-label="City" value="{$filter['content']}">
                                </div>
                                <div class="col-lg-2">
                                    <button type="submit" class="btn btn-main round-input float-end">Rechercher<i class="ms-1 bi bi-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
HTML;

        if($auth->isUserConnected()) {
            $html .= <<<HTML
            <button type="button" onclick="window.location.href='/main/create'" class="my-2 btn btn-main round-input">
                Créer une activité
            </button>
HTML;
        }

        $html .= <<< HTML
        </div>
    </nav>
HTML;
        return $html;
    }
}