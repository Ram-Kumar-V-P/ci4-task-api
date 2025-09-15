<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;

class MailerService
{
    private Email $email;

    public function __construct()
    {
        $this->email = service('email');
    }

    public function notifyTaskAssigned(string $toEmail, string $taskTitle, string $assignerName): bool
    {
        $this->email->setTo($toEmail);
        $this->email->setSubject('You were assigned to a task');
        $this->email->setMessage("Hello,\n\nYou have been assigned to task: {$taskTitle} by {$assignerName}.\n\nThanks,\nTask API");
        return $this->email->send();
    }
}
