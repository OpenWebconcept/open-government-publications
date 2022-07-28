<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Views;

use SudwestFryslan\OpenGovernmentPublications\Support\Template;

class Reset extends Template
{
    public function getTemplatePath(): string
    {
        return $this->pathResolver->view('reset.php');
    }
}
