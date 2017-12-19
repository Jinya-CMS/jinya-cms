<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:51
 */

namespace DataBundle\Services\Form;

use DataBundle\Entity\Form;
use DataBundle\Services\Base\BaseStaticContentService;
use Doctrine\ORM\EntityManager;
use HelperBundle\Services\Slug\SlugServiceInterface;

class FormService extends BaseStaticContentService implements FormServiceInterface
{

    /**
     * FormService constructor.
     * @param EntityManager $entityManager
     * @param SlugServiceInterface $slugService
     */
    public function __construct(EntityManager $entityManager, SlugServiceInterface $slugService)
    {
        parent::__construct($entityManager, $slugService, Form::class);
    }
}