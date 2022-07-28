<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Views;

use SudwestFryslan\OpenGovernmentPublications\Support\Template;

class MetaBox extends Template
{
    public function getTemplatePath(): string
    {
        return $this->pathResolver->view('meta-box.php');
    }
}
