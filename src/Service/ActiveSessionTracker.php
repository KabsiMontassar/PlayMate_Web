<?php 
namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ActiveSessionTracker
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function trackSession($sessionId)
    {
        // Store session ID in database, cache, or any persistent storage
        // Here, we'll just use the session attribute to store active sessions
        $activeSessions = $this->session->get('active_sessions', []);
        $activeSessions[$sessionId] = true;
        $this->session->set('active_sessions', $activeSessions);
    }

    public function countActiveSessions()
    {
        // Count active sessions from stored data
        $activeSessions = $this->session->get('active_sessions', []);
        return count($activeSessions);
    }
}

