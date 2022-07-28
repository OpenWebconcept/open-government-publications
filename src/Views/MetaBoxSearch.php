<?php

declare(strict_types=1);

namespace SudwestFryslan\OpenGovernmentPublications\Views;

use SudwestFryslan\OpenGovernmentPublications\Support\Template;

class MetaBoxSearch extends Template
{
    public function getTemplatePath(): string
    {
        return $this->pathResolver->view('meta-box-search.php');
    }
}
