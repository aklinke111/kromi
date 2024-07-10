<?php
// src/EventListener/DataContainer/MakeTextNotMandatoryCallback.php
namespace App\EventListener\DataContainer;

use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsCallback(table: 'tl_content', target: 'config.onload')]
class MakeTextNotMandatoryCallback
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

//    public function __invoke(DataContainer $dc = null): void
    public function __invoke(DataContainer $dc): void
    {
        
//        echo("Test");
//        die;
        var_dump($dc->id);
        //die();
        
        if (null === $dc || !$dc->id || 'edit' !== $this->requestStack->getCurrentRequest()->query->get('act')) {
            return;
        }

        $element = ContentModel::findById($dc->id);

//        echo("Test". $dc->id);
//        die;
        
        
        if (null === $element || 'my_content_element' !== $element->type) {
            return;
        }

        
        $GLOBALS['TL_DCA']['tl_content']['fields']['text']['eval']['mandatory'] = false;
    }
}
