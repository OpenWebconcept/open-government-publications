<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Views;

use SudwestFryslan\OpenGovernmentPublications\Support\Template;

class Import extends Template
{
    public function getTemplatePath(): string
    {
        return $this->pathResolver->view('import.php');
    }
}
