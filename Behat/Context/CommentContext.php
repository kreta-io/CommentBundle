<?php

/*
 * This file belongs to Kreta.
 * The source code of application includes a LICENSE file
 * with all information about license.
 *
 * @author benatespina <benatespina@gmail.com>
 * @author gorkalaucirica <gorka.lauzirika@gmail.com>
 */

namespace Kreta\Bundle\CommentBundle\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Kreta\Bundle\CoreBundle\Behat\Context\DefaultContext;

/**
 * Class CommentContext.
 *
 * @package Kreta\Bundle\CommentBundle\Behat\Context
 */
class CommentContext extends DefaultContext
{
    /**
     * Populates the database with comments.
     *
     * @param \Behat\Gherkin\Node\TableNode $comments The comments
     *
     * @return void
     *
     * @Given /^the following comments exist:$/
     */
    public function theFollowingCommentsExist(TableNode $comments)
    {
        foreach ($comments as $commentData) {
            $issue = $this->get('kreta_issue.repository.issue')->findOneBy(['title' => $commentData['issue']], false);
            $user = $this->get('kreta_user.repository.user')->findOneBy(['email' => $commentData['user']], false);

            $comment = $this->get('kreta_comment.factory.comment')->create($issue, $user);
            $comment->setDescription($commentData['description']);
            if (isset($commentData['updatedAt'])) {
                $this->setField($comment, 'updatedAt', new \DateTime($commentData['updatedAt']));
            }
            if (isset($commentData['createdAt'])) {
                $this->setField($comment, 'createdAt', new \DateTime($commentData['createdAt']));
            }
            if (isset($commentData['id'])) {
                $this->setId($comment, $commentData['id']);
            }

            $this->get('kreta_comment.repository.comment')->persist($comment);
        }
    }
}
