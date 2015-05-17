<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class BlameableListener
 *
 * @package AppBundle\Listener
 */
class BlameableListener
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(LifeCycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Post || $entity instanceof Comment) {
            // is authentication information available?
            if (null !== $this->tokenStorage->getToken()) {
                // get User
                $user = $this->tokenStorage->getToken()->getUser();
                if (is_object($user)) {
                    $entity->setAuthorEmail($user->getEmail());
                }
            }
        }
    }
}
