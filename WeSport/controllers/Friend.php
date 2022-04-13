<?php
class Friend extends Controller
{
    // http://rimb0012/user/addFriend/$user1/$user2]
    public function addFriend(int $user1, int $user2)
    {
        try {
            Amitie::registerFriend($user1, $user2);
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }

    // http://rimb0012/user/getFriends/$user]
    public function getFriends(int $user)
    {
        try {
            $friends = Amitie::getFriends($user);
            foreach ($friends as $friend)
            {
                echo Sportif::getUserInDatabaseById($friend)->displayInformation();
            }
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }

    // http://rimb0012/user/getFriends/$user]
    public function removeFriend(int $user1, int $user2)
    {
        try {
            Amitie::removeFriend($user1, $user2);
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }

    // http://rimb0012/user/getFriends/$user]
    public function filterUser(string $filter = "")
    {
        try {
            echo urldecode($filter);
            $users = Sportif::filterUser(urldecode($filter));
            foreach ($users as $user)
            {
                echo Sportif::getUserInDatabaseById($user)->displayInformation();
            }
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }

    // http://rimb0012/user/getFriends/$user]
    public function filterFriends(int $user, string $filter = "")
    {
        try {
            echo urldecode($filter);
            $friends = Amitie::filterFriends($user, urldecode($filter));
            foreach ($friends as $friend)
            {
                echo Sportif::getUserInDatabaseById($friend)->displayInformation();
            }
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }
}