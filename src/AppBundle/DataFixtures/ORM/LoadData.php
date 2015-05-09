<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use AppBundle\Entity\User;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadData
 *
 * @package AppBundle\DataFixtures\ORM
 */
class LoadData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Admin
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setPlainPassword('admin');
        $userAdmin->setEmail("admin@randomemail.re");
        $userAdmin->setEnabled(true);
        $userAdmin->setRoles(array('ROLE_ADMIN'));
        $manager->persist($userAdmin);

        // User
        $user1 = new User();
        $user1->setUsername('user');
        $user1->setPlainPassword('user');
        $user1->setEmail("user@randomemail.re");
        $user1->setEnabled(true);
        $user1->setRoles(array('ROLE_USER'));
        $manager->persist($user1);

        // Posts
        for ($i = 1; $i <= 1000; $i++) {
            $post[$i] = new Post();
            $post[$i]->setTitle('Title ' . $i);
            $post[$i]->setContent($this->getPostContent());
            $post[$i]->setVisible(rand(0, 1));
            if (rand(0, 1)) {
                $post[$i]->setAuthorEmail('admin@randomemail.re');
            } else {
                $post[$i]->setAuthorEmail('user@randomemail.re');
            }
            $manager->persist($post[$i]);
        }

        $manager->flush();
    }

    /**
     * Get post content.
     *
     * @return string
     */
    private function getPostContent()
    {
        $content = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.
        At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
        Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.
        At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.";

        return $content;
    }
}
