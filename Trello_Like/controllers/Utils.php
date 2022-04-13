<?php
class Utils extends Controller
{
    public function ProfilePicture(int $id = -1)
    {
        try {
            $img = Sportif::getUserImageById($id);
            header("Content-Type: image/png");
            echo $img;
        } catch (PDOException $pdoEx) {
            http_response_code(500);
        } catch (Exception $ex) {
            http_response_code(400);
        }
    }

    public function Image(int $id = -1)
    {
        try {
            $img = Image::getImageById($id);
            header("Content-Type: image/png");
            echo $img->getBase64Value();
        } catch (PDOException $pdoEx) {
            http_response_code(500);
        } catch (Exception $ex) {
            http_response_code(400);
        }
    }

    public function Activite(int $id = -1)
    {
        try {
            $post = Activite::getActiviteById($id);
            $sender = Sportif::getUserInDatabaseById($post->getIdOrganizer());
            $this->render('activite',
                [
                    'pageTitle' => $post->getTitle(),
                    'post' => $post,
                    'sender' => $sender,
            ],
            '');
        } catch (PDOException $pdoEx) {
            http_response_code(500);
        } catch (Exception $ex) {
            http_response_code(400);
        }
    }

    public function Activites(int $i)
    {
        $filter = $this->CreateFilterOfActivite();
        $activites = Activite::filterActivities($i* 10, 10, $filter);
        foreach ($activites as $act) {
            $sender = Sportif::getUserInDatabaseById($act->getIdOrganizer());
            $this->render('activite',
                [
                    'post' => $act,
                    'sender' => $sender,
                ],
                '');
        }
    }

    public function CreateFilterOfActivite(): array
    {
        return [
            'title' => $_POST['filterTitle'] ?? "",
            'content' => $_POST['filterContent'] ?? "",
            'author' => $_POST['filterAuthor'] ?? "",
            'place' => $_POST['filterCity'] ?? "",
            'skillLevel' => $_POST['filterSkill'] ?? "",
            'dateAfter' => !isset($_POST['filterAfterDate']) || $_POST['filterAfterDate'] == "" ? '1970-01-01' : $_POST['filterAfterDate'],
            'dateBefore' => !isset($_POST['filterBeforeDate']) || $_POST['filterBeforeDate'] == ""? '2170-01-01' : $_POST['filterBeforeDate'],
        ];
    }
}