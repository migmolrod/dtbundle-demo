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
        for ($i = 1; $i <= 100; $i++) {
            $post[$i] = new Post();
            $post[$i]->setTitle('Title ' . $i);
            $post[$i]->setContent($this->getPostContent());
            $post[$i]->setVisible(rand(0, 1));
            if (rand(0, 1)) {
                $post[$i]->setAuthorEmail('admin@randomemail.re');
            } else {
                $post[$i]->setAuthorEmail('user@randomemail.re');
            }

            // Comments
            $count = rand(0, 10);
            for ($c = 0; $c <= $count; $c++) {
                $comment[$c] = new Comment();
                $comment[$c]->setTitle('Comment ' . $c);
                $comment[$c]->setComment($this->getCommentContent());
                $comment[$c]->setPost($post[$i]);
                $comment[$c]->setAuthorEmail($post[$i]->getAuthorEmail());

                $manager->persist($comment[$c]);
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
        $content = "But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of
        the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids
        pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful.
        Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil
        and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage
        from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces
        no resultant pleasure?";

        return $content;
    }

    /**
     * Get comment content.
     *
     * @return string
     */
    private function getCommentContent()
    {
        $content = "On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment,
         so blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue; and equal blame belongs to those who fail in their duty through
         weakness of will, which is the same as saying through shrinking from toil and pain. These cases are perfectly simple and easy to distinguish. In a free hour,
         when our power of choice is untrammelled and when nothing prevents our being able to do what we like best, every pleasure is to be welcomed and every pain avoided.
         But in certain circumstances and owing to the claims of duty or the obligations of business it will frequently occur that pleasures have to be repudiated and
         annoyances accepted. The wise man therefore always holds in these matters to this principle of selection: he rejects pleasures to secure other greater pleasures,
         or else he endures pains to avoid worse pains.";

        return $content;
    }
}
