<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:51
 */

namespace Jinya\Services\Form;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Form;
use Jinya\Services\Base\BaseStaticContentService;
use Jinya\Services\Slug\SlugServiceInterface;

class FormService extends BaseStaticContentService implements FormServiceInterface
{

    /**
     * FormService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService)
    {
        parent::__construct($entityManager, $slugService, Form::class);
    }
}