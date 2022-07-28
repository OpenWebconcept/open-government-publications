<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Views;

use SudwestFryslan\OpenGovernmentPublications\Support\Template;

class Endpoints extends Template
{
    public function getTemplatePath(): string
    {
        return $this->pathResolver->view('endpoints.php');
    }
}
