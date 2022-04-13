<?php
declare(strict_types=1);

class Session
{
    /**
     * Démarre une session si possible
     *
     * @throws SessionException Si la session ne peux pas être démarrée
     */
    public static function start(): void
    {
        switch (session_status())
        {
            case PHP_SESSION_DISABLED:
                throw new SessionException("Les sessions sont désactivés");

            case PHP_SESSION_ACTIVE:
                return;

            case PHP_SESSION_NONE:
                if(headers_sent())
                {
                    throw new SessionException("Entêtes PHP déjà envoyés");
                }
                session_start();
                break;

            default:
                throw new SessionException("Status de session inconnu");
        }
    }
}